<?php
include "db_config.php";
$itemgroup_id = $_POST["itemgroup_id"];
//$itemgroup_id="1000002";

$sql = "select i.item_id,i.item_name,i.tax_pc,i.tax_pc_sgst,ia.price_per_cont as perc,i.hsn from item i JOIN item_association ia ON ia.item_id=i.item_id where ia.item_group_id='" . $itemgroup_id . "'";
//echo $sql;
$result_det = $conn->query($sql);
while ($row_det = $result_det->fetch_assoc()) {
    $item_id = $row_det["item_id"];
    $item_name = $row_det["item_name"];
    $tax_pc = $row_det["tax_pc"];
    $tax_pc_sgst = $row_det["tax_pc_sgst"];
    $perc = $row_det["perc"];
    $hsn = $row_det["hsn"];
    $items .= $item_id . "~" . $item_name . "~" . $tax_pc . "~" . $tax_pc_sgst . "~" . $perc . "~" . $hsn . "@";
}
echo $items;
$conn->close();
?>