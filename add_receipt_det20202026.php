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
$remarks=$_POST["remarks"];
$shop_id = $_COOKIE["shop"];
$subtrans_id=$_POST["subtrans_id"];
/*$cost="2500";
$tax_pc="18";
$tax_pc_sgst="18";
$total="7500";
$tax="1350";
$tax_sgst="1350";
$qty="3";
$item_id="1000001";
$trans_id="1000055";
$trans_id="1000055";
$discount="";

$cost	="2700";
$tax_pc	=	"18";
$tax_pc_sgst		="18";
$total		="5390";
$tax		="970.2";
$tax_sgst		="970.2";
$qty		="2";
$item_id	=	"3";
$trans_id	=	"1000008";
$discount	=	"10";*/
//if already blocked
//echo "1";
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
  ///echo $sql;
$conn->query($sql_upd);

 
 $sql = "update receipt_trans_det set active_status='Z',modified_on=now(),modified_by='".$_SESSION["user_id"]."'  where trans_id='".$trans_id."' and item_id='".$item_id."' and subtrans_id='".$subtrans_id."'";
$result = $conn->query($sql);
//echo $sql;
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
$sql = "insert into receipt_trans_det(trans_id,subtrans_id,item_id,cost,qty,total,tax,tax_amount,active_status,created_on,created_by,shop_id,tax_amount_sgst,tax_sgst,discount,pending,remarks,tax_igst,tax_amount_igst,price)values('".$trans_id."','".$subtrans_id."','".$item_id."','".$cost."','".$qty."','".$total."','".$tax_pc."','".$tax."','A',now(),'".$_SESSION["user_id"]."','".$shop_id."','".$tax_sgst."','".$tax_pc_sgst."','".$discount."','".$qty."','".$remarks."','".$tax_pc_igst."','".$tax_igst."','".$price."')";
$result = $conn->query($sql);
///echo $sql;
$sql = "SELECT sum(total) GRAND_TOTAL FROM receipt_trans_det where trans_id='".$trans_id."' and active_status!='Z'";
$result = $conn->query($sql);
if($row = $result->fetch_assoc()) {
	$grand_total=$row["GRAND_TOTAL"] ;
  }	
  $sql_upd = "update receipt_trans set trans_amount='".$grand_total."',pending='".$grand_total."'  where trans_id='".$trans_id."'";
  //echo $sql_upd;
  $conn->query($sql_upd);
 
//echo $sql;*/
echo $subtrans_id."~".$grand_total;
$conn->close();
?>