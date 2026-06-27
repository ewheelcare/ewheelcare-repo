<?php 
include "db_config.php";
$item=$_POST["item"];
$qty=$_POST["qty"];

$list="";
$sql = "SELECT s.shop_id,s.shop_name FROM  shop s ,inventory i where i.item_id='".$item."' and i.shop_id=s.shop_id and cast(i.qty as signed)>=".$qty;
//echo $sql;
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	
$shop_id= $row["shop_id"];
$shop_name=$row["shop_name"];
$list.=$shop_id."~".$shop_name."#";
}
echo $list;
?>