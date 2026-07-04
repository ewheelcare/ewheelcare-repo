<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include "db_config.php";

$dc_id       = isset($_POST["dc_id"]) ? mysqli_real_escape_string($conn, $_POST["dc_id"]) : '';
$trans_id    = isset($_POST["trans_id"]) ? mysqli_real_escape_string($conn, $_POST["trans_id"]) : '';
$subtrans_id = isset($_POST["sub_trans_id"]) ? mysqli_real_escape_string($conn, $_POST["sub_trans_id"]) : '';
$item_id     = isset($_POST["item_id"]) ? mysqli_real_escape_string($conn, $_POST["item_id"]) : '';
$despatched  = (int)($_POST["qty"] ?? 0);
$vehicle     = isset($_POST["vehicle"]) ? mysqli_real_escape_string($conn, $_POST["vehicle"]) : '';
$odometer    = isset($_POST["odometer"]) ? mysqli_real_escape_string($conn, $_POST["odometer"]) : '';

$conn->begin_transaction();

try {

    // Check Pending Qty
    $sql_chk = "
    SELECT pending
    FROM sales_trans_det
    WHERE trans_id='".$trans_id."'
    AND subtrans_id='".$subtrans_id."'
    AND item_id='".$item_id."'
    ";

    $res_chk = $conn->query($sql_chk);

    if(!$res_chk || !$row_chk = $res_chk->fetch_assoc()){
        throw new Exception("Item not found");
    }

    $pending = (int)$row_chk["pending"];

    $new_pending = $pending - $despatched;
    $dc_status = ($new_pending <= 0) ? 'Y' : 'P';

    if($despatched > $pending){
        throw new Exception("Cannot deliver more than pending qty");
    }

    /* Check Inventory
    $sql_inv = "
    SELECT qty
    FROM inventory_block
    WHERE item_id='".$item_id."'
    AND trans_id='".$trans_id."'
    ";

    $res_inv = $conn->query($sql_inv);

    if($res_inv && $row_inv = $res_inv->fetch_assoc()){

        $inventory = (int)$row_inv["qty"];

        if($despatched > $inventory){
            throw new Exception("Insufficient Inventory");
        }

        $sql_upd = "
        UPDATE inventory_block
        SET qty = qty - ".$despatched."
        WHERE item_id='".$item_id."'
        AND trans_id='".$trans_id."'
        ";

        $conn->query($sql_upd);
    }
    */
    // Insert Delivery Challan Detail
    $sql = "
    INSERT INTO delivery_challan_det
    (
        dc_id,
        trans_id,
        subtrans_id,
        active_status,
        item_id,
        despatched
    )
    VALUES
    (
        ".$dc_id.",
        '".$trans_id."',
        ".$subtrans_id.",
        'A',
        '".$item_id."',
        ".$despatched."       
    )
    ";

    if(!$conn->query($sql)){
        throw new Exception($conn->error);
    }
    

    // Update Pending Qty
//     $sql = "
//         UPDATE sales_trans_det
// SET
//     pending = pending - ".$despatched.",
//     dispatched_qty = dispatched_qty + ".$despatched.",
//     dc_status =
//         CASE
//             WHEN (pending - ".$despatched.") <= 0
//             THEN 'Y'
//             ELSE 'P'
//         END,
//     vehicle = '".$vehicle."',
//     run_km = '".$odometer."'
//     WHERE trans_id='".$trans_id."'
//     AND subtrans_id='".$subtrans_id."'
//     AND item_id='".$item_id."'
//         ";

    $sql = "

    UPDATE sales_trans_det
    SET
    pending='".$new_pending."',
    dispatched_qty = dispatched_qty + ".$despatched.",
    dc_status='".$dc_status."',
    vehicle='".$vehicle."',
    run_km='".$odometer."'
    WHERE
    trans_id='".$trans_id."'
    AND subtrans_id='".$subtrans_id."'
    AND item_id='".$item_id."'
    ";

    if(!$conn->query($sql)){
        throw new Exception($conn->error);
    }

    // Update Delivery Status
    // $sql_status = "
    // SELECT COUNT(*) cnt
    // FROM sales_trans_det
    // WHERE trans_id='".$trans_id."' AND active_status='A' AND account='Y'
    // AND pending > 0
    // ";
    
    $sql_status = "
    SELECT COUNT(*) cnt
    FROM sales_trans_det
    WHERE trans_id='".$trans_id."'
    AND ver = (
        SELECT MAX(ver)
        FROM sales_trans_det
        WHERE trans_id='".$trans_id."'
    )
    AND active_status='A'
    AND account='Y'
    AND pending > 0
    ";

    $res_status = $conn->query($sql_status);
    $row_status = $res_status->fetch_assoc();

    if($row_status["cnt"] == 0)
    {
        $conn->query("
        UPDATE sales_trans
        SET delivery_status='D'
        WHERE trans_id='".$trans_id."'
        ");
    }
    else
    {
        $conn->query("
        UPDATE sales_trans
        SET delivery_status='P'
        WHERE trans_id='".$trans_id."'
        ");
    }

    $conn->commit();

    echo $dc_id;

} catch(Exception $e){

    $conn->rollback();

    http_response_code(500);

    echo $e->getMessage();
}
?>