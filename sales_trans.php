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
					$sql="SELECT trans_id,trans_date,details,trans_amount,pending,m.customer,m.gst FROM sales_trans m,customer v where v.customer_id=m.customer and m.active_status='A' and m.trans_id='".$trans_id."' order by 1";
//echo $sql;
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {$customer= $row["customer"];
$gst=$row["gst"];
?>
<div class="col-md-6 alert alert-warning"><b><?php echo $row["trans_id"];?></b>
<br> Dtd. <b><?php echo $row["trans_date"];?></b>
<br>Amount : <?php echo $row["trans_amount"];?>
</div>
<div class="col-md-6 alert alert-secondary">
<b><?php echo $row["customer"];?></b>
<br> <b><?php echo $row["company_name"];?></b>
<br><?php echo $row["details"];?>

<br>Pending : <?php echo $row["pending"];?>
</diV>
<?php }?>
<div class="col-md-12">
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal" id="add_opener">
  Add
</button>
<a href="sales_receipt1.php?trans_id=<?php echo $trans_id;?>" class="btn btn-primary" target="_blank">Download Receipt</a>
<a href="delivery_receipt1.php?trans_id=<?php echo $trans_id;?>" class="btn btn-primary" target="_blank">Download Work Order</a>

 
 <div class="card-body">
                            <div class="table-responsive">
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Trans Det</th>
                                            <th>Customer Det</th>
											
                                            <th>Details</th>
                                            <th>#</th>
                                           
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                             <th>Trans Det</th>
                                            <th>Customer Det</th>
											
                                            <th>Details</th>
                                            <th>#</th>
                                        </tr>
                                    </tfoot>
									<tbody>
									<?php  $sql="SELECT trans_id,subtrans_id, i.item_id,i.item_name,i.item_description,d.cost,d.qty,d.tax,d.tax_amount,d.tax_amount_sgst,d.tax_sgst,d.total,(d.tax_amount+d.total+d.tax_amount_sgst) full_amount,d.vehicle FROM sales_trans_det d,item i where i.item_id=d.item_id and d.trans_id='".$trans_id."' and d.active_status='A'";
				//echo $sql;
				$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<TR>
<TD><b><?php echo $row["subtrans_id"];?></b>
<br> Dtd. <b><?php echo $row["trans_date"];?></b>
</TD>
<TD><b><?php echo $row["item_id"];?></b>
<br> <b><?php echo $row["item_name"];?></b>
<br><?php echo $row["item_description"];?>
</TD>

<TD>
<br>Amount : Cost : <?php echo $row["cost"];?>* Qty :<?php echo  $row["qty"];?> = <?php echo $row["total"];?><br>
Tax CGST(<?php echo $row["tax"]; ?>): <?php echo $row["tax_amount"]; ?><br>
Tax SGST(<?php echo $row["tax_sgst"]; ?>): <?php echo $row["tax_amount_sgst"]; ?><br>
Total :<?php echo $row["full_amount"]; ?>
</TD><td>
<button class="btn btn-danger btn-xs" onclick="delete_it('<?php echo $row["trans_id"];?>','<?php echo $row["subtrans_id"];?>')">X</button>
<!--<button class="btn btn-warning btn-xs"  data-bs-toggle="modal" data-bs-target="#editModal" onclick="edit_it('<?php echo $row["trans_id"];?>','<?php echo $row["subtrans_id"];?>','<?php echo $row["item_id"];?>~<?php echo $row["cost"];?>~<?php echo $row["tax"];?>~<?php echo $row["tax_sgst"];?>','<?php echo $row["qty"];?>')">Edit</button>-->

</td>
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
	 <tr><td>Vehicle</td><td><select  class="form-control" id="vehicle" >
<option value="">--select---</option>
<?php  $sql="SELECT vehicle_id,vehicle_no,vehicle_model,vehicle_brand FROM vehicle where customer_id='".$customer."' order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['vehicle_id'];?>"><?php echo  $row1['vehicle_no'];?>,Model: <?php echo $row1['vehicle_model'];?>, Brand: <?php echo $row1['vehicle_brand'];?></option>
<?php }?>
	  </select>
	 
	    <input type="hidden" id="vehicle_id">
	  </td></tr>
<tr><td>Item</td><td><select  class="form-control" id="service" onchange="populate_det()">
<option value="">--select---</option>
<?php  $sql="SELECT i.item_id,i.item_name,i.item_description,i.cost,i.tax_pc,i.tax_pc_sgst,iv.qty FROM item i left outer join (select sum(qty) qty,item_id from inventory group by item_id) iv on iv.item_id=i.item_id order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['item_id'];?>~<?php echo $row1['cost'];?>~<?php echo $row1['tax_pc'];?>~<?php echo $row1['tax_pc_sgst'];?>"><?php echo $row1['item_name'];?>(<?php echo  $row1['item_description'];?>,Cost: <?php echo $row1['cost'];?>, Tax (%): <?php echo $row1['tax_pc'];?>)</option>
<?php }?>
	  </select>
	  <input type="hidden" id="cost">
	   <input type="hidden" id="tax_pc">
	      <input type="hidden" id="tax_pc_sgst">
	    <input type="hidden" id="service_id">
	  </td></tr>	  
     <tr><td>Qty</td><td><input type="number" id="qty" class="form-control" oninput="show_amount()"></td></tr>
	 <tr><td>Amount</td><td><input type="number"  class="form-control" id="amount"></td></tr>
	   <tr><td>Tax</td><td><input type="number"  class="form-control" id="tax" readonly></td></tr>
	   <tr><td>Tax (SGST)</td><td><input type="number"  class="form-control" id="tax_sgst" readonly></td></tr>
	<tr><td>Total</td><td><input type="number"  class="form-control" id="total" readonly></td></tr>
	<tr><td>Shop</td><td><select  class="form-control" id="shop_id" readonly></select></td></tr>
	
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
	<tr><td>Vehicle</td><td><select  class="form-control" id="edit_vehicle" >
<option value="">--select---</option>
<?php  $sql="SELECT vehicle_id,vehicle_no,vehicle_model,vehicle_brand FROM vehicle where customer_id='".$customer."' order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['vehicle_id'];?>"><?php echo  $row1['vehicle_no'];?>,Model: <?php echo $row1['vehicle_model'];?>, Brand: <?php echo $row1['vehicle_brand'];?></option>
<?php }?>
	  </select>
	 
	    <input type="hidden" id="vehicle_id">
	  </td></tr> 
<tr><td>Item</td><td><select  class="form-control" id="edit_service" onchange="populate_edit_det()">
<option value="">--select---</option>
<?php  $sql="SELECT service_id,service_name,service_description,cost,tax_pc FROM service order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['service_id'];?>~<?php echo $row1['cost'];?>~<?php echo $row1['tax_pc'];?>"><?php echo $row1['service_name'];?>(<?php echo  $row1['service_description'];?>,Cost: <?php echo $row1['cost'];?>, Tax (%): <?php echo $row1['tax_pc'];?>)</option>
<?php }?>
	  </select>
	  <input type="hidden" id="edit_cost">
	   <input type="hidden" id="edit_tax_pc">
	    <input type="hidden" id="edit_service_id">
	  </td></tr>	  
     <tr><td>Qty</td><td><input type="number" id="edit_qty" class="form-control" oninput="show_edit_amount()"></td></tr>
	 <tr><td>Amount</td><td><input type="number"  class="form-control" id="edit_amount"></td></tr>
	   <tr><td>Tax</td><td><input type="number"  class="form-control" id="edit_tax" readonly></td></tr>
	<tr><td>Total</td><td><input type="number"  class="form-control" id="edit_total" readonly>
	
	
	  <input type='HIDDEN'name="trans_id" id="trans_id">
	   <input type='HIDDEN'name="trans_id" id="subtrans_id">
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
function get_shop(item,qty){
	if(qty=="" || qty==null)qty=0;
 $.post("get_shop.php",
  {
    
    item: item,
	qty:qty
	
	
  },
  function(data, status){
	  let select=document.getElementById("shop_id");
	  select.innerHTML="";
    console.log($.trim(data));
	let parts = $.trim(data).split("#");
	for(var i=0;i<parts.length-1;i++){
		let val=parts[i].split("~")[0];
		let show=parts[i].split("~")[1];
		let newOption = document.createElement("option");
    newOption.value = val;
    newOption.text = show;

    // Append to select
    select.appendChild(newOption);
	}
  });	
	
}
function show_amount(){
	let cost=$("#cost").val()==""?0:$("#cost").val();
	let qty=$("#qty").val()==""?0:$("#qty").val();
	$("#amount").val(cost*qty);
	$("#tax").val(($("#amount").val()*$("#tax_pc").val())/100);
	$("#tax_sgst").val((($("#amount").val()*$("#tax_pc_sgst").val())/100));
	let total=parseFloat($("#amount").val())+parseFloat($("#tax").val())+parseFloat($("#tax_sgst").val());
	$("#total").val(total);
	
	get_shop($("#service_id").val(),$("#qty").val());
}
function show_edit_amount(){
	let cost=$("#edit_cost").val()==""?0:$("#edit_cost").val();
	let qty=$("#edit_qty").val()==""?0:$("#edit_qty").val();
	$("#edit_amount").val(cost*qty);
	$("#edit_tax").val(($("#edit_amount").val()*$("#edit_tax_pc").val())/100);
	let total=parseFloat($("#edit_amount").val())+parseFloat($("#edit_tax").val());
	$("#edit_total").val(total);
}
function populate_det(){
	let gst = "<?php echo $gst;?>";
	let parts = (document.getElementById("service").value).split("~");
	$("#service_id").val(parts[0]);
	$("#cost").val(parts[1]);
	if(gst=="Y")
	{$("#tax_pc").val(parts[2]);
$("#tax_pc_sgst").val(parts[3]);
	}
	else
	{$("#tax_pc").val("0");
$("#tax_pc_sgst").val("0");
}
	//alert(parts[0]+"++"+parts[1]+"===="+parts[2]);
	show_amount();
}
function populate_vehicle_det(){
	let parts = (document.getElementById("vehicle").value).split("~");
	$("#vehicle_id").val(parts[0]);
	
}
function populate_edit_det(){
	let gst = "<?php echo $gst;?>";
	let parts = (document.getElementById("edit_service").value).split("~");
	$("#edit_service_id").val(parts[0]);
	$("#edit_cost").val(parts[1]);
	if(gst=="Y")
	$("#edit_tax_pc").val(parts[2]);
else
	$("#edit_tax_pc").val("0");
	//alert(parts[0]+"++"+parts[1]+"===="+parts[2]);
	show_edit_amount();
}
function populate_edit_vehicle_det(){
	let parts = (document.getElementById("edit_vehicle").value).split("~");
	$("#edit_vehicle_id").val(parts[0]);
	
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
  let item_id=$("#service_id").val();
  let cost=$("#cost").val();
 let tax_pc=$("#tax_pc").val();
 let tax_pc_sgst=$("#tax_pc_sgst").val();
 let total=$("#amount").val();
 let tax=$("#tax").val();
 let tax_sgst=$("#tax_sgst").val();
  let qty=$("#qty").val();
   let shop=$("#shop_id").val();
    let vehicle=$("#vehicle").val();
 $.post("add_sales_det.php",
  {
    
    cost: cost,
	tax_pc:tax_pc,
	total:total,
	tax:tax,
	tax_pc_gst:tax_pc_sgst,
	tax_sgst:tax_sgst,
	qty:qty,
	item_id:item_id,
	shop_id:shop,
	vehicle:vehicle,
	trans_id:"<?php echo $trans_id;?>"
	
	
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });
 
  
});

$('#edit').on('click', function(e) {
  let item=$("#edit_service_id").val();
  let cost=$("#edit_cost").val();
 let tax_pc=$("#edit_tax_pc").val();
 let total=$("#edit_amount").val();
 let tax=$("#edit_tax").val();
  let qty=$("#edit_qty").val();
   let vehicle=$("#edit_vehicle").val();
  let subtrans_id=$("#subtrans_id").val();
 $.post("edit_sales_det.php",
  {
    
    cost: cost,
	tax_pc:tax_pc,
	total:total,
	tax:tax,
	qty:qty,
	item_id:item,
	vehicle:vehicle,
	trans_id:"<?php echo $trans_id;?>",
	subtrans_id:subtrans_id
	
	
  },
  function(data, status){
    //alert($.trim(data));
	console.log(data);
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
		$.post("delete_sales_trans_det.php",
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

function edit_it(trans_id,subtrans_id,service,qty,vehicle){
	
	//alert("====");
	$("#trans_id").val(trans_id);
	$("#subtrans_id").val(subtrans_id);
	$("#edit_service").val(service);
	$("#edit_vehicle").val(vehicle);
	
	$("#edit_qty").val(qty);
	populate_edit_det();
	populate_edit_vehicle_det();
	show_edit_amount();
	 editModal.show();
	
}
</script>


</script>

  

</body>

</html>