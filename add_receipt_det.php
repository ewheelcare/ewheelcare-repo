<?php
include "db_config.php";
ini_set('display_errors', 0);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set("Asia/Kolkata");
header('Content-Type: application/json');
session_start();

try {

    /* =========================
       START TRANSACTION
    ========================= */
    $conn->begin_transaction();

    /* =========================
       INPUTS
    ========================= */
    $item_id     = (int)$_POST["item_id"];
    $trans_id    = $_POST["trans_id"];
    $subtrans_id = $_POST["subtrans_id"] ?? '';

    $qty         = isset($_POST["qty"]) ? (float)$_POST["qty"] : 0;
    $cost        = $_POST["cost"];
    
    $total       = $_POST["total"];
    
    $tax_pc      = $_POST["tax_pc"];
    $tax         = $_POST["tax"];
    $tax_sgst    = $_POST["tax_sgst"];
    $tax_pc_sgst = $_POST["tax_pc_sgst"];
    $tax_igst    = $_POST["tax_igst"];
    $tax_pc_igst = $_POST["tax_pc_igst"];
    $price       = $_POST["price"];
    $discount    = $_POST["discount"];
    $roundoff    = $_POST["roundoff"];
    $remarks = trim($_POST["remarks"] ?? '');
    $shop_id = $_SESSION["shop"] ?? '';
    $user_id = $_SESSION["user_id"] ?? 'SYSTEM';

    /*======================================
        Validation checkes
    =========================================*/

    if($item_id <= 0){
    throw new Exception("Invalid item");
            }

    if($qty <= 0){
    throw new Exception("Quantity must be greater than zero");

    }

    if(!is_numeric($cost) || !is_numeric($total)){
    throw new Exception("Invalid numeric values");
         }
     if(empty($shop_id)){
    throw new Exception("Invalid shop");
}
    /* =========================
       CHECK IF RECORD EXISTS
    ========================= */
    $stmt = $conn->prepare("
    SELECT subtrans_id
    FROM receipt_trans_det
    WHERE trans_id=? AND item_id=? AND subtrans_id=?
    FOR UPDATE
        ");

        $stmt->bind_param("sii", $trans_id, $item_id, $subtrans_id);
        $stmt->execute();

        $res = $stmt->get_result();
        $exists = $res->fetch_assoc();
        $res->free();       
        $stmt->close();    

        if ($exists) {

        /* =========================
           UPDATE EXISTING ROW
        ========================= */
       $stmt = $conn->prepare("
    UPDATE receipt_trans_det
    SET 
        qty=?, cost=?, total=?, tax=?, tax_amount=?,
        tax_amount_sgst=?, tax_sgst=?, tax_amount_igst=?, tax_igst=?,
        price=?, discount=?, roundoff=?, remarks=?, pending=?,
        modified_by=?
         WHERE trans_id=? AND item_id=? AND subtrans_id=?
            ");

            $stmt->bind_param(
                "ddddddddddddsdsiii",
                $qty,
                $cost,
                $total,
                $tax_pc,
                $tax,
                $tax_sgst,
                $tax_pc_sgst,
                $tax_igst,
                $tax_pc_igst,
                $price,
                $discount,
                $roundoff,
                $remarks,
                $qty,
                $user_id,
                $trans_id,
                $item_id,
                $subtrans_id
            );

            $stmt->execute();

    } else {

        /* =========================
           GENERATE NEW SUBTRANS_ID
        ========================= */
        // Lock the parent transaction row
            $stmt = $conn->prepare("
                SELECT last_sub_id 
                FROM receipt_trans 
                WHERE trans_id=? 
                FOR UPDATE
            ");
            $stmt->bind_param("s", $trans_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();

            $res->free();        
            $stmt->close();      
            if (!$row) {
            
                throw new Exception("Invalid transaction");
            }

            $subtrans_id = $row["last_sub_id"] + 1;

            // Update counter
            $stmt = $conn->prepare("
                UPDATE receipt_trans 
                SET last_sub_id=? 
                WHERE trans_id=?
            ");
            $stmt->bind_param("is", $subtrans_id, $trans_id);
            $stmt->execute();
        /* =========================
           INSERT NEW ROW
        ========================= */
                $stmt = $conn->prepare("
            INSERT INTO receipt_trans_det (
                trans_id, subtrans_id, item_id, cost, qty, total,
                tax, tax_amount, created_by, active_status,
                shop_id, tax_amount_sgst, tax_sgst, discount, pending,
                remarks, tax_igst, tax_amount_igst, price, roundoff
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, 'A',
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
            ");

            $stmt->bind_param(
                "siidddddssddddddddd",
                $trans_id,          // s
                $subtrans_id,       // i
                $item_id,           // i
                $cost,              // d
                $qty,               // d
                $total,             // d
                $tax_pc,            // d
                $tax,               // d
                $user_id,           // s
                $shop_id,           // s
                $tax_sgst,          // d
                $tax_pc_sgst,       // d
                $discount,          // d
                $qty,               // d (pending)
                $remarks,           // s
                $tax_pc_igst,       // d
                $tax_igst,          // d
                $price,             // d
                $roundoff           // d
            );

            $stmt->execute();    }

    /* =========================
       OPTIONAL: RETURN LIVE TOTALS (UI USE)
       (NOT saving to receipt_trans)
    ========================= */
    $stmt = $conn->prepare("
    SELECT 
        IFNULL(SUM(total),0),
        IFNULL(SUM(tax_amount),0),
        IFNULL(SUM(tax_amount_sgst),0),
        IFNULL(SUM(tax_amount_igst),0),
        IFNULL(SUM(price),0),
        IFNULL(SUM(discount),0),
        IFNULL(SUM(roundoff),0)
    FROM receipt_trans_det
    WHERE trans_id=? and active_status = 'A'
");

        $stmt->bind_param("s", $trans_id);
        $stmt->execute();

        $stmt->bind_result(
            $grand_total,
            $tax_amount,
            $tax_amount_sgst,
            $tax_amount_igst,
            $price_total,
            $discount_total,
            $roundoff_total
        );

    $stmt->fetch();
    $stmt->close(); 

    /* =========================
       COMMIT
    ========================= */
    $conn->commit();

    /* =========================
       RESPONSE
    ========================= */
   // echo $subtrans_id."~".$grand_total."~".$tax_amount."~".$tax_amount_sgst."~".$tax_amount_igst."~".($price_total - $discount_total + $roundoff_total)."~".$discount_total."~".$roundoff_total."~".$price_total;
    echo json_encode([
    "status" => "success",
    "data" => [
        "subtrans_id" => $subtrans_id,
        "grand_total" => $grand_total,
        "tax" => $tax_amount,
        "sgst" => $tax_amount_sgst,
        "igst" => $tax_amount_igst,
        "net" => ($price_total - $discount_total + $roundoff_total),
        "discount" => $discount_total,
        "roundoff" => $roundoff_total,
        "price" => $price_total
    ],
    "message" => "Row saved successfully"
], JSON_UNESCAPED_UNICODE);

exit;

} catch (Exception $e) {

    /* =========================
       ROLLBACK
    ========================= */
    $conn->rollback();

    /* =========================
       LOG TO APACHE error.log
    ========================= */
    error_log(
        "ERP | add_receipt_det.php | " .
        date("Y-m-d H:i:s") .
        " | User:" . ($_SESSION["user_id"] ?? 'UNKNOWN') .
        " | Trans:" . ($_POST["trans_id"] ?? '') .
        " | Item:" . ($_POST["item_id"] ?? '') .
        " | Error:" . $e->getMessage()
    );

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    exit;
}

$conn->close();
?>