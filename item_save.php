<?php
include 'db_config.php';

function esc($conn, $val) {
    return mysqli_real_escape_string($conn, trim($val));
}

function isValidNum($val) {
    $v = trim($val);
    return $v !== '' && is_numeric($v) && (float)$v >= 0;
}

function isValidPerc($val) {
    return isValidNum($val) && (float)$val <= 100;
}

$item_id          = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
$has_subitems     = isset($_POST['has_subitems']) ? intval($_POST['has_subitems']) : 0;
$sub_items_raw    = isset($_POST['sub_items']) ? $_POST['sub_items'] : '[]';
$sub_items        = json_decode($sub_items_raw, true);
if (!is_array($sub_items)) { $sub_items = []; }

$item_name          = esc($conn, $_POST['item_name']          ?? '');
$item_description   = esc($conn, $_POST['item_description']   ?? '');
$cost               = esc($conn, $_POST['cost']               ?? '');
$tax_pc             = esc($conn, $_POST['tax_pc']             ?? '');
$tax_pc_sgst        = esc($conn, $_POST['tax_pc_sgst']        ?? '');
$hsn                = esc($conn, $_POST['hsn']                ?? '');
$purchase_tax_cgst  = esc($conn, $_POST['purchase_tax_cgst']  ?? '');
$purchase_tax_igst  = esc($conn, $_POST['purchase_tax_igst']  ?? '');

if ($item_name === '')        { echo 'Item Name is required.';        exit; }
if ($item_description === '') { echo 'Item Description is required.'; exit; }
if ($hsn === '')              { echo 'HSN is required.';              exit; }

if (!isValidNum($cost))             { echo 'Cost must be a valid non-negative number.';         exit; }
if (!isValidPerc($tax_pc))          { echo 'Tax CGST must be a number between 0 and 100.';      exit; }
if (!isValidPerc($tax_pc_sgst))     { echo 'Tax SGST must be a number between 0 and 100.';      exit; }
if (!isValidPerc($purchase_tax_cgst)) { echo 'Purchase Tax CGST must be a number 0–100.';      exit; }
if (!isValidPerc($purchase_tax_igst)) { echo 'Purchase Tax IGST must be a number 0–100.';      exit; }

if ($has_subitems && count($sub_items) > 0) {
    $total_perc = 0;
    foreach ($sub_items as $idx => $si) {
        $n = $idx + 1;
        $si_cost = trim($si['cost'] ?? '');
        $si_perc = trim($si['perc'] ?? '');
        $si_hsn  = trim($si['hsn']  ?? '');

        if (trim($si['item_name'] ?? '') === '') {
            echo "Sub Item $n: Name is required."; exit;
        }
        if ($si_hsn === '') {
            echo "Sub Item $n: HSN is required."; exit;
        }
        if (!isValidNum($si_cost)) {
            echo "Sub Item $n: Cost must be a valid non-negative number."; exit;
        }
        if (!isValidPerc($si_perc)) {
            echo "Sub Item $n: Percentage must be a number between 0 and 100."; exit;
        }
        $total_perc += (float)$si_perc;
    }
    $total_perc = round($total_perc, 4);
    if ($total_perc > 100) {
        echo 'Sub item percentages cannot exceed 100%.'; exit;
    }
}

$conn->begin_transaction();

try {

    if ($item_id === 0) {

        $sql = "INSERT INTO item
                    (item_name, item_description, cost, tax_pc, tax_pc_sgst,
                     hsn, price_edit,
                     purchase_tax_cgst, purchase_tax_igst, status)
                VALUES
                    ('$item_name','$item_description','$cost','$tax_pc','$tax_pc_sgst',
                     '$hsn','Y',
                     '$purchase_tax_cgst','$purchase_tax_igst','A')";
        if (!$conn->query($sql)) {
            throw new Exception('Failed to insert parent item: ' . $conn->error);
        }
        $item_id = $conn->insert_id;

    } else {

        $sql = "UPDATE item SET
                    item_name         = '$item_name',
                    item_description  = '$item_description',
                    cost              = '$cost',
                    tax_pc            = '$tax_pc',
                    tax_pc_sgst       = '$tax_pc_sgst',
                    hsn               = '$hsn',
                    price_edit        = 'Y',
                    purchase_tax_cgst = '$purchase_tax_cgst',
                    purchase_tax_igst = '$purchase_tax_igst'
                WHERE item_id = $item_id";
        if (!$conn->query($sql)) {
            throw new Exception('Failed to update parent item: ' . $conn->error);
        }

        $incoming_ids = [];
        foreach ($sub_items as $si) {
            if (!empty($si['item_id'])) {
                $incoming_ids[] = intval($si['item_id']);
            }
        }

        $prev_result = $conn->query(
            "SELECT item_id FROM item_association
             WHERE item_group_id = $item_id AND item_id <> item_group_id"
        );
        while ($prev_result && $prev_row = $prev_result->fetch_assoc()) {
            $prev_sub_id = intval($prev_row['item_id']);
            if (!in_array($prev_sub_id, $incoming_ids)) {
                $conn->query("UPDATE item SET status = 'D' WHERE item_id = $prev_sub_id");
            }
        }

        $conn->query("DELETE FROM item_association WHERE item_group_id = $item_id");
    }

    $saved_subs = [];

    if ($has_subitems && count($sub_items) > 0) {

        foreach ($sub_items as $si) {
            $si_name        = esc($conn, $si['item_name']        ?? '');
            $si_description = esc($conn, $si['item_description'] ?? '');
            $si_hsn         = esc($conn, $si['hsn']              ?? '');
            $si_cost        = esc($conn, $si['cost']             ?? '0');
            $si_perc        = (float)($si['perc']                ?? 0);
            $si_id          = isset($si['item_id']) ? intval($si['item_id']) : 0;

            if ($si_id === 0) {
                $ins = "INSERT INTO item
                            (item_name, item_description, cost,
                             tax_pc, tax_pc_sgst, hsn,
                             price_edit, purchase_tax_cgst, purchase_tax_igst, status)
                        VALUES
                            ('$si_name','$si_description','$si_cost',
                             '$tax_pc','$tax_pc_sgst','$si_hsn',
                             'Y','$purchase_tax_cgst','$purchase_tax_igst','A')";
                if (!$conn->query($ins)) {
                    throw new Exception('Failed to insert sub item: ' . $conn->error);
                }
                $si_id = $conn->insert_id;

            } else {
                $upd = "UPDATE item SET
                            item_name        = '$si_name',
                            item_description = '$si_description',
                            hsn              = '$si_hsn',
                            cost             = '$si_cost'
                        WHERE item_id = $si_id";
                if (!$conn->query($upd)) {
                    throw new Exception('Failed to update sub item: ' . $conn->error);
                }
            }

            $saved_subs[] = ['item_id' => $si_id, 'perc' => $si_perc];
        }
    }


    // Sub item rows
    $sub_total = 0;
    foreach ($saved_subs as $s) {
        $sub_total += $s['perc'];
        $conn->query(
            "INSERT INTO item_association (item_group_id, item_id, price_per_cont)
             VALUES ($item_id, {$s['item_id']}, {$s['perc']})"
        );
    }

    $parent_perc = round(max(0, 100 - $sub_total), 4);
    $conn->query(
        "INSERT INTO item_association (item_group_id, item_id, price_per_cont)
         VALUES ($item_id, $item_id, $parent_perc)"
    );

    $conn->commit();
    echo 'Item Saved Successfully';

} catch (Exception $e) {
    $conn->rollback();
    echo $e->getMessage();
}
?>