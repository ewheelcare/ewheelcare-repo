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
					<div class="alert alert-info" style="text-align:center;font-weight:bold">Services [GST : <?php echo $_COOKIE["gst"];?>]</div>
<a  class="btn btn-primary"  href="service_trans_new.php">
  Add New
</a>
 <div class="card-body">
                            <div class="table-responsive">
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="font-size:80%">
                                    <thead class="btn-primary">
                                        <tr>
                                            <th>Trans ID</th>
											 <th>Trans Date</th>
                                            <th>Vehicle No </th>
                                            <th>Company Name</th>
											  <th>Customer Name</th>
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
                                            <th>Vehicle No </th>
                                            <th>Company <br>Name</th>
											  <th>Customer <br>Name</th>
											  <th>Mobile</th>
											  <th>Amount</th>
											<th>Pending</th>
                                            <th></th>
											<th></th>
                                        </tr>
                                    </tfoot>
									<tbody>
									<?php  
$gst = isset($_COOKIE["gst"]) ? $_COOKIE["gst"] : 'N';

$sql = "SELECT trans_id, DATE_FORMAT(trans_date, '%d-%m-%Y') trans_date,
VEHICLE_NO, COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_MOBILE, trans_amount, pending
FROM service_trans 
WHERE ACTIVE_status='A' 
AND IFNULL(NULLIF(gst, ''), 'N')='".$gst."'
ORDER BY trans_id DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<TR>
	<TD><b><?php echo $row["trans_id"];?></b></td>
<td style="font-size:80%"> <b><?php echo $row["trans_date"];?></b>
</TD>
	<TD><b><?php echo $row["VEHICLE_NO"];?></b></td>
<td><b><?php echo $row["COMPANY_NAME"];?></b></TD>
<TD><?php echo $row["CUSTOMER_NAME"];?></TD>
	 <TD><?php echo $row["CUSTOMER_MOBILE"];?></TD>
	 <td><?php echo $row["trans_amount"];?></td>
<td><?php echo $row["pending"];?></td>
<td>
<button class="btn btn-danger btn-sm" onclick="delete_it('<?php echo htmlspecialchars($row['trans_id']); ?>')" style="padding:7px;font-size:80%">X</button>
<!--<button class="btn btn-warning btn-sm"  data-bs-toggle="modal" data-bs-target="#editModal" onclick="edit_it('<?php echo $row["trans_id"];?>','<?php echo $row["trans_date"];?>','<?php echo $row["customer"];?>','<?php echo $row["details"];?>','<?php echo $row["pending"];?>','<?php echo $row["gst"];?>','<?php echo $row["tally"];?>')">Edit</button>-->
	 </td><td><a href="service_trans_new.php?trans_id=<?php echo $row["trans_id"];?>" class="btn btn-sm btn-primary" style="padding:7px;font-size:80%">View</a>
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
	  <tr><td>Customer</td><td><select  class="form-control" id="customer">
<?php  $sql="SELECT customer_id,company_name FROM customer order by 1";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<option value="<?php echo $row['customer_id'];?>"><?php echo $row['company_name'];?></option>
<?php }?>
	  </select></td></tr>
	   <tr><td>Pending</td><td><input type="number"  class="form-control" id="pending"></td></tr>
	    <tr><td>GST</td><td><SELECT  class="form-control" id="gst">
		<option value="">--Select---</option>
		<option value="Y">Yes</option>
		<option value="N">No</option>
		</select></td></tr>
		 <tr><td>Tally update</td><td><input type="text"  class="form-control" id="tally"></td></tr>
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
	  <tr><td>Customer</td><td><select  class="form-control" id="edit_customer">
<?php  $sql="SELECT customer_id,company_name FROM customer order by 1";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<option value="<?php echo $row['customer_id'];?>"><?php echo $row['company_name'];?></option>
<?php }?>
	  </select></td></tr>
	   <tr><td>Pending</td><td><input type="number"  class="form-control" id="edit_pending"></td></tr>
	    <tr><td>GST</td><td><SELECT  class="form-control" id="edit_gst">
		<option value="">--Select---</option>
		<option value="Y">Yes</option>
		<option value="N">No</option>
		</select></td></tr>
		 <tr><td>Tally update</td><td><input type="text"  class="form-control" id="edit_tally"> <input type='HIDDEN'name="trans_id" id="trans_id"></td></tr>
	
	 
	  
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
  let trans_date=$("#datepicker").val();
  let details=$("#details").val();
 let customer=$("#customer").val();
  let pending=$("#pending").val();
   let gst=$("#gst").val();
    let tally=$("#tally").val();
 $.post("add_service.php",
  {
    trans_date: trans_date,
    details: details,
	customer:customer,
	pending:pending,
	gst:gst,
	tally:tally
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });
 
  
});

$('#edit').on('click', function(e) {
   let trans_date=$("#edit_datepicker").val();
  let details=$("#edit_details").val();
 let customer=$("#edit_customer").val();
  let pending=$("#edit_pending").val();
   let gst=$("#edit_gst").val();
    let tally=$("#edit_tally").val();
 let trans_id=$("#trans_id").val();
  $.post("edit_service.php",
  {
    trans_date: trans_date,
    details: details,
	customer:customer,
	pending:pending,
	gst:gst,
	tally:tally,
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
		$.post("delete_service_trans.php",
  {
    trans_id: trans_id
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });
	}
	
}

function edit_it(trans_id,trans_date,customer,details,pending,gst,tally){
	//alert(customer);
	//alert("====");
	$("#trans_id").val(trans_id);
	$("#edit_datepicker").val(trans_date);
	$("#edit_customer").val(customer);
	$("#edit_details").val(details);
	$("#edit_pending").val(pending);
	$("#edit_gst").val(gst);
	$("#edit_tally").val(tally);
	 editModal.show();
	
}
</script>


</script>

  

</body>

</html>