<?php
include "db_config.php";
$user_id = $_POST["user_id"];
$user_role = $_POST["user_role"];

$stmt = $conn->prepare("SELECT user_id FROM expert_login_role WHERE user_id = ? AND user_role = ? AND active_status = 'A'");
$stmt->bind_param("ss", $user_id, $user_role);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "Error: User already has this role.";
} else {
    $insert = $conn->prepare("INSERT INTO expert_login_role (user_id, user_role, active_status) VALUES (?, ?, 'A')");
    $insert->bind_param("ss", $user_id, $user_role);
    if ($insert->execute()) {
        echo "Role added Successfully";
    } else {
        echo "Error: Could not add role.";
    }
}
$conn->close();
?>