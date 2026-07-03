<?php
include "db_config.php";

$trans_id = $_POST["trans_id"];
$subtrans_id = $_POST["subtrans_id"];
$item_id = $_POST["item_id"];
$ver = $_POST["ver"];

$shop_id = $_COOKIE["shop"];
$user_id = $_COOKIE["user_id"];

/*

$trans_id="1000187";
$subtrans_id="1000005";
$item_id="1000012";
$ver="1";

*/

try {

    //
    // START DB TRANSACTION
    //
    $conn->begin_transaction();

    //
    // 1. SOFT DELETE SALES DETAIL
    //
    $sql = "
    UPDATE sales_trans_det 
    SET 
        active_status='Z',
        modified_on=NOW(),
        modified_by='".$user_id."'
    WHERE 
        trans_id='".$trans_id."'
        AND subtrans_id='".$subtrans_id."'
        AND ver='".$ver."'
    ";

    if (!$conn->query($sql)) {
        throw new Exception($conn->error);
    }

    //
    // 2. UPDATE PARENT ACCOUNT FLAG
    //
    $sql = "
    UPDATE sales_trans_det 
    SET account='Y'  
    WHERE 
        trans_id='".$trans_id."' 
        AND item_id=(
            SELECT parent 
            FROM (
                SELECT DISTINCT parent 
                FROM sales_trans_det 
                WHERE 
                    trans_id='".$trans_id."' 
                    AND subtrans_id='".$subtrans_id."' 
                    AND ver='".$ver."'
            ) AS temp
        ) 
        AND ver='".$ver."'
    ";

    if (!$conn->query($sql)) {
        throw new Exception($conn->error);
    }

    //
    // 3. RECALCULATE TOTALS
    //
    $sql = "
    SELECT 
        IFNULL(SUM(total),0) GRAND_TOTAL,
        IFNULL(SUM(tax_amount),0) TAX_AMOUNT,
        IFNULL(SUM(tax_amount_sgst),0) TAX_AMOUNT_SGST,
        IFNULL(SUM(tax_amount_igst),0) TAX_AMOUNT_IGST,
        IFNULL(SUM(price),0) PRICE,
        IFNULL(SUM(discount),0) DISCOUNT,
        IFNULL(SUM(roundoff),0) ROUNDOFF
    FROM sales_trans_det 
    WHERE 
        trans_id='".$trans_id."' 
        AND active_status!='Z' 
        AND ver='".$ver."' 
        AND IFNULL(account,'Y')='Y'
    ";

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception($conn->error);
    }

    $grand_total = 0;
    $tax_amount = 0;
    $tax_amount_sgst = 0;
    $tax_amount_igst = 0;
    $price = 0;
    $discount = 0;
    $roundoff = 0;

    if($row = $result->fetch_assoc()) {

        $grand_total = $row["GRAND_TOTAL"];
        $tax_amount = $row["TAX_AMOUNT"];
        $tax_amount_sgst = $row["TAX_AMOUNT_SGST"];
        $tax_amount_igst = $row["TAX_AMOUNT_IGST"];
        $price = $row["PRICE"];
        $discount = $row["DISCOUNT"];
        $roundoff = $row["ROUNDOFF"];
    }

    //
    // 4. UPDATE SALES HEADER
    //
    $sql = "
    UPDATE sales_trans 
    SET 
        trans_amount='".$grand_total."'
    WHERE 
        trans_id='".$trans_id."'
    ";

    if (!$conn->query($sql)) {
        throw new Exception($conn->error);
    }

    //
    // 5. GET BLOCKED INVENTORY
    //
    $sql_check = "
    SELECT qty 
    FROM inventory_block  
    WHERE 
        item_id='".$item_id."' 
        AND trans_id='".$trans_id."'
    LIMIT 1
    ";

    $result = $conn->query($sql_check);

    if (!$result) {
        throw new Exception($conn->error);
    }

    if($row = $result->fetch_assoc()) {

        $inventory = (float)$row["qty"];

        //
        // SAFETY CHECK
        //
        if($inventory <= 0){
            throw new Exception("Invalid Inventory Qty");
        }

        //
        // 6. RESTORE STOCK IN inventory_shop
        //
        $sql_upd = "
        UPDATE inventory_shop
        SET 
            qty = qty + ".$inventory.",
            modified_on = NOW(),
            modified_by = '".$user_id."'
        WHERE 
            item_id='".$item_id."'
            AND shop_name='".$shop_id."'
        ";

        if (!$conn->query($sql_upd)) {
            throw new Exception($conn->error);
        }

        //
        // 7. DELETE INVENTORY BLOCK
        //
        $sql_delete_block = "
        DELETE FROM inventory_block 
        WHERE 
            trans_id='".$trans_id."' 
            AND item_id='".$item_id."'
        ";

        if (!$conn->query($sql_delete_block)) {
            throw new Exception($conn->error);
        }
    }

    //
    // 8. DELETE INVENTORY TRANSACTION
    //
    $sql_inventory_trans = "
    DELETE FROM inventory_trans
    WHERE 
        trans_id='".$trans_id."'
        AND item_id='".$item_id."'
        AND trans_type='SALE'
    ";

    if (!$conn->query($sql_inventory_trans)) {
        throw new Exception($conn->error);
    }

    //
    // COMMIT
    //
    $conn->commit();

    //
    // RESPONSE
    //
    echo $subtrans_id
        ."~".$grand_total
        ."~".$tax_amount
        ."~".$tax_amount_sgst
        ."~".$tax_amount_igst
        ."~".($price - $discount + $roundoff);

} catch (Exception $e) {

    //
    // ROLLBACK
    //
    $conn->rollback();

    echo "Error : ".$e->getMessage();
}

$conn->close();

?>