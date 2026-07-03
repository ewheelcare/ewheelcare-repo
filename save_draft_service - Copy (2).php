<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include "db_config.php";

header('Content-Type: application/json');
$user_id = "";
$shop="";
$user_id = $_SESSION["user_id"] ?? ($_COOKIE["user_id"] ?? "");
$shop    = $_SESSION["shop"] ?? ($_COOKIE["shop"] ?? "");
$user_id = "";
if ($user_id == "" || $shop == "") {

    echo json_encode([
        "success" => false,
        "login_required" => true,
        "message" => "Session expired. Please login again."
    ]);
    exit();
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
            vehicle_no,
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
            $user_id,
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
            active_status = 'A',
            modified_by = ?,
            modified_on = NOW()
        WHERE trans_id = ?
    ");

    $stmt2->bind_param(
        "ddss",
        $grand_total,
        $grand_total,
        $user_id,
        $trans_id
    );

    $stmt2->execute();

    $conn->commit();

    echo json_encode([
         "success" => true,
        "message" => "Saved Successfully"
                    ]);

} catch (Exception $e) {

    $conn->rollback();

   error_log(
   "save_draft_service.php ".
   "TransID=".$trans_id.
   " User=".$user_id.
   " Error=".$e->getMessage()
        );

    echo json_encode([
   "success" => false,
   "message" => "Save Failed"
    ]);
                    }
?>