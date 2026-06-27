<?php
include "db_config.php";
$user_id = $_POST["user_id"];
$user_name = $_POST["user_name"];
$user_pwd = $_POST["user_pwd"];
	
$sql = "insert into expert_login(user_id,user_name,user_pwd,active_status)values('".$user_id."','".$user_name."','".$user_pwd."','A')";
$result = $conn->query($sql);
//echo $sql;
echo "User added Successfully";
$conn->close();
?>