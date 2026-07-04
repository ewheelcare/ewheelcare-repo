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

$receipt_type = isset($_GET['receipt_type']) ? $_GET['receipt_type'] : '';

if ($receipt_type == 'sales') {
    $pend_subquery = "(SELECT IFNULL(SUM(pending),0) FROM sales_trans WHERE customer=c.customer_id AND active_status='A')";
    $vehicle_select = "GROUP_CONCAT(DISTINCT v.trans_id SEPARATOR ', ') AS vehicles";
    $vehicle_join = "LEFT JOIN sales_trans v ON c.customer_id = v.customer AND v.active_status='A' AND v.pending != 0";
} else if ($receipt_type == 'service') {
    $pend_subquery = "(SELECT IFNULL(SUM(pending),0) FROM service_trans WHERE customer=c.customer_id AND active_status='A')";
    $vehicle_select = "GROUP_CONCAT(DISTINCT v.vehicle_no SEPARATOR ', ') AS vehicles";
    $vehicle_join = "LEFT JOIN vehicle v ON c.customer_id = v.customer_id";
} else {
    $pend_subquery = "((SELECT IFNULL(SUM(pending),0) FROM service_trans WHERE customer=c.customer_id AND active_status='A') + (SELECT IFNULL(SUM(pending),0) FROM sales_trans   WHERE customer=c.customer_id AND active_status='A'))";
    $vehicle_select = "GROUP_CONCAT(DISTINCT v.vehicle_no SEPARATOR ', ') AS vehicles";
    $vehicle_join = "LEFT JOIN vehicle v ON c.customer_id = v.customer_id";
}

try {
    if ($search === '') {
        $sql = "SELECT c.customer_id, c.company_name, c.owner_name, c.owner_mobile,
                       $vehicle_select,
                       $pend_subquery AS total_pending
                FROM customer c
                $vehicle_join
                GROUP BY c.customer_id
                HAVING total_pending != 0
                ORDER BY c.company_name
                LIMIT $fetch OFFSET $offset";
    } else {
        $sql = "SELECT c.customer_id, c.company_name, c.owner_name, c.owner_mobile,
                       $vehicle_select,
                       $pend_subquery AS total_pending
                FROM (
                    SELECT c.customer_id
                    FROM customer c
                    LEFT JOIN vehicle v ON c.customer_id = v.customer_id
                    WHERE c.company_name LIKE '%$search%'
                       OR c.owner_name LIKE '%$search%'
                       OR c.owner_mobile LIKE '%$search%'
                       OR v.vehicle_no LIKE '%$search%'
                    UNION
                    SELECT customer AS customer_id
                    FROM service_trans
                    WHERE (CUSTOMER_NAME LIKE '%$search%' OR trans_id LIKE '%$search%') AND customer > 0
                    UNION
                    SELECT customer AS customer_id
                    FROM sales_trans
                    WHERE (CUSTOMER_NAME LIKE '%$search%' OR trans_id LIKE '%$search%') AND customer > 0
                ) AS matched
                JOIN customer c ON c.customer_id = matched.customer_id
                $vehicle_join
                GROUP BY c.customer_id
                HAVING total_pending != 0
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

        $pending = (float)$row['total_pending'];

        $items[] = [
            'id'      => $row['customer_id'] . '~' . $label,
            'text'    => $label,
            'mobile'  => $row['owner_mobile'] ?? '',
            'vehicles'=> $row['vehicles'] ?? '',
            'address' => '',
            'pending' => number_format($pending, 2),
            'vehicle_label' => ($receipt_type == 'sales') ? 'Invoice No:' : 'Vehicles:'
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
