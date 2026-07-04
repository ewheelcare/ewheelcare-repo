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
$trans_id = isset($_POST["trans_id"]) ? mysqli_real_escape_string($conn, $_POST["trans_id"]) : '';
$vehicle = isset($_POST["vehicle"]) ? mysqli_real_escape_string($conn, $_POST["vehicle"]) : '';
$odometer = isset($_POST["odometer"]) ? mysqli_real_escape_string($conn, $_POST["odometer"]) : '';

$customer = mysqli_real_escape_string($conn, $customer);
$company_name = mysqli_real_escape_string($conn, $company_name);
$customer_name = mysqli_real_escape_string($conn, $customer_name);
$customer_mobile = mysqli_real_escape_string($conn, $customer_mobile);
$trans_date = mysqli_real_escape_string($conn, $trans_date);

// Prepare insert statement
$sql = "INSERT INTO delivery_challan (
    customer, dc_date, active_status, company_name, customer_name,customer_mobile,trans_id,vehicle,odometer) VALUES ('".$customer."', STR_TO_DATE('".$trans_date."', '%d-%m-%Y'), 'A', '".$company_name."', '".$customer_name."','".$customer_mobile."','".$trans_id."','".$vehicle."','".$odometer."')";

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
