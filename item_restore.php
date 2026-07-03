<?php

include 'db_config.php';

$item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

if ($item_id === 0) { echo 'Invalid item.'; exit; }

$conn->begin_transaction();

try {

    // Collect sub item IDs linked to this parent
    $sub_result = $conn->query(
        "SELECT item_id FROM groupassociation
         WHERE itemgroup_id = $item_id AND item_id <> itemgroup_id"
    );

    $sub_ids = [];
    while ($sub_result && $row = $sub_result->fetch_assoc()) {
        $sub_ids[] = intval($row['item_id']);
    }

    // Restore all sub items
    if (count($sub_ids) > 0) {
        $ids_csv = implode(',', $sub_ids);
        $conn->query("UPDATE item SET status = 'A' WHERE item_id IN ($ids_csv)");
    }

    // Restore parent
    $conn->query("UPDATE item SET status = 'A' WHERE item_id = $item_id");

    $conn->commit();
    echo 'Item restored to Active successfully.';

} catch (Exception $e) {
    $conn->rollback();
    echo 'Restore failed: ' . $e->getMessage();
}
?>