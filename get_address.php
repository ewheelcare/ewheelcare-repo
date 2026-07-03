<?php 
include "db_config.php";
$customer_id=$_POST["customer_id"];
$sql="SELECT address FROM customer_address v where v.customer_id='".$customer_id."'";
//echo $sql;
$result = $conn->query($sql);
$response_str="";
while($row = $result->fetch_assoc()) {
	$gst=$row["address"]; 
	$response_str.=$gst."~";
	}
echo $response_str;
?>