<?php
if (isset($_COOKIE["user_id"])) {
    header("Location: index.php");
    exit();
}
// ✅ capture redirect URL
//$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
$redirect = "index.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
body {
    background: url('img/tyre.png') no-repeat center center fixed;
    background-size: cover;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', sans-serif;
    overflow: hidden;
}

/* Dark gradient overlay */
body::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.4));
    z-index: 0;
}

/* Glass Card */
.login-card {
    width: 360px;
    padding: 35px;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    box-shadow: 0 8px 40px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff;
    position: relative;
    z-index: 1;
    animation: fadeIn 0.6s ease-in-out;
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Title */
.login-title {
    text-align: center;
    margin-bottom: 25px;
    font-weight: 600;
    letter-spacing: 1px;
}

/* Inputs */
.form-control {
    background: rgba(255,255,255,0.1);
    border: none;
    color: #fff;
    border-radius: 8px;
    padding: 12px;
    transition: 0.3s;
}

.form-control::placeholder {
    color: rgba(255,255,255,0.6);
}

/* Focus glow */
.form-control:focus {
    background: rgba(255,255,255,0.15);
    box-shadow: 0 0 8px rgba(255, 0, 60, 0.7);
    color: #fff;
}

/* Button futuristic glow */
.btn-primary {
    background: linear-gradient(45deg, #ff003c, #ff4d6d);
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-weight: 600;
    letter-spacing: 1px;
    transition: 0.3s;
    box-shadow: 0 0 10px rgba(255,0,60,0.6);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 20px rgba(255,0,60,0.9);
}

/* Error text */
#errorMsg {
    color: #ff6b6b;
}

/* Optional: subtle floating effect */
.login-card:hover {
    transform: translateY(-2px);
    transition: 0.3s;
}
/* Fix dropdown options visibility */
select.form-control {
    background: rgba(255,255,255,0.1);
    color: #fff;
        /* subtle arrow */
    background-image: linear-gradient(45deg, transparent 50%, #aaa 50%),
                      linear-gradient(135deg, #aaa 50%, transparent 50%);
    background-position: calc(100% - 15px) calc(50% - 3px),
                         calc(100% - 10px) calc(50% - 3px);
    background-size: 5px 5px, 5px 5px;
    background-repeat: no-repeat;
    transition: all 0.25s ease;
}

/* VERY IMPORTANT: dropdown list items */
select.form-control option {
    color: #1a1a1a;   /* dark background */
    background: #ffffff;        /* white text */
}

/* When dropdown is opened (hover/selected) */
/*select.form-control option:checked,*/
/*select.form-control option:hover {*/
/*    background: #ff003c;*/
/*    color: #fff;*/
/*}*/
</style>
</head>

<body>

<div class="login-card">
    <h4 class="login-title">Expert Wheel Care Login</h4>

    <div class="form-group">
        <input type="text" class="form-control"
        id="uid" placeholder="👤 User ID">
    </div>
    
    <div class="form-group mt-3">
        <input type="password" class="form-control"
        id="pwd" placeholder="🔑 Password">
    </div>

<div class="form-group mt-3">
    <select id="shop" class="form-control">
        <option value="" disabled selected>Select Shop</option>
        <option value="SIRASAPALLI">SIRASAPALLI SHOP</option>
        <option value="GAJUWAKA">GAJUWAKA SHOP</option>
    </select>
</div>

<button type="button" onclick="loginUser()" 
    class="btn btn-primary w-100 mt-3">
    Login
</button>

    <p id="errorMsg" class="text-danger text-center mt-2"></p>
</div>

<script>
function loginUser() {
    let redirectPage = "<?php echo $redirect; ?>";
    let uid = $("#uid").val().trim();
    let pwd = $("#pwd").val().trim();
    let shop = $("#shop").val();

    if (!uid || !pwd || !shop) {
        $("#errorMsg").text("All fields are required");
        return;
    }

    // Disable button (prevent double click)
    $("button").prop("disabled", true).text("Logging in...");

    $.ajax({
        url: "loginvalidate.php",
        type: "POST",
        data: {
            uid: uid,
            pwd: pwd,
            shop: shop,
            shop_name: $("#shop option:selected").text()
        },
        success: function(res) {
            try {
                let data = JSON.parse(res);

                if (data.status === "S") {
                    window.location.href = redirectPage;
                } else {
                    $("#errorMsg").text(data.message);
                }
            } catch (e) {
                $("#errorMsg").text("Invalid server response");
            }
        },
        error: function() {
            $("#errorMsg").text("Server error. Try again.");
        },
        complete: function() {
            $("button").prop("disabled", false).text("Login");
        }
    });
}

// Enter key support
$(document).keypress(function(e) {
    if (e.which == 13) {
        loginUser();
    }
});
</script>

</body>
</html>