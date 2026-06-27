<?php
include "db_config.php";

file_put_contents(
    "sales_debug.log",
    date('Y-m-d H:i:s') .
    " | " .
    json_encode($_POST) .
    PHP_EOL,
    FILE_APPEND
);

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

if((float)$total <= 0){
    exit;
}

$account = (!isset($_POST["account"]) || $_POST["account"] == "") 
    ? "Y" 
    : $_POST["account"];

$parent = $_POST["parent"];
$trans_id = $_POST["trans_id"];

$invoice_discount = isset($_POST["invoice_discount"]) ? (float)$_POST["invoice_discount"] : 0;
$invoice_roundoff = isset($_POST["invoice_roundoff"]) ? (float)$_POST["invoice_roundoff"] : 0;
$invoice_final_amount = isset($_POST["invoice_final_amount"]) ? (float)$_POST["invoice_final_amount"] : 0;

$shop_id = isset($_COOKIE["shop"]) ? $_COOKIE["shop"] : '';
$user_id = isset($_COOKIE["user_id"]) ? $_COOKIE["user_id"] : '';

$ver = $_POST["ver"];
$perc = $_POST["perc"];
$subtrans_id = intval($_POST["subtrans_id"] ?? 0);

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

/*
 * STOCK CHECK
 */
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
        file_put_contents(
            "stock_error.log",
            "BLOCKED\n",
            FILE_APPEND
        );

        die("Error: Available Stock=".$effective_stock." Requested=".$qty);
    }
}
else
{
    // No inventory row found = zero stock
    if((float)$qty > 0 && $account !== 'N'){
        die("Error: Available Stock=0 Requested=".$qty);
    }
}

/*
 * SOFT DELETE OLD SALES DETAIL
 */
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

$sql_delete = "
DELETE FROM sales_trans_det 
WHERE
    active_status='Z'
    AND trans_id='".$trans_id."'
    AND item_id='".$item_id."'
    AND subtrans_id='".$subtrans_id."'
    AND ver='".$ver."'
";

$conn->query($sql_delete);

/*
 * SKIP LOW VALUE MAPPED ROWS
 */
if(
    $parent != '' &&
    $account == 'Y' &&
    (float)$total > 0 &&
    (float)$total < 2
){
    exit;
}

$cost      = round((float)$cost);
$total     = round((float)$total);
$tax       = round((float)$tax);
$tax_sgst  = round((float)$tax_sgst);
$tax_igst  = round((float)$tax_igst);
$discount  = round((float)($discount ?? 0));
$price1    = round((float)$price1);
$roundoff  = round((float)$roundoff);



/*
 * INSERT NEW SALES DETAIL
 */
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
    '0',
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

/*
 * RECALCULATE GRAND TOTAL FROM DB (SERVER SIDE - DO NOT TRUST FRONTEND)
 */
$sql_recalc = "
SELECT 
    IFNULL(SUM(total), 0) GRAND_TOTAL,
    IFNULL(SUM(tax_amount), 0) TAX_AMOUNT,
    IFNULL(SUM(tax_amount_sgst), 0) TAX_AMOUNT_SGST,
    IFNULL(SUM(tax_amount_igst), 0) TAX_AMOUNT_IGST,
    IFNULL(SUM(price), 0) PRICE,
    IFNULL(SUM(discount), 0) DISCOUNT,
    IFNULL(SUM(roundoff), 0) ROUNDOFF
FROM sales_trans_det 
WHERE 
    trans_id='".$trans_id."'
    AND active_status='A'
    AND ver='".$ver."'
    AND IFNULL(account,'Y')='Y'
";

$result_recalc = $conn->query($sql_recalc);

$grand_total      = 0;
$tax_amount       = 0;
$tax_amount_sgst  = 0;
$tax_amount_igst  = 0;
$price_sum        = 0;
$discount_sum     = 0;
$roundoff_sum     = 0;

if($row_recalc = $result_recalc->fetch_assoc()){
    $grand_total     = (float)$row_recalc["GRAND_TOTAL"];
    $tax_amount      = (float)$row_recalc["TAX_AMOUNT"];
    $tax_amount_sgst = (float)$row_recalc["TAX_AMOUNT_SGST"];
    $tax_amount_igst = (float)$row_recalc["TAX_AMOUNT_IGST"];
    $price_sum       = (float)$row_recalc["PRICE"];
    $discount_sum    = (float)$row_recalc["DISCOUNT"];
    $roundoff_sum    = (float)$row_recalc["ROUNDOFF"];
}

/*
 * CALCULATE SAFE FINAL AMOUNT
 */
$safe_final_amount = $grand_total - $invoice_discount + $invoice_roundoff;
if($safe_final_amount < 0){ $safe_final_amount = 0; }

/*
 * UPDATE SALES HEADER WITH SAFE VALUES
 */
$sql_upd = "
UPDATE sales_trans
SET
    trans_amount='".$safe_final_amount."',
    pending='".$safe_final_amount."',
    discount='".$invoice_discount."',
    roundoff='".$invoice_roundoff."',
    modified_on=CURDATE(),
    modified_by='".$user_id."'
WHERE
    trans_id='".$trans_id."'
";

$conn->query($sql_upd);

/*
 * RESPONSE
 */
echo $subtrans_id
    ."~".$grand_total
    ."~".$tax_amount
    ."~".$tax_amount_sgst
    ."~".$tax_amount_igst
    ."~".($price_sum - $discount_sum + $roundoff_sum);

$conn->close();
?>