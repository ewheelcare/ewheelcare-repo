<?php
session_start();

if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php");
    exit();
}
?>