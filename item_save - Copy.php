<?php
include 'db_config.php';

$item_id=$_POST['item_id'] ?? '';
$item_name=mysqli_real_escape_string($conn,$_POST['item_name']);
$item_description=mysqli_real_escape_string($conn,$_POST['item_description']);
$cost=mysqli_real_escape_string($conn,$_POST['cost']);
$tax_pc=mysqli_real_escape_string($conn,$_POST['tax_pc']);
$tax_pc_sgst=mysqli_real_escape_string($conn,$_POST['tax_pc_sgst']);
$hsn=mysqli_real_escape_string($conn,$_POST['hsn']);
$tyre_type_name=mysqli_real_escape_string($conn,$_POST['tyre_type_name']);
$price_edit=mysqli_real_escape_string($conn,$_POST['price_edit']);
$purchase_tax_cgst=mysqli_real_escape_string($conn,$_POST['purchase_tax_cgst']);
$purchase_tax_igst=mysqli_real_escape_string($conn,$_POST['purchase_tax_igst']);
$mappings=json_decode($_POST['mappings'],true);

$conn->begin_transaction();

try{

$checkSql="SELECT item_id FROM item WHERE UPPER(item_name)=UPPER('$item_name')";
if(!empty($item_id)){
 $checkSql.=" AND item_id<>'".intval($item_id)."'";
}
$checkResult=$conn->query($checkSql);

if($checkResult->num_rows>0){
 throw new Exception('Item already exists');
}

if($item_id==''){

$sql="INSERT INTO item
(item_name,item_description,cost,tax_pc,tax_pc_sgst,hsn,tyre_type_name,price_edit,purchase_tax_cgst,purchase_tax_igst)
VALUES
('$item_name','$item_description','$cost','$tax_pc','$tax_pc_sgst','$hsn','$tyre_type_name','$price_edit','$purchase_tax_cgst','$purchase_tax_igst')";

$conn->query($sql);
$item_id=$conn->insert_id;

}else{

$sql="UPDATE item SET
item_name='$item_name',
item_description='$item_description',
cost='$cost',
tax_pc='$tax_pc',
tax_pc_sgst='$tax_pc_sgst',
hsn='$hsn',
tyre_type_name='$tyre_type_name',
price_edit='$price_edit',
purchase_tax_cgst='$purchase_tax_cgst',
purchase_tax_igst='$purchase_tax_igst'
WHERE item_id='".intval($item_id)."'";

$conn->query($sql);

$conn->query("DELETE FROM groupassociation WHERE itemgroup_id='".intval($item_id)."'");
}

if(is_array($mappings) && count($mappings)>0){

$total=0;
$used=[];

foreach($mappings as $m){
$total+=(float)$m['perc'];

if(in_array($m['item_id'],$used)){
 throw new Exception('Duplicate sub items not allowed');
}
$used[]=$m['item_id'];
}

if($total!=100){
 throw new Exception('Percentage should equal 100');
}

foreach($mappings as $m){
$conn->query("INSERT INTO groupassociation(itemgroup_id,item_id,perc)
VALUES('".$item_id."','".$m['item_id']."','".$m['perc']."')");
}
}

$conn->commit();
echo 'Item Saved Successfully';

}catch(Exception $e){
$conn->rollback();
echo $e->getMessage();
}
?>