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
        $sql = "SELECT c.customer_id, c.company_name, c.owner_name, c.owner_mobile,
                       GROUP_CONCAT(DISTINCT v.vehicle_no SEPARATOR ', ') AS vehicles
                FROM customer c
                LEFT JOIN vehicle v ON c.customer_id = v.customer_id
                GROUP BY c.customer_id
                ORDER BY c.company_name
                LIMIT $fetch OFFSET $offset";
    } else {
        $sql = "SELECT c.customer_id, c.company_name, c.owner_name, c.owner_mobile,
                       GROUP_CONCAT(DISTINCT v.vehicle_no SEPARATOR ', ') AS vehicles
                FROM customer c
                LEFT JOIN vehicle v ON c.customer_id = v.customer_id
                WHERE c.company_name  LIKE '$search%'
                   OR c.owner_name    LIKE '$search%'
                   OR c.owner_mobile  LIKE '$search%'
                   OR v.vehicle_no    LIKE '$search%'
                GROUP BY c.customer_id
                ORDER BY c.company_name
                LIMIT $fetch OFFSET $offset";
    }

    $result = $conn->query($sql);
    $items  = [];
    $count  = 0;

    while ($row = $result->fetch_assoc()) {
        $count++;
        if ($count > $limit) break;

        $company = trim($row['company_name']);
        $owner   = trim($row['owner_name']);
        $label   = ($company && $owner && $company !== $owner)
                   ? "$company ($owner)"
                   : ($company ?: $owner);

        // Calculate pending balance only for matched customer
        $cid = (int)$row['customer_id'];
        $pend_sql = "SELECT
            (SELECT IFNULL(SUM(pending),0) FROM service_trans WHERE customer=$cid AND active_status='A') +
            (SELECT IFNULL(SUM(pending),0) FROM sales_trans   WHERE customer=$cid AND active_status='A')
            AS total_pending";
        $pend_res = $conn->query($pend_sql);
        $pending  = $pend_res ? (float)$pend_res->fetch_assoc()['total_pending'] : 0;

        $items[] = [
            'id'      => $row['customer_id'] . '~' . $label,
            'text'    => $label,
            'mobile'  => $row['owner_mobile'] ?? '',
            'vehicles'=> $row['vehicles'] ?? '',
            'address' => '',
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
