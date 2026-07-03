<?php
include "db_config.php";
$service_id = $_POST["service_id"];
$vehicle = $_POST["vehicle"];
$cost = $_POST["cost"];
$tax_pc = $_POST["tax_pc"];
$tax_pc_sgst = $_POST["tax_pc_sgst"];
$total = $_POST["total"];
$tax = $_POST["tax"];
$tax_sgst = $_POST["tax_sgst"];
$qty = $_POST["qty"];
$trans_id = $_POST["trans_id"];
$subtrans_id = $_POST["subtrans_id"];


$sql = "update service_trans_det set service_id='" . $service_id . "',vehicle='" . $vehicle . "',cost='" . $cost . "',qty='" . $qty . "',total='" . $total . "',tax='" . $tax_pc . "',tax_amount='" . $tax . "',modified_on=now(),modified_by='" . $_SESSION["user_id"] . "',tax_sgst='" . $tax_pc_sgst . "',tax_amount_sgst='" . $tax_sgst . "'  where trans_id='" . $trans_id . "' and subtrans_id='" . $subtrans_id . "'";
$result = $conn->query($sql);
//echo $sql;
$sql = "SELECT sum(total+tax_amount) GRAND_TOTAL FROM service_trans_det where trans_id='" . $trans_id . "' and active_status='A'";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
  $grand_total = $row["GRAND_TOTAL"];
}
$sql = "update service_trans set trans_amount='" . $grand_total . "'  where trans_id='" . $trans_id . "'";
$conn->query($sql);
echo "Receipt added Successfully";
$conn->close();
?>