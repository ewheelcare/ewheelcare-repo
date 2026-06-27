<!DOCTYPE html>
<html lang="en">

<head>
<link href="multi/searchableOptionList.css" rel="stylesheet">
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
	 <td>From date<br><input type="text" name="from_date" id="from_date" class="form-control" autocomplete="off"></td>
	 <td>To date<br><input type="text" name="to_date" id="to_date" class="form-control" autocomplete="off"></td>
	 <td>GST<br><select  name="gst" id="gst" class="form-control" autocomplete="off">
	 <option value="">--Select--</option>
	  <option value="Y">Yes</option>
	  <option value="N">No</option>
	 </select>
	 </td>
	 <td>Customer<br><select class="form-control" name="customer[]" id="customer_search" multiple="multiple" style="max-width:300px!important">

 <?php $sql="SELECT customer_id,company_name,owner_name,owner_mobile FROM customer";
$result = $conn->query($sql);
while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1["customer_id"]?>"><?php echo $row1["company_name"]?>,<?php echo $row1["owner_name"]?> </option>
<?php }?>
</select></td>
<td>Shop<br><select  class="form-control" name="shop" autocomplete="off">
	 <option value="">--select---</option>
<?php   $sql="SELECT shop_id,shop_name FROM shop ORDER BY shop_id;";
$result = $conn->query($sql);
$shop_array=[];
$shop_name_array=[];
$shop_cnt=0;
while ($row = $result->fetch_assoc()) {
	$shop_name_array[$shop_cnt]=$row['shop_name'];
$shop_array[$shop_cnt++]=$row['shop_id'];
	?>

<option value="<?php echo $row['shop_id'];?>"><?php echo $row['shop_name'];?></option>
<?php }?>
	  </select></td>	 
	  <td><BR><input type="submit" class="btn btn-primary btn-sm" value="Search"></td>
	 </tr>
	 </table>
		</form>
	<?php function startsWith($string, $start) {
    return substr($string, 0, strlen($start)) === $start;
}?>	
		<?php 
		$from_date=$_POST["from_date"];
		$to_date=$_POST["to_date"];
		$gst_param = $_POST["gst"];
		
		if (isset($_POST['customer'])) {

    $customers = $_POST['customer']; // array in PHP < 7 also
	$customer="";
    foreach ($customers as $customer_id) {
       $customer=$customer."'".$customer_id . "',";
    }
}
if(isset($customer)){
	
	$customer = substr($customer,0, strlen($customer)-1);
}
		$shop_param=$_POST["shop"];
	//	echo $from_date."====".$to_date."====".$item."=====".$customer;?>
	<button class="btn btn-sm btn-primary" type="button" STYLE="float:right;margin:3px;"  onclick="print_table()">Excel</button>
	
	<table class="table table-striped table-bordered" id="report_table" border=1 style="width:100%;font-size:75%;font-weight:bold;text-transform:uppercase"><thead>
	<tr style="background-color:#f0f0f0"><td></td>
	<?php $sql="select service_id,service_name from service";
		$result = $conn->query($sql);
$serv_array=[];	
$serv_total_array=[];
$serv_cnt=0;	
while ($row = $result->fetch_assoc()) {	
$service_id= $row["service_id"];
$service_name= $row["service_name"];?>
<td style="color:black !important; font-weight:bold;"><?php echo $service_name;?></td>
<?php $serv_array[$serv_cnt]=$service_id;
$serv_total_array[$serv_cnt++]=0;

}?>
<td style="color:black !important; font-weight:bold;">TOTAL</td></tr>
	</thead><tbody>	
	
		<?php 
		$i=0;
		$s=0;
		//echo $shop_cnt;
		while($s<$shop_cnt){$i=0;$gt=0;?>
		<tr><td><?php echo $shop_name_array[$s];?> </td>
		<?php while($i<$serv_cnt){
		$sql="select SUM(d.total) total from service_trans_det d, service_trans t where t.trans_id=d.trans_id and t.active_status='A' and d.active_status='A' AND  d.service_id='".$serv_array[$i]."'";
		 if(isset($from_date) && $from_date!="")
		$sql.="  AND IFNULL(t.trans_date,t.trans_date) BETWEEN  STR_TO_DATE('".$from_date."', '%d-%m-%Y') AND  STR_TO_DATE('".$to_date."', '%d-%m-%Y') ";
if(isset($customer) && $customer!="")
	$sql.="  AND COALESCE(NULLIF(t.customer, ''), '-') in (".$customer.") ";
	$sql.="  AND IFNULL(t.shop,'1000002')='".$shop_array[$s]."'";
if(isset($gst_param) && $gst_param!="")
	$sql.="  AND IFNULL(t.gst,'N')='".$gst_param."'";
		//echo "s is ".$s."==".$sql;
		echo $sql;
		$result2 = $conn->query($sql);
		if ($row2 = $result2->fetch_assoc()) {	
$total= $row2["total"];
$serv_total_array[$i]+=$total;
$gt+=$total;
?>
<td><?php echo $total ;?></td>
		<?php } $i++;
		}?>
		<td><?php echo $gt;?></td>
		</tr>
		<?php $s++;
		}
		$i=0;?><tr><td>Total</td>
	<?php  $ggt=0;while($i<$serv_cnt){$ggt+=$serv_total_array[$i];?>
	<td><?php echo $serv_total_array[$i];?></td>
	
	<?php $i++; }?>
	<td><?php echo $ggt;?></td></tr>	
		

</table>	
						
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
	  <script src="multi/searchableOptionList.js"></script>
      
         <script type="text/javascript">
    $(function() {
        $('#customer_search').searchableOptionList({
            maxHeight: '350px',
            showSelectAll: true
		
       // alert("jaimaa");
    });
	 });
</script>

<script>
 $(function () {
    $("#from_date").datepicker({
        dateFormat: "dd-mm-yy"  // Set format to yyyy-mm-dd
      });
	  $("#to_date").datepicker({
        dateFormat: "dd-mm-yy"  // Set format to yyyy-mm-dd
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