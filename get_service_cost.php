<?php 
include "db_config.php";
$service_id=$_POST["service_id"];
$make=$_POST["make"];
$model=$_POST["model"];
$sql="SELECT cost FROM servicecost s, make mk,model md where s.service_id='".$service_id."' and s.make_id=mk.make_id and s.model_id=md.model_id and mk.make_name='".$make."' and md.model_name='".$model."'";
$result = $conn->query($sql);
$cost=0;
if ($row = $result->fetch_assoc()) {
	$cost=$row["cost"]; 

	}
$response_str =$cost;
echo $response_str;
?>