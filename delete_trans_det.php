<?php
include "db_config.php";

$trans_id=$_POST["trans_id"];
$subtrans_id=$_POST["subtrans_id"]; 


$sql = "update service_trans_det set active_status='Z',modified_on=now(),modified_by='".$_SESSION["user_id"]."'  where trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."'";
$result = $conn->query($sql);
//echo $sql;
$sql = "SELECT sum(total+tax_amount+tax_amount_sgst) GRAND_TOTAL FROM service_trans_det where trans_id='".$trans_id."' and active_status='A'";
$result = $conn->query($sql);
if($row = $result->fetch_assoc()) {
	$grand_total=$row["GRAND_TOTAL"] ;
  }	
  $sql = "update service_trans set trans_amount='".$grand_total."'  where trans_id='".$trans_id."'";
  $conn->query($sql);
echo $trans_id."~".$grand_total;
$conn->close();
?>