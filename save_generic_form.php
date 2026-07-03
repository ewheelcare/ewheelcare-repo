<?php 
function extractKeyFromJson($string, $keyToExtract) {
    // Try to decode
    $decoded = json_decode($string, true);

    // Check for JSON validity
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        // Check if key exists
        if (array_key_exists($keyToExtract, $decoded)) {
            return $decoded[$keyToExtract];
        } else {
            return "Key '$keyToExtract' not found.";
        }
    } else {
        return "Not valid JSON.";
    }
}
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db_config.php";
$pk =explode("_",$_POST["form_name"])[0]."_id";
$form_name=$_POST["form_name"].".json";

$sql = "SELECT ifnull(max(".$pk."),1000001)+1 ".$pk." FROM ".explode("_",$_POST["form_name"])[0]; 
//echo $sql;
 $result = $conn->query($sql);
   if($row = $result->fetch_assoc()) {
	$trans_id=$row[$pk] ;
  }
//echo $trans_id;  
$json = file_get_contents($form_name);
//var_dump($json);
$data = json_decode($json, true);
$json_list=[];
$value_list=[];
foreach ($data as $entry) {

    $table_name = explode(".", $entry["Save_to"])[0];
    $field_name = explode(".", $entry["Save_to"])[1];

    // ✅ skip missing fields
    if (!isset($_POST[$field_name])) {
        continue;
    }

    // ✅ add column ONLY if field exists
    $json_list[$table_name] = ($json_list[$table_name] ?? "") . "," . $entry["Save_to"];

    $original_value = $_POST[$field_name];
$decoded = json_decode($original_value, true);

if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
    
    if (array_key_exists("Save_from", $entry)) {
        $actual_value = $decoded[$entry["Save_from"]] ?? '';
    } else {
        $actual_value = $decoded[$field_name] ?? '';
    }

} else {
    // ✅ normal value (like 1000002 or text)
    $actual_value = $original_value;
}
$value_list[$table_name] = ($value_list[$table_name] ?? "") . ",'" . $actual_value . "'";
}
$json_list1 = json_decode($json_list, true);
foreach ($json_list as $key => $value) {

    // ✅ skip if no values
    if (!isset($value_list[$key]) || empty($value_list[$key])) {
        continue;
    }

    $name_of_table = "insert into " . $key;

    $col_list = "(" . substr($value, 1) . "," . $pk . ") values ";
    $val_list = "(" . substr($value_list[$key], 1) . "," . $trans_id . ")";

    $sql = $name_of_table . $col_list . $val_list;

    // ✅ DEBUG ERROR
    if (!$conn->query($sql)) {
        echo "SQL ERROR: " . $conn->error;
        exit;
    }
}