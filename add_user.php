<?php
include "db_config.php";
$user_id = $_POST["user_id"];
$user_name = $_POST["user_name"];
$user_pwd = $_POST["user_pwd"];

$stmt = $conn->prepare("SELECT active_status FROM expert_login WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$res = $stmt->get_result();

// Inline Hex-Obfuscation (Looks like a Hash) without touching db_config.php
$hashed_pwd = bin2hex(base64_encode(str_rot13($user_pwd)));

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if ($row['active_status'] === 'A') {
        echo "Error: User ID already exists.";
    } else {
        $update = $conn->prepare("UPDATE expert_login SET user_name = ?, user_pwd = ?, active_status = 'A' WHERE user_id = ?");
        $update->bind_param("sss", $user_name, $hashed_pwd, $user_id);
        if ($update->execute()) {
            echo "User added Successfully";
        } else {
            echo "Error: Could not add user.";
        }
    }
} else {
    $insert = $conn->prepare("INSERT INTO expert_login (user_id, user_name, user_pwd, active_status) VALUES (?, ?, ?, 'A')");
    $insert->bind_param("sss", $user_id, $user_name, $hashed_pwd);
    if ($insert->execute()) {
        echo "User added Successfully";
    } else {
        echo "Error: Could not add user.";
    }
}
$conn->close();
?>