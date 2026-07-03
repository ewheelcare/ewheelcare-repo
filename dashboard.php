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
					<form action="#" method="post">
 <table class="table table-striped">  
     <tr>
	 <td>From date<br><input type="text" name="from_date" id="from_date" class="form-control"></td>
	 <td>To date<br><input type="text" name="to_date" id="to_date" class="form-control"></td>
	 
	  <td><input type="submit" class="btn btn-primary btn-sm" value="Search"></td>
	 </tr>
	 </table>
		</form>
		</div>
		<?php 
		$from_date=$_POST["from_date"];
		$to_date=$_POST["to_date"];?>
	<div class="col-md-4 alert alert-info" style="text-align:center;border:2px solid white">
	<?php $sql="SELECT SUM(S.trans_amount) total_amount FROM sales_trans s , sales_trans_det d where s.trans_id=d.trans_id and d.active_status='A' and s.active_status='A'";
	if(isset($from_date)){
		$sql.="  AND S.trans_date BETWEEN '$from_date' AND '$to_date' ";
	}
	$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {$sales_amount= $row["total_amount"];}?>
	<H2>Sales Amount</h2><br>
	<h3 class="btn btn-primary" style="font-size:160%">Rs. <?php echo $sales_amount;?></h3>

	</div>
	<div  class="col-md-4 alert alert-warning" style="text-align:center;border:2px solid white">
	<?php $sql="SELECT SUM(S.trans_amount) total_amount FROM service_trans s , service_trans_det d where s.trans_id=d.trans_id and d.active_status='A' and s.active_status='A'";
	if(isset($from_date)){
		$sql.="  AND S.trans_date BETWEEN '$from_date' AND '$to_date' ";
	}
	$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {$service_amount= $row["total_amount"];}?>
	<H2>Service Amount</h2><br>
	<h3 class="btn btn-warning" style="font-size:160%">Rs. <?php echo $service_amount;?></h3>

	</div>
	<div class="col-md-4 alert alert-success" style="text-align:center;border:2px solid white">>
	<?php $sql="SELECT SUM(S.trans_amount) total_amount FROM receipt_trans s , receipt_trans_det d where s.trans_id=d.trans_id and d.active_status='A' and s.active_status='A'";
	if(isset($from_date)){
		$sql.="  AND S.trans_date BETWEEN '$from_date' AND '$to_date' ";
	}
	$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {$receipt_amount= $row["total_amount"];}?>
	<H2>Receipt Amount</h2><br>
	<h3 class="btn btn-success" style="font-size:160%">Rs. <?php echo $receipt_amount;?></h3>
	</div>
	<div class="col-md-12 alert alert-info" style="text-align:center;border:2px solid white">
	<center><h5>Daywise Sales Transactions</h5></center>
	<?php $sql="SELECT SUM(S.trans_amount) total_amount,s.trans_date FROM sales_trans s , sales_trans_det d where s.trans_id=d.trans_id and d.active_status='A' and s.active_status='A'";
	if(isset($from_date)){
		$sql.="  AND S.trans_date BETWEEN '$from_date' AND '$to_date' ";
	}
	$sql.=" group by s.trans_date";
	$result = $conn->query($sql);
	$x_axis="";$y_axis="";
while ($row = $result->fetch_assoc()) {$x_axis.="'".$row["trans_date"]."',";$y_axis=$row["total_amount"].",";}?>
 <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
	 
	</div>
	<div class="col-md-12 alert alert-warning" style="text-align:center;border:2px solid white">
	<center><h5>Daywise Service Transactions</h5></center>
	<?php $sql="SELECT SUM(S.trans_amount) total_amount,s.trans_date FROM service_trans s , service_trans_det d where s.trans_id=d.trans_id and d.active_status='A' and s.active_status='A'";
	if(isset($from_date)){
		$sql.="  AND S.trans_date BETWEEN '$from_date' AND '$to_date' ";
	}
	$sql.=" group by s.trans_date";
	$result = $conn->query($sql);
	$x_axis1="";$y_axis1="";
while ($row = $result->fetch_assoc()) {$x_axis1.="'".$row["trans_date"]."',";$y_axis1=$row["total_amount"].",";}?>
 <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart1"></canvas>
                                    </div>
                                </div>
	 
	</div>
	<div class="col-md-12 alert alert-success" style="text-align:center;border:2px solid white">
	<center><h5>Daywise Receipts Transactions</h5></center>
	<?php $sql="SELECT SUM(S.trans_amount) total_amount,s.trans_date FROM receipt_trans s , receipt_trans_det d where s.trans_id=d.trans_id and d.active_status='A' and s.active_status='A'";
	if(isset($from_date)){
		$sql.="  AND S.trans_date BETWEEN '$from_date' AND '$to_date' ";
	}
	$sql.=" group by s.trans_date";
	$result = $conn->query($sql);
	$x_axis2="";$y_axis2="";
while ($row = $result->fetch_assoc()) {$x_axis2.="'".$row["trans_date"]."',";$y_axis2=$row["total_amount"].",";}?>
 <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart2"></canvas>
                                    </div>
                                </div>
	 
	</div>	
	<div class="col-md-6 alert alert-primary">
<center><h5>Sales vs Services</h5></center>	
	<div class="chart-pie pt-4 pb-2">
    <canvas id="myPieChart"></canvas>
                                    </div>				
	</div>
<div class="col-md-6 alert alert-secondary">
<center><h5>Inflow vs Outflow</h5></center>		
	<div class="chart-pie pt-4 pb-2">
    <canvas id="myPieChart2"></canvas>
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
   
   
   

<?php include "footer_include.php";?>
	<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
	 <script src="js/demo/datatables-demo.js"></script>

	 <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
	<script>
	let labels="<?php echo $x_axis;?>";
let lables_arr = labels.split(",");
let values = "<?php echo $y_axis;?>";
let values_arr = values.split(",");
let labels1="<?php echo $x_axis1;?>";
let lables_arr1 = labels1.split(",");
let values1 = "<?php echo $y_axis1;?>";
let values_arr1 = values1.split(",");
let labels2="<?php echo $x_axis2;?>";
let lables_arr2 = labels2.split(",");
let values2 = "<?php echo $y_axis2;?>";
let values_arr2 = values2.split(",");
let sales_services_data=[<?php echo $sales_amount;?>,<?php echo $service_amount;?>];
let inflow_outflow_data=[<?php echo $sales_amount+$service_amount;?>,<?php echo $receipt_amount;?>];
	</script>
    <script src="js/demo/chart-area-demo1.js"></script>
	   <script src="js/demo/chart-pie-demo.js"></script>  
<script>
 $(function () {
    $("#from_date").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
      });
	  $("#to_date").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
      });
  });

</script>


</script>

  

</body>

</html>