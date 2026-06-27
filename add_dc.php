<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>

<?php
include "db_config.php";

// Collect and sanitize POST data


$trans_date = isset($_POST["trans_date"]) ? $_POST["trans_date"] : '';
$customer = isset($_POST["customer"]) ? $_POST["customer"] : '';

$company_name = isset($_POST["company_name"]) ? $_POST["company_name"] : '';
$customer_name = isset($_POST["customer_name"]) ? $_POST["customer_name"] : '';
$customer_mobile = isset($_POST["customer_mobile"]) ? $_POST["customer_mobile"] : '0';
// Added this as it was used but not defined

// Get new transaction ID
// $sql = "SELECT IFNULL(MAX(dc_id), 1000000) + 1  AS dc_id FROM delivery_challan";
// $result = $conn->query($sql);
// $dc_id = 1000001;

// if ($result && $row = $result->fetch_assoc()) {
//     $tradc_idns_id = $row["dc_id"];
// }
$trans_id = isset($_POST["trans_id"]) ? $_POST["trans_id"] : '';
$vehicle     = $_POST["vehicle"] ?? '';
$odometer    = $_POST["odometer"] ?? '';

// Prepare insert statement
$sql = "INSERT INTO delivery_challan (
    customer, dc_date, active_status, company_name, customer_name,customer_mobile,trans_id,vehicle,odometer) VALUES ('".$customer."', STR_TO_DATE('".$trans_date."', '%d-%m-%Y'), 'A', '".$company_name."', '".$customer_name."','".$customer_mobile."','".$trans_id."','".$vehicle."','".$odometer."')";
//echo $sql;
//  $conn->query($sql);

// Execute and check result
    // echo $trans_id;

if($conn->query($sql))
{
    echo $conn->insert_id;
}
else
{
    die($conn->error);
}

$conn->close();
?>
