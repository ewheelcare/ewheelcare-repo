<?php
include "db_config.php";
$user_id = $_POST["user_id"];
$user_name = $_POST["user_name"];
$user_pwd = $_POST["user_pwd"];

$update_query = "UPDATE expert_login SET user_name = ?";
$params = [$user_name];
$types = "s";

if (!empty($user_pwd)) {
    // Inline Hex-Obfuscation without touching db_config.php
    $hashed_pwd = bin2hex(base64_encode(str_rot13($user_pwd)));
    $update_query .= ", user_pwd = ?";
    $params[] = $hashed_pwd;
    $types .= "s";
}

$update_query .= " WHERE user_id = ?";
$params[] = $user_id;
$types .= "s";

$stmt = $conn->prepare($update_query);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo "User updated Successfully";
} else {
    echo "Error updating user";
}
$conn->close();
?>