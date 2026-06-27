<?php
/**
 * item_check_name.php
 * Returns "1" if the name already exists (warning only — not a blocker).
 * Called via AJAX from item_create.php
 */
include 'db_config.php';

$name    = mysqli_real_escape_string($conn, trim($_GET['item_name'] ?? ''));
$item_id = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;

if ($name === '') { echo '0'; exit; }

$sql = "SELECT item_id FROM item WHERE UPPER(item_name) = UPPER('$name')";
if ($item_id > 0) {
    $sql .= " AND item_id <> $item_id";
}

$r = $conn->query($sql);
echo ($r && $r->num_rows > 0) ? '1' : '0';
?>
