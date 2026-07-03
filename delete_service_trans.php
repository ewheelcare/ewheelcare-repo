<?php
include "db_config.php";
$trans_id = $_POST["trans_id"];
$sql = "update service_trans set active_status='Z' where trans_id='".$trans_id."'";
$result = $conn->query($sql);
//echo $sql;
echo "Service Trans deleted Successfully";
$conn->close();
?>