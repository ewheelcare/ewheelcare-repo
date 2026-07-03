<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // ❗ IMPORTANT: don't print errors in JSON

header('Content-Type: application/json');

try {
    include "db_config.php";

    $module = $_POST["module"];
    $pk = $module . "_id";

    $jsonFile = $module . "_layout.json";

    if (!file_exists($jsonFile)) {
        throw new Exception("Layout file not found");
    }

    $json = file_get_contents($jsonFile);
    $fields = json_decode($json, true);

    if (!$fields) {
        throw new Exception("Invalid JSON layout");
    }

    $selectFields = [];
    $tables = [];
    $joins = [];
    $baseTable = null;

    foreach ($fields as $fieldData) {
        $field = $fieldData['field'];
        $ref = $fieldData['ref'];
        $condition = isset($fieldData['condition']) ? $fieldData['condition'] : null;

        if (!in_array($field, $selectFields)) {
            $selectFields[] = $field;
        }

        if (!$baseTable) {
            $baseTable = $ref;
            $tables[$ref] = 'base';
            continue;
        }

        if ($ref !== $baseTable && $condition) {
            if (!isset($tables[$ref])) {
                $joins[] = "LEFT JOIN $ref ON $condition";
                $tables[$ref] = 'joined';
            }
        }
    }

    $sql = "SELECT $pk, " . implode(", ", $selectFields) . " FROM $baseTable ";

    if (!empty($joins)) {
        $sql .= implode(" ", $joins);
    }

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("SQL Error: " . $conn->error);
    }

    $data = [];

    while ($row = $result->fetch_assoc()) {

        $row['action_button'] =
            '<button class="btn btn-sm btn-secondary badge" onclick="callFunction(' . $row[$pk] . ')">Edit</button>
             <button class="btn btn-sm btn-secondary badge" onclick="delFunction(' . $row[$pk] . ')">Delete</button>';

        if ($module == "customer") {
            $row['action_button'] .=
                '<a class="btn btn-sm btn-secondary badge" href="attach_gst.php?customer_id=' . $row[$pk] . '">Attach GST</a>
                 <a class="btn btn-sm btn-secondary badge" href="attach_address.php?customer_id=' . $row[$pk] . '">Attach Address</a>';
        }

        if ($module == "item") {
            $row['action_button'] .=
                '<a class="btn btn-sm btn-secondary badge" href="attach_subitem.php?item_id=' . $row[$pk] . '">Attach Subitem</a>';
        }

        $data[] = $row;
    }

    echo json_encode($data);

    $conn->close(); // ✅ correct variable

} catch (Exception $e) {

    // ❗ Always return JSON even on error
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
?>