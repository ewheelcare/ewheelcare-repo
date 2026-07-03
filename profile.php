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
					<div class="col-md-5 alert alert-secondary">
					<?php $user_id=$_COOKIE["user_id"];
					$user_name=$_COOKIE["user_name"];?>
					<h3> Roles for  <?php echo $user_name;?> (User ID : <?php echo $user_id;?>) </h3>

									<?php  $sql="SELECT distinct user_id, user_role from expert_login_role where active_status='A' and user_id='".$user_id."'";
									//echo $sql;
$result = $conn->query($sql);?>
<UL>
<?PHP while ($row = $result->fetch_assoc()) {?>

<LI><b><?php echo $row["user_role"];?></b>
</LI>
<?php }?>
</UL>									
									</div>
									<div class="col-md-6" style="border:2px solid #4a4a4a;border-radius:3%;margin:5px">
									<center><input class="form-control" name="newpwd" id="newpwd" type="password" placeholder="Enter New Password"><br>
									<input class="form-control" name="repwd" id="repwd" type="password" placeholder="Confirm New Password">
									<br><button class="btn btn-success btn-sm" onclick="change_password()">Confirm</button>
									</center></DIV>
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
function change_password(){
	let pwd=$("#newpwd").val();
	let re_pwd=$("#repwd").val();
	const regex = /^[A-Za-z0-9@#$*_]{8,20}$/;


const isValid = regex.test(pwd);
if(!isValid){
	alert("Password should have numbers, alphabets and must be at least 8 in length");
	return false;
	
}
else{
if(pwd!=re_pwd)	{
alert("Password and re enterede passwors do not match");
	return false;	
	
}else{
	$.post("change_pwd.php",
  {
    pwd:pwd
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });	
	
}
}
}


</script>

  

</body>

</html>