<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
<?php
include "db_config.php";
$address = $_POST["address"];
$customer_id = $_POST["customer_id"];



$sql = "insert into customer_address(customer_id,address)values('".$customer_id."','".$address."')";
$result = $conn->query($sql);
//echo $sql;
echo "Address added Successfully";
$conn->close();
?>