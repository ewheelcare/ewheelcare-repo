<?php
include "db_config.php";
$item_id=$_POST["item"];
$old_qty=$_POST["old_qty"];
$cost=$_POST["cost"];
$tax_pc=$_POST["tax_pc"];
$total=$_POST["total"];
$tax=$_POST["tax"];
$qty=$_POST["qty"];
$trans_id=$_POST["trans_id"];
$subtrans_id=$_POST["subtrans_id"]; 
$base_price=$_POST["base_price"];
$tax_sgst=$_POST["tax_sgst"];
$tax_pc_sgst=$_POST["tax_pc_sgst"];

$sql = "update receipt_trans_det set item_id='".$item_id."',cost='".$cost."',qty='".$qty."',total='".$total."',tax='".$tax_pc."',tax_amount='".$tax."',modified_on=now(),modified_by='".$_SESSION["user_id"]."',tax_sgst='".$tax_pc_sgst."',tax_amount_sgst='".$tax_sgst."',base_price='".$base_price."'  where trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."'";
$result = $conn->query($sql);
//echo $sql;
$sql = "SELECT sum(total+tax_amount) GRAND_TOTAL FROM receipt_trans_det where trans_id='".$trans_id."' and active_status='A'";
$result = $conn->query($sql);
if($row = $result->fetch_assoc()) {
	$grand_total=$row["GRAND_TOTAL"] ;
  }	
  $sql = "update receipt_trans set trans_amount='".$grand_total."'  where trans_id='".$trans_id."'";
  $conn->query($sql);
  $sql="update inventory set qty=qty+".$qty."-".$old_qty." where item_id=".$item_id." and shop_id='0'l" ;
  //echo $sql;
$result = $conn->query($sql);
if ($result) {
if($conn->affected_rows<=0){
   $sql="insert into inventory (item_id,qty,shop_id) values ('".$item_id."',".$qty.",'0')";
  // echo $sql;
$result = $conn->query($sql);
}
} 
echo "Receipt added Successfully";
$conn->close();
?>