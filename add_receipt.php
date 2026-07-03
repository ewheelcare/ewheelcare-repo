<?php
session_start();
include "db_config.php";
ini_set('display_errors', 0);
error_reporting(E_ALL);
/* =====================================
   Transaction Control + Error Catching
   + Apache error_log
   + created_by from Cookie user_id
   + config uses column VALUE
   ===================================== */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set("Asia/Kolkata");
header('Content-Type: application/json');
try {

    /* Start Transaction */
    $conn->begin_transaction();

    /* User ID from Cookie */
    $user_id = $_SESSION['user_id'] ?? 'SYSTEM';
    $shop_id = $_SESSION['shop'] ?? null;


        if (!$shop_id) {
            throw new Exception("Shop ID missing");
        }

    /* Get POST Values */
    $trans_date       = isset($_POST["trans_date"]) ? $_POST["trans_date"] : '';
    $vendor           = isset($_POST["customer"]) ? $_POST["customer"] : '';
    $gst              = 'Y';
    $invoice_no       = isset($_POST["invoice_no"]) ? $_POST["invoice_no"] : '';
    $vehicle_no       = isset($_POST["vehicle_no"]) ? $_POST["vehicle_no"] : '';
    $company_name     = isset($_POST["company_name"]) ? $_POST["company_name"] : '';
    $customer_name    = isset($_POST["customer_name"]) ? $_POST["customer_name"] : '';
    $customer_address = isset($_POST["customer_address"]) ? $_POST["customer_address"] : '';
    $customer_mobile  = isset($_POST["customer_mobile"]) ? $_POST["customer_mobile"] : '';
    $customer_gst     = isset($_POST["customer_gst"]) ? $_POST["customer_gst"] : '';

    /*========================================
       Validation check
===================================== */
       if(empty($company_name)){
    throw new Exception("Company name is required");
}

if(empty($customer_mobile)){
    throw new Exception("Mobile number is required");
}

if(empty($invoice_no)){
    throw new Exception("Invoice number is required");
}

if(empty($trans_date)){
    throw new Exception("Transaction date is required");
}
  
    /* =====================================
       Duplicate Invoice Check
       ===================================== */

    $stmt = $conn->prepare("
    SELECT trans_id
    FROM receipt_trans
    WHERE vendor=? AND invoice_no=? AND active_status='A'
    LIMIT 1
        ");
        $stmt->bind_param("ss", $vendor, $invoice_no);
        $stmt->execute();
        $result_receipt = $stmt->get_result();
    if ($result_receipt && $result_receipt->fetch_assoc()) {
        throw new Exception("Invoice already added for vendor");
    }

    /* =====================================
       Lock Config Row
       SELECT value (not slno)
       ===================================== */

    $sql = "SELECT slno
            FROM config
            WHERE item='PURCHASE'
            FOR UPDATE";

    $res = $conn->query($sql);

    if (!$row = $res->fetch_assoc()) {
        throw new Exception("Config row PURCHASE not found");
    }

    $trans_id = $row["slno"] + 1;

    /* Update Config */

    $sql2 = "UPDATE config
             SET slno='$trans_id'
             WHERE item='PURCHASE'";

    $conn->query($sql2);

    /* =====================================
       Insert Receipt
       ===================================== */

    $stmt = $conn->prepare("
        INSERT INTO receipt_trans (
            trans_id, vendor, trans_date, active_status, gst,
            company_name, customer_name, customer_address,
            customer_gst, customer_mobile, invoice_no, created_by,shop
        ) VALUES (
            ?, ?, STR_TO_DATE(?,'%d-%m-%Y'), 'D', ?,
            ?, ?, ?, ?, ?, ?, ?,?
        )");
        $stmt->bind_param(
            "ssssssssssss",
            $trans_id,
            $vendor,
            $trans_date,
            $gst,
            $company_name,
            $customer_name,
            $customer_address,
            $customer_gst,
            $customer_mobile,
            $invoice_no,
            $user_id,
            $shop_id
        );
        $stmt->execute();

    /* Commit */
    $conn->commit();

echo json_encode([
    "status" => "success",
    "trans_id" => $trans_id,
    "message" => "Receipt created successfully"
]);
exit;
    
} catch (Exception $e) {

    /* Rollback */
    $conn->rollback();

    error_log(
        "ERP | add_receipt.php | " .
        date("Y-m-d H:i:s") .
        " | User:" . $user_id .
        " | Invoice:" . $invoice_no .
        " | Vendor:" . $vendor .
        " | Error:" . $e->getMessage()
    );

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    exit;
}

$conn->close();
?>