<?php
session_start();
include "db_config.php";

error_reporting(E_ALL);
ini_set('log_errors', 1);
header('Content-Type: application/json');

/* =========================
   INPUT VALIDATION
========================= */
$trans_id = trim($_POST["trans_id"] ?? '');

if ($trans_id === '') {
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
    exit;
}

$shop_id = $_SESSION["shop_id"] ?? null;
$user_id = $_SESSION["user_id"] ?? null;

if (!$shop_id || !$user_id) {
    echo json_encode([
        "status" => "error",
        "message" => "Session expired. Please login again."
    ]);
    exit;
}
try {

    $conn->begin_transaction();

    /* =========================
       1️⃣ LOCK & VALIDATE HEADER
    ========================= */
    $lock = $conn->prepare("
        SELECT active_status 
        FROM receipt_trans 
        WHERE trans_id = ? 
        FOR UPDATE
    ");
    if (!$lock)
        throw new Exception($conn->error);

    $lock->bind_param("s", $trans_id);
    $lock->execute();
    $res = $lock->get_result()->fetch_assoc();
    $lock->close();

    if (!$res) {
        throw new Exception("Invalid trans_id");
    }

    if ($res["active_status"] === 'A') {
        throw new Exception("Transaction already posted");
    }

    /* =========================
       2️⃣ CHECK DETAIL EXISTS
    ========================= */
    $chk = $conn->prepare("
        SELECT COUNT(*) as cnt 
        FROM receipt_trans_det 
        WHERE trans_id = ? AND active_status = 'A'
    ");
    if (!$chk)
        throw new Exception($conn->error);

    $chk->bind_param("s", $trans_id);
    $chk->execute();
    $cnt = $chk->get_result()->fetch_assoc()['cnt'];
    $chk->close();

    if ($cnt == 0) {
        throw new Exception("No detail rows found");
    }
    /* =========================
       2.5️⃣ VENDOR CREATION / FETCH
    ========================= */

    $stmt = $conn->prepare("
    SELECT company_name, customer_name, customer_mobile, customer_gst
    FROM receipt_trans
    WHERE trans_id = ?
");
    $stmt->bind_param("s", $trans_id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $company_name = trim($data['company_name']);
    $customer_name = trim($data['customer_name']);
    $customer_mobile = trim($data['customer_mobile']);
    $customer_gst = trim($data['customer_gst']);

    if ($company_name == '' || $customer_mobile == '') {
        throw new Exception("Vendor details missing");
    }

    /* Check vendor */
    $chk = $conn->prepare("
    SELECT vendor_id
    FROM vendor
    WHERE LOWER(company_name)=LOWER(?) AND owner_mobile=?
    LIMIT 1
");
    $chk->bind_param("ss", $company_name, $customer_mobile);
    $chk->execute();
    $res = $chk->get_result();

    if ($row = $res->fetch_assoc()) {

        $vendor_id = $row["vendor_id"];

    } else {

        /* Generate vendor_id */
        $res_id = $conn->query("
        SELECT IFNULL(MAX(vendor_id),1000000)+1 AS vendor_id
        FROM vendor
        FOR UPDATE
    ");
        $row_id = $res_id->fetch_assoc();
        $vendor_id = $row_id["vendor_id"];

        /* Insert vendor */
        $ins = $conn->prepare("
        INSERT INTO vendor (
            vendor_id, company_name, owner_name, owner_mobile, owner_aadhar, created_by,trans_id,trans_creation
        ) VALUES (?, ?, ?, ?, ?,?,?,'PROCUREMENT')
    ");
        $ins->bind_param(
            "ssssssi",
            $vendor_id,
            $company_name,
            $customer_name,
            $customer_mobile,
            $customer_gst,
            $user_id,
            $trans_id
        );
        $ins->execute();
    }


    /* =========================
       3️⃣ REVERSE OLD INVENTORY
    ========================= */
    $sql_rev = "
    UPDATE inventory_shop s
    JOIN (
        SELECT item_id, shop_id, qty
        FROM inventory_trans
        WHERE trans_id = ? AND trans_type = 'IN'
    ) t
        ON s.item_id = t.item_id
       AND s.shop_name = t.shop_id
    SET
        s.qty = s.qty - t.qty,
        s.modified_by = ?
";

    $stmt = $conn->prepare($sql_rev);

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("ss", $trans_id, $user_id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();
    /* =========================
       4️⃣ DELETE OLD inventory_trans
    ========================= */
    $stmt = $conn->prepare("DELETE FROM inventory_trans WHERE trans_id = ?");
    if (!$stmt)
        throw new Exception($conn->error);

    $stmt->bind_param("s", $trans_id);
    $stmt->execute();
    $stmt->close();
    /* =========================
       5️⃣ INSERT INVENTORY (UPSERT)
    ========================= */
    $sql1 = "
    INSERT INTO inventory_shop
        (item_id, shop_name, qty, modified_by)
    SELECT
        item_id,
        shop_id,
        SUM(qty),
        ?
    FROM receipt_trans_det
    WHERE trans_id = ?
      AND active_status = 'A'
    GROUP BY item_id, shop_id
    ON DUPLICATE KEY UPDATE
        qty = qty + VALUES(qty),
        modified_by = VALUES(modified_by)
";

    $stmt = $conn->prepare($sql1);

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("ss", $user_id, $trans_id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();
    /* =========================
       6️⃣ INSERT inventory_trans
    ========================= */
    $sql2 = "
    INSERT INTO inventory_trans 
    (item_id, shop_id, qty, trans_id, trans_type, created_by)
    SELECT item_id, ?, SUM(qty), ?, 'IN', ?
    FROM receipt_trans_det
    WHERE trans_id = ? AND active_status = 'A'
    GROUP BY item_id
    ON DUPLICATE KEY UPDATE 
        qty = VALUES(qty)
    ";
    $stmt = $conn->prepare($sql2);
    if (!$stmt)
        throw new Exception($conn->error);

    $stmt->bind_param("ssss", $shop_id, $trans_id, $user_id, $trans_id);

    $stmt->execute();
    $stmt->close();

    /* =========================
       7️⃣ UPDATE RECEIPT SUMMARY
    ========================= */
    $sql3 = "
        UPDATE receipt_trans r
        JOIN (
            SELECT 
                trans_id,
                SUM(total) AS gross_total,
                SUM(IFNULL(discount,0)) AS total_discount,
                SUM(IFNULL(roundoff,0)) AS total_roundoff
            FROM receipt_trans_det
            WHERE trans_id = ? AND active_status = 'A'
            GROUP BY trans_id
        ) d ON r.trans_id = d.trans_id
        SET 
            r.vendor        = ?,   
            r.trans_amount = d.gross_total,
            r.discount     = d.total_discount,
            r.roundoff     = d.total_roundoff,
            r.pending      = d.gross_total,
            r.active_status = 'A',
            r.modified_on = NOW(),
            r.modified_by = ?
        WHERE r.trans_id = ?
        and r.active_status = 'D'
    ";

    $stmt = $conn->prepare($sql3);
    if (!$stmt)
        throw new Exception($conn->error);

    $stmt->bind_param("ssss", $trans_id, $vendor_id, $user_id, $trans_id);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        throw new Exception("Finalize failed or already finalized");
    }
    if ($stmt->errno) {
        throw new Exception($stmt->error);
    }

    $stmt->close();

    /* =========================
       COMMIT
    ========================= */
    $conn->commit();

    echo json_encode([
        "status" => "success",
        "trans_id" => $trans_id,
        "message" => "Receipt finalized successfully"
    ]);

} catch (Exception $e) {

    $conn->rollback();

    error_log("Finalize Error: " . $e->getMessage());

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>