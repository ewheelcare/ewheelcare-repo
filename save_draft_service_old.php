<?php 
include "db_config.php";
$trans_id=$_POST["value"];
$sql="update  service_trans set active_status='A' WHERE  trans_id='".$trans_id."'";
$result = $conn->query($sql);
$sql="update  service_trans set active_status='A' WHERE  trans_id='".$trans_id."'";
$result = $conn->query($sql);

?>