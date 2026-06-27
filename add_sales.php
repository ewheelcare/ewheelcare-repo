<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

function getCurrentFinancialYear() {
    $month = date("n");
    $year = date("Y");
    if ($month >= 4) { $startYear = $year; $endYear = $year + 1; }
    else             { $startYear = $year - 1; $endYear = $year; }
    return $startYear . "-" . substr($endYear, -2);
}

include "db_config.php";

$trans_date       = isset($_POST["trans_date"])       ? $_POST["trans_date"]       : '';
$customer         = isset($_POST["customer"])         ? $_POST["customer"]         : '';
$gst              = isset($_POST["gst"])              ? $_POST["gst"]              : '';
$vehicle_no       = isset($_POST["vehicle_no"])       ? $_POST["vehicle_no"]       : '';
$company_name     = isset($_POST["company_name"])     ? $_POST["company_name"]     : '';
$customer_name    = isset($_POST["customer_name"])    ? $_POST["customer_name"]    : '';
$customer_address = isset($_POST["customer_address"]) ? $_POST["customer_address"] : '';
$customer_mobile  = isset($_POST["customer_mobile"])  ? $_POST["customer_mobile"]  : '';
$customer_gst     = isset($_POST["customer_gst"])     ? $_POST["customer_gst"]     : '';

// ---------------------------------------------------------------
// CUSTOMER LOOKUP ONLY
// Customer creation is now handled by add_customer.php
// If customer id was passed (selected from dropdown or just created),
// verify it exists. Otherwise fall back to match by company + mobile.
// ---------------------------------------------------------------
if (!empty($customer)) {
    $res_v = $conn->query("SELECT customer_id FROM customer WHERE customer_id='" . $conn->real_escape_string($customer) . "' LIMIT 1");
    if (!$res_v || $res_v->num_rows === 0) {
        $customer = ''; // reset — will try fallback below
    }
}

if (empty($customer)) {
    $sql_find = "SELECT customer_id FROM customer
                 WHERE company_name='" . $conn->real_escape_string($company_name) . "'
                   AND owner_mobile='" . $conn->real_escape_string($customer_mobile) . "'
                 LIMIT 1";
    $res_find = $conn->query($sql_find);
    if ($res_find && $row_find = $res_find->fetch_assoc()) {
        $customer = $row_find["customer_id"];
    } else {
        die("Error: Customer not found. Please select or create a customer first.");
    }
}

// ---------------------------------------------------------------
// INVOICE NUMBER — identical to original
// ---------------------------------------------------------------
$fy = getCurrentFinancialYear();

mysqli_begin_transaction($conn);

$sql_config = "SELECT * FROM config WHERE item='SALES' AND fy='" . $fy . "/' FOR UPDATE";
$result_config = mysqli_query($conn, $sql_config);
$row_config    = mysqli_fetch_assoc($result_config);
$next_no       = $row_config["slno"] + 1;

$sql_update = "UPDATE config SET slno='" . $next_no . "' WHERE item='SALES' AND fy='" . $row_config["fy"] . "'";
mysqli_query($conn, $sql_update);
mysqli_commit($conn);

$trans_id  = $next_no;
$year_part = $row_config["part1"] . $row_config["fy"];
$shop_id   = isset($_COOKIE["shop"]) ? $_COOKIE["shop"] : '';

// ---------------------------------------------------------------
// INSERT SALES HEADER — identical to original
// ---------------------------------------------------------------
$sql = "
INSERT INTO sales_trans
(
    trans_id, customer, trans_date, active_status, gst,
    company_name, customer_name, CUSTOMER_ADDRESS, CUSTOMER_GST,
    customer_mobile, year_part, ver, shop, created_by, created_on
)
VALUES
(
    '" . $trans_id . "',
    '" . $customer . "',
    STR_TO_DATE('" . $trans_date . "', '%d-%m-%Y'),
    'D',
    '" . $gst . "',
    '" . $company_name . "',
    '" . $customer_name . "',
    '" . $customer_address . "',
    '" . $customer_gst . "',
    '" . $customer_mobile . "',
    '" . $year_part . "',
    '0',
    '" . $shop_id . "',
    '" . $_COOKIE["user_id"] . "',
    CURDATE()
)
";

if (!$conn->query($sql)) {
    die("Insert Error : " . $conn->error);
}

echo $year_part . "~" . $next_no . "~1";

$conn->close();
?>