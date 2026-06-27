<?php
include "db_config.php";

$trans_id=$_POST["trans_id"];
$subtrans_id=$_POST["subtrans_id"]; 


$sql = "update receipt_trans_det set active_status='Z',modified_on=now(),modified_by='".$_SESSION["user_id"]."'  where trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."'";
$result = $conn->query($sql);
//echo $sql;
$sql = "SELECT sum(total+tax_amount) GRAND_TOTAL,shop_id,item_id,qty FROM service_trans_det where trans_id='".$trans_id."' and active_status='A'";
$result = $conn->query($sql);
if($row = $result->fetch_assoc()) {
	$grand_total=$row["GRAND_TOTAL"] ;
	$shop_id=$row["shop_id"] ;
	$item_id=$row["item_id"] ;
	$qty=$row["qty"] ;
  }	
  $sql = "update service_trans set trans_amount='".$grand_total."'  where trans_id='".$trans_id."'";
  $conn->query($sql);
    $sql="update inventory set qty=qty+".$qty." where item_id=".$item_id." and shop_id='".$shop_id."'" ;
  //echo $sql;
$result = $conn->query($sql);
echo "Receipt deleted Successfully";
$conn->close();
?>