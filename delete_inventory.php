<?php

include "db_config.php";
$item_id=$_POST["item_id"];
$shop_id=$_POST["shop_id"];
$storagelocation_id=$_POST["storagelocation_id"];
$trans_id=$_POST["trans_id"];
$subtrans_id=$_POST["subtrans_id"];
$qty=$_POST["qty"];


  $sql="update inventory set qty=qty-".$qty." where item_id=".$item_id." and shop_id='".$shop_id."' and storagelocation_id='".$storagelocation_id."'" ;
  echo $sql;
$result = $conn->query($sql);

$sql="delete from  inventory_trans where item_id='".$item_id."' and qty='".$qty."' and shop_id='".$shop_id."'and storagelocation_id='".$storagelocation_id."' and trans_id='".$trans_id."' and subtrans_id=".$subtrans_id;
$result = $conn->query($sql);
echo $sql;
?>