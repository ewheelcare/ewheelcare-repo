<?php 
include "db_config.php";
$trans_id=$_POST["value"];
$sql="delete from  receipt_trans where active_status='D' and trans_id='".$trans_id."'";
$result = $conn->query($sql);
$sql="delete from  receipt_trans_det d,receipt_trans m where m.active_status='D' and m.trans_id='".$trans_id."' and m.trans_id=d.trans_id";
$result = $conn->query($sql);
?>