<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_log("Legacy add_service_det.php called");
include "db_config.php";

// ✅ SESSION FIX
if (!isset($_SESSION["user_id"])) {
    $_SESSION["user_id"] = "SYSTEM";
}

$service_id        = $_POST["service_id"];
$vehicle           = $_POST["vehicle"];
$price             = floatval($_POST["price"] ?? 0);
$qty               = floatval($_POST["qty"] ?? 0);
$discount          = floatval($_POST["discount"] ?? 0);
$trans_id          = $_POST["trans_id"];

$taxable           = floatval($_POST["taxable"] ?? 0);
$tax_pc            = floatval($_POST["tax"] ?? 0);
$tax_pc_sgst       = floatval($_POST["tax_sgst"] ?? 0);
$tax_amount        = floatval($_POST["tax_amount"] ?? 0);
$tax_amount_sgst   = floatval($_POST["tax_amount_sgst"] ?? 0);
$grand_total       = floatval($_POST["grand_total"] ?? 0);

// ================= GET GST =================
// $stmt = $conn->prepare("SELECT CUSTOMER_GST FROM service_trans WHERE trans_id=?");
// $stmt->bind_param("s", $trans_id);
// $stmt->execute();
// $result = $stmt->get_result();

// $customer_gst = "";
// if ($row = $result->fetch_assoc()) {
//     $customer_gst = $row["CUSTOMER_GST"];
// }

// // ================= TAX =================
// // Also fetch the shop-level GST flag from service_trans
// $stmt2 = $conn->prepare("SELECT gst FROM service_trans WHERE trans_id=?");
// $stmt2->bind_param("s", $trans_id);
// $stmt2->execute();
// $row2 = $stmt2->get_result()->fetch_assoc();
// $shop_gst = $row2["gst"] ?? "N";

// $tax_pc              = floatval($_POST['tax']);
// $tax_pc_sgst         = floatval($_POST['tax_sgst']);
// $tax_amount          = floatval($_POST['tax_amount']);
// $tax_amount_sgst     = floatval($_POST['tax_amount_sgst']);
// $total_cost          = floatval($_POST['total']);

// ================= CALC =================
// $total_cost = ($cost * $qty) - $discount;
// if ($total_cost < 0) $total_cost = 0;

// $tax_amount = ($total_cost * $tax_pc) / 100;
// $tax_amount_sgst = ($total_cost * $tax_pc_sgst) / 100;

// ================= SOFT DELETE =================
$stmt = $conn->prepare("
    UPDATE service_trans_det 
    SET active_status='Z', modified_on=NOW(), modified_by=? 
    WHERE trans_id=? AND service_id=?
");
$stmt->bind_param("sss", $_SESSION["user_id"], $trans_id, $service_id);
$stmt->execute();

// ================= NEXT ID =================
$stmt = $conn->prepare("
    SELECT IFNULL(MAX(subtrans_id),1000001)+1 subtrans_id 
    FROM service_trans_det WHERE trans_id=?
");
$stmt->bind_param("s", $trans_id);
$stmt->execute();
$result = $stmt->get_result();

$subtrans_id = 1000001;
if ($row = $result->fetch_assoc()) {
    $subtrans_id = $row["subtrans_id"];
}

// ================= INSERT =================
$line_total = $taxable; // taxable before tax

$stmt = $conn->prepare("
INSERT INTO service_trans_det(
    trans_id, subtrans_id, service_id, vehicle,
    cost, qty, total, tax, tax_amount,
    active_status, created_on, created_by,
    tax_sgst, tax_amount_sgst, discount
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'A', NOW(), ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssdddidsidd",
    $trans_id,
    $subtrans_id,
    $service_id,
    $vehicle,
    $price,
    $qty,
    $line_total,
    $tax_pc,
    $tax_amount,
    $_SESSION["user_id"],
    $tax_pc_sgst,
    $tax_amount_sgst,
    $discount
);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->execute()) {
    die("Insert failed: " . $stmt->error);
}

// ================= TOTAL =================
$stmt = $conn->prepare("
    SELECT SUM(total+tax_amount+tax_amount_sgst) GRAND_TOTAL 
    FROM service_trans_det 
    WHERE trans_id=? AND active_status!='Z'
");
$stmt->bind_param("s", $trans_id);
$stmt->execute();
$result = $stmt->get_result();

$grand_total = $result->fetch_assoc()["GRAND_TOTAL"] ?? 0;

// ================= UPDATE HEADER =================
$stmt = $conn->prepare("
    UPDATE service_trans 
    SET trans_amount=?, pending=? 
    WHERE trans_id=?
");
$stmt->bind_param("dds", $grand_total, $grand_total, $trans_id);
$stmt->execute();

echo $subtrans_id . "~" . $grand_total;

$conn->close();
?>