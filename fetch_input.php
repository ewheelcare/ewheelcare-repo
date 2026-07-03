<?php
include "db_config.php";
$dbname="expert_tyre";
$data_src=$_POST["data_src"];
$data_condition=$_POST["data_condition"];
if(isset($data_condition) and $data_condition!="" )
$sql="select * from $data_src where $data_condition";
else
$sql="select * from $data_src";

//echo $sql;	
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>