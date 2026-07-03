<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include "db_config.php";

// =====================================================
// COMMON JSON LOG FUNCTION
// =====================================================
function write_log($type, $data = [])
{
    error_log(json_encode([
        "time" => date("Y-m-d H:i:s"),
        "type" => $type,
        "data" => $data
    ]));
}

$conn->autocommit(false);

try {

    // =====================================================
    // SESSION VALIDATION
    // =====================================================
    $user_id = $_SESSION["user_id"] ?? "";
    $login_shop = $_SESSION["shop"] ?? "";

    if ($user_id == "" || $login_shop == "") {

        echo json_encode([
            "success" => false,
            "message" => "Session expired"
        ]);

        exit();
    }

    // =====================================================
    // INPUTS
    // =====================================================
    $item_id  = trim($_POST["item_id"] ?? '');
    $to_shop  = trim($_POST["to_shop"] ?? '');
    $from_qty = (float)($_POST["from_qty"] ?? 0);
    $Shop_Name = trim($_POST["Shop_Name"] ?? '');
    // =====================================================
    // VALIDATION
    // =====================================================
    if ($item_id == '') {
        throw new Exception("Item ID missing");
    }

    if ($to_shop == '') {
        throw new Exception("Destination shop missing");
    }

    if ($from_qty <= 0) {
        throw new Exception("Invalid quantity");
    }

    write_log("TRANSFER_REQUEST", [

        "item_id" => $item_id,
        "to_shop" => $to_shop,
        "qty" => $from_qty,
        "user_id" => $user_id,
        "Shop_name" => $Shop_Name
    ]);

    // =====================================================
    // START TRANSACTION
    // =====================================================
    $conn->begin_transaction();

    // =====================================================
    // FETCH INVENTORY STOCK
    // =====================================================
    $sql = "
        SELECT qty
        FROM inventory
        WHERE item_id = ?
        FOR UPDATE
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $item_id);

    $stmt->execute();

    $res = $stmt->get_result();

    if (!$row = $res->fetch_assoc()) {

        throw new Exception(
            "Item not found in inventory"
        );
    }

    $available_qty = (float)$row['qty'];

    // =====================================================
    // VALIDATE STOCK
    // =====================================================
    if ($from_qty > $available_qty) {

        throw new Exception(
            "Insufficient stock. Available Qty = " .
            $available_qty
        );
    }

    write_log("STOCK_VALIDATED", [

        "item_id" => $item_id,
        "available_qty" => $available_qty,
        "transfer_qty" => $from_qty
    ]);

    $stmt->close();

    // =====================================================
    // DEDUCT STOCK FROM INVENTORY
    // =====================================================
    $sql = "
        UPDATE inventory
        SET qty = qty - ?
        WHERE item_id = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ds",
        $from_qty,
        $item_id
    );

    $stmt->execute();

    if ($stmt->affected_rows <= 0) {

        throw new Exception(
            "Inventory update failed"
        );
    }

    write_log("INVENTORY_UPDATED", [

        "item_id" => $item_id,
        "deducted_qty" => $from_qty,
        "remaining_qty" => $available_qty - $from_qty
    ]);

    $stmt->close();

    // =====================================================
    // INSERT / UPDATE SHOP INVENTORY
    // =====================================================
    $sql = "
        INSERT INTO inventory_shop
        (
            item_id,
            shop_name,
            qty,
            modified_by
        )
        VALUES (?, ?, ?,?)

        ON DUPLICATE KEY UPDATE
        qty = qty + VALUES(qty)
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssds",
        $item_id,
        $Shop_Name,
        $from_qty,
        $user_id
    );

    $stmt->execute();

    write_log("SHOP_INVENTORY_UPDATED", [

        "item_id" => $item_id,
        "shop_name" => $Shop_Name,
        "qty_added" => $from_qty,
        "affected_rows" => $stmt->affected_rows
    ]);

    $stmt->close();

    // =====================================================
    // INSERT INVENTORY TRANSACTION
    // =====================================================
      $trans_id = uniqid("TRN");
   // $trans_id = "From Inventory";
    $sql = "
        INSERT INTO inventory_trans
        (
            trans_id,
            item_id,
            shop_id,
            qty,
            trans_type,
            created_by,
            remarks
        )
        VALUES (?, ?, ?, ?, ?, ?,?)
    ";

    $stmt = $conn->prepare($sql);

    $trans_type = "TRANSFER";
    $remarks =
    "Transferred from Inventory table to " .
    $Shop_Name .
    " shop inventory";

    $stmt->bind_param(
        "sssdsss",
        $trans_id,
        $item_id,
        $Shop_Name,
        $from_qty,
        $trans_type,
        $user_id,
        $remarks
    );

    $stmt->execute();

    write_log("INVENTORY_TRANSACTION_INSERTED", [

        "trans_id" => $trans_id,
        "item_id" => $item_id,
        "shop_id" => $to_shop,
        "qty" => $from_qty,
        "trans_type" => $trans_type,
        "created_by" => $user_id
    ]);

    $stmt->close();

    // =====================================================
    // COMMIT
    // =====================================================
    $conn->commit();

    write_log("TRANSFER_SUCCESS", [

        "trans_id" => $trans_id,
        "item_id" => $item_id,
        "to_shop" => $to_shop,
        "qty" => $from_qty
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Transfer successful",
        "trans_id" => $trans_id
    ]);

} catch (Exception $e) {

    // =====================================================
    // ROLLBACK
    // =====================================================
    if (isset($conn)) {

        try {

            $conn->rollback();

            write_log("TRANSACTION_ROLLBACK", [

                "reason" => $e->getMessage()
            ]);

        } catch (Exception $rollbackError) {

            write_log("ROLLBACK_FAILED", [

                "error" => $rollbackError->getMessage()
            ]);
        }
    }

    // =====================================================
    // ERROR LOG
    // =====================================================
    write_log("TRANSFER_ERROR", [

        "item_id" => $item_id ?? '',
        "to_shop" => $to_shop ?? '',
        "qty" => $from_qty ?? '',

        "error" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine(),
        "trace" => $e->getTraceAsString()
    ]);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

// =====================================================
// CLOSE CONNECTION
// =====================================================
if (isset($conn)) {
    $conn->close();
}

?>