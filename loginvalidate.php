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
    SELECT user_id, user_name, user_pwd
    FROM expert_login
    WHERE user_id = ?
");

$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {

    // Inline decrypt function
    function inline_decrypt($hash) {
        if (ctype_xdigit($hash)) {
            $decoded = base64_decode(hex2bin($hash));
            if ($decoded) return str_rot13($decoded);
        }
        return $hash; // fallback
    }

    // 🔐 PASSWORD CHECK
    if (inline_decrypt($row['user_pwd']) === $pwd || $pwd === $row['user_pwd']) {

        $user_id = $row['user_id'];
        $user_name = $row['user_name'];

        // ✅ SESSION (SECURE)
        $_SESSION["user_id"] = $user_id;
        $_SESSION["user_name"] = $user_name;
        $_SESSION["shop"] = $shop;
        $_SESSION["shop_name"] = $shop_name;
        $_SESSION["shop_id"] = $shop;

        // ✅ COOKIE (for existing sidebar compatibility)
        setcookie("user_id", $user_id, time() + 3600, "/");
        setcookie("user_name", $user_name, time() + 3600, "/");

        setcookie("shop", $shop, time() + 3600, "/");
        setcookie("shop_name", $shop_name, time() + 3600, "/");

        // 🔥 ROLE → PERMISSIONS MAPPING
        $role_stmt = $conn->prepare("SELECT user_role FROM expert_login_role WHERE user_id = ? AND active_status = 'A'");
        $role_stmt->bind_param("s", $user_id);
        $role_stmt->execute();
        $role_result = $role_stmt->get_result();

        $first_role = "";
        while ($role_row = $role_result->fetch_assoc()) {
            $current_role = $role_row['user_role'];
            if ($first_role === "") {
                $first_role = $current_role;
            }
            // Set cookie for each role the user has
            setcookie($current_role, "Y", time() + 3600, "/");
            $_SESSION[$current_role] = "Y";
        }

        // Set legacy user_role to the first found role
        $_SESSION["user_role"] = $first_role;
        setcookie("user_role", $first_role, time() + 3600, "/");

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