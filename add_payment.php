<?php
header('Content-Type: application/json');
session_start();
include "db_config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


$payment_date = $_POST["payment_date"];
$nature = $_POST["nature"];
$vendor = $_POST["vendor"];
$vendor_name = $_POST["vendor_name"];
$customer = $_POST["customer"];
$customer_name = $_POST["customer_name"];
$mode = $_POST["mode"];
$account = $_POST["account"];
$account_to = $_POST["account_to"];
$amount = $_POST["amount"];
$reference = $_POST["reference"];
$receipt_type = $_POST["receipt_type"] ?? '';
$amount = (float)$_POST["amount"];
$payment_amount = $amount;
$pending_total = 0;
try
{
$conn->begin_transaction();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['shop'])) {
    throw new Exception("Session expired. Please login again.");
}
    $user_id = $_SESSION['user_id'];
    $shop_id = $_SESSION['shop'];
if ($nature == "DEBIT" || $mode == "CREDIT") {
    if (isset($_COOKIE["SA"])) {
        $verified = "Y";
    } else {
        $verified = "N";
    }
} else {
    $verified = "Y";
}
///check if amount is permissible

if ($nature == "DEBIT") {

    $select_receipt_trans = "SELECT sum(pending) pending FROM receipt_trans where vendor='" . $vendor . "' and pending>0 and active_status='A'";
    $result = $conn->query($select_receipt_trans);

    if (($row = $result->fetch_assoc()) && $amount > 0) {
        $pending_total = $row["pending"];
    }
}
if ($nature == "CREDIT") {
    $pending_total = 0;
    if ($receipt_type == "service" || $receipt_type == "") {
        $select_receipt_trans = "SELECT sum(pending) pending FROM service_trans where customer='" . $customer . "' and pending>0 and active_status='A'";
        $result = $conn->query($select_receipt_trans);

        if (($row = $result->fetch_assoc()) && $amount > 0) {
            $pending_total += $row["pending"];
        }
    }

    if ($receipt_type == "sales" || $receipt_type == "") {
        $select_receipt_trans = "SELECT sum(pending) pending FROM sales_trans where customer='" . $customer . "' and pending>0 and active_status='A'";
        $result = $conn->query($select_receipt_trans);

        if (($row = $result->fetch_assoc()) && $amount > 0) {
            $pending_total += $row["pending"];
        }
    }
}
if ($pending_total < $amount) {
    throw new Exception("Cannot pay more than what is pending");
}

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

$payment_id = (int)$row['slno'] + 1;


$stmt = $conn->prepare("
    UPDATE config
    SET slno=?
    WHERE item='PAYMENT'
");

$stmt->bind_param("i", $payment_id);
$stmt->execute();

$sql = "INSERT INTO payments (
            payment_id, payment_date, nature, vendor, vendor_name,
            customer, customer_name, mode, account,
            amount, reference, active_status, shop_id, created_on, to_account, VERIFIED
        ) VALUES (
            '$payment_id', STR_TO_DATE('$payment_date', '%d-%m-%Y'), '$nature', '$vendor', '$vendor_name',
            '$customer', '$customer_name', '$mode', '$account', $amount,
            '$reference', 'A', '$shop_id', NOW(),'" . $account_to . "','" . $verified . "'
        )";
$result = $conn->query($sql);
//echo $sql;
if ($nature == "DEBIT") {

    $select_receipt_trans = "SELECT trans_id,pending FROM receipt_trans where vendor='" . $vendor . "' and pending>0 and active_status='A' order by trans_date";
    $result = $conn->query($select_receipt_trans);

    while (($row = $result->fetch_assoc()) && $amount > 0) {
        $pending = $row["pending"];
        $trans_id = $row["trans_id"];
        $amount_settled = 0;
        if ($amount > $pending) {
            $amount_settled = $pending;
            $pending = 0;

        } else {
            $pending = $pending - $amount;
            $amount_settled = $amount;
        }
        if ($mode != "CREDIT") {
            $update_receipt_trans = "update receipt_trans set pending=" . $pending . " where trans_id='" . $trans_id . "'";
            $conn->query($update_receipt_trans);
        }
        $insert_pay_track = "insert into pay_track(payment_id,amount_settled,trans_id,mode) values('" . $payment_id . "','" . $amount_settled . "','" . $trans_id . "','RECEIPT')";
        $conn->query($insert_pay_track);
        $amount = $amount - $amount_settled;    }



}

if ($nature == "CREDIT") {
    if ($receipt_type == "service" || $receipt_type == "") {
        $select_receipt_trans = "SELECT trans_id,pending FROM service_trans where customer='" . $customer . "' and pending>0 and active_status='A' order by trans_date";
        $result = $conn->query($select_receipt_trans);

        while (($row = $result->fetch_assoc()) && $amount > 0) {
            $pending = $row["pending"];
            $trans_id = $row["trans_id"];
            $amount_settled = 0;
            if ($amount > $pending) {
                $amount_settled = $pending;
                $pending = 0;
            } else {
                $pending = $pending - $amount;
                $amount_settled = $amount;
            }
            if ($mode != "CREDIT") {
                $update_receipt_trans = "
                UPDATE service_trans
                 SET
                  pending = $pending,
              modified_on = NOW(),
               modified_by = '$user_id'
             WHERE trans_id = '$trans_id'
            ";
                $conn->query($update_receipt_trans);
            }
            $insert_pay_track = "insert into pay_track(payment_id,amount_settled,trans_id,mode) values('" . $payment_id . "','" . $amount_settled . "','" . $trans_id . "','service')";

            $conn->query($insert_pay_track);
            $amount = $amount - $amount_settled;
        }
    }

    if ($amount > 0 && ($receipt_type == "sales" || $receipt_type == "")) {
        $select_receipt_trans = "SELECT trans_id,pending FROM sales_trans where customer='" . $customer . "' and pending>0 and active_status='A' order by trans_date";
        $result = $conn->query($select_receipt_trans);

        while (($row = $result->fetch_assoc()) && $amount > 0) {
            $pending = $row["pending"];
            $trans_id = $row["trans_id"];
            $amount_settled = 0;
            if ($amount > $pending) {
                $amount_settled = $pending;
                $pending = 0;
            } else {
                $pending = $pending - $amount;
                $amount_settled = $amount;
            }
            if ($mode != "CREDIT") {
                $update_receipt_trans = $update_receipt_trans = "
                UPDATE sales_trans
                SET
                  modified_on = NOW(),
                  modified_by = '$user_id',
                  pending = $pending
             WHERE trans_id = '$trans_id'
            ";
                $conn->query($update_receipt_trans);
            }
            $insert_pay_track = "insert into pay_track(payment_id,amount_settled,trans_id,mode) values('" . $payment_id . "','" . $amount_settled . "','" . $trans_id . "','sales')";

            $conn->query($insert_pay_track);
            $amount = $amount - $amount_settled;
        }
    }
}
if (($nature == "DEBIT" || $nature == "BANKTOCASH" || $nature == "BANKTOBANK" || $nature == "CASHTOBANK") && $mode != "CREDIT") {
    $update_master = "update account set balance=balance-" . $payment_amount . " where account_id='" . $account . "'";
    //echo 	$update_master;
    $conn->query($update_master);
}
if ($nature == "CREDIT" && $mode != "CREDIT") {
    $update_master = "update account set balance=balance+" . $payment_amount . " where account_id='" . $account . "'";
    $conn->query($update_master);
}
if (($nature == "BANKTOBANK" || $nature == "CASHTOBANK" || $nature == "BANKTOCASH") && $mode != "CREDIT") {
    $update_master = "update account set balance=balance+" . $payment_amount . " where account_id='" . $account_to . "'";
    $conn->query($update_master);
}
$conn->commit();
echo json_encode([
    "status" => "success",
    "payment_id" => $payment_id,
    "message" => "Receipt added successfully"
]);

}
catch (Exception $e) {

    if (isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
    }

    error_log(
        "payments_save.php ERROR: ".
        $e->getMessage().
        " File: ".$e->getFile().
        " Line: ".$e->getLine()
    );
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
finally {

    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>