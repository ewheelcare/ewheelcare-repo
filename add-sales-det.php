<?php
include "db_config.php";

// echo "<pre>";
// print_r($_POST);
// exit;

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
// $discount = $_POST["discount"];

$invoice_discount =
isset($_POST["invoice_discount"])
? $_POST["invoice_discount"]
: 0;

$invoice_roundoff =
isset($_POST["invoice_roundoff"])
? $_POST["invoice_roundoff"]
: 0;

$invoice_final_amount =
isset($_POST["invoice_final_amount"])
? $_POST["invoice_final_amount"]
: 0;


$shop_id = isset($_COOKIE["shop"]) ? $_COOKIE["shop"] : '';
$user_id = isset($_COOKIE["user_id"]) ? $_COOKIE["user_id"] : '';

$ver = $_POST["ver"];
$perc = $_POST["perc"];
$subtrans_id = $_POST["subtrans_id"];


/*
 * GET OLD QTY (FOR EDIT CASE)
 */
$old_qty = 0;

$sql_old = "
SELECT qty
FROM sales_trans_det
WHERE
    trans_id='".$trans_id."'
    AND item_id='".$item_id."'
    AND subtrans_id='".$subtrans_id."'
    AND ver='".$ver."'
    AND active_status='A'
";

$result_old = $conn->query($sql_old);

if($result_old && $row_old = $result_old->fetch_assoc())
{
    $old_qty = (float)$row_old["qty"];
}

$sql_stock = "
SELECT qty
FROM inventory_shop
WHERE
    item_id='".$item_id."'
    AND shop_name='".$shop_id."'
";

$result_stock = $conn->query($sql_stock);

if($result_stock && $result_stock->num_rows > 0)
{
    $row_stock = $result_stock->fetch_assoc();

    $available_stock = (float)$row_stock["qty"];

    $effective_stock = $available_stock + $old_qty;

    if($qty > $effective_stock)
    {
        die(
            "Available Stock = ".$effective_stock.
            ", Requested Qty = ".$qty
        );
    }
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
// 5. TEMP SUBTRANS ID
//
$subtrans_id = 0;
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

if(!$conn->query($sql)){
    die("Error : ".$conn->error);
}

$new_id = $conn->insert_id;

$sql_sub = "
UPDATE sales_trans_det
SET subtrans_id='".$new_id."'
WHERE id='".$new_id."'
";

if(!$conn->query($sql_sub)){
    die("Error : ".$conn->error);
}

$subtrans_id = $new_id;

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

// $sql_upd = "
// UPDATE sales_trans 
// SET 
//     trans_amount='".$grand_total."',
//     pending='".$grand_total."'
// WHERE 
//     trans_id='".$trans_id."'
// ";

$sql_upd = "
UPDATE sales_trans
SET
    trans_amount='".$invoice_final_amount."',
    pending='".$invoice_final_amount."',
    discount='".$invoice_discount."',
    roundoff='".$invoice_roundoff."',
    modified_on=CURDATE(),
    modified_by='".$user_id."'
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
    ."~".($price - $discount + $roundoff);

$conn->close();

?>