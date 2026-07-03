<?php
session_start();
include "db_config.php";

header('Content-Type: application/json');

try {

    // 🔴 Validate input
    $trans_id = trim($_POST["trans_id"] ?? '');

    if ($trans_id === '') {
        throw new Exception("Invalid transaction");
    }

    // 🔴 Session check
    $user_id = $_SESSION["user_id"] ?? '';

    if ($user_id === '') {
        throw new Exception("Session expired");
    }

    // 🔴 Get values
    $company_name     = trim($_POST["company_name"] ?? '');
    $customer_name    = trim($_POST["customer_name"] ?? '');
    $customer_mobile  = trim($_POST["customer_mobile"] ?? '');
    $customer_address = trim($_POST["customer_address"] ?? '');
    $customer_gst     = trim($_POST["customer_gst"] ?? '');
    $invoice_no       = trim($_POST["invoice_no"] ?? '');
    $trans_date       = trim($_POST["trans_date"] ?? '');

    // 🔴 Update only if still in Draft
    $stmt = $conn->prepare("
        UPDATE receipt_trans SET
            company_name     = ?,
            customer_name    = ?,
            customer_mobile  = ?,
            customer_address = ?,
            customer_gst     = ?,
            invoice_no       = ?,
            trans_date       = STR_TO_DATE(?, '%d-%m-%Y'),
            modified_by      = ?,
            modified_on      = NOW()
        WHERE trans_id = ?
        AND active_status = 'D'
    ");

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param(
        "sssssssss",
        $company_name,
        $customer_name,
        $customer_mobile,
        $customer_address,
        $customer_gst,
        $invoice_no,
        $trans_date,
        $user_id,
        $trans_id
    );

    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        // Either already finalized OR no change
        echo json_encode([
            "status" => "warning",
            "message" => "No update (maybe finalized or no changes)"
        ]);
        exit;
    }

    $stmt->close();

    echo json_encode([
        "status" => "success",
        "message" => "Header updated"
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>