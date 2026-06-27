<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
<?PHP function getCurrentFinancialYear() {
    $month = date("n"); // current month (1-12)
    $year = date("Y");  // current year

    if ($month >= 4) {
        $startYear = $year;
        $endYear = $year + 1;
    } else {
        $startYear = $year - 1;
        $endYear = $year;
    }

    return $startYear . "-" . substr($endYear, -2);
}
?>
<?php
include "db_config.php";

$trans_date = isset($_POST["trans_date"]) ? $_POST["trans_date"] : '';
$customer = isset($_POST["customer"]) ? $_POST["customer"] : '';
$gst = isset($_POST["gst"]) ? $_POST["gst"] : '';
$vehicle_no = isset($_POST["vehicle_no"]) ? $_POST["vehicle_no"] : '';
$company_name = isset($_POST["company_name"]) ? $_POST["company_name"] : '';
$customer_name = isset($_POST["customer_name"]) ? $_POST["customer_name"] : '';
$customer_address = isset($_POST["customer_address"]) ? $_POST["customer_address"] : '';
$customer_mobile = isset($_POST["customer_mobile"]) ? $_POST["customer_mobile"] : '';
$customer_gst = isset($_POST["customer_gst"]) ? $_POST["customer_gst"] : '';

/*$customer_mobile="9966380603";
$company_name="SREE LAXMI NARASIMHA TRANSPORT";
$customer_name="RAJU GARU";
$customer_address="9-1-223/B,CRM COMPOUND, NEAR KARNATAKA BANK,RAMA TALKIES ROAD, VISAKHAPATNAM";
$customer_gst="37ADPFS4120H1ZX";
$customer="1000014";
$trans_date="22-10-2025";
$gst="Y";*/
// Get new transaction ID


///check if customer is in master
$sql = "SELECT * FROM customer where company_name='".$company_name."' and owner_mobile='".$customer_mobile."'";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    
}else{
	//generate cust ID
	//echo "to add cust";
	
	$sql2 = "SELECT IFNULL(MAX(customer_id), 1000000) + 1  AS customer_id FROM customer";
$result2 = $conn->query($sql2);
$customer_id = 1000001;

if ($result2 && $row = $result2->fetch_assoc()) {
    $customer_id = $row["customer_id"];
	$customer =  $row["customer_id"];
}
$sql3 = "insert into customer (customer_id,company_name,owner_name,owner_mobile)values('".$customer_id."','". $company_name."','". $customer_name."','".$customer_mobile."')";
//echo $sql3;
$conn->query($sql3);
$sql4 = "insert into customer_gst (customer_id,gst)values('".$customer_id."','". $customer_gst."')";
$conn->query($sql4);
$sql5 = "insert into customer_address (customer_id,address)values('".$customer_id."','". $customer_address."')";
$conn->query($sql5);
}

$sql = "SELECT IFNULL(MAX(trans_id), 1000000) + 1  AS trans_id FROM sales_trans";
$result = $conn->query($sql);
$trans_id = 1000001;

if ($result && $row = $result->fetch_assoc()) {
    $trans_id = $row["trans_id"];
}
// $trans_id=.$trans_id;
//echo $trans_id;
// Prepare insert statement
$year_part="EWC/".getCurrentFinancialYear()."/";
$sql = "INSERT INTO sales_trans (
    trans_id, customer, trans_date, active_status, gst, company_name, customer_name,CUSTOMER_ADDRESS, CUSTOMER_GST,customer_mobile,year_part,ver) VALUES ('".$trans_id."','".$customer."', STR_TO_DATE('".$trans_date."', '%d-%m-%Y'), 'D', '".$gst."', '".$company_name."', '".$customer_name."','".$customer_address."', '".$customer_gst."', '".$customer_mobile."','".$year_part."','0')";
//echo $sql;
 $conn->query($sql);

    echo $year_part."~".$trans_id."~1";

//$stmt->close();
$conn->close();
?>
