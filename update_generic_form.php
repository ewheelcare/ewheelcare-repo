<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // ❗ prevent breaking output

include "db_config.php";

// ✅ SAFE form_name handling
$form_name = $_POST["form_name"] ?? '';

if (!$form_name) {
    echo "❌ Error: form_name missing";
    exit;
}

// ✅ Get module name
$module = explode("_module", $form_name)[0];

// ✅ Primary key
$pk = $module . "_id";

// ✅ JSON file
$json_file = $form_name . ".json";

if (!file_exists($json_file)) {
    echo "❌ Error: JSON file not found";
    exit;
}

// Load config
$json = file_get_contents($json_file);
$data = json_decode($json, true);

if (!$data) {
    echo "❌ Error: Invalid JSON";
    exit;
}

// Check if UPDATE or INSERT
$id = $_POST[$pk] ?? '';

// Prepare arrays
$json_list = [];
$value_list = [];

foreach ($data as $entry) {

    $table_name = explode(".", $entry["Save_to"])[0];
    $column = explode(".", $entry["Save_to"])[1];

    $json_list[$table_name] = ($json_list[$table_name] ?? "") . "," . $column;

    $actual_value = $_POST[$column] ?? '';

    // Date format fix
    if ($column == "date" && !empty($actual_value)) {
        $actual_value = date("Y-m-d", strtotime($actual_value));
    }

    $actual_value = mysqli_real_escape_string($conn, $actual_value);

    $value_list[$table_name] = ($value_list[$table_name] ?? "") . ",'" . $actual_value . "'";
}

// Execute queries
foreach ($json_list as $table => $columns) {

    $col_list = explode(",", substr($columns, 1));
    $val_list = explode(",", substr($value_list[$table], 1));

    // ✅ UPDATE
    if (!empty($id)) {

        $sql = "UPDATE $table SET ";

        for ($i = 0; $i < count($col_list); $i++) {
            $sql .= $col_list[$i] . "=" . $val_list[$i] . ",";
        }

        $sql = rtrim($sql, ",");
        $sql .= " WHERE $pk='" . mysqli_real_escape_string($conn, $id) . "'";
    } 
    // ✅ INSERT
    else {

        $sql = "INSERT INTO $table (" . implode(",", $col_list) . ") VALUES (" . implode(",", $val_list) . ")";
    }

    // DEBUG (optional)
    // echo $sql; exit;

    if (!$conn->query($sql)) {
        echo "❌ SQL ERROR: " . $conn->error;
        echo "<br>QUERY: " . $sql;
        exit;
    }

    if (empty($id)) {
        $id = $conn->insert_id;
    }
}

echo "✅ Saved successfully. ID: " . $id;
?>