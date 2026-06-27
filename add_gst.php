<?php
include "db_config.php";
$gst = $_POST["gst"];
$customer_id = $_POST["customer_id"];



$sql = "insert into customer_gst(customer_id,gst)values('".$customer_id."','".$gst."')";
$result = $conn->query($sql);
//echo $sql;
echo "GST added Successfully";
$conn->close();
?>