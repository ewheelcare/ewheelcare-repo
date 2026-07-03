<?php
include "db_config.php";

$dc_id = $_POST["dc_id"] ?? '';

if ($dc_id == '') {
    die("DC ID Missing");
}

// Get all details for this DC
$sql_det = "SELECT * FROM delivery_challan_det WHERE dc_id='" . $conn->real_escape_string($dc_id) . "' AND active_status!='Z'";
$res_det = $conn->query($sql_det);

$trans_id = '';
while ($row = $res_det->fetch_assoc()) {
    $trans_id = $row['trans_id'];
    $subtrans_id = $row['subtrans_id'];
    $item_id = $row['item_id'];
    $despatched = $row['despatched'];

    // Restore pending qty and dispatched qty in sales_trans_det
    $sql_restore = "UPDATE sales_trans_det 
                    SET pending = pending + " . (float)$despatched . ",
                        dispatched_qty = dispatched_qty - " . (float)$despatched . "
                    WHERE trans_id='" . $trans_id . "' 
                    AND subtrans_id='" . $subtrans_id . "' 
                    AND item_id='" . $item_id . "'
                    AND ver = (
                        SELECT MAX_VER FROM
                        (
                            SELECT MAX(ver) MAX_VER
                            FROM sales_trans_det
                            WHERE trans_id='" . $trans_id . "'
                        ) x
                    )";
    $conn->query($sql_restore);
}

// Mark DC and its details as deleted
$sql_del_hdr = "UPDATE delivery_challan SET active_status='Z' WHERE dc_id='" . $conn->real_escape_string($dc_id) . "'";
$conn->query($sql_del_hdr);

$sql_del_det = "UPDATE delivery_challan_det SET active_status='Z' WHERE dc_id='" . $conn->real_escape_string($dc_id) . "'";
$conn->query($sql_del_det);

// Update trans delivery status
if ($trans_id != '') {
    $sql_status = "
    SELECT COUNT(*) cnt
    FROM sales_trans_det
    WHERE trans_id='".$trans_id."'
    AND ver = (
        SELECT MAX(ver)
        FROM sales_trans_det
        WHERE trans_id='".$trans_id."'
    )
    AND pending > 0 AND active_status!='Z' AND IFNULL(account, 'Y') = 'Y'
    ";
    
    $res_status = $conn->query($sql_status);
    $row_status = $res_status->fetch_assoc();
    
    if ($row_status["cnt"] == 0) {
        $conn->query("
            UPDATE sales_trans
            SET delivery_status='D'
            WHERE trans_id='".$trans_id."'
        ");
    } else {
        $conn->query("
            UPDATE sales_trans
            SET delivery_status='P'
            WHERE trans_id='".$trans_id."'
        ");
    }
}

echo "Success";
?>
