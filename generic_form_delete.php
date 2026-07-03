<?php
include "db_config.php";
$module=$_POST["module"];

$id = $_POST["id"];

$db = $module;
$pk=$module."_id";
$sql = "delete FROM ".$db." where ".$pk."='".$id."'" ;
//echo $sql;
$result = $conn->query($sql);
echo "Deleted Successfully";
?>