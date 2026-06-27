<?php
include "db_config.php";
$item_id=$_POST["item_id"];

$cost=isset($_POST["cost"])?$_POST["cost"]:0;
$tax_pc=$_POST["tax_pc"];
$tax_pc_sgst=$_POST["tax_pc_sgst"];
$tax_pc_igst=$_POST["tax_pc_igst"];
$total=isset($_POST["total"])?$_POST["total"]:0;
$tax=$_POST["tax"];
$tax_sgst=$_POST["tax_sgst"];
$tax_igst=$_POST["tax_igst"];
$qty=isset($_POST["qty"])?$_POST["qty"]:0;
$price1=$_POST["price"];
$roundoff=$_POST["roundoff"];
$account=(!isset($_POST["account"])|| $_POST["account"]=="")?"Y":$_POST["account"];
$parent=$_POST["parent"];
$trans_id=$_POST["trans_id"];
$discount=$_POST["discount"];
$shop_id = $_COOKIE["shop"];
$ver = $_POST["ver"];
$perc = $_POST["perc"];
$subtrans_id=$_POST["subtrans_id"];
/*$cost="0";
$tax_pc="9";
$tax_pc_sgst="9";
$tax_pc_igst="";
$total="";
$tax="0.00";
$tax_sgst="0.00";
$tax_igst="0.00";
$qty="2";
$subtrans_id="";
$item_id="1000001";
$price="0";
$roundoff="";
$parent="";
$account="N";
$trans_id="1000227";
$discount="";
$ver="1";*/

/*$cost = "490.1960784313726";
$tax_pc = "18";
$tax_pc_sgst = "18";
$tax_pc_igst = "";
$total = "2000";
$tax = "264.71";
$tax_sgst = "264.71";
$tax_igst = "0.00";
$qty = "3";
$item_id = "3";
$price = "1470.5882352941178";
$roundoff = "";
$parent = "1000001";
$account = "Y";
$trans_id = "1000141";
$discount = "";*/

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
$sql_check="select qty from inventory_block  where item_id=".$item_id." and trans_id ='". $trans_id."'";
//echo $sql_check;
$result = $conn->query($sql_check);
if($row = $result->fetch_assoc()) {
	//echo "here";
$inventory=	$row["qty"] ;

//  $sql_upd="update inventory set qty=qty+".$inventory." where item_id=".$item_id." and shop_id ='". $shop_id."' " ;
//  $conn->query($sql_upd);
//  $sql_insert="delete from inventory_block where trans_id='".$trans_id."' and item_id='".$item_id."'";
// $conn->query($sql_insert);

$sql_upd="
update inventory_shop
set 
    qty = qty + ".$inventory.",
    modified_on = now(),
    modified_by = '".$_COOKIE["user_id"]."'
where 
    item_id='".$item_id."'
    and shop_name='".$shop_id."'
";

//echo "there";
}
//echo "2";
// $sql_inventory="select qty from inventory  where item_id=".$item_id." and shop_id ='". $shop_id."'";

$sql_inventory="
select qty 
from inventory_shop
where 
    item_id='".$item_id."'
    and shop_name='".$shop_id."'
";
$result = $conn->query($sql_inventory);
if($row = $result->fetch_assoc()) {
	$inventory=$row["qty"] ;
	if($qty<=$inventory){
		//  $sql_upd="update inventory set qty=qty-".$qty." where item_id=".$item_id." and shop_id ='". $shop_id."'" ;

	$sql_upd="
	update inventory_shop
	set 
		qty = qty - ".$qty.",
		modified_on = now(),
		modified_by = '".$_COOKIE["user_id"]."'
	where 
		item_id='".$item_id."'
		and shop_name='".$shop_id."'
	";

  ///echo $sql;
$conn->query($sql_upd);
// $sql_insert="insert into inventory_block(trans_id,item_id,qty)values('".$trans_id."','".$item_id."','".$qty."')";

$sql_insert="
INSERT INTO inventory_block
(trans_id,item_id,qty)
VALUES
(
    '".$trans_id."',
    '".$item_id."',
    '".$qty."'
)

ON DUPLICATE KEY UPDATE
qty = VALUES(qty)
";
$conn->query($sql_insert);

	}else{
	//echo "Error : Insufficient stock"  ;
//die();	
	}
  }else{
	//echo "Error : Insufficient stock"  ;
	//die();
  }

//echo "here";
 
 $sql = "update sales_trans_det set active_status='Z',modified_on=now(),modified_by='".$_SESSION["user_id"]."'  where trans_id='".$trans_id."' and item_id='".$item_id."' and subtrans_id='".$subtrans_id."'  and ver='".$ver."'";
$result = $conn->query($sql);
//echo $sql;
$sql = "SELECT sum(total) GRAND_TOTAL,sum(tax_amount) TAX_AMOUNT,sum(tax_amount_sgst) TAX_AMOUNT_SGST, sum(tax_amount_igst) TAX_AMOUNT_IGST,sum(price) PRICE,sum(discount) DISCOUNT,sum(roundoff) ROUNDOFF FROM sales_trans_det where trans_id='".$trans_id."' and active_status!='Z' and  ifnull(account,'Y')='Y'  and ver='".$ver."'";
//echo $sql;
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
  $sql = "update sales_trans set trans_amount='".$grand_total."'  where trans_id='".$trans_id."' ";
  $conn->query($sql);
//echo $sql;
//echo $grand_total;
$sql = "SELECT ifnull(max(subtrans_id),1000001)+1 subtrans_id FROM sales_trans_det where trans_id='".$trans_id."'";
$result = $conn->query($sql);

  if($row = $result->fetch_assoc()) {
	$subtrans_id=$row["subtrans_id"] ;
  }	
 // echo $subtrans_id;
// $sql = "insert into sales_trans_det(trans_id,subtrans_id,item_id,cost,qty,total,tax,tax_amount,active_status,created_on,created_by,shop_id,tax_amount_sgst,tax_sgst,discount,pending,price,roundoff,parent, account,ver,perc)values('".$trans_id."','".$subtrans_id."','".$item_id."','".$cost."','".$qty."','".$total."','".$tax_pc."','".$tax."','A',now(),'".$_SESSION["user_id"]."','".$shop_id."','".$tax_sgst."','".$tax_pc_sgst."','".$discount."','".$qty."','".$price1."','".$roundoff."','".$parent."','".$account."','".$ver."','".$perc."')";
// $result = $conn->query($sql);

$sql = "insert into sales_trans_det(trans_id,subtrans_id,item_id,cost,qty,total,tax,tax_amount,active_status,created_on,created_by,shop_id,tax_amount_sgst,tax_sgst,discount,pending,price,roundoff,parent, account,ver,perc,tax_igst,tax_amount_igst)values('".$trans_id."','".$subtrans_id."','".$item_id."','".$cost."','".$qty."','".$total."','".$tax_pc."','".$tax."','A',now(),'".$_SESSION["user_id"]."','".$shop_id."','".$tax_sgst."','".$tax_pc_sgst."','".$discount."','".$qty."','".$price1."','".$roundoff."','".$parent."','".$account."','".$ver."','".$perc."','".$tax_pc_igst."','".$tax_igst."')";
$result = $conn->query($sql);

$sql_inventory_trans = "
INSERT INTO inventory_trans
(
    trans_id,
    item_id,
    shop_id,
    qty,
    trans_type,
    created_on,
    created_by
)
VALUES
(
    '".$trans_id."',
    '".$item_id."',
    '".$shop_id."',
    '".$qty."',
    'SALE',
    NOW(),
    '".$_COOKIE["user_id"]."'
)

ON DUPLICATE KEY UPDATE

    qty = VALUES(qty),
    modified_on = NOW(),
    modified_by = '".$_COOKIE["user_id"]."'
";

if (!$conn->query($sql_inventory_trans)) {
    die('Inventory Trans Error : '.$conn->error);
}

//echo $sql;
$sql = "SELECT sum(total) GRAND_TOTAL,sum(tax_amount) TAX_AMOUNT,sum(tax_amount_sgst) TAX_AMOUNT_SGST, sum(tax_amount_igst) TAX_AMOUNT_IGST,sum(price) PRICE,sum(discount) DISCOUNT,sum(roundoff) ROUNDOFF FROM sales_trans_det where trans_id='".$trans_id."' and active_status!='Z'  and ver='".$ver."' and ifnull(account,'Y')='Y'";
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
  $sql_upd = "update sales_trans set trans_amount='".$grand_total."',pending='".$grand_total."'  where trans_id='".$trans_id."'";
  //echo $sql_upd;
  $conn->query($sql_upd);
 
//echo $sql;*/
echo $subtrans_id."~".$grand_total."~".$tax_amount."~".$tax_amount_sgst."~".$tax_amount_igst."~".($price+$discount+$roundoff);

$conn->close();
?>