<?php
if (!isset($_COOKIE["user_id"])) {
    echo "Error: Unauthorized";
    exit();
}

include "db_config.php";

$company_name     = isset($_POST["company_name"])     ? trim($conn->real_escape_string($_POST["company_name"]))     : '';
$customer_name    = isset($_POST["customer_name"])    ? trim($conn->real_escape_string($_POST["customer_name"]))    : '';
$customer_mobile  = isset($_POST["customer_mobile"])  ? trim($conn->real_escape_string($_POST["customer_mobile"]))  : '';
$customer_gst     = isset($_POST["customer_gst"])     ? trim($conn->real_escape_string($_POST["customer_gst"]))     : '';
$customer_address = isset($_POST["customer_address"]) ? trim($conn->real_escape_string($_POST["customer_address"])) : '';

if (empty($customer_name) && empty($company_name)) {
    echo "Error: Customer Name or Company Name is required";
    exit();
}

// Duplicate Check
$sql_chk = "SELECT customer_id, company_name, owner_name, owner_mobile
            FROM customer
            WHERE company_name='$company_name'
            AND owner_mobile='$customer_mobile'
            LIMIT 1";

$res_chk = $conn->query($sql_chk);

if ($res_chk && $row_chk = $res_chk->fetch_assoc()) {
    echo "EXISTS~{$row_chk['customer_id']}~{$row_chk['company_name']}~{$row_chk['owner_name']}~{$row_chk['owner_mobile']}";
    exit();
}

try {

    $conn->begin_transaction();

    // Generate Customer ID
    $res_max = $conn->query("SELECT IFNULL(MAX(customer_id),1000000)+1 AS customer_id FROM customer");

    if (!$res_max) {
        throw new Exception($conn->error);
    }

    $customer_id = $res_max->fetch_assoc()["customer_id"];

    // Customer
    if (!$conn->query("
        INSERT INTO customer
        (customer_id, company_name, owner_name, owner_mobile)
        VALUES
        ('$customer_id','$company_name','$customer_name','$customer_mobile')
    ")) {
        throw new Exception($conn->error);
    }

    // GST
    if (!empty($customer_gst)) {

        if (!$conn->query("
            INSERT INTO customer_gst
            (customer_id,gst)
            VALUES
            ('$customer_id','$customer_gst')
        ")) {
            throw new Exception($conn->error);
        }
    }

    // Address
    if (!empty($customer_address)) {

        if (!$conn->query("
            INSERT INTO customer_address
            (customer_id,address)
            VALUES
            ('$customer_id','$customer_address')
        ")) {
            throw new Exception($conn->error);
        }
    }

    $conn->commit();

    echo "SUCCESS~{$customer_id}~{$company_name}~{$customer_name}~{$customer_mobile}";

} catch (Exception $e) {

    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();