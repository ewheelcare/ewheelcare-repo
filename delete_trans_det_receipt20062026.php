<?php
include "db_config.php";

$trans_id=$_POST["trans_id"];
$subtrans_id=$_POST["subtrans_id"]; 
$item_id=$_POST["item_id"]; 

$sql = "update receipt_trans_det set active_status='Z',modified_on=now(),modified_by='".$_SESSION["user_id"]."'  where trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."'";
$result = $conn->query($sql);
//echo $sql;
$sql = "SELECT sum(total+tax_amount+tax_amount_sgst) GRAND_TOTAL FROM receipt_trans_det where trans_id='".$trans_id."' and active_status!='Z'";
$result = $conn->query($sql);
if($row = $result->fetch_assoc()) {
	$grand_total=$row["GRAND_TOTAL"] ;
  }	
  $sql = "update receipt_trans set trans_amount='".$grand_total."'  where trans_id='".$trans_id."'";
  $conn->query($sql);
echo $trans_id."~".$grand_total;
$sql_check="select qty from receipt_trans_det  where item_id=".$item_id." and trans_id ='". $trans_id."'";
//echo $sql_check;
$result = $conn->query($sql_check);
if($row = $result->fetch_assoc()) {
	//echo "here";
$inventory=	$row["qty"] ;
 $sql_upd="update inventory set qty=qty-".$inventory." where item_id=".$item_id." and shop_id ='". $shop_id."'" ;
 $conn->query($sql_upd);

}
$conn->close();
?>