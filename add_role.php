<?php
include "db_config.php";
$user_id = $_POST["user_id"];
$user_role = $_POST["user_role"];
	
$sql = "insert into expert_login_role(user_id,user_role,active_status)values('".$user_id."','".$user_role."','A')";
$result = $conn->query($sql);
//echo $sql;
echo "User added Successfully";
$conn->close();
?>