<?php
include "db_config.php";

$trans_id=$_POST["trans_id"];
$subtrans_id=$_POST["subtrans_id"]; 
$item_id=$_POST["subtrans_id"]; 
$ver=$_POST["ver"];

/*$trans_id="1000187";
$subtrans_id="1000005";
$item_id="1000012";*/


$sql = "update sales_trans_det set active_status='Z',modified_on=now(),modified_by='".$_SESSION["user_id"]."'  where trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."' and ver='".$ver."'";
$result = $conn->query($sql);
//echo $sql;

  $sql = "update sales_trans set trans_amount='".$grand_total."'  where trans_id='".$trans_id."'";
  $conn->query($sql);
  
  $sql = "update sales_trans_det set account='Y'  where trans_id='".$trans_id."' and item_id=(SELECT parent FROM (select distinct parent from sales_trans_det where trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."' and ver='".$ver."') as temp) and ver='".$ver."'";

//UPDATE sales_trans_det SET account = 'Y' WHERE trans_id = '1000187' AND item_id = ( SELECT parent FROM ( SELECT DISTINCT parent FROM sales_trans_det WHERE trans_id = '1000187' AND subtrans_id = '1000005' ) AS temp );



//echo $sql;
$result = $conn->query($sql);

$sql = "SELECT sum(total) GRAND_TOTAL,sum(tax_amount) TAX_AMOUNT,sum(tax_amount_sgst) TAX_AMOUNT_SGST, sum(tax_amount_igst) TAX_AMOUNT_IGST,sum(price) PRICE,sum(discount) DISCOUNT,sum(roundoff) ROUNDOFF,parent FROM sales_trans_det where trans_id='".$trans_id."' and active_status!='Z' and ver='".$ver."' and ifnull(account,'Y')='Y'";
$result = $conn->query($sql);
if($row = $result->fetch_assoc()) {
	$grand_total=$row["GRAND_TOTAL"] ;
	$tax_amount=$row["TAX_AMOUNT"] ;
	$tax_amount_sgst=$row["TAX_AMOUNT_SGST"] ;
	$tax_amount_igst=$row["TAX_AMOUNT_IGST"] ;
	$price=$row["PRICE"] ;
	$discount=$row["DISCOUNT"] ;
	$roundoff=$row["ROUNDOFF"] ;
	//$parent=$row["ROUNDOFF"] ;
  }	
echo $subtrans_id."~".$grand_total."~".$tax_amount."~".$tax_amount_sgst."~".$tax_amount_igst."~".($price+$discount+$roundoff);

$sql_check="select qty from inventory_block  where item_id=".$item_id." and trans_id ='". $trans_id."'";
//echo $sql_check;
$result = $conn->query($sql_check);
if($row = $result->fetch_assoc()) {
	//echo "here";
$inventory=	$row["qty"] ;
 $sql_upd="update inventory set qty=qty+".$inventory." where item_id=".$item_id." and shop_id ='". $shop_id."'" ;
 $conn->query($sql_upd);
 $sql_insert="delete from inventory_block where trans_id='".$trans_id."' and item_id='".$item_id."'";
$conn->query($sql_insert);

//echo "there";
}
$conn->close();
?>