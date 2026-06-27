<?php
include "db_config.php";
$item_id=$_POST["item_id"];
$cost=$_POST["cost"];
$tax_pc=$_POST["tax_pc"];
$tax_pc_sgst=$_POST["tax_pc_sgst"];
$tax_pc_igst=$_POST["tax_pc_igst"];
$total=$_POST["total"];
$tax=$_POST["tax"];
$tax_sgst=$_POST["tax_sgst"];
$tax_igst=$_POST["tax_igst"];
$qty=$_POST["qty"];
$price=$_POST["price"];
$trans_id=$_POST["trans_id"];
$discount=$_POST["discount"];
$roundoff=$_POST["roundoff"];
// $remarks=$_POST["remarks"];
$shop_id = $_COOKIE["shop"];
$subtrans_id=$_POST["subtrans_id"];

$qty = isset($qty) && $qty ? $qty : 0;


$sql_check="select qty from receipt_trans_det  where item_id=".$item_id." and trans_id ='". $trans_id."' and subtrans_id='".$subtrans_id."'";
//echo $sql_check;
$result = $conn->query($sql_check);
if($row = $result->fetch_assoc()) {
	//echo "here";
$inventory=	$row["qty"] ;
 $sql_upd="update inventory set qty=qty-".$inventory." where item_id=".$item_id." and shop_id ='". $shop_id."'" ;
 $conn->query($sql_upd);

}

$sql_upd="update inventory set qty=qty+".$qty." where item_id=".$item_id." and shop_id ='". $shop_id."'" ;
 // echo $sql_upd;
 
$conn->query($sql_upd);


 $sql = "update receipt_trans_det set active_status='Z',modified_on=now(),modified_by='".$_SESSION["user_id"]."'  where trans_id='".$trans_id."' and item_id='".$item_id."' and subtrans_id='".$subtrans_id."'";
 //echo $sql;

$result = $conn->query($sql);
 
$sql = "SELECT sum(total) GRAND_TOTAL FROM receipt_trans_det where trans_id='".$trans_id."' and active_status!='Z'";
$result = $conn->query($sql);
if($row = $result->fetch_assoc()) {
	$grand_total=$row["GRAND_TOTAL"] ;
  }	
  $sql = "update receipt_trans set trans_amount='".$grand_total."'  where trans_id='".$trans_id."'";
  $conn->query($sql);


$sql = "SELECT ifnull(max(subtrans_id),'1000001')+1 subtrans_id FROM receipt_trans_det where trans_id='".$trans_id."'";
$result = $conn->query($sql);

  if($row = $result->fetch_assoc()) {
	$subtrans_id=$row["subtrans_id"] ;
  }	
$sql = "insert into receipt_trans_det(trans_id,subtrans_id,item_id,cost,qty,total,tax,tax_amount,active_status,created_on,created_by,shop_id,tax_amount_sgst,tax_sgst,discount,pending,remarks,tax_igst,tax_amount_igst,price,roundoff)values('".$trans_id."','".$subtrans_id."','".$item_id."','".$cost."','".$qty."','".$total."','".$tax_pc."','".$tax."','A',now(),'".$_SESSION["user_id"]."','".$shop_id."','".$tax_sgst."','".$tax_pc_sgst."','".$discount."','".$qty."','".$remarks."','".$tax_pc_igst."','".$tax_igst."','".$price."','".$roundoff."')";
$result = $conn->query($sql);
///echo $sql;

$sql = "SELECT sum(total) GRAND_TOTAL, sum(tax_amount) TAX_AMOUNT,sum(tax_amount_sgst) TAX_AMOUNT_SGST, sum(tax_amount_igst) TAX_AMOUNT_IGST,sum(price) PRICE,sum(discount) DISCOUNT,sum(roundoff) ROUNDOFF  FROM receipt_trans_det where trans_id='".$trans_id."' and active_status!='Z'";
$result = $conn->query($sql);
if($row = $result->fetch_assoc()) {
	$grand_total=$row["GRAND_TOTAL"] ;
	$tax_amount=$row["TAX_AMOUNT"] ;
	$tax_amount_sgst=$row["TAX_AMOUNT_SGST"] ;
	$tax_amount_igst=$row["TAX_AMOUNT_IGST"] ;
	$price=$row["PRICE"] ;
	$discount=$row["DISCOUNT"] ;
	$roundoff=$row["ROUNDOFF"] ;
  }	

  $sql_upd = "update receipt_trans set trans_amount='".$grand_total."',pending='".$grand_total."'  where trans_id='".$trans_id."'";
  //echo $sql_upd;
  $conn->query($sql_upd);
 
//echo $sql;*/
echo $subtrans_id."~".$grand_total."~".$tax_amount."~".$tax_amount_sgst."~".$tax_amount_igst."~".($price- $discount+$roundoff)."~".$discount."~".$roundoff."~".$price;
$conn->close();

?>