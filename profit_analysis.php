<!DOCTYPE html>
<html lang="en">

<head>
<?php include "header_include.php";?>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
		 <?php include "db_config.php";?>
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
					<table class="table table-striped">
					<thead>
					<tr>
					
					<td>Item</td>
					<td>Receipt</td>
					<td>Sales</td>
					<td>Profit(%)</td>
					</tr>
					</thead>
					<tbody>	
					<tr style="background-color:#4a4a4a;color:white">
					<td>MRF Tyre , 5mm, Smooth</td>
					<td>2500</td>
					<td>3000</td>
					<td>20%</td>
					</tr>
					<tr>
					<td>Shop @ AgnamPudi</td>
					<td>2000</td>
					<td>2200</td>
					<td>10%</td>
					</tr>
					<tr>
					<td>Shop @ Gajuwaka</td>
					<td>500</td>
					<td>800</td>
					<td>60%</td>
					</tr>
					<tr style="background-color:#4a4a4a;color:white">
					<td>MRF Tyre , 5mm, Rugged</td>
					<td>2000</td>
					<td>2500</td>
					<td>25%</td>
					</tr>
					<tr>
					<td>SLOC1</td>
					<td>1000</td>
					<td>1200</td>
					<td>20%</td>
					</tr>
					<tr>
					<td>SLOC2</td>
					<td>1000</td>
					<td>1300</td>
					<td>30%</td>
					</tr>
					<tbody>
					</table>
                    </div>
  <div class="card shadow mb-12">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Analysis of Profit</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="myBarChart"></canvas>
                                    </div>
                                    <hr>
                                    Styling for the bar chart can be found in the
                                    <code>/js/demo/chart-bar-demo.js</code> file.
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
        <h5 class="modal-title">Reorder to Vendor 1</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" id="">
      <table class="table table-striped">  
     <tr><td>MRF Tyre, 5mm , Rugged</td><td><input type="text" class="form-control" value="230"></td></tr>
	 <tr><td>MRF Tyre, 5mm , Smooth</td><td><input type="text" class="form-control" value="30"></td></tr>
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
 <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>
    <script src="js/demo/chart-bar-demo.js"></script>
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
 </script>  

</body>

</html>