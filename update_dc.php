<?php

include "db_config.php";

$dc_id = $_POST['dc_id'] ?? '';

if($dc_id=='')
{
    die("Invalid DC");
}

$conn->begin_transaction();

try
{
    $trans_id = '';

    $res = mysqli_query($conn,"
        SELECT trans_id
        FROM delivery_challan
        WHERE dc_id='$dc_id'
    ");

    if($row = mysqli_fetch_assoc($res))
    {
        $trans_id = $row['trans_id'];
    }

    $vehicle  = mysqli_real_escape_string($conn,$_POST['vehicle'] ?? '');
    $odometer = mysqli_real_escape_string($conn,$_POST['odometer'] ?? '');

    $rows = json_decode($_POST['rows'], true);

    foreach($rows as $row)
    {
        $item_id     = mysqli_real_escape_string($conn,$row['item_id']);
        $subtrans_id = (int)$row['subtrans_id'];
        $new_qty     = (int)$row['qty'];

        /*
        Get old DC qty
        */
        // $old_res = mysqli_query($conn,"
        //     SELECT despatched
        //     FROM delivery_challan_det
        //     WHERE dc_id='$dc_id'
        //     AND item_id='$item_id'
        //     AND subtrans_id='$subtrans_id'
        // ");

        $old_res = mysqli_query($conn,"
            SELECT despatched
            FROM delivery_challan_det
            WHERE dc_id='$dc_id'
            AND item_id='$item_id'
            AND subtrans_id='$subtrans_id'
        ");

        if($old_res && ($old_row = mysqli_fetch_assoc($old_res)))
        {
            $old_qty = (int)$old_row['despatched'];
            $row_exists = true;
        }
        else
        {
            $old_qty = 0;
            $row_exists = false;
        }

        $diff = $new_qty - $old_qty;

        /*
        Validate against ordered quantity
        */
        $qty_res = mysqli_query($conn,"
            SELECT qty
            FROM sales_trans_det
            WHERE trans_id='$trans_id'
            AND item_id='$item_id'
            AND ver = (
                SELECT MAX(ver)
                FROM sales_trans_det
                WHERE trans_id='$trans_id'
            )
        ");

        $qty_row = mysqli_fetch_assoc($qty_res);

        $ordered_qty = (int)$qty_row['qty'];

        /*
        Get dispatched qty from OTHER DCs
        */
        $other_res = mysqli_query($conn,"
            SELECT COALESCE(SUM(despatched),0) total_dispatched
            FROM delivery_challan_det
            WHERE trans_id='$trans_id'
            AND item_id='$item_id'
            AND NOT (
                dc_id='$dc_id'
                AND subtrans_id='$subtrans_id'
            )
        ");

        $other_row = mysqli_fetch_assoc($other_res);

        $other_dispatched = (int)$other_row['total_dispatched'];

        $new_total_dispatched = $other_dispatched + $new_qty;

        if($new_total_dispatched > $ordered_qty)
        {
            throw new Exception(
                "Cannot dispatch more than ordered quantity. ".
                "Ordered Qty: ".$ordered_qty.
                ", Already Dispatched in other DCs: ".$other_dispatched.
                ", Maximum Allowed: ".($ordered_qty - $other_dispatched)
            );
        }        

        /*
        Update DC Qty
        */
        // mysqli_query($conn,"
        //     UPDATE delivery_challan_det
        //     SET
        //         despatched='$new_qty',
        //         vehicle='$vehicle',
        //         odometer='$odometer'
        //     WHERE dc_id='$dc_id'
        //     AND item_id='$item_id'
        //     AND subtrans_id='$subtrans_id'
        // ");

        if($row_exists)
        {

           mysqli_query($conn,"
                UPDATE delivery_challan
                SET                   
                    vehicle='$vehicle',
                    odometer='$odometer'
                WHERE dc_id='$dc_id'                
            ");

            mysqli_query($conn,"
                UPDATE delivery_challan_det
                SET
                    despatched='$new_qty'                    
                WHERE dc_id='$dc_id'
                AND item_id='$item_id'
                AND subtrans_id='$subtrans_id'
            ");

            // mysqli_query($conn,"
            //     UPDATE sales_trans_det
            //     SET
            //         pending = pending - ($diff),
            //         dispatched_qty = dispatched_qty + ($diff),
            //         vehicle = '$vehicle',
            //         run_km = '$odometer'
            //     WHERE trans_id='$trans_id'
            //     AND subtrans_id='$subtrans_id'
            //     AND item_id='$item_id'
            //     AND ver = (
            //         SELECT MAX_VER FROM
            //         (
            //             SELECT MAX(ver) MAX_VER
            //             FROM sales_trans_det
            //             WHERE trans_id='$trans_id'
            //         ) x
            //     )
            // ");

            mysqli_query($conn,"
                UPDATE sales_trans_det
                SET
                    pending = pending - ($diff),
                    dispatched_qty = dispatched_qty + ($diff),
                    vehicle = '$vehicle',
                    run_km = '$odometer'
                WHERE trans_id='$trans_id'
                AND item_id='$item_id'
                AND ver = (
                    SELECT MAX_VER FROM
                    (
                        SELECT MAX(ver) MAX_VER
                        FROM sales_trans_det
                        WHERE trans_id='$trans_id'
                    ) x
                )
            ");
            
        }
        else
        {
            if($new_qty > 0)
            {
                mysqli_query($conn,"
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
                        '$dc_id',
                        '$trans_id',
                        '$subtrans_id',
                        'A',
                        '$item_id',
                        '$new_qty'
                    )
                ");
            }
        }

        /*
        Update sales_trans_det
        */
        // mysqli_query($conn,"
        //     UPDATE sales_trans_det
        //     SET
        //         pending = pending - ($diff),
        //         dispatched_qty = dispatched_qty + ($diff),
        //         vehicle = '$vehicle',
        //         run_km = '$odometer'
        //     WHERE trans_id='$trans_id'
        //     AND subtrans_id='$subtrans_id'
        //     AND item_id='$item_id'
        // ");

        // mysqli_query($conn,"
        //     UPDATE sales_trans_det
        //     SET dc_status='$dc_status'
        //     WHERE trans_id='$trans_id'
        //     AND item_id='$item_id'
        //     AND ver = (
        //         SELECT MAX_VER FROM
        //         (
        //             SELECT MAX(ver) MAX_VER
        //             FROM sales_trans_det
        //             WHERE trans_id='$trans_id'
        //         ) x
        //     )
        // ");

        /*
        Update dc_status
        */
        // $status_res = mysqli_query($conn,"
        //     SELECT pending
        //     FROM sales_trans_det
        //     WHERE trans_id='$trans_id'
        //     AND subtrans_id='$subtrans_id'
        //     AND item_id='$item_id'
        // ");

        $status_res = mysqli_query($conn,"
            SELECT pending
            FROM sales_trans_det
            WHERE trans_id='$trans_id'
            AND item_id='$item_id'
            AND ver = (
                SELECT MAX_VER FROM
                (
                    SELECT MAX(ver) MAX_VER
                    FROM sales_trans_det
                    WHERE trans_id='$trans_id'
                ) x
            )
        ");

        $status_row = mysqli_fetch_assoc($status_res);

        $pending = (int)$status_row['pending'];

        $dc_status = ($pending <= 0) ? 'Y' : 'P';

        // mysqli_query($conn,"
        //     UPDATE sales_trans_det
        //     SET dc_status='$dc_status'
        //     WHERE trans_id='$trans_id'
        //     AND subtrans_id='$subtrans_id'
        //     AND item_id='$item_id'
        // ");

        mysqli_query($conn,"
            UPDATE sales_trans_det
            SET dc_status='$dc_status'
            WHERE trans_id='$trans_id'
            AND item_id='$item_id'
            AND ver = (
                SELECT MAX_VER FROM
                (
                    SELECT MAX(ver) MAX_VER
                    FROM sales_trans_det
                    WHERE trans_id='$trans_id'
                ) x
            )
        ");

        /*
        Update Inventory
        */
        mysqli_query($conn,"
            UPDATE inventory_block
            SET qty = qty - ($diff)
            WHERE trans_id='$trans_id'
            AND item_id='$item_id'
        ");
    }

    /*
    Recalculate Delivery Status
    */
    // $sql_status = "
    // SELECT COUNT(*) cnt
    // FROM sales_trans_det
    // WHERE trans_id='".$trans_id."'
    // AND ver = (
    //     SELECT MAX(ver)
    //     FROM sales_trans_det
    //     WHERE trans_id='".$trans_id."'
    // )
    // AND active_status='A'
    // AND account='Y'
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
AND pending > 0
    ";

    $res_status = mysqli_query($conn,$sql_status);

    $row_status = mysqli_fetch_assoc($res_status);

    if($row_status['cnt']==0)
    {
        mysqli_query($conn,"
            UPDATE sales_trans
            SET delivery_status='D'
            WHERE trans_id='$trans_id'
        ");
    }
    else
    {
        mysqli_query($conn,"
            UPDATE sales_trans
            SET delivery_status='P'
            WHERE trans_id='$trans_id'
        ");
    }

    $conn->commit();

    echo json_encode([
        "status" => "SUCCESS",
        "trans_id" => $trans_id
    ]);
}
catch(Exception $e)
{
    $conn->rollback();

    echo json_encode([
        "status" => "ERROR",
        "message" => $e->getMessage()
    ]);
}
?>