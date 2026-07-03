<?php
include "db_config.php";
$shop_id=$_POST["shop_id"];
//$shop_id="7";
$sql = "SELECT storagelocation_id, storagelocation_name FROM storagelocation where shop_id='".$shop_id."'";
//echo $sql;
$result = $conn->query($sql);
$result_str="";
while ($row = $result->fetch_assoc()) {
$result_str.=$row["storagelocation_id"]."#".$row["storagelocation_name"]."~";	
	
}
echo $result_str;
?>
