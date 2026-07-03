<?php

date_default_timezone_set('Asia/Kolkata');

include_once "db_config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$logFile = __DIR__ . "/logs/customer_opening_balance.log";

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

    writeLog("========================================");
    writeLog("Customer Opening Balance Update Started");

    $conn->query("CALL update_customer_opening_balance()");

    while ($conn->more_results()) {
        $conn->next_result();
    }

    writeLog("Customer Opening Balance Updated Successfully");

    echo "SUCCESS";

} catch (Exception $e) {

    writeLog("ERROR : " . $e->getMessage());

    echo "ERROR : " . $e->getMessage();
}

$conn->close();

?>