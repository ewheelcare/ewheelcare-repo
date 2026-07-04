<?php
include "db_config.php";

$trans_id = $_POST["trans_id"] ?? "";
$ver = intval($_POST["ver"] ?? 0);

if ($trans_id && $ver > 0) {
    // Check if it is currently unlocked / draft
    $sql = "SELECT active_status FROM sales_trans WHERE trans_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $trans_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row["active_status"] == "D") {
            $prev_ver = $ver - 1;
            // Revert the header to active_status A and previous version
            $sql_revert = "UPDATE sales_trans SET active_status='A', ver=? WHERE trans_id=?";
            $stmt_rev = $conn->prepare($sql_revert);
            $stmt_rev->bind_param("is", $prev_ver, $trans_id);
            $stmt_rev->execute();
            
            // Delete the draft rows from sales_trans_det
            $sql_clean = "DELETE FROM sales_trans_det WHERE trans_id=? AND ver=?";
            $stmt_cln = $conn->prepare($sql_clean);
            $stmt_cln->bind_param("si", $trans_id, $ver);
            $stmt_cln->execute();
        }
    }
}
?>
