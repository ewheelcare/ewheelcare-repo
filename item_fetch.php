<?php
/**
 * item_fetch.php
 * Returns items grouped parent → sub items.
 * Accepts POST param: status = 'A' (active, default) or 'D' (deleted)
 */
include 'db_config.php';
header('Content-Type: application/json');

$status = isset($_POST['status']) && $_POST['status'] === 'D' ? 'D' : 'A';

$rows = [];

// ── Parents: items that appear as itemgroup_id in groupassociation ────────────
$parents = $conn->query(
    "SELECT DISTINCT i.item_id, i.item_name, i.item_description,
            i.cost, i.tax_pc, i.tax_pc_sgst, i.hsn,
            i.tyre_type_name, i.price_edit,
            i.purchase_tax_cgst, i.purchase_tax_igst, i.status
     FROM item i
     WHERE i.status = '$status'
       AND i.item_id IN (
           SELECT DISTINCT itemgroup_id FROM groupassociation
       )
     ORDER BY i.item_name"
);

// ── Standalone items: active, not a sub of anyone, not a parent ───────────────
$standalones = $conn->query(
    "SELECT i.item_id, i.item_name, i.item_description,
            i.cost, i.tax_pc, i.tax_pc_sgst, i.hsn,
            i.tyre_type_name, i.price_edit,
            i.purchase_tax_cgst, i.purchase_tax_igst, i.status
     FROM item i
     WHERE i.status = '$status'
       AND i.item_id NOT IN (
           SELECT DISTINCT item_id FROM groupassociation WHERE item_id <> itemgroup_id
       )
       AND i.item_id NOT IN (
           SELECT DISTINCT itemgroup_id FROM groupassociation
       )
     ORDER BY i.item_name"
);

// ── Process parents ───────────────────────────────────────────────────────────
if ($parents) {
    while ($parent = $parents->fetch_assoc()) {
        $pid = intval($parent['item_id']);

        $rows[] = [
            'item_id'           => $parent['item_id'],
            'item_name'         => $parent['item_name'],
            'item_description'  => $parent['item_description'],
            'cost'              => $parent['cost'],
            'tax_pc'            => $parent['tax_pc'],
            'tax_pc_sgst'       => $parent['tax_pc_sgst'],
            'hsn'               => $parent['hsn'],
            'tyre_type_name'    => $parent['tyre_type_name'],
            'price_edit'        => $parent['price_edit'],
            'purchase_tax_cgst' => $parent['purchase_tax_cgst'],
            'purchase_tax_igst' => $parent['purchase_tax_igst'],
            '_role'             => 'parent',
            '_parent_id'        => null,
            '_status'           => $parent['status']
        ];

        // Sub items for this parent (always fetch active sub items even in deleted view)
        $subs = $conn->query(
            "SELECT i.item_id, i.item_name, i.item_description,
                    i.cost, i.tax_pc, i.tax_pc_sgst, i.hsn,
                    i.tyre_type_name, i.price_edit,
                    i.purchase_tax_cgst, i.purchase_tax_igst, i.status,
                    g.perc
             FROM groupassociation g
             INNER JOIN item i ON g.item_id = i.item_id
             WHERE g.itemgroup_id = $pid
               AND g.item_id <> g.itemgroup_id
             ORDER BY i.item_name"
        );

        while ($subs && $sub = $subs->fetch_assoc()) {
            $rows[] = [
                'item_id'           => $sub['item_id'],
                'item_name'         => $sub['item_name'],
                'item_description'  => $sub['item_description'],
                'cost'              => $sub['cost'],
                'tax_pc'            => $sub['tax_pc'],
                'tax_pc_sgst'       => $sub['tax_pc_sgst'],
                'hsn'               => $sub['hsn'],
                'tyre_type_name'    => $sub['tyre_type_name'],
                'price_edit'        => $sub['price_edit'],
                'purchase_tax_cgst' => $sub['purchase_tax_cgst'],
                'purchase_tax_igst' => $sub['purchase_tax_igst'],
                '_role'             => 'sub',
                '_parent_id'        => $pid,
                '_status'           => $sub['status']
            ];
        }
    }
}

// ── Process standalones ───────────────────────────────────────────────────────
if ($standalones) {
    while ($sa = $standalones->fetch_assoc()) {
        $rows[] = [
            'item_id'           => $sa['item_id'],
            'item_name'         => $sa['item_name'],
            'item_description'  => $sa['item_description'],
            'cost'              => $sa['cost'],
            'tax_pc'            => $sa['tax_pc'],
            'tax_pc_sgst'       => $sa['tax_pc_sgst'],
            'hsn'               => $sa['hsn'],
            'tyre_type_name'    => $sa['tyre_type_name'],
            'price_edit'        => $sa['price_edit'],
            'purchase_tax_cgst' => $sa['purchase_tax_cgst'],
            'purchase_tax_igst' => $sa['purchase_tax_igst'],
            '_role'             => 'parent',
            '_parent_id'        => null,
            '_status'           => $sa['status']
        ];
    }
}

echo json_encode($rows);
?>