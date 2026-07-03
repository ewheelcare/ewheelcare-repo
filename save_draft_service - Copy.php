<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include "db_config.php";

if (!isset($_SESSION["user_id"])) {
    $_SESSION["user_id"] = "SYSTEM";
}

try {

    $conn->begin_transaction();

    $trans_id = $_POST["trans_id"];
    $rows     = json_decode($_POST["rows"], true);

    if (!$trans_id || !is_array($rows) || count($rows) == 0) {
        throw new Exception("No service rows found");
    }

    // remove previous rows for this invoice
    $stmt = $conn->prepare("
        DELETE FROM service_trans_det
        WHERE trans_id = ?
    ");
    $stmt->bind_param("s", $trans_id);
    $stmt->execute();

    $subtrans_id = 1000001;
    $grand_total = 0;

    $stmt = $conn->prepare("
        INSERT INTO service_trans_det (
            trans_id,
            subtrans_id,
            service_id,
            vehicle,
            cost,
            qty,
            total,
            tax,
            tax_amount,
            active_status,
            created_on,
            created_by,
            tax_sgst,
            tax_amount_sgst,
            discount
        )
        VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?,
            'A',
            NOW(),
            ?,
            ?, ?, ?
        )
    ");

    foreach ($rows as $r) {

        $price           = (float)$r["price"];
        $qty             = (float)$r["qty"];
        $taxable         = (float)$r["taxable"];
        $tax             = (float)$r["tax"];
        $tax_amount      = (float)$r["tax_amount"];
        $tax_sgst        = (float)$r["tax_sgst"];
        $tax_amount_sgst = (float)$r["tax_amount_sgst"];
        $discount        = (float)$r["discount"];

        $stmt->bind_param(
            "ssssdddidsidd",
            $trans_id,
            $subtrans_id,
            $r["service_id"],
            $r["vehicle"],
            $price,
            $qty,
            $taxable,
            $tax,
            $tax_amount,
            $_SESSION["user_id"],
            $tax_sgst,
            $tax_amount_sgst,
            $discount
        );

        $stmt->execute();

        $grand_total += ($taxable + $tax_amount + $tax_amount_sgst);

        $subtrans_id++;
    }

    // update header totals + final save
    $stmt2 = $conn->prepare("
        UPDATE service_trans
        SET trans_amount = ?,
            pending      = ?,
            active_status = 'A'
        WHERE trans_id = ?
    ");

    $stmt2->bind_param(
        "dds",
        $grand_total,
        $grand_total,
        $trans_id
    );

    $stmt2->execute();

    $conn->commit();

    echo "Saved Successfully";

} catch (Exception $e) {

    $conn->rollback();

    echo "Save Failed : " . $e->getMessage();
}
?>