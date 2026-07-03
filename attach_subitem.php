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
					<?php $item_id=$_GET["item_id"];
					$sql="SELECT item_id,item_name FROM item m where item_id='".$item_id."' order by 1";
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {?>
<div class="col-md-6 alert alert-warning"><b><?php echo $row["item_id"];?></b>
</div>
<div class="col-md-6 alert alert-secondary">
<br> <b><?php echo $row["item_name"];?></b>
</diV>
<?php }?>
					
					<div class="col-md-12">
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal" id="add_opener">
  Add
</button>
 <div class="card-body">
                            <div class="table-responsive">
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Subitem</th>
                                            <th>Perc</th>
                                            <th>#</th>
                                           
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                             <th>Subitem</th>
                                            <th>Perc</th>
                                            
                                            <th>#</th>
                                        </tr>
                                    </tfoot>
									<tbody>
									<?php  $sql="SELECT i.item_name,g.perc,g.item_id  FROM groupassociation g, item i  where itemgroup_id='".$item_id."' and g.item_id=i.item_id";
									//echo $sql;
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<TR>
<TD><b><?php echo $row["item_name"];?></b>
</TD>
<TD><b><?php echo $row["perc"];?></b>
</TD>
<td>
<button class="btn btn-danger btn-sm" onclick="delete_it('<?php echo $row["item_id"];?>','<?php echo $item_id;?>')">X</button>
<button class="btn btn-warning btn-sm" onclick="edit_it('<?php echo $row["item_id"];?>','<?php echo $row["item_name"];?>','<?php echo $row["perc"];?>')">EDIT</button>
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
   <tr><td>Sub item</td><td><input type="text"  class="form-control" id="item_id" list="item_list">
   <datalist id="item_list"><?php $sql_det="SELECT t.item_name,t.item_id FROM item t order by item_name";	
$result_det = $conn->query($sql_det);
while ($row_det = $result_det->fetch_assoc()) {?>
<option value="<?php echo $row_det["item_name"];?>~<?php echo $row_det["item_id"];?>"></option>
<?php }?>	
   </datalist>
   </td></tr>
 <tr><td>Perc</td><td><input type="text"  class="form-control" id="perc"></td></tr>
	 
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
   <tr><td>Sub item</td><td><input type="text"  class="form-control" id="edit_item_id" list="edit_item_list">
   <datalist id="edit_item_list"><?php $sql_det="SELECT t.item_name,t.item_id FROM item t order by item_name";	
$result_det = $conn->query($sql_det);
while ($row_det = $result_det->fetch_assoc()) {?>
<option value="<?php echo $row_det["item_name"];?>~<?php echo $row_det["item_id"];?>"></option>
<?php }?>	
   </datalist>
   </td></tr>
 <tr><td>Perc</td><td><input type="text"  class="form-control" id="edit_perc"></td></tr>
	 
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
  let item_id=$("#item_id").val().split("~")[1];
  let perc=$("#perc").val();
 let itemgroup_id="<?php echo $item_id;?>";
 $.post("add_subitem.php",
  {
    item_id: item_id,
	perc:perc,
    itemgroup_id: itemgroup_id
  },
  function(data, status){
    alert($.trim(data));
	console.log(data);
	location.reload();
  });
 
  
});

$('#edit').on('click', function(e) {
  let item_id=$("#edit_item_id").val().split("~")[1];
  let perc=$("#edit_perc").val();
 let itemgroup_id="<?php echo $item_id;?>";
 $.post("edit_subitem.php",
  {
    item_id: item_id,
	perc:perc,
    itemgroup_id: itemgroup_id
  },
  function(data, status){
    alert($.trim(data));
	//console.log(data);
	location.reload();
  });
 
  
});

function delete_it(item_id,itemgroup_id){
	var r = confirm("Are you sure that you want to delete the subitem");
	if(r){
		$.post("delete_subitem.php",
  {
    item_id: item_id,
	itemgroup_id:itemgroup_id
  },
  function(data, status){
    alert($.trim(data));
	location.reload();
  });
	}
	
}
function edit_it(item_id,item_name,item_perc){
	$("#edit_item_id").val(item_name+"~"+item_id);
	$("#edit_perc").val(item_perc);
	 editModal.show();
	
}
</script>


</script>

  

</body>

</html>