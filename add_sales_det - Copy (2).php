<?php
include "db_config.php";

$item_id = $_POST["item_id"];

$cost = isset($_POST["cost"]) ? $_POST["cost"] : 0;
$tax_pc = $_POST["tax_pc"];
$tax_pc_sgst = $_POST["tax_pc_sgst"];
$tax_pc_igst = $_POST["tax_pc_igst"];

$total = isset($_POST["total"]) ? $_POST["total"] : 0;

$tax = $_POST["tax"];
$tax_sgst = $_POST["tax_sgst"];
$tax_igst = $_POST["tax_igst"];

$qty = isset($_POST["qty"]) ? $_POST["qty"] : 0;

$price1 = $_POST["price"];
$roundoff = $_POST["roundoff"];

$account = (!isset($_POST["account"]) || $_POST["account"] == "") 
    ? "Y" 
    : $_POST["account"];

$parent = $_POST["parent"];
$trans_id = $_POST["trans_id"];
$discount = $_POST["discount"];

$shop_id = $_COOKIE["shop"];
$user_id = $_COOKIE["user_id"];

$ver = $_POST["ver"];
$perc = $_POST["perc"];
$subtrans_id = $_POST["subtrans_id"];


//
// 1. RESTORE OLD BLOCKED STOCK IF EXISTS
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

if($row = $result->fetch_assoc()) {

    $inventory = $row["qty"];

    //
    // RESTORE OLD STOCK
    //
    $sql_restore = "
    UPDATE inventory_shop
    SET 
        qty = qty + ".$inventory.",
        modified_on = NOW(),
        modified_by = '".$user_id."'
    WHERE 
        item_id='".$item_id."'
        AND shop_name='".$shop_id."'
    ";

    $conn->query($sql_restore);

    //
    // DELETE OLD BLOCK
    //
    $sql_delete_block = "
    DELETE FROM inventory_block
    WHERE 
        trans_id='".$trans_id."'
        AND item_id='".$item_id."'
    ";

    $conn->query($sql_delete_block);
}


// //
// // 2. CHECK CURRENT STOCK
// //
// $sql_inventory = "
// SELECT qty 
// FROM inventory_shop
// WHERE 
//     item_id='".$item_id."'
//     AND shop_name='".$shop_id."'
// ";

// $result = $conn->query($sql_inventory);

// if($row = $result->fetch_assoc()) {

//     $inventory = $row["qty"];

//     if($qty <= $inventory){

//         //
//         // DEDUCT STOCK
//         //
//         $sql_deduct = "
//         UPDATE inventory_shop
//         SET 
//             qty = qty - ".$qty.",
//             modified_on = NOW(),
//             modified_by = '".$user_id."'
//         WHERE 
//             item_id='".$item_id."'
//             AND shop_name='".$shop_id."'
//         ";

//         $conn->query($sql_deduct);

//         //
//         // INSERT / UPDATE BLOCK
//         //
//         $sql_insert_block = "
//         INSERT INTO inventory_block
//         (
//             trans_id,
//             item_id,
//             qty
//         )
//         VALUES
//         (
//             '".$trans_id."',
//             '".$item_id."',
//             '".$qty."'
//         )

//         ON DUPLICATE KEY UPDATE
//         qty = VALUES(qty)
//         ";

//         $conn->query($sql_insert_block);

//     } else {

//         die("Error : Insufficient Stock");
//     }

// } else {

//     die("Error : Item Not Found In Inventory");
// }

//
// 2. CHECK CURRENT STOCK
//
$sql_inventory = "
SELECT qty 
FROM inventory_shop
WHERE 
    item_id='".$item_id."'
    AND shop_name='".$shop_id."'
";

$result = $conn->query($sql_inventory);

if($row = $result->fetch_assoc()) {

    $inventory = (float)$row["qty"];
    $qty = (float)$qty;

    //
    // VALIDATE ONLY IF QTY ENTERED
    //
    if($qty > 0){

        //
        // STOCK VALIDATION
        //
        if($qty > $inventory){

            die("Error : Item entered above available stock limit.");
        }

        //
        // DEDUCT STOCK
        //
        $sql_deduct = "
        UPDATE inventory_shop
        SET 
            qty = qty - ".$qty.",
            modified_on = NOW(),
            modified_by = '".$user_id."'
        WHERE 
            item_id='".$item_id."'
            AND shop_name='".$shop_id."'
        ";

        if(!$conn->query($sql_deduct)){

            die("Error : Unable to update inventory stock.");
        }

        //
        // INSERT / UPDATE BLOCK
        //
        $sql_insert_block = "
        INSERT INTO inventory_block
        (
            trans_id,
            item_id,
            qty
        )
        VALUES
        (
            '".$trans_id."',
            '".$item_id."',
            '".$qty."'
        )

        ON DUPLICATE KEY UPDATE
        qty = VALUES(qty)
        ";

        if(!$conn->query($sql_insert_block)){

            die("Error : Unable to block inventory.");
        }
    }

} else {

    //
    // ITEM NOT AVAILABLE IN INVENTORY
    //
    die("Error : Item entered above available stock limit.");
}

//
// 3. SOFT DELETE OLD SALES DETAIL
//
$sql = "
UPDATE sales_trans_det 
SET 
    active_status='Z',
    modified_on=NOW(),
    modified_by='".$user_id."'
WHERE 
    trans_id='".$trans_id."'
    AND item_id='".$item_id."'
    AND subtrans_id='".$subtrans_id."'
    AND ver='".$ver."'
";

$conn->query($sql);


//
// 4. UPDATE SALES TOTAL
//
$sql = "
SELECT 
    SUM(total) GRAND_TOTAL,
    SUM(tax_amount) TAX_AMOUNT,
    SUM(tax_amount_sgst) TAX_AMOUNT_SGST,
    SUM(tax_amount_igst) TAX_AMOUNT_IGST,
    SUM(price) PRICE,
    SUM(discount) DISCOUNT,
    SUM(roundoff) ROUNDOFF
FROM sales_trans_det 
WHERE 
    trans_id='".$trans_id."'
    AND active_status!='Z'
    AND IFNULL(account,'Y')='Y'
    AND ver='".$ver."'
";

$result = $conn->query($sql);

if($row = $result->fetch_assoc()) {

    $grand_total = $row["GRAND_TOTAL"];
    $tax_amount = $row["TAX_AMOUNT"];
    $tax_amount_sgst = $row["TAX_AMOUNT_SGST"];
    $tax_amount_igst = $row["TAX_AMOUNT_IGST"];
    $price = $row["PRICE"];
    $discount = $row["DISCOUNT"];
    $roundoff = $row["ROUNDOFF"];
}

$sql = "
UPDATE sales_trans 
SET 
    trans_amount='".$grand_total."'
WHERE 
    trans_id='".$trans_id."'
";

$conn->query($sql);


//
// 5. GET NEW SUBTRANS ID
//
$sql = "
SELECT IFNULL(MAX(subtrans_id),1000001)+1 subtrans_id 
FROM sales_trans_det 
WHERE trans_id='".$trans_id."'
";

$result = $conn->query($sql);

if($row = $result->fetch_assoc()) {

    $subtrans_id = $row["subtrans_id"];
}


//
// 6. INSERT SALES DETAIL
//
$sql = "
INSERT INTO sales_trans_det
(
    trans_id,
    subtrans_id,
    item_id,
    cost,
    qty,
    total,
    tax,
    tax_amount,
    active_status,
    created_on,
    created_by,
    shop_id,
    tax_amount_sgst,
    tax_sgst,
    discount,
    pending,
    price,
    roundoff,
    parent,
    account,
    ver,
    perc,
    tax_igst,
    tax_amount_igst
)
VALUES
(
    '".$trans_id."',
    '".$subtrans_id."',
    '".$item_id."',
    '".$cost."',
    '".$qty."',
    '".$total."',
    '".$tax_pc."',
    '".$tax."',
    'A',
    NOW(),
    '".$user_id."',
    '".$shop_id."',
    '".$tax_sgst."',
    '".$tax_pc_sgst."',
    '".$discount."',
    '".$qty."',
    '".$price1."',
    '".$roundoff."',
    '".$parent."',
    '".$account."',
    '".$ver."',
    '".$perc."',
    '".$tax_pc_igst."',
    '".$tax_igst."'
)
";

$conn->query($sql);


//
// 7. INSERT INVENTORY TRANSACTION
//
$sql_inventory_trans = "
INSERT INTO inventory_trans
(
    trans_id,
    item_id,
    shop_id,
    qty,
    trans_type,
    created_on,
    created_by
)
VALUES
(
    '".$trans_id."',
    '".$item_id."',
    '".$shop_id."',
    '".$qty."',
    'SALE',
    NOW(),
    '".$user_id."'
)

ON DUPLICATE KEY UPDATE

    qty = VALUES(qty),
    modified_on = NOW(),
    modified_by = '".$user_id."'
";

if (!$conn->query($sql_inventory_trans)) {

    die('Inventory Trans Error : '.$conn->error);
}


//
// 8. FINAL TOTAL RECALCULATION
//
$sql = "
SELECT 
    SUM(total) GRAND_TOTAL,
    SUM(tax_amount) TAX_AMOUNT,
    SUM(tax_amount_sgst) TAX_AMOUNT_SGST,
    SUM(tax_amount_igst) TAX_AMOUNT_IGST,
    SUM(price) PRICE,
    SUM(discount) DISCOUNT,
    SUM(roundoff) ROUNDOFF
FROM sales_trans_det 
WHERE 
    trans_id='".$trans_id."'
    AND active_status!='Z'
    AND ver='".$ver."'
    AND IFNULL(account,'Y')='Y'
";

$result = $conn->query($sql);

if($row = $result->fetch_assoc()) {

    $grand_total = $row["GRAND_TOTAL"];
    $tax_amount = $row["TAX_AMOUNT"];
    $tax_amount_sgst = $row["TAX_AMOUNT_SGST"];
    $tax_amount_igst = $row["TAX_AMOUNT_IGST"];
    $price = $row["PRICE"];
    $discount = $row["DISCOUNT"];
    $roundoff = $row["ROUNDOFF"];
}

$sql_upd = "
UPDATE sales_trans 
SET 
    trans_amount='".$grand_total."',
    pending='".$grand_total."'
WHERE 
    trans_id='".$trans_id."'
";

$conn->query($sql_upd);


//
// 9. RESPONSE
//
echo $subtrans_id
    ."~".$grand_total
    ."~".$tax_amount
    ."~".$tax_amount_sgst
    ."~".$tax_amount_igst
    ."~".($price + $discount + $roundoff);

$conn->close();

?>