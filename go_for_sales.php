<?php
$gst=$_GET["GSN"];
echo $gst;
setcookie("gst", $gst, time() + 3600, "/");
echo $_COOKIE["gst"];
header("location:sales.php");
?>