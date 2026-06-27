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
	 <td>Customer<br><select  class="form-control" name="customer">
	 <option value="">--select---</option>
<?php  $sql="SELECT customer_id,COALESCE(NULLIF(company_name, ''), owner_name) AS company_name FROM customer ORDER BY customer_id;";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>

<option value="<?php echo $row['customer_id'];?>"><?php echo $row['company_name'];?></option>
<?php }?>
	  </select></td>
	  <td>Service<br><select  class="form-control" name="service">
<option value="">--select---</option>
<?php  $sql="SELECT i.service_id,i.service_name,i.service_description FROM service i order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['service_id'];?>"><?php echo $row1['service_name'];?>(<?php echo  $row1['service_description'];?></option>
<?php }?>
	  </select>
	  </td>
	  <td><input type="submit" class="btn btn-primary btn-sm" value="Search"></td>
	 </tr>
	 </table>
		</form>
		
		<?php 
		$from_date=$_POST["from_date"];
		$to_date=$_POST["to_date"];
		$service=$_POST["service"];
		$customer=$_POST["customer"];
	//	echo $from_date."====".$to_date."====".$item."=====".$customer;?>
	<button class="btn btn-sm btn-primary" type="button" STYLE="float:right;margin:3px;"  onclick="print_table()">Excel</button>
	
	<table class="table table-striped table-bordered" id="report_table"><thead>
	<tr>
	<td>Trans details</td>
	<td>Customer details</td>
	<td>Service details</td>
	<td>Cost details</td>
	<tr>
	</thead><tbody>	
		<?php $sql="SELECT s.trans_id,d.subtrans_id,DATE_FORMAT(s.trans_date, '%d-%m-%Y') AS trans_date,s.customer_name company_name,i.service_name,d.total,d.tax_amount,s.gst FROM service_trans s, service_trans_det d,customer c,service i WHERE s.trans_id=d.trans_id and s.active_status='A' AND d.active_status='A' AND s.customer=c.customer_id and d.service_id=i.service_id ";
		if(isset($from_date) && $from_date!="")
		$sql.="  AND s.trans_date BETWEEN '$from_date' AND '$to_date' ";
if(isset($customer) && $customer!="")
	$sql.="  AND s.customer='".$customer."'";
if(isset($service)  && $service!="")
$sql.="  AND d.service_id='".$service."'";
	//echo $sql;	
		$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	
$trans_id= $row["trans_id"];
$subtrans_id=$row["subtrans_id"];
$trans_date=$row["trans_date"];
$company_name=$row["company_name"];
$service_name=$row["service_name"];
$total=$row["total"];
$tax_amount=$row["tax_amount"];
$gst=$row["gst"];?>
<tr>
	<td><?php echo $trans_id;?>(<?php echo $subtrans_id;?>) Dtd <?php echo $trans_date;?> </td>
	<td><?php echo $company_name;?></td>
	<td><?php echo $service_name;?></td>
	<td>Amount : <?php echo $total;?> + Tax : <?php echo $tax_amount;?> = <?php echo $total+$tax_amount;?> </td>
	<tr>
<?php }
		?>
		</tbody></table>
		
		
					
									
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
<script src="js/tableToExcel.js"></script>
 <script type="text/javascript">
		function print_table(){
		TableToExcel.convert(document.getElementById("report_table"));
		}
		</script>


</body>

</html>