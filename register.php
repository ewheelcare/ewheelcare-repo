<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
<?php

function mapType($type) {
    switch (strtolower($type)) {
        case 'alphanumeric': return 'VARCHAR(1600)';
        case 'numeric': return 'INT';
		 case 'date': return 'date';
        default: return 'TEXT';
    }
}
?>
<?php
include "db_config.php";
$dbname="expert_tyre";
// Load JSON
$json = file_get_contents('servicecost.json');
var_dump($json);
$data = json_decode($json, true);
var_dump($data);
// Helper to map JSON types to SQL types

$entity = $data['entity'];
$fields = $data['fields'];

// Check if table exists
$tableExists = false;
$result = $conn->query("SHOW TABLES LIKE '$entity'");
if ($result && $result->num_rows > 0) {
    $tableExists = true;
}

if (!$tableExists) {
    // Create table
    $sql = "CREATE TABLE `$entity` (".
       $entity."_id INT AUTO_INCREMENT PRIMARY KEY";

    foreach ($fields as $field) {
        $name = $field['name'];
        $type = mapType($field['type']);
        $sql .= ", `$name` $type";
    }

    $sql .= "\n) ENGINE=InnoDB;";

    if ($conn->query($sql) === TRUE) {
        echo "Table `$entity` created successfully.<br>";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

// Now ensure all fields and foreign keys exist (even if table was just created)
$existingCols = [];
$result = $conn->query("SHOW COLUMNS FROM `$entity`");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $existingCols[$row['Field']] = strtoupper($row['Type']);
    }
}

$alterStmts = [];

foreach ($fields as $field) {
    $name = $field['name'];
    $type = mapType($field['type']);

    if (!array_key_exists($name, $existingCols)) {
        $alterStmts[] = "ADD COLUMN `$name` $type";
    }

    // Foreign key support
    if (!empty($field['refers_to']) && isset($field['refers_to']['entity'], $field['refers_to']['field'])) {
        $fkName = "fk_{$entity}_{$name}";
        $refTable = $field['refers_to']['entity'];
        $refField = $field['refers_to']['field'];

        // Check if FK already exists
        $checkFK = $conn->query("
            SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = '$entity' AND COLUMN_NAME = '$name' 
            AND CONSTRAINT_SCHEMA = '$dbname' AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
       // if ($checkFK->num_rows === 0) {
            $alterStmts[] = "ADD CONSTRAINT `$fkName` FOREIGN KEY (`$name`) REFERENCES `$refTable`(`$refField`)";
       // }
    }
}

// Apply ALTER TABLE if needed
if (!empty($alterStmts)) {
    $alterSQL = "ALTER TABLE `$entity`\n" . implode(",\n", $alterStmts) . ";";
	echo $alterSQL;
    if ($conn->query($alterSQL) === TRUE) {
        echo "Table `$entity` updated with new columns and constraints.<br>";
    } else {
        echo "Error updating table: " . $conn->error;
    }
} else {
    echo "Table `$entity` is already up to date.<br>";
}

$conn->close();
?>
