<?php
include "db_config.php";

$trans_id = $_POST["trans_id"];
$ver      = $_POST["ver"];
$user_id  = $_COOKIE["user_id"];

$new_ver = $ver + 1;


try {

    $conn->begin_transaction();

    /* Update Header */

  

    $sql = "
    UPDATE sales_trans
    SET
        active_status='D',
        ver='".$new_ver."',
        modified_on=CURDATE(),
        modified_by='".$user_id."'
    WHERE trans_id='".$trans_id."'
    ";

     //echo "ERROR : ".$sql;exit;

    if(!$conn->query($sql)){
        throw new Exception($conn->error);
    }

    /* Check if version already exists */

    $sql = "
    SELECT COUNT(*) CNT
    FROM sales_trans_det
    WHERE
        trans_id='".$trans_id."'
        AND ver='".$new_ver."'
    ";

    $result = $conn->query($sql);

    if(!$result){
        throw new Exception($conn->error);
    }

    $row = $result->fetch_assoc();

    if($row["CNT"] == 0){

        $sql = "
        INSERT INTO sales_trans_det
        (
            trans_id,
            subtrans_id,
            item_id,
            cost,
            qty,
            tax,
            tax_amount,
            total,
            created_by,
            created_on,
            modified_by,
            modified_on,
            active_status,
            vehicle,
            shop_id,
            tax_sgst,
            tax_amount_sgst,
            run_km,
            discount,
            pending,
            tax_igst,
            tax_amount_igst,
            roundoff,
            account,
            price,
            remarks,
            parent,
            ver,
            perc
        )
        SELECT
            trans_id,
            subtrans_id,
            item_id,
            cost,
            qty,
            tax,
            tax_amount,
            total,
            created_by,
            created_on,
            modified_by,
            modified_on,
            active_status,
            vehicle,
            shop_id,
            tax_sgst,
            tax_amount_sgst,
            run_km,
            discount,
            pending,
            tax_igst,
            tax_amount_igst,
            roundoff,
            account,
            price,
            remarks,
            parent,
            '".$new_ver."',
            perc
        FROM sales_trans_det
        WHERE
            trans_id='".$trans_id."'
            AND ver='".$ver."'
            AND active_status <> 'Z'
        ";

        if(!$conn->query($sql)){
            throw new Exception($conn->error);
        }
    }

    $conn->commit();

    echo "SUCCESS";

}
catch(Exception $e){

    $conn->rollback();

    echo "ERROR : ".$e->getMessage();
}

$conn->close();
?>