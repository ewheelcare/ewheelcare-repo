<?php
session_start();

include "db_config.php";

header('Content-Type: application/json');

$user_id = $_SESSION["user_id"] ?? ($_COOKIE["user_id"] ?? "");
$shop    = $_SESSION["shop"]    ?? ($_COOKIE["shop"] ?? "");

if ($user_id == "" || $shop == "") {
    echo json_encode([
        "success" => false,
        "login_required" => true,
        "message" => "Session expired. Please login again."
    ]);
    exit();
}
$paid_amount   = $_POST["paid_amount"];
$trans_amount  = $_POST["trans_amount"];
$pay_type      = $_POST["pay_type"];
$ref_no        = $_POST["ref_no"];
$gst           = $_POST["gst"];
$tally         = $_POST["tally"];
$trans_id      = $_POST["trans_id"];
$payment_date  = $_POST["trans_date"];
$customer      = $_POST["customer"];
$account       = $_POST["account"];
$customer_name = $_POST["customer_name"];
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    $conn->begin_transaction();

    /* -----------------------------------
       Generate payment_id from config table
       ----------------------------------- */

    $sql = "SELECT slno 
            FROM config 
            WHERE item='PAYMENT'
            FOR UPDATE";

    $result = $conn->query($sql);

    if ($row = $result->fetch_assoc()) {
        $payment_id = $row["slno"] + 1;
    } else {
        throw new Exception("PAYMENT row not found in config table");
    }

    $sql = "UPDATE config 
            SET slno='$payment_id'
            WHERE item='PAYMENT'";

    $conn->query($sql);

    /* -----------------------------------
       Update service transaction
       ----------------------------------- */

    if ($pay_type != "CREDIT") {

        $sql = "UPDATE service_trans 
                SET pay_type='$pay_type',
                    ref_no='$ref_no',
                    pending = pending - $paid_amount,
                    modified_by = '$user_id',
                    modified_on = NOW()
                WHERE trans_id='$trans_id'";

        $conn->query($sql);
    }

    /* -----------------------------------
       Insert payment entry
       ----------------------------------- */

    $sql = "INSERT INTO payments (
                payment_id,
                payment_date,
                nature,
                customer,
                customer_name,
                mode,
                account,
                amount,
                reference,
                active_status,
                shop_id,
                created_by
            ) VALUES (
                '$payment_id',
                STR_TO_DATE('$payment_date','%d-%m-%Y'),
                'CREDIT',
                '$customer',
                '$customer_name',
                '$pay_type',
                '$account',
                '$paid_amount',
                '$ref_no',
                'A',
                '$shop',
                '$user_id'
            )";

    $conn->query($sql);

    /* -----------------------------------
       Update account balance
       ----------------------------------- */

    if ($pay_type != "CREDIT") {

        $sql = "UPDATE account
                SET balance = balance + $paid_amount
                WHERE account_id='$account'";

        $conn->query($sql);
    }

    /* -----------------------------------
       Insert pay tracking
       ----------------------------------- */

    $sql = "INSERT INTO pay_track(
                payment_id,
                amount_settled,
                trans_id,
                mode
            ) VALUES (
                '$payment_id',
                '$paid_amount',
                '$trans_id',
                'service'
            )";

    $conn->query($sql);

    $conn->commit();

    echo json_encode([
              "success"=>true,
              "message"=>"Payment Updated Successfully"
                    ]);

} 
    catch (Exception $e) {

    $conn->rollback();

    error_log(
        "update_pay.php ".
        "TransID=".$trans_id.
        " User=".$user_id.
        " Shop=".$shop.
        " Error=".$e->getMessage()
    );

    echo json_encode([
        "success"=>false,
        "message"=>"Payment update failed"
    ]);
                      }

$conn->close();
?>