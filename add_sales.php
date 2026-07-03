<?php
session_start();

include "db_config.php";

ini_set('display_errors', 0);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json');

date_default_timezone_set("Asia/Kolkata");

function getCurrentFinancialYear()
{
    $month = date("n");
    $year  = date("Y");

    if ($month >= 4) {
        $startYear = $year;
        $endYear   = $year + 1;
    } else {
        $startYear = $year - 1;
        $endYear   = $year;
    }

    return $startYear . "-" . substr($endYear, -2);
}

try {

    $conn->begin_transaction();

    /* ==========================
       SESSION VALIDATION
       ========================== */

    $user_id = $_SESSION['user_id'] ?? null;
    $shop_id = $_SESSION['shop'] ?? null;

    if (!$user_id) {
        throw new Exception("Session expired. Please login again.");
    }

    if (!$shop_id) {
        throw new Exception("Shop not found in session.");
    }

    /* ==========================
       INPUTS
       ========================== */

    $trans_date       = trim($_POST["trans_date"] ?? '');
    $customer         = trim($_POST["customer"] ?? '');
    $gst              = trim($_POST["gst"] ?? '');
    $vehicle_no       = trim($_POST["vehicle_no"] ?? '');
    $company_name     = trim($_POST["company_name"] ?? '');
    $customer_name    = trim($_POST["customer_name"] ?? '');
    $customer_address = trim($_POST["customer_address"] ?? '');
    $customer_mobile  = trim($_POST["customer_mobile"] ?? '');
    $customer_gst     = trim($_POST["customer_gst"] ?? '');

    /* ==========================
       VALIDATION
       ========================== */

    if (empty($trans_date)) {
        throw new Exception("Transaction Date is required.");
    }


    if (empty($customer_mobile)) {
        throw new Exception("Customer Mobile is required.");
    }

    /* ==========================
       CUSTOMER CHECK
       ========================== */

    $stmt = $conn->prepare("
        SELECT customer_id
        FROM customer
        WHERE company_name = ?
        AND owner_mobile = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "ss",
        $company_name,
        $customer_mobile
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $customer = $row['customer_id'];

    } else {

        /* ==========================
           CREATE CUSTOMER
           ========================== */

        $stmt = $conn->prepare("
            INSERT INTO customer
            (
                company_name,
                owner_name,
                owner_mobile
            )
            VALUES
            (
                ?, ?, ?
            )
        ");

        $stmt->bind_param(
            "sss",
            $company_name,
            $customer_name,
            $customer_mobile
        );

        $stmt->execute();

        $customer = $conn->insert_id;

        /* GST */

        $stmt = $conn->prepare("
            INSERT INTO customer_gst
            (
                customer_id,
                gst
            )
            VALUES
            (
                ?, ?
            )
        ");

        $stmt->bind_param(
            "is",
            $customer,
            $customer_gst
        );

        $stmt->execute();

        /* ADDRESS */

        $stmt = $conn->prepare("
            INSERT INTO customer_address
            (
                customer_id,
                address
            )
            VALUES
            (
                ?, ?
            )
        ");

        $stmt->bind_param(
            "is",
            $customer,
            $customer_address
        );

        $stmt->execute();
    }

    /* ==========================
       INVOICE NUMBER GENERATION
       ========================== */

    $fy = getCurrentFinancialYear() . "/";

    $stmt = $conn->prepare("
        SELECT *
        FROM config
        WHERE item='SALES'
        AND fy=?
        FOR UPDATE
    ");

    $stmt->bind_param("s", $fy);
    $stmt->execute();

    $result = $stmt->get_result();

    if (!$row_config = $result->fetch_assoc()) {
        throw new Exception("Sales Config Missing.");
    }

    $next_no = $row_config['slno'] + 1;

    $stmt = $conn->prepare("
        UPDATE config
        SET slno=?
        WHERE item='SALES'
        AND fy=?
    ");

    $stmt->bind_param(
        "is",
        $next_no,
        $row_config['fy']
    );

    $stmt->execute();

    $trans_id = $next_no;

    $year_part =
        $row_config["part1"] .
        $row_config["fy"];

    /* ==========================
       INSERT SALES HEADER
       ========================== */

    $stmt = $conn->prepare("
        INSERT INTO sales_trans
        (
            trans_id,
            customer,
            trans_date,
            active_status,
            gst,
            company_name,
            customer_name,
            customer_address,
            customer_gst,
            customer_mobile,
            year_part,
            ver,
            shop,
            created_by,
            created_on
        )
        VALUES
        (
            ?,
            ?,
            STR_TO_DATE(?,'%d-%m-%Y'),
            'D',
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            '0',
            ?,
            ?,
            CURDATE()
        )
    ");

    $stmt->bind_param(
        "ssssssssssss",
        $trans_id,
        $customer,
        $trans_date,
        $gst,
        $company_name,
        $customer_name,
        $customer_address,
        $customer_gst,
        $customer_mobile,
        $year_part,
        $shop_id,
        $user_id
    );

    $stmt->execute();

    /* ==========================
       COMMIT
       ========================== */

    $conn->commit();

    echo json_encode([
    "status"      => "success",
    "trans_id"    => $trans_id,
    "year_part"   => $year_part,
    "invoice_no"  => $year_part . $trans_id,
    "customer_id" => $customer,
    "message"     => "Sales Invoice Created Successfully"
]);
} catch (Exception $e) {

    if ($conn) {
        $conn->rollback();
    }

    error_log(
        "ERP | add_sales.php | " .
        date("Y-m-d H:i:s") .
        " | User:" . ($user_id ?? 'UNKNOWN') .
        " | Company:" . ($company_name ?? '') .
        " | Mobile:" . ($customer_mobile ?? '') .
        " | Error:" . $e->getMessage()
    );

    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>