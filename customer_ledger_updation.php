<?php
/*********************************************************************
 * File : update_customer_ledger.php
 * Purpose : Update Customer Ledger (Sales + Service)
 * Schedule : Run daily using Hostinger Cron Job
 *********************************************************************/

date_default_timezone_set('Asia/Kolkata');

include_once "db_config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Dedicated log file
$logFile = __DIR__ . "/logs/customer_ledger.log";

function writeLog($message)
{
    global $logFile;

    file_put_contents(
        $logFile,
        date("Y-m-d H:i:s") . " | " . $message . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

try {

    writeLog("==================================================");
    writeLog("Customer Ledger Update Started");

    // Sales Ledger
    $conn->query("CALL update_sales_customer_ledger(CURDATE())");

    while ($conn->more_results()) {
        $conn->next_result();
    }

    writeLog("Sales Ledger Updated Successfully");

    // Service Ledger
    $conn->query("CALL update_service_customer_ledger(CURDATE())");

    while ($conn->more_results()) {
        $conn->next_result();
    }

    writeLog("Service Ledger Updated Successfully");

    writeLog("Customer Ledger Update Completed Successfully");

    echo "SUCCESS";

} catch (Exception $e) {

    writeLog("ERROR : " . $e->getMessage());

    echo "ERROR : " . $e->getMessage();
}

$conn->close();
?>