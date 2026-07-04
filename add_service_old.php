<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db_config.php";

// ================= INPUT =================
$trans_date = $_POST["trans_date"] ?? '';
$customer = $_POST["customer"] ?? '';
$gst = $_POST["gst"] ?? '';
$vehicle_no = $_POST["vehicle_no"] ?? '';
$vehicle_model = $_POST["vehicle_model"] ?? '';
$vehicle_make = $_POST["vehicle_make"] ?? '';
$no_of_wheels = $_POST["no_of_wheels"] ?? '';
$vehicle = $_POST["vehicle"] ?? '';
$company_name = $_POST["company_name"] ?? '';
$customer_name = $_POST["customer_name"] ?? '';
$customer_address = $_POST["customer_address"] ?? '';
$customer_mobile = $_POST["customer_mobile"] ?? '';
$customer_gst = $_POST["customer_gst"] ?? '';
$vehicle_odometer = $_POST["vehicle_odometer"] ?? '0';
$mech = $_POST["mech"] ?? '0';

// ================= VALIDATION =================
if ($vehicle_no == "") {
    die("Missing required fields");
}

// ================= CUSTOMER CHECK =================
$stmt = $conn->prepare("SELECT customer_id FROM customer WHERE company_name=? AND owner_mobile=?");
$stmt->bind_param("ss", $company_name, $customer_mobile);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {

    $result2 = $conn->query("SELECT IFNULL(MAX(customer_id),1000000)+1 AS customer_id FROM customer");
    $customer_id = $result2->fetch_assoc()["customer_id"];
    $customer = $customer_id;

    $stmt = $conn->prepare("INSERT INTO customer (customer_id,company_name,owner_name,owner_mobile) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $customer_id, $company_name, $customer_name, $customer_mobile);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO customer_gst (customer_id,gst) VALUES (?,?)");
    $stmt->bind_param("ss", $customer_id, $customer_gst);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO customer_address (customer_id,address) VALUES (?,?)");
    $stmt->bind_param("ss", $customer_id, $customer_address);
    $stmt->execute();
}
else {
    $customer = $row["customer_id"]; // ← ADD THIS
}

// ================= VEHICLE CHECK =================
$stmt = $conn->prepare("SELECT vehicle_id FROM vehicle WHERE vehicle_no=? AND customer_id=?");
$stmt->bind_param("ss", $vehicle_no, $customer);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {

    $result2 = $conn->query("SELECT IFNULL(MAX(vehicle_id),1000000)+1 AS vehicle_id FROM vehicle");
    $vehicle_id = $result2->fetch_assoc()["vehicle_id"];
    $vehicle = $vehicle_id;

    $stmt = $conn->prepare("INSERT INTO vehicle (vehicle_id,vehicle_no,vehicle_model,vehicle_brand,customer_id,vehicle_tyre) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $vehicle_id, $vehicle_no, $vehicle_model, $vehicle_make, $customer, $no_of_wheels);
    $stmt->execute();
}
else {
    $vehicle = $row["vehicle_id"]; // ← ADD THIS
}
// ================= CONFIG FETCH =================
$stmt = $conn->prepare("
    SELECT * FROM config 
    WHERE STR_TO_DATE(?, '%d-%m-%Y') BETWEEN FROM_TIME AND TO_TIME 
    AND item='SERVICE' AND gst=? LIMIT 1
");
$stmt->bind_param("ss", $trans_date, $gst);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    die("Config not found");
}

$part1 = $row["part1"];
$part2 = $row["part2"];
$fy = $row["fy"];

// ================= SAFE TRANS_ID GENERATION =================
$conn->begin_transaction();

$stmt = $conn->prepare("
    SELECT slno FROM config 
    WHERE STR_TO_DATE(?, '%d-%m-%Y') BETWEEN from_time AND to_time 
    AND gst=? AND item='SERVICE'
    FOR UPDATE
");
$stmt->bind_param("ss", $trans_date, $gst);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    $conn->rollback();
    die("Config not found for trans_id generation");
}

$trans_id_number = $row["slno"] + 1;
$trans_id = $part1 . $fy . $part2 . $trans_id_number;

// update config
$stmt = $conn->prepare("
    UPDATE config SET slno=? 
    WHERE STR_TO_DATE(?, '%d-%m-%Y') BETWEEN from_time AND to_time 
    AND gst=? AND item='SERVICE'
");
$stmt->bind_param("iss", $trans_id_number, $trans_date, $gst);
$stmt->execute();

$conn->commit();

// ================= INSERT TRANSACTION =================
$stmt = $conn->prepare("
INSERT INTO service_trans (
    trans_id, vehicle_no, customer, trans_date, active_status, gst,
    vehicle_model, no_of_wheels, vehicle_odometer, vehicle,
    company_name, customer_name, CUSTOMER_ADDRESS,
    CUSTOMER_GST, customer_mobile, vehicle_make, mech, shop
) VALUES (?, ?, ?, STR_TO_DATE(?, '%d-%m-%Y'), 'D', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssssisssssssss",
    $trans_id,
    $vehicle_no,
    $customer,
    $trans_date,
    $gst,
    $vehicle_model,
    $no_of_wheels,
    $vehicle_odometer,
    $vehicle,
    $company_name,
    $customer_name,
    $customer_address,
    $customer_gst,
    $customer_mobile,
    $vehicle_make,
    $mech,
    $_COOKIE["shop"]
);

if (!$stmt->execute()) {
    die("Insert failed: " . $stmt->error);
}

// ================= SUCCESS =================
echo $trans_id;

$conn->close();
?>