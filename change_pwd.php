<?php
include "db_config.php";
$user_id = $_COOKIE["user_id"];
$user_pwd = $_POST["pwd"];

// Inline Hex-Obfuscation without touching db_config.php
$hashed_pwd = bin2hex(base64_encode(str_rot13($user_pwd)));

$stmt = $conn->prepare("UPDATE expert_login SET user_pwd = ? WHERE user_id = ?");
$stmt->bind_param("ss", $hashed_pwd, $user_id);

if ($stmt->execute()) {
    echo "Password changed successfully";
} else {
    echo "Error changing password";
}
$conn->close();
?>