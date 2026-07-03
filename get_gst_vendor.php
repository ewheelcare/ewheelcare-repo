<?php 
include "db_config.php";
$customer_id=$_POST["customer_id"];
$sql="SELECT owner_aadhar FROM vendor v where v.vendor_id='".$customer_id."'";
//echo $sql;
$result = $conn->query($sql);
$response_str="";
while($row = $result->fetch_assoc()) {
	$gst=$row["owner_aadhar"]; 
	$response_str.=$gst."~";
	}
echo $response_str;
?>