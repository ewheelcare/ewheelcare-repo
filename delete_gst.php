<?php
include "db_config.php";
$gst = $_POST["gst"];
$customer_id = $_POST["customer_id"];



$sql = "delete from customer_gst where customer_id='".$customer_id."' and gst='".$gst."'";
$result = $conn->query($sql);
//echo $sql;
echo "GST added Successfully";
$conn->close();
?>