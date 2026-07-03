<?php
error_reporting(0);
$conn = new mysqli("localhost", "u307626220_EWC_DB", "India@104701", "u307626220_EWC_DB");
$conn->query("SET time_zone = '+05:30'");
session_start();
?>