<?php

include "db_config.php";
$item_id=$_POST["item_id"];
$shop_id=$_POST["shop_id"];
$storagelocation_id=$_POST["storagelocation_id"];
$trans_id=$_POST["trans_id"];
$subtrans_id=$_POST["subtrans_id"];
$qty=$_POST["qty"];


  $sql="update inventory set qty=qty+".$qty." where item_id=".$item_id." and shop_id='".$shop_id."' and storagelocation_id='".$storagelocation_id."'" ;
  echo $sql;
$result = $conn->query($sql);
if ($result) {
if($conn->affected_rows<=0){
   $sql="insert into inventory (item_id,qty,shop_id,storagelocation_id) values ('".$item_id."',".$qty.",'".$shop_id."','".$storagelocation_id."')";
   echo $sql;
$result = $conn->query($sql);
}
$sql="insert into inventory_trans (item_id,qty,shop_id,storagelocation_id,trans_id,subtrans_id) values ('".$item_id."',".$qty.",'".$shop_id."','".$storagelocation_id."','".$trans_id."','".$subtrans_id."')";
echo $sql;
 $conn->query($sql);
}