<?php
include "db_config.php";

$trans_id = $_POST["trans_id"];
$user_id  = $_COOKIE["user_id"];

try {

    $conn->begin_transaction();

    /*
     * Sales Header
     */
    $sql = "
    UPDATE sales_trans
    SET
        active_status='Z',
        modified_on=NOW(),
        modified_by='".$user_id."'
    WHERE trans_id='".$trans_id."'
    ";

    if(!$conn->query($sql)){
        throw new Exception($conn->error);
    }

    /*
     * Sales Details
     */
    $sql = "
    UPDATE sales_trans_det
    SET
        active_status='Z',
        modified_on=NOW(),
        modified_by='".$user_id."'
    WHERE trans_id='".$trans_id."'
    ";

    if(!$conn->query($sql)){
        throw new Exception($conn->error);
    }

    /*
     * Delivery Challan Header
     */
    $sql = "
    UPDATE delivery_challan
    SET active_status='Z'
    WHERE trans_id='".$trans_id."'
    ";

    if(!$conn->query($sql)){
        throw new Exception($conn->error);
    }

    /*
     * Delivery Challan Details
     */
    $sql = "
    UPDATE delivery_challan_det
    SET active_status='Z'
    WHERE trans_id='".$trans_id."'
    ";

    if(!$conn->query($sql)){
        throw new Exception($conn->error);
    }

    

    /*
     * Inventory Transactions
     */
    // $sql = "
    // DELETE FROM inventory_trans
    // WHERE trans_id='".$trans_id."'
    // ";

    // if(!$conn->query($sql)){
    //     throw new Exception($conn->error);
    // }

    /*
 * Restore Inventory Stock
 */
    $sql = "
    SELECT item_id, qty, shop_id
    FROM inventory_trans
    WHERE trans_id='".$trans_id."'
    ";

    $result = $conn->query($sql);

    if(!$result){
        throw new Exception($conn->error);
    }

    while($row = $result->fetch_assoc()){

        $item_id = $row["item_id"];
        $qty     = $row["qty"];
        $shop_id = $row["shop_id"];

        $sql_restore = "
        UPDATE inventory_shop
        SET
            qty = qty + ".$qty.",
            modified_on = NOW(),
            modified_by = '".$user_id."'
        WHERE
            item_id='".$item_id."'
            AND shop_name='".$shop_id."'
        ";

        if(!$conn->query($sql_restore)){
            throw new Exception($conn->error);
        }
    }

    /*
    * Remove Inventory Transactions
    */
    $sql = "
    DELETE FROM inventory_trans
    WHERE trans_id='".$trans_id."'
    ";

    if(!$conn->query($sql)){
        throw new Exception($conn->error);
    }

    $conn->commit();

    echo "Sales Invoice Deleted Successfully";

}
catch(Exception $e){

    $conn->rollback();

    echo "Error : ".$e->getMessage();
}

$conn->close();
?>