<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors',0);
ini_set('log_errors',1);

include "db_config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json');
// Check whether user logged in
if (!isset($_SESSION['user_id'])) {

    // redirect to login page
    header("Location: login.php");
    exit();
}


try {

    $payment_id = $_POST["payment_id"] ?? '';
    $account    = $_POST["account"] ?? '';
    $account_to = $_POST["account_to"] ?? '';
    $nature     = $_POST["nature"] ?? '';
    $amount     = floatval($_POST["amount"] ?? 0);
    $mode1      = $_POST["mode"] ?? '';

    if ($payment_id == '') {
        throw new Exception("Payment ID missing");
    }

    // ================= START TRANSACTION =================

    $conn->begin_transaction();

    // ====================================================
    // CHECK PAYMENT STATUS
    // ====================================================

    $stmt = $conn->prepare("
        SELECT active_status
        FROM payments
        WHERE payment_id=?
        FOR UPDATE
    ");

    $stmt->bind_param("s", $payment_id);

    $stmt->execute();

    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if (!$row) {
        throw new Exception("Payment not found");
    }

    if ($row["active_status"] == 'Z') {
        throw new Exception("Payment already deleted");
    }

    // ====================================================
    // MARK PAYMENT DELETED
    // ====================================================

    $stmt = $conn->prepare("
        UPDATE payments
        SET active_status='Z'
        WHERE payment_id=?
    ");

    $stmt->bind_param("s", $payment_id);

    $stmt->execute();

    // ====================================================
    // FETCH TRACK RECORDS
    // ====================================================

    $stmt = $conn->prepare("
        SELECT trans_id, amount_settled, mode
        FROM pay_track
        WHERE payment_id=?
    ");

    $stmt->bind_param("s", $payment_id);

    $stmt->execute();

    $result = $stmt->get_result();

    $is_credit = ($mode1 == "CREDIT");

    while ($row = $result->fetch_assoc()) {

        $amount_settled = floatval($row["amount_settled"]);
        $trans_id       = $row["trans_id"];
        $track_mode     = $row["mode"];

        // ================================================
        // SERVICE TRANS
        // ================================================

        if ($track_mode == "service") {

            if (!$is_credit) {

                $stmt2 = $conn->prepare("
                    UPDATE service_trans
                    SET pending = IFNULL(pending,0) + ?
                    WHERE trans_id=?
                ");

                $stmt2->bind_param(
                    "ds",
                    $amount_settled,
                    $trans_id
                );

                $stmt2->execute();
            }

        } else {

            // ============================================
            // RECEIPT TRANS
            // ============================================

            if (!$is_credit) {

                $stmt2 = $conn->prepare("
                    UPDATE receipt_trans
                    SET pending = IFNULL(pending,0) + ?
                    WHERE trans_id=?
                ");

                $stmt2->bind_param(
                    "ds",
                    $amount_settled,
                    $trans_id
                );

                $stmt2->execute();
            }
        }

        // ================================================
        // DELETE TRACK ENTRY
        // ================================================

        $stmt2 = $conn->prepare("
            DELETE FROM pay_track
            WHERE payment_id=?
            AND trans_id=?
        ");

        $stmt2->bind_param(
            "ss",
            $payment_id,
            $trans_id
        );

        $stmt2->execute();
    }

    // ====================================================
    // ACCOUNT BALANCE REVERSAL
    // ====================================================

    if (
        $nature == "DEBIT" ||
        $nature == "BANKTOCASH" ||
        $nature == "BANKTOBANK"
    ) {

        if (!$is_credit) {

            $stmt = $conn->prepare("
                UPDATE account
                SET balance = IFNULL(balance,0) + ?
                WHERE account_id=?
            ");

            $stmt->bind_param(
                "ds",
                $amount,
                $account
            );

            $stmt->execute();
        }
    }

    // ====================================================
    // CREDIT REVERSAL
    // ====================================================

    if (
        $nature == "CREDIT" ||
        $nature == "CASHTOBANK"
    ) {

        if (!$is_credit) {

            $stmt = $conn->prepare("
                UPDATE account
                SET balance = IFNULL(balance,0) - ?
                WHERE account_id=?
            ");

            $stmt->bind_param(
                "ds",
                $amount,
                $account
            );

            $stmt->execute();
        }
    }

    // ====================================================
    // BANK TO BANK
    // ====================================================

    if ($nature == "BANKTOBANK") {

        if (!$is_credit) {

            $stmt = $conn->prepare("
                UPDATE account
                SET balance = IFNULL(balance,0) - ?
                WHERE account_id=?
            ");

            $stmt->bind_param(
                "ds",
                $amount,
                $account_to
            );

            $stmt->execute();
        }
    }

    // ================= COMMIT =================

    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Deleted Successfully"
    ]);

} catch (Exception $e) {

    $conn->rollback();

    error_log(
        "delete_payment.php Error = " .
        $e->getMessage()
    );

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

$conn->close();

?>