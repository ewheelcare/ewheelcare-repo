<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
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
					<div class="col-md-12">
					<div class="alert alert-info" style="text-align:center;font-size:80%"><h2>Purchase</h2></div>
<a  class="btn btn-primary" href="receipt_trans_new.php" >
  Add New Invoice
</a>
 <div class="card-body">
                            <div class="table-responsive">
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="font-size:80%">
                                    <thead class="btn-primary">
                                        <tr>
                                          <th>Trans ID</th>
											 <th>Trans Date</th>
                                          
                                            <th>Vendor Company</th>
											  <th>Owner Name</th>
											  <th>Mobile</th>
											  <th>Amount</th>
											<th>Pending</th>
                                            <th></th>
                                            <th></th>
                                           
                                        </tr>
                                    </thead>
                                    <tfoot class="btn-primary">
                                        <tr>
                                             <th>Trans ID</th>
											 <th>Trans Date</th>
                                          
                                            <th>Vendor Company</th>
											  <th>Owner Name</th>
											  <th>Mobile</th>
											  <th>Amount</th>
											<th>Pending</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
									<tbody>
									<?php  $sql="SELECT trans_id,trans_date,details,trans_amount,pending,m.vendor,v.company_name,v.owner_name,v.owner_mobile,active_status FROM receipt_trans m,vendor v where v.vendor_id=m.vendor and m.active_status='A'
order by 1";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<TR>
<TD><b><?php echo $row["trans_id"];?></b>
</td><td>. <b><?php echo $row["trans_date"];?></b>
</TD>
<TD>
,  <b><?php echo $row["company_name"];?></b></TD>
<TD>
,  <b><?php echo $row["owner_name"];?></b></TD>
<TD><?php echo $row["owner_mobile"];?></td>
<td> <?php echo $row["trans_amount"];?></td><td> Pending : <?php echo $row["pending"];?>
</TD><td>
<button class="btn btn-danger btn-sm" onclick="delete_it('<?php echo $row["trans_id"];?>')">X</button></td>
<!--<button class="btn btn-warning btn-sm"  data-bs-toggle="modal" data-bs-target="#editModal" onclick="edit_it('<?php echo $row["trans_id"];?>','<?php echo $row["trans_date"];?>','<?php echo $row["vendor"];?>','<?php echo $row["details"];?>')">Edit</button>
--><td><a href="receipt_trans_new.php?trans_id=<?php echo $row["trans_id"];?>" class="btn btn-sm btn-primary">View</a>
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
     <tr><td>Date of Trnsaction</td><td><input type="text" id="datepicker" class="form-control"></td></tr>
	 <tr><td>Details</td><td><input type="text"  class="form-control" id="details"></td></tr>
	  <tr><td>Vendor</td><td><input  class="form-control" id="vendor" list="vendor_list">
<?php  $sql="SELECT vendor_id,company_name FROM vendor order by 1";
$result = $conn->query($sql);?>
<datalist id="vendor_list">
<?php while ($row = $result->fetch_assoc()) {?>
<option value="<?php echo $row['vendor_id'];?>~<?php echo $row['company_name'];?>"></option>
<?php }?>
	  </datalist></td></tr>
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
     <tr><td>Date of Trnsaction</td><td><input type="text" id="edit_datepicker" class="form-control"></td></tr>
	 <tr><td>Details</td><td><input type="text"  class="form-control" id="edit_details"></td></tr>
	  <tr><td>Vendor</td><td><input  class="form-control" id="edit_vendor" list="edit_vendor_list">
<?php  $sql="SELECT vendor_id,company_name FROM vendor order by 1";
$result = $conn->query($sql);?>
<datalist id="edit_vendor_list">
<?php while ($row = $result->fetch_assoc()) {?>
<option value="<?php echo $row['vendor_id'];?>~<?php echo $row['company_name'];?>"></option>
<?php }?>
	  </datalist>
	  </select>
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
	 $(function () {
    $("#datepicker").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
      }).datepicker("setDate", new Date());;
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
  let trans_date=$("#datepicker").val();
  let details=$("#details").val();
 let vendor=$("#vendor").val().split("~")[0];
 $.post("add_receipt.php",
  {
    trans_date: trans_date,
    details: details,
	vendor:vendor
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });
 
  
});

$('#edit').on('click', function(e) {
  let trans_date=$("#edit_datepicker").val();
  let details=$("#edit_details").val();
 let vendor=$("#edit_vendor").val().split("~")[0];
 let trans_id=$("#trans_id").val();
 $.post("update_receipt.php",
  {
    trans_date: trans_date,
    details: details,
	vendor:vendor,
	trans_id:trans_id
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
function delete_it(trans_id){
	var r = confirm("Are you sure that you want to delete the transaction");
	if(r){
		$.post("delete_receipt.php",
  {
    trans_id: trans_id
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });
	}
	
}

function edit_it(trans_id,trans_date,vendor,details){
	
	//alert("====");
	$("#trans_id").val(trans_id);
	$("#edit_datepicker").val(trans_date);
	
	$("#edit_details").val(details);
	let datalist=document.getElementById("edit_vendor_list");
	const options = datalist.getElementsByTagName('option');

      // Set partial value
      

      // Try to auto-complete
      for (let i = 0; i < options.length; i++) {
        const optionVal = options[i].value.toLowerCase();
        if (optionVal.startsWith(vendor)) {
         // input.value = options[i].value;
		  $("#edit_vendor").val(options[i].value);
          break;
        }
      }
	 editModal.show();
	
}
</script>


</script>

  

</body>

</html>