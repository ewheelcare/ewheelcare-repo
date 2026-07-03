<?php
include "db_config.php";
$user_id = $_POST["user_id"];
$role_id = $_POST["role_id"];
	
$sql = "update  expert_login_role set active_status='Z' where user_id='".$user_id."' and user_role='".$role_id."'";
$result = $conn->query($sql);
//echo $sql;
echo "User added Successfully";
$conn->close();
?>