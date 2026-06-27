<?php
include "db_config.php";
$address = $_POST["address"];
$customer_id = $_POST["customer_id"];



$sql = "delete from customer_address where customer_id='".$customer_id."' and address='".$address."'";
$result = $conn->query($sql);
//echo $sql;
echo "Address deleted Successfully";
$conn->close();
?>