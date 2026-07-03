<?php

session_start();

include "db_config.php";

header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['shop'])) {
        throw new Exception("Session expired. Please login again.");
    }

    $user_id = $_SESSION['user_id'];
    $shop_id = $_SESSION['shop'];

    $conn->begin_transaction();

    $paid_amount = $_POST["paid_amount"];
    $trans_amount = $_POST["trans_amount"];
    $pay_type = $_POST["pay_type"];
    $ref_no = $_POST["ref_no"];
    $pending = $trans_amount - $paid_amount;
    $gst = $_POST["gst"];
    $tally = $_POST["tally"];
    $trans_id = $_POST["trans_id"];
    $payment_date = $_POST["trans_date"];
    $customer = $_POST["customer"];
    $account = $_POST["account"];
    $customer_name = $_POST["customer_name"];

    if ($payment_date == "") {
        $payment_date_sql = "NOW()";
    } else {
        $payment_date_sql =
            "STR_TO_DATE('$payment_date','%d-%m-%Y')";

    }

    $actual_settled = $paid_amount;
    if (strtolower(trim($pay_type)) == 'credit') {
        $actual_settled = 0;
    }

    $sql = "update sales_trans set pay_type='" . $pay_type . "',ref_no='" . $ref_no . "',pending=pending-" . $actual_settled . ",paid_amount=IFNULL(paid_amount,0)+" . $paid_amount . "
 WHERE trans_id='" . $trans_id . "'";

    $result = $conn->query($sql);

    /* ==========================
       GENERATE PAYMENT ID
       ========================== */

    $stmt = $conn->prepare("
    SELECT slno
    FROM config
    WHERE item='PAYMENT'
    FOR UPDATE
");
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        throw new Exception("PAYMENT row not found in config table.");
    }

    $payment_id = (int) $row['slno'] + 1;

    $stmt = $conn->prepare("
    UPDATE config
    SET slno=?
    WHERE item='PAYMENT'
");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();

    $sql = "INSERT INTO payments (
            payment_id, payment_date, nature, 
            customer, customer_name, mode, account,
            amount, reference, active_status, shop_id, created_on,created_by
        ) VALUES (
            '$payment_id', $payment_date_sql, '$pay_type', 
            '$customer', '$customer_name', '$pay_type', '$account', $paid_amount,
            '$ref_no', 'A', '$shop_id', NOW(),'" . $user_id . "'
        )";
    $result = $conn->query($sql);

    if ($actual_settled > 0) {
        $sql = "update account set balance=balance+" . $actual_settled . " where account_id='" . $account . "'";
        $result = $conn->query($sql);
    }

    $sql = "insert into pay_track(payment_id,amount_settled,trans_id,mode) values('" . $payment_id . "','" . $actual_settled . "','" . $trans_id . "','sales')";

    $conn->query($sql);

    $conn->commit();

    echo json_encode([
        "status" => "success",
        "payment_id" => $payment_id,
        "message" => "Sales Transaction added successfully"
    ]);


} catch (Exception $e) {

    if (isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
    }

    error_log("update_pay.php : " . $e->getMessage());

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    $conn->close();

}
?>