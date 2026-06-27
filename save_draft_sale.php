<?php
include "db_config.php";

$trans_id     = $_POST["value"];
$trans_amount = $_POST["trans_amount"];
$roundoff     = $_POST["roundoff"];
$discount     = $_POST["discount"];
$ver          = $_POST["ver"];

$user_id = isset($_COOKIE["user_id"]) ? $_COOKIE["user_id"] : '';

$sql = "
SELECT IFNULL(SUM(total),0) GRAND_TOTAL
FROM sales_trans_det
WHERE
    trans_id='".$trans_id."'
    AND active_status!='Z'
    AND IFNULL(account,'Y')='Y'
    AND ver='".$ver."'
";

$result = $conn->query($sql);

$grand_total = 0;

if($row = $result->fetch_assoc()) {
    $grand_total = $row["GRAND_TOTAL"];
}

/*
If discount is positive:
Final = Grand Total - Discount + Roundoff

If discount is already negative:
Keep current logic.
*/

// $final_amount = $grand_total + $discount + $roundoff;

$final_amount = $grand_total - $discount + $roundoff;

$conn->begin_transaction();

try {

    /*
     * Update Sales Header
     */
    //  echo "BEFORE UPDATE";
    // exit;

    $sql = "
    UPDATE sales_trans
    SET
        active_status='A',
        discount='".$discount."',
        roundoff='".$roundoff."',
        ver='".$ver."',
        trans_amount='".$final_amount."',
        pending='".$final_amount."',
        modified_on=NOW(),
        modified_by='".$user_id."'
    WHERE
        trans_id='".$trans_id."'
    ";

    //$conn->query($sql);

    if(!$conn->query($sql)){
        die($conn->error);
    }

    $conn->commit();

    // echo "UPDATED AND COMMITTED";
    // exit;

    // if(!$conn->query($sql)){
    //     throw new Exception($conn->error);
    // }

    /*
     * Remove deleted rows from current version
     */
    $sql_cleanup = "
    DELETE FROM sales_trans_det
    WHERE
        trans_id='".$trans_id."'
        AND active_status='Z'
        AND ver='".$ver."'
    ";

    // if(!$conn->query($sql_cleanup)){
    //     throw new Exception($conn->error);
    // }

    // /*
    // * REVERSE OLD INVENTORY POSTING
    // */
    // $sql = "
    // SELECT item_id, qty, shop_id
    // FROM inventory_trans
    // WHERE trans_id='".$trans_id."'
    // AND trans_type='SALE'
    // ";

    if(!$conn->query($sql_cleanup)){
       throw new Exception($conn->error);
    }



/*
* REVERSE OLD INVENTORY POSTING
*/
$sql = "
SELECT item_id, qty, shop_id
FROM inventory_trans
WHERE trans_id='".$trans_id."'
AND trans_type='SALE'
";

    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()){

        $sql_restore = "
        UPDATE inventory_shop
        SET
            qty = qty + ".$row["qty"].",
            modified_on = NOW(),
            modified_by = '".$user_id."'
        WHERE
            item_id='".$row["item_id"]."'
            AND shop_name='".$row["shop_id"]."'
        ";

        if(!$conn->query($sql_restore)){
            throw new Exception($conn->error);
        }
    }

    /*
    * DELETE OLD INVENTORY TRANS
    */
    $sql = "
    DELETE FROM inventory_trans
    WHERE trans_id='".$trans_id."'
    AND trans_type='SALE'
    ";

    if(!$conn->query($sql)){
        throw new Exception($conn->error);
    }


    /*
 * VALIDATE STOCK BEFORE SAVE
 */
$sql_validate = "
SELECT
    item_id,
    shop_id,
    SUM(qty) qty
FROM sales_trans_det
WHERE
    trans_id='".$trans_id."'
    AND ver='".$ver."'
    AND active_status='A'
    AND IFNULL(account,'Y')='Y'
GROUP BY item_id, shop_id
";

//echo $sql_validate;exit;

$result_validate = $conn->query($sql_validate);

while($row_validate = $result_validate->fetch_assoc()){

    $item_id = $row_validate["item_id"];
    $shop_id = $row_validate["shop_id"];
    $req_qty = $row_validate["qty"];

    $sql_stock = "
    SELECT qty
    FROM inventory_shop
    WHERE
        item_id='".$item_id."'
        AND shop_name='".$shop_id."'
    ";

    $result_stock = $conn->query($sql_stock);

    if(!$result_stock || $result_stock->num_rows == 0){
        throw new Exception(
            "Stock Not Found For Item ".$item_id
        );
    }

    $row_stock = $result_stock->fetch_assoc();

    if($req_qty > $row_stock["qty"]){
        throw new Exception(
            "Insufficient Stock For Item ".$item_id.
            ". Available=".$row_stock["qty"].
            ", Required=".$req_qty
        );
    }
}

        /*
        * POST LATEST INVENTORY
        */
        $sql = "
        SELECT
            item_id,
            shop_id,
            SUM(qty) qty
        FROM sales_trans_det
        WHERE
            trans_id='".$trans_id."'
            AND ver='".$ver."'
            AND active_status='A'
            AND IFNULL(account,'Y')='Y'
        GROUP BY item_id, shop_id
        ";

        $result = $conn->query($sql);

        while($row = $result->fetch_assoc()){

            /*
            * DEDUCT STOCK
            */
            $sql_stock = "
            UPDATE inventory_shop
            SET
                qty = qty - ".$row["qty"].",
                modified_on = NOW(),
                modified_by = '".$user_id."'
            WHERE
                item_id='".$row["item_id"]."'
                AND shop_name='".$row["shop_id"]."'
            ";

            if(!$conn->query($sql_stock)){
                throw new Exception($conn->error);
            }

            /*
            * INSERT INVENTORY TRANS
            */
            $sql_trans = "
            INSERT INTO inventory_trans
            (
                trans_id,
                item_id,
                shop_id,
                qty,
                trans_type,
                created_by,
                modified_by
            )
            VALUES
            (
                '".$trans_id."',
                '".$row["item_id"]."',
                '".$row["shop_id"]."',
                '".$row["qty"]."',
                'SALE',
                '".$user_id."',
                '".$user_id."'
            )
            ";

            if(!$conn->query($sql_trans)){
                throw new Exception($conn->error);
            }
        }

    $conn->commit();

    echo "SUCCESS";

}
catch(Exception $e){

    $conn->rollback();

    echo "Error : ".$e->getMessage();
}

$conn->close();
?>