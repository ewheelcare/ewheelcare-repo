<?php
session_start();

$user_id = $_SESSION["user_id"] ?? '';
$shop    = $_SESSION["shop"] ?? '';

if ($user_id === '' || $shop === '') {
    header("Location: login.php?redirect=" . urlencode($_SERVER["REQUEST_URI"]));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php include "header_include.php";
include "db_config.php";
?>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include "sidemenu.php";?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include "topmenu.php";?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                  
                    <!-- Content Row -->
                    <div class="row">
					
					<?php $trans_id=$_GET["trans_id"];
					$sql="SELECT trans_id,trans_date,details,trans_amount,pending,m.vendor,v.company_name FROM receipt_trans m,vendor v where v.vendor_id=m.vendor and m.active_status='A' and m.trans_id='".$trans_id."' order by 1";
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {?>
<div class="col-md-6 alert alert-warning"><b><?php echo $row["trans_id"];?></b>
<br> Dtd. <b><?php echo $row["trans_date"];?></b>
<br>Amount : <?php echo $row["trans_amount"];?>
</div>
<div class="col-md-6 alert alert-secondary">
<b><?php echo $row["vendor"];?></b>
<br> <b><?php echo $row["company_name"];?></b>
<br><?php echo $row["details"];?>

<br>Pending : <?php echo $row["pending"];?>
</diV>
<?php }?>
<div class="col-md-12">
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal" id="add_opener">
  Add
</button>
<!--<a href="receipt_receipt.php?trans_id=<?php echo $trans_id;?>" class="btn btn-primary" target="_blank">Download Receipt</a>-->
 <div class="card-body">
                            <div class="table-responsive">
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Trans Det</th>
                                            <th>Vendor Det</th>
                                            <th>Details</th>
                                            <th>#</th>
                                           
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                             <th>Trans Det</th>
                                            <th>Vendor Det</th>
                                            <th>Details</th>
                                            <th>#</th>
                                        </tr>
                                    </tfoot>
									<tbody>
									<?php  
									$sql="SELECT trans_id,subtrans_id, i.item_id,i.item_name,i.item_description,d.cost,d.qty,d.tax,d.tax_sgst,d.tax_amount,d.tax_amount_sgst,d.total,(d.tax_amount+d.tax_amount_sgst+d.total) total_amonunt,d.base_price,s.shop_name FROM receipt_trans_det d,item i,shop s where s.shop_id=d.shop_id and i.item_id=d.item_id and d.trans_id='".$trans_id."' and d.acTive_status='A'";
				//echo $sql;
				$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<TR>
<TD><b><?php echo $row["subtrans_id"];?></b>
<br> Dtd. <b><?php echo $row["trans_date"];?></b>
</TD>
<TD><b><?php echo $row["item_id"];?></b>
<br> <b><?php echo $row["item_name"];?></b><br>
<?php echo $row["item_description"];?>,<br>
Shop : <?php echo $row["shop_name"];?>
</TD>
<TD>Qty(<?php echo $row["qty"];?>)*Cost(<?php echo $row["base_price"];?>) = <?php echo $row["qty"]*$row["base_price"];?>
<br>Tax(<?php echo $row["tax"];?>) : <?php echo $row["tax_amount"];?>
<br>Tax, SGST(<?php echo $row["tax_sgst"];?>) : <?php echo $row["tax_amount_sgst"];?>
<br>Total Amount : <?php echo $row["total_amonunt"];?>
</TD><td>
<button class="btn btn-danger btn-xs" onclick="delete_it('<?php echo $row["trans_id"];?>','<?php echo $row["subtrans_id"];?>')">X</button>
<!--<button class="btn btn-warning btn-xs"  data-bs-toggle="modal" data-bs-target="#editModal" onclick="edit_it('<?php echo $row["trans_id"];?>','<?php echo $row["subtrans_id"];?>','<?php echo $row["item_id"];?>~<?php echo $row["cost"];?>~<?php echo $row["tax"];?>~<?php echo $row["tax_sgst"];?>','<?php echo $row["qty"];?>','<?php echo $row["base_price"];?>')">Edit</button>
<a href="distribute_receipt.php?trans_id=<?php echo $trans_id;?>&subtrans_id=<?php echo $row["subtrans_id"];?>&item_id=<?php echo $row["item_id"];?>&item_description=<?php echo $row["item_description"];?>&qty=<?php echo $row["qty"];?>">Distribute</a>
--></td>
</TR>
<?php }?>
									</tbody>
									</table>
									</div>
									</div>
									</div>
                    </div>

                    

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
           <?php include "footer.php";?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
   <?php include "modals.php";?>
   
   
   <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" id="">
      <table class="table table-striped"> 
<tr><td>Item</td><td><input  class="form-control" id="item" list="items" onblur="populate_det()">
<datalist id="items">

<?php  $sql="SELECT item_id,item_name,item_description,cost,tax_pc,tax_pc_sgst FROM item order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['item_id'];?>~<?php echo $row1['cost'];?>~<?php echo $row1['tax_pc'];?>~<?php echo $row1['tax_pc_sgst'];?>~<?php echo $row1['item_name'];?>"></option>
<?php }?>
	  </datalist>
	  <input type="hidden" id="cost">
	   <input type="hidden" id="tax_pc">
	   <input type="hidden" id="tax_pc_sgst">
	    <input type="hidden" id="item_id">
	  </td></tr>
 <tr><td>Base Price</td><td><input type="number" id="base_price" class="form-control" oninput="show_amount()"></td></tr>
		  
     <tr><td>Qty</td><td><input type="number" id="qty" class="form-control" oninput="show_amount()"></td></tr>
	 <tr><td>Amount</td><td><input type="number"  class="form-control" id="amount"></td></tr>
	   <tr><td>Tax</td><td><input type="number"  class="form-control" id="tax" readonly></td></tr>
	      <tr><td>Tax (SGST)</td><td><input type="number"  class="form-control" id="tax_sgst" readonly></td></tr>
	<tr><td>Total</td><td><input type="number"  class="form-control" id="total" readonly></td></tr>
	<tr><td>shop</td><td><select type="text"  class="form-control" id="shop_loc" readonly>
	<?php  $sql="SELECT shop_id,shop_name FROM shop order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['shop_id'];?>"><?php echo $row1['shop_name'];?></option>
<?php }?>
	</select>
	</td></tr>
	
	
	</table>
    </div>
	 <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		 <button type="button" class="btn btn-success" id="add">Save</button>
		
       
      </div>
  </div>
</div>
</div>

 <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" id="">
      <table class="table table-striped"> 
<tr><td>Item</td><td><select  class="form-control" id="edit_item" onchange="populate_det()">
<option value="">--select---</option>
<?php  $sql="SELECT item_id,item_name,item_description,cost,tax_pc,tax_pc_sgst FROM item order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['item_id'];?>~<?php echo $row1['cost'];?>~<?php echo $row1['tax_pc'];?>~<?php echo $row1['tax_pc_sgst'];?>"><?php echo $row1['item_name'];?>(<?php echo  $row1['item_description'];?>,Cost: <?php echo $row1['cost'];?>, Tax (%): <?php echo $row1['tax_pc'];?>)</option>
<?php }?>
	  </select>
	  <input type="hidden" id="edit_cost">
	   <input type="hidden" id="edit_tax_pc">
	   <input type="hidden" id="edit_tax_pc_sgst">
	    <input type="hidden" id="edit_item_id">
	  </td></tr>
<tr><td>Base Price</td><td><input type="number" id="edit_base_price" class="form-control" oninput="show_amount_edit()"> </td></tr>
	 	  
     <tr><td>Qty</td><td><input type="number" id="edit_qty" class="form-control" oninput="show_amount_edit()"><input type="hidden" id="edit_qty_old" class="form-control"> </td></tr>
	 <tr><td>Amount</td><td><input type="number"  class="form-control" id="edit_amount"></td></tr>
	   <tr><td>Tax</td><td><input type="number"  class="form-control" id="edit_tax" readonly></td></tr>
	      <tr><td>Tax (SGST)</td><td><input type="number"  class="form-control" id="edit_tax_sgst" readonly></td></tr>
	<tr><td>Total</td><td><input type="number"  class="form-control" id="edit_total" readonly></td></tr>
	
	</table>
	  <input type='HIDDEN'name="subtrans_id" id="subtrans_id">
	  <input type='HIDDEN'name="trans_id" id="trans_id">
	  </td></tr>
	</table>
    </div>
	 <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		 <button type="button" class="btn btn-success" id="edit">Save</button>
		
       
      </div>
  </div>
</div>
</div>
<?php include "footer_include.php";?>
	<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
	 <script src="js/demo/datatables-demo.js"></script>


<script>
var addModal = new bootstrap.Modal(document.getElementById('addModal'));
var editModal = new bootstrap.Modal(document.getElementById('editModal'));
function show_amount(){
	let cost=$("#base_price").val()==""?0:$("#base_price").val();
	let qty=$("#qty").val()==""?0:$("#qty").val();
	$("#amount").val(cost*qty);
	$("#tax").val(($("#amount").val()*$("#tax_pc").val())/100);
	$("#tax_sgst").val(($("#amount").val()*$("#tax_pc_sgst").val())/100);
	let total=parseFloat($("#amount").val())+parseFloat($("#tax").val())+parseFloat($("#tax_sgst").val());
	$("#total").val(total);
}
function show_amount_edit(){
	let cost=$("#edit_base_price").val()==""?0:$("#edit_base_price").val();
	let qty=$("#edit_qty").val()==""?0:$("#edit_qty").val();
	$("#edit_amount").val(cost*qty);
	$("#edit_tax").val(($("#edit_amount").val()*$("#edit_tax_pc").val())/100);
	$("#edit_tax_sgst").val(($("#edit_amount").val()*$("#edit_tax_pc_sgst").val())/100);
	
	let total=parseFloat($("#edit_amount").val())+parseFloat($("#edit_tax").val())+parseFloat($("#edit_tax_sgst").val());
	$("#edit_total").val(total);
}
function populate_det(){
	let parts = (document.getElementById("item").value).split("~");
	$("#item_id").val(parts[0]);
	$("#cost").val(parts[1]);
	$("#base_price").val(parts[1]);
	$("#tax_pc").val(parts[2]);
	$("#tax_pc_sgst").val(parts[3]);
	//alert(parts[0]+"++"+parts[1]+"===="+parts[2]);
	show_amount();
}
function populate_det_edit(){
	let parts = (document.getElementById("edit_item").value).split("~");
	$("#edit_item_id").val(parts[0]);
	$("#edit_cost").val(parts[1]);
	//$("#edit_base_price").val(parts[1]);
	$("#edit_tax_pc").val(parts[2]);
	$("#edit_tax_pc_sgst").val(parts[3]);
	alert(parts[0]+"++"+parts[1]+"===="+parts[2]+"====="+parts[3]);
	show_amount_edit();
}
	 $(function () {
    $("#datepicker").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
      });
	  $("#edit_datepicker").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
      });
  });
 $('#add_opener').on('click', function() {
      //alert('Button was clicked!');
      // You can run any function here
	 addModal.show();
	/*  let module="<?php echo $module;?>";
	 $.post( "generic_form.php", {module:module})
  .done(function( data ) {
	  $("#add_block").html($.trim(data));
	    addModal.show();

  
  });*/
    });

$('#add').on('click', function(e) {
  let item=$("#item_id").val();
  let cost=$("#cost").val();
 let tax_pc=$("#tax_pc").val();
  let tax_pc_sgst=$("#tax_pc_sgst").val();
 let total=$("#amount").val();
 let tax=$("#tax").val();
  let tax_sgst=$("#tax_sgst").val();
   let base_price=$("#base_price").val();
  let qty=$("#qty").val();
 $.post("add_receipt_det.php",
  {
    item: item,
    cost: cost,
	tax_pc:tax_pc,
	tax_pc_sgst:tax_pc_sgst,
	total:total,
	tax:tax,
	qty:qty,
	tax_sgst:tax_sgst,
	base_price:base_price,
	trans_id:"<?php echo $trans_id;?>",
	shop:$("#shop_loc").val()
	
	
  },
  function(data, status){
    alert($.trim(data));
	console.log(data);
	location.reload();
  });
 
  
});

$('#edit').on('click', function(e) {
    let item=$("#edit_item_id").val();
  let cost=$("#edit_cost").val();
 let tax_pc=$("#edit_tax_pc").val();
  let tax_pc_sgst=$("#edit_tax_pc_sgst").val();
 let total=$("#edit_amount").val();
 let tax=$("#edit_tax").val();
  let qty=$("#edit_qty").val();
  let tax_sgst=$("#edit_tax_sgst").val();
  // let base_price=$("#edit_base_price").val();
   let old_qty=$("#edit_qty").val();
   // let tax_sgst=$("#edit_tax_sgst").val();
   let base_price=$("#edit_base_price").val();
      let subtrans_id=$("#subtrans_id").val();
 $.post("edit_receipt_det.php",
  {
    item: item,
    cost: cost,
	tax_pc:tax_pc,
	tax_pc_sgst:tax_pc_sgst,
	total:total,
	tax:tax,
	qty:qty,
	old_qty:old_qty,
	tax_sgst:tax_sgst,
	base_price:base_price,
	subtrans_id:subtrans_id,
	trans_id:"<?php echo $trans_id;?>"
	
	
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });
 
  
});


/*$(document).ready(function() {
	 let module="<?php echo $module;?>"; 
 $.post( "generic_fetch.php", { module: module })
  .done(function( data ) {
	  let jsonData=(data);
	 if (jsonData.length === 0) {
    $('#myTable').html("<p>No data available</p>");
    return;
  }

  // 1. Generate columns dynamically based on keys of first object
  const columns = Object.keys(jsonData[0]).map(key => ({
    title: key,   // column header
    data: key     // property name to pull from
  }));

  // 2. Initialize DataTable with dynamic columns and data
  $('#myTable').DataTable({
    data: jsonData,
    columns: columns
  });
  });
});*/
function delete_it(trans_id,subtrans_id){
	var r = confirm("Are you sure that you want to delete the transaction");
	if(r){
		$.post("delete_receipt_det.php",
  {
    trans_id: trans_id,
	subtrans_id:subtrans_id
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });
	}
	
}

function edit_it(trans_id,subtrans_id,item,qty,base_price){
	
	//alert("====");
	$("#trans_id").val(trans_id);
	$("#subtrans_id").val(subtrans_id);
	$("#edit_item").val(item);
	$("#edit_qty_old").val(qty);
	
	$("#edit_qty").val(qty);
	$("#edit_base_price").val(base_price);
	populate_det_edit();
	//populate_edit_vehicle_det();
	//populate_det_edit();
	show_amount_edit();
	 editModal.show();
	
}
</script>


</script>

  

</body>

</html>