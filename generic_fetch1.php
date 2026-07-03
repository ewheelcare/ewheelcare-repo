<?php
include "db_config.php";

$module=$_POST["module"];


// 3. Run your SELECT query
$sql = "SELECT * FROM ".$module;
//echo $sql;
$result =  $conn->query($sql);

// 4. Initialize array
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;  // Each row is an associative array
    }
}

// 5. Convert to JSON
$json = json_encode($data, JSON_PRETTY_PRINT);

// 6. Output (or return it)
header('Content-Type: application/json');
echo $json;

// 7. Close connection
$mysqli->close();
?>
