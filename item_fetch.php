<?php

include 'db_config.php';
header('Content-Type: application/json');

$status = isset($_POST['status']) && $_POST['status'] === 'D' ? 'D' : 'A';

$rows = [];

$parents = $conn->query(
    "SELECT DISTINCT i.item_id, i.item_name, i.item_description,
            i.cost, i.tax_pc, i.tax_pc_sgst, i.hsn,
            i.tyre_type_name, i.price_edit,
            i.purchase_tax_cgst, i.purchase_tax_igst, i.status
     FROM item i
     WHERE i.status = '$status'
       AND i.item_id IN (
           SELECT DISTINCT item_group_id FROM item_association
       )
     ORDER BY i.item_name"
);

$standalones = $conn->query(
    "SELECT i.item_id, i.item_name, i.item_description,
            i.cost, i.tax_pc, i.tax_pc_sgst, i.hsn,
            i.tyre_type_name, i.price_edit,
            i.purchase_tax_cgst, i.purchase_tax_igst, i.status
     FROM item i
     WHERE i.status = '$status'
       AND i.item_id NOT IN (
           SELECT DISTINCT item_id FROM item_association WHERE item_id <> item_group_id
       )
       AND i.item_id NOT IN (
           SELECT DISTINCT item_group_id FROM item_association
       )
     ORDER BY i.item_name"
);

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

        $subs = $conn->query(
            "SELECT i.item_id, i.item_name, i.item_description,
                    i.cost, i.tax_pc, i.tax_pc_sgst, i.hsn,
                    i.tyre_type_name, i.price_edit,
                    i.purchase_tax_cgst, i.purchase_tax_igst, i.status,
                    g.price_per_cont AS perc
             FROM item_association g
             INNER JOIN item i ON g.item_id = i.item_id
             WHERE g.item_group_id = $pid
               AND g.item_id <> g.item_group_id
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