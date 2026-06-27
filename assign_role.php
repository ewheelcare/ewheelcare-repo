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
					<?php $user_id=$_GET["user_id"];
					$user_name=$_GET["user_name"];?>
					<h3>Assign Roles to <?php echo $user_name;?> </h3>
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal" id="add_opener">
  Add
</button>
 <div class="card-body">
                            <div class="table-responsive">
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            
											  <th>User ID</th>
                                            <th>Role</th>
                                            
                                            <th>#</th>
                                           
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                             <th>User ID</th>
                                            <th>Role</th>
                                            
                                            <th>#</th>
                                        </tr>
                                    </tfoot>
									<tbody>
									<?php  $sql="SELECT distinct user_id, user_role from expert_login_role where active_status='A' and user_id='".$user_id."'";
									//echo $sql;
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<TR>
<TD><b><?php echo $row["user_id"];?>
</TD>
<TD><b><?php echo $row["user_role"];?></b>
</TD>

</TD><td>
<button class="btn btn-danger btn-sm" onclick="delete_it('<?php echo $row["user_id"];?>','<?php echo $row["user_role"];?>')">X</button>
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
     <tr><td>User ID</td><td><input  type="text" id="user_id" name="user_id" class="form-control" readonly value="<?php echo $user_id;?>"></td></tr>
	  <tr><td>User Password</td><td><select  class="form-control" id="user_role">
<?php  $sql="SELECT role FROM role_master where active_status='A' order by 1";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<option value="<?php echo $row['role'];?>"><?php echo $row['role'];?></option>
<?php }?>
	  </select></td></tr>
	 
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
     <tr><td>User Name</td><td><input type="text" id="edit_user_name" name="edit_user_name" class="form-control"></td></tr>
	 <tr><td>User ID</td><td><input  type="text" id="edit_user_id" name="edit_user_id" class="form-control" readonly></td></tr>
	  <tr><td>User Password</td><td><input  type="text" id="edit_user_pwd" name="edit_user_pwd" class="form-control" readonly></td></tr>
	
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
function generateRandomString(length = 7) {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let result = '';
  for (let i = 0; i < length; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return result;
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
      
	 addModal.show();
	
    });

$('#add').on('click', function(e) {
  let user_id=$("#user_id").val();
  let user_role=$("#user_role").val();
 
 $.post("add_role.php",
  {
    user_id: user_id,
    user_role: user_role
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });
 
  
});

$('#edit').on('click', function(e) {
   let user_id=$("#edit_user_id").val();
  let user_name=$("#edit_user_name").val();
 let user_pwd=$("#edit_user_pwd").val();

  $.post("edit_user.php",
  {
    user_id: user_id,
    user_name: user_name,
	user_pwd:user_pwd
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
function delete_it(user_id,role_id){
	var r = confirm("Are you sure that you want to delete the User");
	if(r){
		$.post("delete_role.php",
  {
    user_id: user_id,
	role_id:role_id
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });
	}
	
}

function edit_it(user_id,user_name,user_pwd){
	//alert(customer);
	//alert("====");
	$("#edit_user_id").val(user_id);
	$("#edit_user_name").val(user_name);
	$("#edit_user_pwd").val(user_pwd);
	 editModal.show();
	
}
</script>


</script>

  

</body>

</html>