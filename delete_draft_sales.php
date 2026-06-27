<?php 
include "db_config.php";
$trans_id=$_POST["value"];
$ver = $_POST["ver"];

///0 hole sob kato
$sql="delete from  sales_trans_det where trans_id=(select trans_id from  sales_trans where active_status='D' and trans_id='".$trans_id."' and ver='0')";
$result = $conn->query($sql);
$sql="delete from  sales_trans where active_status='D' and trans_id='".$trans_id."' and ver='0'";
$result = $conn->query($sql);

$sql="delete from  sales_trans_det d where  D.trans_id=(select trans_id from  sales_trans where active_status='D' and trans_id='".$trans_id."' and ver<>'0') and ver='".$ver."'";
$result = $conn->query($sql);
$sql="update  sales_trans set  active_status='A' WHERE trans_id='".$trans_id."' and ver<>'0'";
$result = $conn->query($sql);
//$sql="delete from  sales_trans where active_status='D' and trans_id='".$trans_id."' and ver='".$ver."'";
//$result = $conn->query($sql);

?>