<?php
include "db_config.php";

$trans_id=$_POST["trans_id"];
$subtrans_id=$_POST["subtrans_id"]; 
$ver=$_POST["ver"];

$sql = "update sales_trans_det set active_status='Z',modified_on=now(),modified_by='".$_SESSION["user_id"]."'  where trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."' and ver='".$ver."'";
$result = $conn->query($sql);
//echo $sql;
$sql = "SELECT sum(total+tax_amount) GRAND_TOTAL FROM sales_trans_det where trans_id='".$trans_id."' and active_status='A' and ver='".$ver."'";
$result = $conn->query($sql);
if($row = $result->fetch_assoc()) {
	$grand_total=$row["GRAND_TOTAL"] ;
  }	
  $sql = "update sales_trans set trans_amount='".$grand_total."'  where trans_id='".$trans_id."'";
  $conn->query($sql);
  $sql = "SELECT qty,shop_id,item_id FROM sales_trans_det where trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."' and active_status='Z'";
$result = $conn->query($sql);
if($row = $result->fetch_assoc()) {
	$qty=$row["qty"] ;
	$shop_id=$row["shop_id"] ;
	$item_id=$row["item_id"] ;
  }

  $sql="update inventory set qty=qty+".$qty." where item_id=".$item_id." and shop_id ='". $shop_id."'" ;
  //echo $sql;
$result = $conn->query($sql);  
$sql = "update sales_trans_det set account='Y'  where trans_id='".$trans_id."' and ver='".$ver."' and item_id=(select parent from sales_trans_det where trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."' and ver='".$ver."')";
echo $sql;
$result = $conn->query($sql);

echo "Receipt added Successfully";
$conn->close();
?>