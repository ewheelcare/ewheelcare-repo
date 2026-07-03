<?php
include "db_config.php";

$trans_id    = $_POST["trans_id"];
$subtrans_id = $_POST["subtrans_id"];
$item_id     = $_POST["item_id"];
$ver         = $_POST["ver"];
$user_id     = isset($_COOKIE["user_id"]) ? $_COOKIE["user_id"] : '';

try {

    $conn->begin_transaction();

    /*
     * STEP 1: Read account/parent BEFORE deleting
     */
    $sql_check = "
    SELECT account, parent
    FROM sales_trans_det
    WHERE
        trans_id='".$trans_id."'
        AND subtrans_id='".$subtrans_id."'
        AND ver='".$ver."'
        AND active_status='A'
    ";

    $result_check = $conn->query($sql_check);
    $row_check = $result_check ? $result_check->fetch_assoc() : null;

    $account = $row_check ? $row_check["account"] : "";
    $parent  = $row_check ? $row_check["parent"] : "";

    /*
     * STEP 2: Based on account type, decide what to delete
     *
     * account='N' = parent row of a mapped group
     *   -> delete parent AND all children (parent=item_id)
     *
     * account='Y' with parent set = child row of a mapped group
     *   -> delete ALL children of same parent (siblings)
     *   -> also delete the parent row (account='N')
     *   -> DO NOT touch other standalone items
     *
     * account='Y' with no parent = standalone item
     *   -> delete only this single row
     */

    if($account === "N"){
        // Delete parent row itself
        $sql_del_parent = "
        UPDATE sales_trans_det
        SET active_status='Z', modified_on=NOW(), modified_by='".$user_id."'
        WHERE trans_id='".$trans_id."'
        AND subtrans_id='".$subtrans_id."'
        AND ver='".$ver."'
        AND active_status='A'
        ";
        if(!$conn->query($sql_del_parent)){
            throw new Exception($conn->error);
        }

        // Delete all children of this parent
        $sql_del_children = "
        UPDATE sales_trans_det
        SET active_status='Z', modified_on=NOW(), modified_by='".$user_id."'
        WHERE trans_id='".$trans_id."'
        AND parent='".$item_id."'
        AND ver='".$ver."'
        AND active_status='A'
        ";
        if(!$conn->query($sql_del_children)){
            throw new Exception($conn->error);
        }

    } else if($parent !== "" && $parent !== null && $parent !== "0"){
        // Child row clicked - delete parent row AND all siblings (same parent)
        $sql_del_group = "
        UPDATE sales_trans_det
        SET active_status='Z', modified_on=NOW(), modified_by='".$user_id."'
        WHERE trans_id='".$trans_id."'
        AND ver='".$ver."'
        AND active_status='A'
        AND (
            (item_id='".$parent."' AND IFNULL(account,'Y')='N')
            OR parent='".$parent."'
        )
        ";
        if(!$conn->query($sql_del_group)){
            throw new Exception($conn->error);
        }

    } else {
        // Standalone row - delete only this row
        $sql_del_single = "
        UPDATE sales_trans_det
        SET active_status='Z', modified_on=NOW(), modified_by='".$user_id."'
        WHERE trans_id='".$trans_id."'
        AND subtrans_id='".$subtrans_id."'
        AND ver='".$ver."'
        AND active_status='A'
        ";
        if(!$conn->query($sql_del_single)){
            throw new Exception($conn->error);
        }
    }

    /*
     * STEP 3: Hard delete all Z rows for this trans+ver
     */
    $sql_hard = "
    DELETE FROM sales_trans_det
    WHERE trans_id='".$trans_id."'
    AND active_status='Z'
    AND ver='".$ver."'
    ";
    if(!$conn->query($sql_hard)){
        throw new Exception($conn->error);
    }

    /*
     * STEP 4: Recalculate header total from remaining active rows
     */
    $sql_total = "
    SELECT IFNULL(SUM(total), 0) GRAND_TOTAL
    FROM sales_trans_det
    WHERE trans_id='".$trans_id."'
    AND active_status='A'
    AND ver='".$ver."'
    AND IFNULL(account,'Y')='Y'
    ";
    $result_total = $conn->query($sql_total);
    $grand_total = 0;
    if($result_total && $row_total = $result_total->fetch_assoc()){
        $grand_total = (float)$row_total["GRAND_TOTAL"];
    }

    $sql_upd = "
    UPDATE sales_trans
    SET
        trans_amount='".$grand_total."',
        pending='".$grand_total."',
        modified_on=NOW(),
        modified_by='".$user_id."'
    WHERE trans_id='".$trans_id."'
    ";
    if(!$conn->query($sql_upd)){
        throw new Exception($conn->error);
    }

    $conn->commit();

    echo "DELETED";

} catch(Exception $e){
    $conn->rollback();
    echo "Error : ".$e->getMessage();
}

$conn->close();
?>