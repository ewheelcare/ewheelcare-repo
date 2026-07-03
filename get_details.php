<?php 
include "db_config.php";
$vehicle_no=$_POST["vehicle_no"];
$sql="SELECT vehicle_id,vehicle_model,vehicle_tyre,c.company_name,c.owner_name,c.customer_id,owner_mobile,vehicle_brand,vehicle_model FROM vehicle v ,customer c where vehicle_no='".$vehicle_no."' and v.customer_id=c.customer_id";
//echo $sql;
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
	$vehicle_id=$row["vehicle_id"]; 
	$vehicle_model=$row["vehicle_model"];
	$no_of_wheels=$row["vehicle_tyre"];
	$customer_id=$row["customer_id"];
	$company_name=$row["company_name"];
	$owner_name=$row["owner_name"];
	$customer_mobile=$row["owner_mobile"];
	$vehicle_brand=$row["vehicle_brand"];
	
	}
$response_str =$vehicle_id."~".$vehicle_model."~".$no_of_wheels."~".$customer_id."~".$company_name."~".$owner_name."~".$customer_mobile."~".$vehicle_brand;
echo $response_str;
?>
