<?php
include "db_config.php";
$item_id = $_POST["item_id"];
$itemgroup_id = $_POST["itemgroup_id"];
$perc = $_POST["perc"];



$sql = "insert into groupassociation(itemgroup_id,item_id,perc)values('".$itemgroup_id."','".$item_id."','".$perc."')";
$result = $conn->query($sql);
//echo $sql;
echo "Address added Successfully";
$conn->close();
?>