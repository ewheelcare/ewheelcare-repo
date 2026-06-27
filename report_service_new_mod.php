<!DOCTYPE html>
<html lang="en">

<head>
<link href="multi/searchableOptionList.css" rel="stylesheet">
<?php include "header_include.php";
include "db_config.php";
?>
</head>
<style>
#report_table thead td {
    color: black !important;
    font-size: 11px;
}
</style>
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


while ($row = $result->fetch_assoc()) {?>

<option value="<?php echo $row['shop_id'];?>"><?php echo $row['shop_name'];?></option>
<?php }?>
	  </select></td>
	  <td><input type="submit" class="btn btn-primary btn-sm" value="Search"></td>
	 </tr>
	 </table>
		</form>
	<?php function startsWith($string, $start) {
    return substr($string, 0, strlen($start)) === $start;
}?>	
	<?php 

// SAFE INPUT HANDLING
$from_date = isset($_POST["from_date"]) ? $_POST["from_date"] : "";
$to_date   = isset($_POST["to_date"]) ? $_POST["to_date"] : "";
$shop      = isset($_POST["shop"]) ? $_POST["shop"] : "";

// CUSTOMER ARRAY FIX
$customer = "";
if(isset($_POST['customer']) && count($_POST['customer']) > 0){
    $customer = "'" . implode("','", $_POST['customer']) . "'";
}

// DEBUG (optional)
// echo $shop;

// MAIN QUERY
$sql = "SELECT 
    s.trans_id,
    s.trans_date,
    s.VEHICLE_NO,
    s.COMPANY_NAME,
    s.CUSTOMER_NAME,
    s.CUSTOMER_MOBILE,
    s.CUSTOMER_GST,
    s.trans_amount,
    s.paid_amount,
    s.pending,
    t.tax,
    t.tax_sgst,
    t.total,
    s.mech,
    sh.shop_name
FROM service_trans s
JOIN shop sh 
    ON sh.shop_name LIKE CONCAT(s.shop, '%')
JOIN (
    SELECT 
        d.trans_id, 
        SUM(d.tax_amount) tax,
        SUM(d.tax_amount_sgst) tax_sgst,
        SUM(d.total) total 
    FROM service_trans_det d 
    WHERE d.active_status='A' 
    GROUP BY d.trans_id
) t 
    ON t.trans_id = s.trans_id
WHERE s.active_status='A' ";

// DATE FILTER
if($from_date != ""){
    $sql .= " AND s.trans_date >= STR_TO_DATE('".$from_date."', '%d-%m-%Y')
              AND s.trans_date < DATE_ADD(STR_TO_DATE('".$to_date."', '%d-%m-%Y'), INTERVAL 1 DAY) ";
}

// CUSTOMER FILTER
if($customer != ""){
    $sql .= " AND s.customer IN (".$customer.") ";
}

// SHOP FILTER
if($shop != ""){
    $sql .= " AND sh.shop_id = '".$shop."' ";
}

// DEBUG QUERY
echo $sql;

// EXECUTE QUERY
$result = $conn->query($sql);

if (!$result) {
    die("Query Error: " . $conn->error);
}

$slno = 1;
while ($row = $result->fetch_assoc()) {    

    $trans_id = $row["trans_id"];
    $vehicle_no = $row["VEHICLE_NO"];
    $trans_date = $row["trans_date"];
    $company_name = $row["COMPANY_NAME"];
    $customer_name = $row["CUSTOMER_NAME"];
    $customer_mobile = $row["CUSTOMER_MOBILE"];
    $customer_gst = $row["CUSTOMER_GST"];
    $trans_amount = $row["trans_amount"];
    $paid_amount = $row["paid_amount"];
    $pending = $row["pending"];
    $tax = $row["tax"];
    $tax_sgst = $row["tax_sgst"];
    $total = $row["total"];
    $mech = $row["mech"];
    $shop_name = $row["shop_name"];

    if(substr($customer_gst,0,2) == "37"){
        $tax_igst = 0;
    } else {
        $tax_igst = $tax;
        $tax = 0;
    }
?>
<tr>
    <td><?php echo $slno;?></td>
    <td><?php echo $trans_id;?></td>
    <td><?php echo $trans_date;?></td>
    <td><?php echo $vehicle_no;?></td>
    <td><?php echo $company_name;?></td>
    <td><?php echo $customer_name;?></td>
    <td><?php echo $customer_mobile;?></td>
    <td><?php echo $customer_gst;?></td>
    <td><?php echo $mech;?></td>
    <td><?php echo $total;?></td>
    <td><?php echo $tax;?></td>
    <td><?php echo $tax_sgst;?></td>
    <td><?php echo $tax_igst;?></td>
    <td><?php echo $trans_amount;?></td>
    <td><?php echo $pending;?></td>
    <td><?php echo $shop_name;?></td>
</tr>
<?php 
    $slno++;
}
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