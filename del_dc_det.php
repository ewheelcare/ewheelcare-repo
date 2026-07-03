<?php
include "db_config.php";

// Collect and sanitize POST data
$dc_id = $_POST["dc_id"] ?? '';
$trans_id = $_POST["trans_id"] ?? '';
$subtrans_id = $_POST["sub_trans_id"] ?? '';
$item_id = $_POST["item_id"] ?? '';
$despatched = $_POST["qty"] ?? '';

// Prepare insert statement
$sql = "update delivery_challan_det set active_status='Z' where dc_id='".$dc_id."' and trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."' and item_id='".$item_id."' and despatched='".$despatched."'";
 $conn->query($sql);
$sql="update sales_trans_det set pending=pending+".$despatched." where trans_id='".$trans_id."' and subtrans_id='".$subtrans_id."' and item_id='".$item_id."'";
// Execute and check result
$conn->query($sql);
    echo $dc_id;

?>