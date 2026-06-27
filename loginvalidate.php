<?php
session_start();
include "db_config.php";

$uid = $_POST["uid"];
$pwd = $_POST["pwd"];
$shop = $_POST["shop"];
$shop_name = $_POST["shop_name"];

$response_obj = [];

// Fetch user
$stmt = $conn->prepare("
    SELECT l.user_id, l.user_name, l.user_pwd, r.user_role 
    FROM expert_login l 
    JOIN expert_login_role r ON l.user_id = r.user_id 
    WHERE l.user_id = ?
");

$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {

    // 🔐 PASSWORD CHECK
    // If using plain text → keep as is
    // If using hash → use password_verify()
    
    if ($pwd === $row['user_pwd']) {
    // if (password_verify($pwd, $row['user_pwd'])) {

        $user_id   = $row['user_id'];
        $user_name = $row['user_name'];
        $user_role = $row['user_role'];

        // ✅ SESSION (SECURE)
        $_SESSION["user_id"]   = $user_id;
        $_SESSION["user_name"] = $user_name;
        $_SESSION["user_role"] = $user_role;
        $_SESSION["shop"]      = $shop;
        $_SESSION["shop_name"] = $shop_name;
        $_SESSION["shop_id"]      = $shop;
        // ✅ COOKIE (for existing sidebar compatibility)
        setcookie("user_id", $user_id, time() + 3600, "/");
        setcookie("user_name", $user_name, time() + 3600, "/");
        setcookie("user_role", $user_role, time() + 3600, "/");

        setcookie("shop", $shop, time() + 3600, "/");
        setcookie("shop_name", $shop_name, time() + 3600, "/");

        // 🔥 ROLE → PERMISSIONS MAPPING
        // You MUST define this properly based on your system

        if ($user_role == "SA") {
            setcookie("SA", "Y", time() + 3600, "/");
        }

        if ($user_role == "MASTER") {
            setcookie("MASTER", "Y", time() + 3600, "/");
        }

        if ($user_role == "ENTRY") {
            setcookie("ENTRY", "Y", time() + 3600, "/");
        }

        if ($user_role == "REPORT") {
            setcookie("REPORT", "Y", time() + 3600, "/");
        }

        if ($user_role == "DASHBOARD") {
            setcookie("DASHBOARD", "Y", time() + 3600, "/");
        }

        if ($user_role == "INVENTORY") {
            setcookie("INVENTORY", "Y", time() + 3600, "/");
        }

        $response_obj["status"] = "S";
        $response_obj["message"] = "Login Successful";

    } else {
        $response_obj["status"] = "F";
        $response_obj["message"] = "Invalid Password";
    }

} else {
    $response_obj["status"] = "F";
    $response_obj["message"] = "User Not Found";
}

echo json_encode($response_obj);
?>