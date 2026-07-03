<?php 
include "db_config.php";
$customer_id=$_POST["customer_id"];
$sql="SELECT company_address FROM vendor v where v.vendor_id='".$customer_id."'";
//echo $sql;
$result = $conn->query($sql);
$response_str="";
while($row = $result->fetch_assoc()) {
	$gst=$row["company_address"]; 
	$response_str.=$gst."~";
	}
echo $response_str;
?>