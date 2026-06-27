<?php
include "db_config.php";
$user_id = $_POST["user_id"];
$user_name = $_POST["user_name"];
$user_pwd = $_POST["user_pwd"];
	
$sql = "update expert_login set user_name='".$user_name."' where user_id='".$user_id."'";
$result = $conn->query($sql);
echo "User added Successfully";
$conn->close();
?>