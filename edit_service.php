<?php
include "db_config.php";
$trans_date = $_POST["trans_date"];
$details = $_POST["details"];
$customer = $_POST["customer"];
$pending = $_POST["pending"];
$gst = $_POST["gst"];
$tally = $_POST["tally"];
$trans_id = $_POST["trans_id"];
	
$sql = "update service_trans set details='".$details."',customer='".$customer."',trans_date='".$trans_date."',active_status='A',pending='".$pending."',gst='".$gst."',tally='".$tally."' WHERE trans_id='".$trans_id."'";
$result = $conn->query($sql);
//echo $sql;
echo "Service Trans added Successfully";
$conn->close();
?>