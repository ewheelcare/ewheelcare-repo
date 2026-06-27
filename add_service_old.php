<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db_config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    // ================= INPUT =================
    $user_id          = $_COOKIE["user_id"];
    $trans_date       = $_POST["trans_date"] ?? '';
    $gst              = $_POST["gst"] ?? '';
    $vehicle_no       = trim($_POST["vehicle_no"] ?? '');
    $vehicle_model    = trim($_POST["vehicle_model"] ?? '');
    $vehicle_make     = trim($_POST["vehicle_make"] ?? '');
    $no_of_wheels     = $_POST["no_of_wheels"] ?? '0';
    $company_name     = trim($_POST["company_name"] ?? '');
    $customer_name    = trim($_POST["customer_name"] ?? '');
    $customer_address = trim($_POST["customer_address"] ?? '');
    $customer_mobile  = trim($_POST["customer_mobile"] ?? '');
    $customer_gst     = trim($_POST["customer_gst"] ?? '');
    $vehicle_odometer = $_POST["vehicle_odometer"] ?? '0';
    $mech             = $_POST["mech"] ?? '0';
    $shop             = $_COOKIE["shop"] ?? '';

    if ($vehicle_no == '') {
        throw new Exception("Vehicle number required");
    }

    if ($trans_date == '') {
        throw new Exception("Transaction date required");
    }

    // ================= START TRANSACTION =================
    $conn->begin_transaction();

    // =====================================================
    // 1. CUSTOMER CHECK / CREATE
    // =====================================================
    $stmt = $conn->prepare("
        SELECT customer_id
        FROM customer
        WHERE company_name=? AND owner_mobile=?
        FOR UPDATE
    ");
    $stmt->bind_param("ss", $company_name, $customer_mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $customer_id = $row["customer_id"];

    } else {

        // lock config row
        $stmt = $conn->prepare("
            SELECT slno
            FROM config
            WHERE item='CUSTOMER'
            FOR UPDATE
        ");
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        $customer_id = $row["slno"] + 1;
        error_log("Customer ID:".$customer_id);
        // update config
        $stmt = $conn->prepare("
            UPDATE config
            SET slno=?
            WHERE item='CUSTOMER'
        ");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();

        // insert customer
        $stmt = $conn->prepare("
            INSERT INTO customer
            (customer_id, company_name, owner_name, owner_mobile)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isss",
            $customer_id,
            $company_name,
            $customer_name,
            $customer_mobile
        );
        $stmt->execute();
        error_log("this is reached".$customer_id);
        // insert gst
        $stmt = $conn->prepare("
            INSERT INTO customer_gst
            (customer_id, gst)
            VALUES (?, ?)
        ");
        $stmt->bind_param("is", $customer_id, $customer_gst);
        $stmt->execute();

        // insert address
        $stmt = $conn->prepare("
            INSERT INTO customer_address
            (customer_id, address)
            VALUES (?, ?)
        ");
        $stmt->bind_param("is", $customer_id, $customer_address);
        $stmt->execute();
    }

    // =====================================================
    // 2. VEHICLE CHECK / CREATE
    // =====================================================
    $stmt = $conn->prepare("
        SELECT vehicle_id
        FROM vehicle
        WHERE vehicle_no=? AND customer_id=?
        FOR UPDATE
    ");
    $stmt->bind_param("si", $vehicle_no, $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $vehicle_id = $row["vehicle_id"];
        
    } else {

        // lock config row
        $stmt = $conn->prepare("
            SELECT slno
            FROM config
            WHERE item='VEHICLE'
            FOR UPDATE
        ");
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        
        $vehicle_id = $row["slno"] + 1;

        // update config
        $stmt = $conn->prepare("
            UPDATE config
            SET slno=?
            WHERE item='VEHICLE'
        ");
        $stmt->bind_param("i", $vehicle_id);
        $stmt->execute();

        // insert vehicle
        $stmt = $conn->prepare("
            INSERT INTO vehicle
            (vehicle_id, vehicle_no, vehicle_model, vehicle_brand, customer_id, vehicle_tyre)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isssis",
            $vehicle_id,
            $vehicle_no,
            $vehicle_model,
            $vehicle_make,
            $customer_id,
            $no_of_wheels
        );
        $stmt->execute();
    }
      error_log("THIS IS VEHICLE REACHED".$vehicle_id);
    // =====================================================
    // 3. SERVICE TRANS ID GENERATION
    // =====================================================
    $stmt = $conn->prepare("
        SELECT part1, part2, fy, slno
        FROM config
        WHERE STR_TO_DATE(?, '%d-%m-%Y')
              BETWEEN from_time AND to_time
        AND item='SERVICE'
        AND gst=?
        FOR UPDATE
    ");
    $stmt->bind_param("ss", $trans_date, $gst);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        throw new Exception("SERVICE config not found");
    }

    $next_no = $row["slno"] + 1;
    error_log("This is trans sl no reached".$next_no);
    $trans_id =
        $row["part1"] .
        $row["fy"] .
        $row["part2"] .
        $next_no;

    // update config
    $stmt = $conn->prepare("
        UPDATE config
        SET slno=?
        WHERE STR_TO_DATE(?, '%d-%m-%Y')
              BETWEEN from_time AND to_time
        AND item='SERVICE'
        AND gst=?
    ");
    $stmt->bind_param("iss", $next_no, $trans_date, $gst);
    $stmt->execute();
         error_log("This is trans sl no reached".$trans_date);
         $trans_date = date('Y-m-d', strtotime($_POST["trans_date"]));
     
         // =====================================================
    // 4. INSERT SERVICE TRANS
    // =====================================================
     $stmt = $conn->prepare("
        INSERT INTO service_trans (
            trans_id, vehicle_no, customer, trans_date, active_status, gst,
            vehicle_model, no_of_wheels, vehicle_odometer, vehicle,
            company_name, customer_name, customer_address,
            customer_gst, customer_mobile, vehicle_make, mech, shop,created_by
        ) VALUES (?, ?, ?, ?, 'D', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)
    ");

    $vehicle_odometer = (int)$vehicle_odometer;

    $stmt->bind_param(
        "sssssssissssssssss",
        $trans_id,
        $vehicle_no,
        $customer_name,
        $trans_date,
        $gst,
        $vehicle_model,
        $no_of_wheels,
        $vehicle_odometer,
        $vehicle_no,
        $company_name,
        $customer_name,
        $customer_address,
        $customer_gst,
        $customer_mobile,
        $vehicle_make,
        $mech,
        $shop,
        $user_id
    );

    $stmt->execute();

   
        error_log("End of the Trans reached is trans sl no reached".$next_no);

    // ================= COMMIT =================
    $conn->commit();

    echo $trans_id;

} catch (Exception $e) {

    if ($conn->errno == 0 || $conn) {
        $conn->rollback();
    }

    echo "Error : " . $e->getMessage();
}

$conn->close();
?>