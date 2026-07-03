<?php
error_reporting(0);
ini_set('display_errors', 0);
include "db_config.php";
header('Content-Type: application/json');

$search = isset($_GET['q']) ? $conn->real_escape_string(trim($_GET['q'])) : '';
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = 20;
$offset = ($page - 1) * $limit;
$fetch  = $limit + 1;

try {
    if ($search === '') {
        $sql = "SELECT vendor_id, company_name, owner_name,
                       IFNULL(owner_mobile, '') AS owner_mobile
                FROM vendor
                ORDER BY company_name
                LIMIT $fetch OFFSET $offset";
    } else {
        $sql = "SELECT vendor_id, company_name, owner_name,
                       IFNULL(owner_mobile, '') AS owner_mobile
                FROM vendor
                WHERE company_name LIKE '$search%'
                   OR owner_name   LIKE '$search%'
                   OR owner_mobile LIKE '$search%'
                ORDER BY company_name
                LIMIT $fetch OFFSET $offset";
    }

    $result = $conn->query($sql);
    $items  = [];
    $count  = 0;

    while ($row = $result->fetch_assoc()) {
        $count++;
        if ($count > $limit) break;

        // Calculate pending balance only for matched vendor
        $vid = (int)$row['vendor_id'];
        $pend_sql = "SELECT IFNULL(SUM(pending), 0) AS total_pending FROM receipt_trans WHERE vendor = $vid AND active_status = 'A'";
        $pend_res = $conn->query($pend_sql);
        $pending  = $pend_res ? (float)$pend_res->fetch_assoc()['total_pending'] : 0;

        $label   = $row['company_name'] . ($row['owner_mobile'] ? ' (' . $row['owner_mobile'] . ')' : '');
        $items[] = [
            'id'      => $row['vendor_id'] . '~' . $row['company_name'],
            'text'    => $label,
            'details' => $row['owner_name'],
            'pending' => number_format($pending, 2)
        ];
    }

    echo json_encode([
        'results'    => $items,
        'pagination' => ['more' => $count > $limit]
    ]);

} catch (Exception $e) {
    echo json_encode(['results' => [], 'pagination' => ['more' => false]]);
}
?>
