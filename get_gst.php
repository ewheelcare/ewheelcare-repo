<?php 
include "db_config.php";
$customer_id=$_POST["customer_id"];
$sql="SELECT gst FROM customer_gst v where v.customer_id='".$customer_id."'";
//echo $sql;
$result = $conn->query($sql);
$response_str="";
while($row = $result->fetch_assoc()) {
	$gst=$row["gst"]; 
	$response_str.=$gst."~";
	}
echo $response_str;
?>