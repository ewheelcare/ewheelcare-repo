<?php
include "db_config.php";
session_start();
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

// 🔹 Inputs
$trans_id    = $_POST["trans_id"] ?? '';
$subtrans_id = $_POST["subtrans_id"] ?? '';
$item_id     = (int)($_POST["item_id"] ?? 0);

// 🔹 Session validation
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == '') {
    error_log("DELETE API: Session expired");
    echo json_encode([
        "status" => "error",
        "message" => "Session expired. Please login again."
    ]);
    exit;
}

$user_id = $_SESSION["user_id"];

try {

    // 🔴 Input validation
    if ($trans_id === '' || $subtrans_id === '' || $item_id === 0) {
        error_log("DELETE API: Invalid request | trans_id=$trans_id, subtrans_id=$subtrans_id, item_id=$item_id");
        throw new Exception("Invalid request");
    }

    $conn->begin_transaction();

    // 🔹 Lock row
    $lock_sql = "
        SELECT subtrans_id 
        FROM receipt_trans_det
        WHERE trans_id='$trans_id'
        AND item_id='$item_id'
        AND subtrans_id='$subtrans_id'
        AND active_status!='Z'
        FOR UPDATE
    ";

    // error_log("DELETE API: Lock query => $lock_sql");

    $res = $conn->query($lock_sql);

    if (!$res || $res->num_rows === 0) {
        error_log("DELETE API: Record not found or already deleted");
        throw new Exception("Record not found or already deleted");
    }
    $res->free();
    // 🔹 Soft delete
    $delete_sql = "
        UPDATE receipt_trans_det 
        SET active_status='Z',
            modified_on=NOW(),
            modified_by='$user_id'
        WHERE trans_id='$trans_id'
        AND item_id='$item_id'
        AND subtrans_id='$subtrans_id'
    ";

    // error_log("DELETE API: Delete query => $delete_sql");

    if (!$conn->query($delete_sql)) {
        error_log("DELETE API: Delete failed | ".$conn->error);
        throw new Exception("Delete failed");
    }

    // 🔹 Recalculate totals
    $total_sql = "
        SELECT 
            COALESCE(SUM(total),0) AS grand_total,
            COALESCE(SUM(tax_amount),0) AS tax,
            COALESCE(SUM(tax_amount_sgst),0) AS sgst,
            COALESCE(SUM(tax_amount_igst),0) AS igst,
            COALESCE(SUM(price),0) AS price,
            COALESCE(SUM(discount),0) AS discount,
            COALESCE(SUM(roundoff),0) AS roundoff
        FROM receipt_trans_det
        WHERE trans_id='$trans_id'
        AND active_status!='Z'
    ";

    // error_log("DELETE API: Total query => $total_sql");

    $res = $conn->query($total_sql);

    if (!$res) {
        error_log("DELETE API: Total calculation failed | ".$conn->error);
        throw new Exception("Total calculation failed");
    }

    $data = $res->fetch_assoc();
    $res->free();
    if (!$data) {
        $data = [
            "grand_total" => 0,
            "tax" => 0,
            "sgst" => 0,
            "igst" => 0,
            "price" => 0,
            "discount" => 0,
            "roundoff" => 0
        ];
    }

    // 🔹 Update header
    $header_sql = "
        UPDATE receipt_trans 
        SET trans_amount='{$data['grand_total']}',
            pending='{$data['grand_total']}'
        WHERE trans_id='$trans_id'
    ";

   // error_log("DELETE API: Header update => $header_sql");

    if (!$conn->query($header_sql)) {
        error_log("DELETE API: Header update failed | ".$conn->error);
        throw new Exception("Header update failed");
    }

    $conn->commit();

    // error_log("DELETE API: SUCCESS | trans_id=$trans_id, subtrans_id=$subtrans_id");

    // 🔹 Response
    echo json_encode([
        "status" => "success",
       "data" => [
            "subtrans_id" => $subtrans_id,
            "grand_total" => $data['grand_total'],
            "tax" => $data['tax'],
            "sgst" => $data['sgst'],
            "igst" => $data['igst'],
            "price" => $data['price'],
            "discount" => $data['discount'],
            "roundoff" => $data['roundoff'],
            "net" => ($data['price'] - $data['discount'] + $data['roundoff'])
]
    ]);
exit;

} catch (Throwable  $e) {


if ($conn->in_transaction) {
        $conn->rollback();
    }

error_log("DELETE API ERROR: " . $e->getMessage() . 
          " | trans_id=$trans_id, subtrans_id=$subtrans_id, item_id=$item_id");
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    exit;
}


$conn->close();
?>