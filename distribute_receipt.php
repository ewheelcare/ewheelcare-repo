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
					
					<?php 
					$trans_id=$_GET["trans_id"];
					$subtrans_id=$_GET["subtrans_id"];
					$item_id=$_GET["item_id"];
					$item_description=$_GET["item_description"];
					$qty=$_GET["qty"];?>
					
<div class="col-md-6 alert alert-warning"><b><?php echo $trans_id;?></b>
<br> Sub transaction. <b><?php echo $subtrans_id;?></b>
</div>
<div class="col-md-6 alert alert-secondary">
<b><?php echo $item_id;?></b>
<br> <b><?php echo $item_description;?></b>
<br><?php echo $qty;?>

</diV>

<div class="col-md-12">
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal" id="add_opener">
  Add
</button>
<a href="receipt_receipt.php?trans_id=<?php echo $trans_id;?>" class="btn btn-primary" target="_blank">Download Receipt</a>
 <div class="card-body">
                            <div class="table-responsive">
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Location</th>
                                            <th>Quantity</th>
                                            <th>#</th>
                                           
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                             <th>Location</th>
                                            <th>Quantity</th>
                                            <th>#</th>
                                        </tr>
                                    </tfoot>
									<tbody>
									<?php  
									$sql="SELECT t.item_id,t.shop_id,t.storagelocation_id,t.qty,s.shop_name,l.storagelocation_name FROM inventory_trans t, shop s , storagelocation l where s.shop_id=t.shop_id and l.shop_id=s.shop_id and l.storagelocation_id=t.storagelocation_id and trans_id=".$trans_id." and subtrans_id=".$subtrans_id;
				//echo $sql;
				$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<TR>
<TD><b><?php echo $row["storagelocation_name"];?>, <?php echo $row["shop_name"];?></b>
</TD>
<TD><b><?php echo $row["qty"];?></b>
</TD>
<td>
<button class="btn btn-danger btn-xs" onclick="delete_it('<?php echo $trans_id;?>','<?php echo $subtrans_id;?>','<?php echo $row["item_id"];?>','<?php echo $row["qty"];?>','<?php echo $row["shop_id"];?>','<?php echo $row["storagelocation_id"];?>')">X</button>
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
<tr><td>Shop</td><td><select  class="form-control" id="shop" onchange="get_storage_location()">
<option value="">--select---</option>
<?php  $sql="SELECT shop_id,shop_name FROM shop order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['shop_id'];?>"><?php echo $row1['shop_name'];?></option>
<?php }?>
	  </select>
	  </td></tr>
 <tr><td>Storage Location</td><td><select id="storage_location" class="form-control"></select></td></tr>
		  
     <tr><td>Qty</td><td><input type="number" id="qty" class="form-control"></td></tr>
	 
	</table>
    </div>
	 <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		 <button type="button" class="btn btn-success" id="add">Save</button>
		
       
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
function get_storage_location(){
	let shop_id=document.getElementById("shop").value;
let select = document.getElementById("storage_location");
$.post("get_storage_locations.php",
  {
    shop_id:shop_id
	
	
  },
  function(data, status){
	  alert(data);
	  let parts=$.trim(data).split("~");
	  for(var i=0;i<parts.length;i++){
	  const option = document.createElement('option');
  option.value =parts[i].split("#")[0];             // Set the value attribute
  option.textContent = parts[i].split("#")[1]; 

  // Append the option to the select box
	  select.appendChild(option);}
   
  });
 	
	
}
$('#add').on('click', function(e) {
  let item_id="<?php echo $item_id;?>";
  let shop_id=$("#shop").val();
 let storagelocation_id=$("#storage_location").val();
  let trans_id="<?php echo $trans_id;?>";
 let subtrans_id=" <?php echo $subtrans_id;?>";
  let qty=$("#qty").val();
 $.post("update_inventory.php",
  {
    item_id: item_id,
    shop_id: shop_id,
	storagelocation_id:storagelocation_id,
	trans_id:trans_id,
	subtrans_id:subtrans_id,
	
	qty:qty
	
	
  },
  function(data, status){
    alert($.trim(data));
	console.log(data);
	//location.reload();
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
function delete_it(trans_id,subtrans_id,item,qty,shop,storage_location){
	var r = confirm("Are you sure that you want to delete the transaction");
	if(r){
		 $.post("delete_inventory.php",
  {
    item_id: item,
    shop_id: shop,
	storagelocation_id:storage_location,
	trans_id:trans_id,
	subtrans_id:subtrans_id,
	
	qty:qty
	
	
  },
  function(data, status){
    alert($.trim(data));
	console.log(data);
	//location.reload();
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