<?php
include "db_config.php";
$item_id = $_POST["item_id"];
$itemgroup_id = $_POST["itemgroup_id"];
$perc = $_POST["perc"];



$sql = "update groupassociation set perc='".$perc."' where itemgroup_id='".$itemgroup_id."' and item_id='".$item_id."'";
$result = $conn->query($sql);
//echo $sql;
echo "Sub Item added Successfully";
$conn->close();
?>