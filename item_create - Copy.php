<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
include "db_config.php";

$item_id = $_GET['item_id'] ?? '';

$item=[
'item_name'=>'','item_description'=>'','cost'=>'','tax_pc'=>'','tax_pc_sgst'=>'',
'hsn'=>'','tyre_type_name'=>'','price_edit'=>'',
'purchase_tax_cgst'=>'','purchase_tax_igst'=>''
];

$mappings=[];

if($item_id!=''){
    $r=$conn->query("SELECT * FROM item WHERE item_id='".intval($item_id)."'");
    if($r && $r->num_rows){ $item=$r->fetch_assoc(); }

    $r2=$conn->query("SELECT * FROM groupassociation WHERE itemgroup_id='".intval($item_id)."'");
    while($r2 && $m=$r2->fetch_assoc()){ $mappings[]=$m; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "header_include.php"; ?>

<style>
label{
    color:#000 !important;
    font-weight:600;
}
</style>
</head>
<body id="page-top">
<div id="wrapper">
<?php include "sidemenu.php"; ?>
<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<?php include "topmenu.php"; ?>

<div class="container-fluid">
<div class="alert alert-secondary text-center font-weight-bold">
<?= $item_id ? 'EDIT ITEM' : 'CREATE ITEM' ?>
</div>

<div class="card shadow mb-4">
<div class="card-body">

<input type="hidden" id="item_id" value="<?= $item_id ?>">

<div class="row">

<div class="col-md-6 mb-3">
<label>Name</label>
<input type="text" class="form-control" id="item_name" value="<?= htmlspecialchars($item['item_name']) ?>">
</div>

<div class="col-md-6 mb-3">
<label>Description</label>
<input type="text" class="form-control" id="item_description" value="<?= htmlspecialchars($item['item_description']) ?>">
</div>

<div class="col-md-6 mb-3">
<label>Cost</label>
<input type="text" class="form-control" id="cost" value="<?= htmlspecialchars($item['cost']) ?>">
</div>

<div class="col-md-6 mb-3">
<label>Tax CGST (%)</label>
<input type="text" class="form-control" id="tax_pc" value="<?= htmlspecialchars($item['tax_pc']) ?>">
</div>

<div class="col-md-6 mb-3">
<label>Tax SGST (%)</label>
<input type="text" class="form-control" id="tax_pc_sgst" value="<?= htmlspecialchars($item['tax_pc_sgst']) ?>">
</div>

<div class="col-md-6 mb-3">
<label>HSN</label>
<input type="text" class="form-control" id="hsn" value="<?= htmlspecialchars($item['hsn']) ?>">
</div>

<div class="col-md-6 mb-3">
<label>Tyre</label>
<select class="form-control" id="tyre_type_name">
<option value="">Select</option>
<?php
$t=$conn->query("SELECT tyre_type_name FROM tyre_type ORDER BY tyre_type_name");
while($row=$t->fetch_assoc()){
$s=($item['tyre_type_name']==$row['tyre_type_name'])?'selected':'';
echo "<option value='".htmlspecialchars($row['tyre_type_name'])."' $s>".htmlspecialchars($row['tyre_type_name'])."</option>";
}
?>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Price Edit</label>
<input type="text" class="form-control" id="price_edit" value="<?= htmlspecialchars($item['price_edit']) ?>">
</div>

<div class="col-md-6 mb-3">
<label>Purchase Tax CGST (%)</label>
<input type="text" class="form-control" id="purchase_tax_cgst" value="<?= htmlspecialchars($item['purchase_tax_cgst']) ?>">
</div>

<div class="col-md-6 mb-3">
<label>Purchase Tax IGST (%)</label>
<input type="text" class="form-control" id="purchase_tax_igst" value="<?= htmlspecialchars($item['purchase_tax_igst']) ?>">
</div>

</div>

<hr>

<label><input type="checkbox" id="has_mapping" <?= count($mappings)?'checked':'' ?>> Want to map sub items?</label>

<div id="mappingBlock" style="<?= count($mappings)?'':'display:none' ?>">
<hr>
<button type="button" class="btn btn-primary btn-sm" id="addRow">Add Row</button>

<table class="table table-bordered mt-2" id="mappingTable">
<thead><tr><th>Sub Item</th><th>%</th><th>#</th></tr></thead>
<tbody>
<?php foreach($mappings as $m){ ?>
<tr>
<td>
<select class="form-control subitem">
<option value="">Select</option>
<?php
$r=$conn->query("SELECT item_id,item_name FROM item ORDER BY item_name");
while($i=$r->fetch_assoc()){
$s=($i['item_id']==$m['item_id'])?'selected':'';
echo "<option value='{$i['item_id']}' $s>".htmlspecialchars($i['item_name'])."</option>";
}
?>
</select>
</td>
<td><input type="number" class="form-control perc" value="<?= $m['perc'] ?>"></td>
<td><button type="button" class="btn btn-danger removeRow">X</button></td>
</tr>
<?php } ?>
</tbody>
</table>

<div class="alert alert-light border">Total Percentage : <strong id="totalPerc">0</strong>%</div>
</div>

<div class="col-md-6 mb-3">
<button type="button" id="saveItem" class="btn btn-success">Save</button>
<!-- <a href="module.php?param=item" class="btn btn-secondary">Back</a> -->
</div>

</div></div></div>

<?php include "footer.php"; ?>
</div></div>
<?php include "footer_include.php"; ?>

<script src="js/jquery-3.5.1.min.js"></script>
<script>
let optionHtml=`<?php
$x=$conn->query("SELECT item_id,item_name FROM item ORDER BY item_name");
while($i=$x->fetch_assoc()){
echo "<option value=\"{$i['item_id']}\">".htmlspecialchars($i['item_name'])."</option>";
}
?>`;

$('#has_mapping').change(function(){ $('#mappingBlock').toggle(this.checked); });

$('#addRow').click(function(){
$('#mappingTable tbody').append('<tr><td><select class="form-control subitem"><option value="">Select</option>'+optionHtml+'</select></td><td><input type="number" class="form-control perc" value="0"></td><td><button type="button" class="btn btn-danger removeRow">X</button></td></tr>');
});

$(document).on('click','.removeRow',function(){ $(this).closest('tr').remove(); calc(); });
$(document).on('keyup change','.perc',calc);

function calc(){
let t=0;
$('.perc').each(function(){ t+=parseFloat($(this).val())||0; });
$('#totalPerc').html(t);
return t;
}

function hasDuplicateItems(){
let arr=[];
let dup=false;
$('.subitem').each(function(){
 let v=$(this).val();
 if(v=='') return;
 if(arr.includes(v)){ dup=true; return false; }
 arr.push(v);
});
return dup;
}

calc();

$('#saveItem').click(function(){

if($('#item_name').val().trim()==''){
 alert('Item Name required');
 return;
}

let mappings=[];

if($('#has_mapping').is(':checked')){

 if(calc()!=100){
   alert('Total percentage must be 100');
   return;
 }

 if(hasDuplicateItems()){
   alert('Duplicate sub items not allowed');
   return;
 }

 let parent=$('#item_name').val().trim().toUpperCase();

 let selfMap=false;

 $('#mappingTable tbody tr').each(function(){
   let txt=$(this).find('.subitem option:selected').text().trim().toUpperCase();
   if(txt==parent){ selfMap=true; }
 });

 if(selfMap){
   alert('Item cannot map to itself');
   return;
 }

 $('#mappingTable tbody tr').each(function(){
   mappings.push({
      item_id:$(this).find('.subitem').val(),
      perc:$(this).find('.perc').val()
   });
 });
}

$.post('item_save.php',{
item_id:$('#item_id').val(),
item_name:$('#item_name').val(),
item_description:$('#item_description').val(),
cost:$('#cost').val(),
tax_pc:$('#tax_pc').val(),
tax_pc_sgst:$('#tax_pc_sgst').val(),
hsn:$('#hsn').val(),
tyre_type_name:$('#tyre_type_name').val(),
price_edit:$('#price_edit').val(),
purchase_tax_cgst:$('#purchase_tax_cgst').val(),
purchase_tax_igst:$('#purchase_tax_igst').val(),
mappings:JSON.stringify(mappings)
},function(r){
 alert(r);
 if(r.indexOf('Successfully')>-1){
   location='module.php?param=item';
 }
});
});
</script>
</body>
</html>
