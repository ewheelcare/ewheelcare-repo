<?php
include "db_config.php";
$item_id = $_POST["item_id"];
$itemgroup_id = $_POST["itemgroup_id"];



$sql = "delete from groupassociation where item_id='".$item_id."' and itemgroup_id='".$itemgroup_id."'";
$result = $conn->query($sql);
//echo $sql;
echo "Sub Item Deleted Successfully";
$conn->close();
?>