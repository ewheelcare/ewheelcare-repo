<?php 
setcookie("user_id", "", time() - 3600, "/");
setcookie("user_role", "", time() - 3600, "/");
setcookie("user_name", "", time() - 3600, "/");
setcookie("DASHBOARD", "", time() - 3600, "/");
setcookie("ENTRY", "", time() - 3600, "/");
setcookie("INVENTORY", "", time() - 3600, "/");
setcookie("MASTER", "", time() - 3600, "/");
setcookie("REPORT", "", time() - 3600, "/");
setcookie("SA", "", time() - 3600, "/");
header('location:index.php');
?>