<?php
include "db_config.php";
$user_id = $_COOKIE["user_id"];
$user_pwd = $_POST["pwd"];
	
$sql = "UPDATE expert_login SET user_pwd='".$user_pwd."' where user_id='".$user_id."'";
$result = $conn->query($sql);
//echo $sql;
echo "User added Successfully";
$conn->close();
?>