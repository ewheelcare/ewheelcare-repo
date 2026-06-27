<?php
include "db_config.php";
include "db_config.php";
$from_qty = $_POST["from_qty"];
$to_shop = $_POST["to_shop"];
$item_name = $_POST["item_name"];

// 1. Find all Global Inventory IDs and their quantities for this ITEM_NAME
$sql = "SELECT inv.item_id, inv.qty 
        FROM inventory inv 
        JOIN item it ON inv.item_id = it.ITEM_ID 
        WHERE it.ITEM_NAME = ? AND inv.qty > 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $item_name);
$stmt->execute();
$res = $stmt->get_result();

$remaining_to_transfer = $from_qty;
$first_item_id = null;

while ($row = $res->fetch_assoc()) {
    if ($remaining_to_transfer <= 0) break;
    
    if (!$first_item_id) $first_item_id = $row['item_id'];
    
    $take = min($row['qty'], $remaining_to_transfer);
    
    // Subtract from this specific ID
    $upd = $conn->prepare("UPDATE inventory SET qty = qty - ? WHERE item_id = ?");
    $upd->bind_param("ds", $take, $row['item_id']);
    $upd->execute();
    
    $remaining_to_transfer -= $take;
}

// 2. Add to Shop Inventory
if ($first_item_id) {
    // 🔥 FIX: Check if an item with the SAME NAME already exists in this shop to avoid duplicates
    $sql_check = "SELECT si.item_id FROM shop_inventory si 
                  JOIN item it ON si.item_id = it.ITEM_ID 
                  WHERE it.ITEM_NAME = ? AND si.shop_id = ? LIMIT 1";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $item_name, $to_shop);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    
    $target_item_id = $first_item_id;
    if ($row_check = $res_check->fetch_assoc()) {
        $target_item_id = $row_check['item_id'];
    }

    $sloc = $to_shop; // Default sloc
    $sql2 = "INSERT INTO shop_inventory (shop_id, item_id, storagelocation_id, qty) 
             VALUES (?, ?, ?, ?) 
             ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("sssd", $to_shop, $target_item_id, $sloc, $from_qty);

    if ($stmt2->execute()) {
        echo "Transfer Successful";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Error: Item not found in Warehouse.";
}
?>