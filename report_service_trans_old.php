<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
<!DOCTYPE html>
<html lang="en">

<head>
<link href="multi/searchableOptionList.css" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<?php include "header_include.php";
include "db_config.php";
?>
</head>
<style>
#report_table thead td {
    color: black !important;
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
    $from_date = isset($_POST["from_date"]) ? $_POST["from_date"] : "";
    $to_date   = isset($_POST["to_date"]) ? $_POST["to_date"] : "";
    $shop_param = isset($_POST["shop"]) ? $_POST["shop"] : "";
		
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
	<div id="export_area">
	<table class="table table-striped table-bordered report_table" id="report_table" border=1 style="width:100%;font-size:75%;font-weight:bold;text-transform:uppercase"><thead>
	<tr style="background-color:#f0f0f0">
	<td>SL NO</td>
	<td>Trans id</td>
	<td>Invoice</td>
	<td>Trans Date</td>
	<td>Vehicle</td>
	<td>Company/Customer</td>
	
	<td>Mobile</td>
		<td>GST</td>
	

	<td>Mode</td>
	<td>Shop</td>
		<td>Amount</td>
	
	</tr>
	</thead><tbody>	
		<?php $sql="select p.payment_id,COALESCE(NULLIF(p.customer_name, ''), c.owner_name) AS customer_name,st.customer_mobile,st.customer_gst,st.vehicle_no,p.amount,p.mode,s.shop_name,IFNULL(p.payment_date,p.created_on) trans_date,st.trans_id from payments p, shop s,customer c,pay_track t,service_trans st where s.shop_id=p.shop_id and p.active_status='A' and c.customer_id=p.customer and t.payment_id=p.payment_id and t.mode='service' and st.trans_id=t.trans_id ";
		if(!empty($from_date) && !empty($to_date))
		$sql.="  AND IFNULL(p.payment_date,p.created_on) BETWEEN  STR_TO_DATE('".$from_date."', '%d-%m-%Y') AND  STR_TO_DATE('".$to_date."', '%d-%m-%Y') ";
if(isset($customer) && $customer!="")
	$sql.="  AND COALESCE(NULLIF(p.customer, ''), st.customer) in (".$customer.") ";
if(isset($shop_param) && $shop_param!="")
	$sql.="  AND s.shop_id='".$shop_param."'";
		
	$sql.=" order by s.shop_name, p.mode,COALESCE(NULLIF(p.customer_name, ''), c.owner_name);";
	//echo $sql;
		$result = $conn->query($sql);
		$slno=1;
		$prev_shop="";$prev_mode="";
while ($row = $result->fetch_assoc()) {	
$trans_id= $row["payment_id"];
$vehicle_no=$row["vehicle_no"];
$trans_date=$row["trans_date"];
$customer_name=$row["customer_name"];
$customer_mobile=$row["customer_mobile"];
$customer_gst=$row["customer_gst"];
$trans_amount=$row["amount"];
$mode=$row["mode"];
$shop_name=$row["shop_name"];
$invoice=$row["trans_id"];
if(($prev_shop!=$shop_name || $prev_mode!=$mode)&& ($prev_mode!="")){?>
<tr><td colspan=11 style="text-align:right"><?php echo $total_amount;?></td>	
<?php $total_amount=0;}
?>
<tr>
	<td><?php echo $slno;?></td>
	<td><?php echo $trans_id;?></td>
	<td><?php echo $invoice;?></td>
	<td><?php echo $trans_date;?> </td>
	<td><?php echo $vehicle_no;?></td>

	<td><?php echo $customer_name;?></td>
	<td><?php echo $customer_mobile;?></td>
	<td><?php echo $customer_gst;?></td>
	<td><?php echo $mode;?> </td>
	
	<td><?php echo $shop_name;?> </td>
	<td><?php echo $trans_amount;?></td>
	
	</tr>
<?php $total_amount+=$trans_amount;
$prev_mode=$mode;$prev_shop=$shop_name;
$slno=$slno+1; }
		?>
		<tr><td colspan=11 style="text-align:right"><?php echo $total_amount;?></td>	
		</tbody></table>
		
<hr>

<CENTER><H5>EXPENDITURE</H5></CENTER>
<table class="table table-striped report_table">
<thead>
<tr>
<td>SHOP</td>
<td>HEAD OF ACCOUNT</td>
<td>DESCRIPTION</td>
<td>AMOUNT</td>
</tr>
</thead>	
<?PHP 
$sql="select p.amount,
             s.shop_name,
             t.paytype_name,
             IFNULL(h.headofaccount_name,'N/A') as headofaccount_name,
             p.description
from expenditure p
JOIN shop s ON s.shop_id=p.shop_id
JOIN paytype t ON p.paytype_id=t.paytype_id
LEFT JOIN headofaccount h ON h.headofaccount_id=p.headofaccount_id
WHERE 1=1 ";
	if(isset($from_date) && $from_date!="")
		$sql.="  AND STR_TO_DATE(p.date,'%d-%m-%Y') BETWEEN  STR_TO_DATE('".$from_date."', '%d-%m-%Y') AND  STR_TO_DATE('".$to_date."', '%d-%m-%Y') ";

if(isset($shop_param) && $shop_param!="")
	$sql.="  AND p.shop_id='".$shop_param."'";
	//$sql.=" group by s.shop_name,t.paytype_name)) ";
	
$sql.= " order by 2,4"	;
//echo $sql;
	$result = $conn->query($sql);
		$slno=1;
		$prev_nature="";
		$arr_total=[];
while ($row = $result->fetch_assoc()) {	
$amount= $row["amount"];
$shop_name=$row["shop_name"];
$mode=$row["headofaccount_name"];
$description=$row["description"];
$total_expenditure+=$amount;
?>
<TR>
<TD><?php echo $shop_name;?> </TD>
<TD><?php echo $mode;?> </TD>
<TD><?php echo $description;?> </TD>
<TD><?php echo $amount;?> </TD>
</TR>	
<?php }?>
<TR>
<TD></TD>
<TD></TD>
<TD></TD>
<TD><?php echo $total_expenditure;?> </TD>
</TR>
</TABLE>
<CENTER><H5>SUMMARY</H5></CENTER>
<table class="table table-striped report_table">
<thead>
<tr>
<td>SHOP</td>
<td>MODE</td>
<td>AMOUNT</td>
</tr>
</thead>	
	
	<?php 
$sql="((select sum(p.amount) amount,s.shop_name,p.mode,'INFLOW(INCOME)' nature from payments p, shop s,customer c,pay_track t,service_trans st where s.shop_id=p.shop_id and p.active_status='A' and c.customer_id=p.customer and t.payment_id=p.payment_id and t.mode='service' and st.trans_id=t.trans_id ";
		if(isset($from_date) && $from_date!="")
		$sql.="  AND IFNULL(p.payment_date,p.created_on) BETWEEN  STR_TO_DATE('".$from_date."', '%d-%m-%Y') AND  STR_TO_DATE('".$to_date."', '%d-%m-%Y') ";
if(isset($customer) && $customer!="")
	$sql.="  AND COALESCE(NULLIF(p.customer, ''), st.customer) in (".$customer.") ";
if(isset($shop_param) && $shop_param!="")
	$sql.="  AND s.shop_id='".$shop_param."'";
		
	$sql.=" group by s.shop_name,p.mode) UNION ";
	$sql.="(select sum(p.amount) amount,s.shop_name,t.paytype_name,'OUTFLOW(EXPENDITURE)' nature from expenditure p, shop s, paytype t where s.shop_id=p.shop_id  and p.paytype_id=t.paytype_id  ";
	if(isset($from_date) && $from_date!="")
		$sql.="  AND STR_TO_DATE(p.date,'%d-%m-%Y') BETWEEN  STR_TO_DATE('".$from_date."', '%d-%m-%Y') AND  STR_TO_DATE('".$to_date."', '%d-%m-%Y') ";

if(isset($shop_param) && $shop_param!="")
	$sql.="  AND p.shop_id='".$shop_param."'";
	$sql.=" group by s.shop_name,t.paytype_name)) ";
	
$sql = "select * from (".$sql.") x order by 4,2,3"	;
	$result = $conn->query($sql);
		$slno=1;
		$prev_nature="";
		$arr_total=[];
while ($row = $result->fetch_assoc()) {	
$amount= $row["amount"];
$shop_name=$row["shop_name"];
$mode=$row["mode"];
$nature=$row["nature"];
if($nature=="INFLOW(INCOME)"){
	$arr_total[$shop_name."~".$mode]=$amount;
}
if($nature=="OUTFLOW(EXPENDITURE)"){
	if (isset($arr_total[$shop_name."~".$mode])){
		//echo "====here".$arr_total[$shop_name."~".$mode];
		$arr_total[$shop_name."~".$mode]=$arr_total[$shop_name."~".$mode] -$amount;
		
	}else{
		$arr_total[$shop_name."~".$mode]=$amount*(-1.0);
		
	}
}
if($prev_nature!=$nature)	{?>
<tr><td colspan=3 style="background-color:black;color:white"><?php echo $nature;?></td></tr>	
<?php $prev_nature=$nature;}
	
	?>	
<TR>
<TD><?php echo $shop_name;?> </TD>
<TD><?php echo $mode;?> </TD>
<TD><?php echo $amount;?> </TD>
</TR>	
<?php }?>
<tr><td colspan=3 style="background-color:black;color:white">NET INCOME</td></tr>	
<?php foreach ($arr_total as $key => $value) {?>
<TR>
<TD><?php echo explode("~",$key)[0];?> </TD>
<TD><?php echo explode("~",$key)[1];?> </TD>
<TD><?php echo $value;?> </TD>
</TR>
<?php }
$payTypeSum = [];

foreach ($arr_total as $key => $amount) {
    // Split key into Shop and Pay Type
   $tmp = explode('~', $key);
$shop = $tmp[0];
$payType = $tmp[1];
//echo $shop;
    // Initialize if not exists
    if (!isset($payTypeSum[$payType])) {
        $payTypeSum[$payType] = 0;
    }

    // Add amount (cast to int for safety)
    $payTypeSum[$payType] += (int)$amount;
}
//var_dump($payTypeSum);
?>
<tr><td colspan=3 style="background-color:black;color:white">TOTAL PAY TYPE WISE</td></tr>	

<?php foreach ($payTypeSum as $key => $value) {?>
<TR>
<TD>-- </TD>
<TD><?php echo $key;?> </TD>
<TD><?php echo $value;?> </TD>
</TR>
<?php } $payTypeSum = [];
$grand_total=0;
foreach ($arr_total as $key => $amount) {
    // Split key into Shop and Pay Type
   $tmp = explode('~', $key);
$shop = $tmp[0];
$payType = $tmp[1];
//echo $shop;
    // Initialize if not exists
    if (!isset($payTypeSum[$shop])) {
        $payTypeSum[$shop] = 0;
    }

    // Add amount (cast to int for safety)
    $payTypeSum[$shop] += (int)$amount;
	$grand_total+=(int)$amount;
}
//var_dump($payTypeSum);
?>
<tr><td colspan=3 style="background-color:black;color:white">TOTAL SHOP WISE</td></tr>	

<?php foreach ($payTypeSum as $key => $value) {?>
<TR>
<TD><?php echo $key;?> </TD>
<TD>--</TD>
<TD><?php echo $value;?> </TD>
</TR>
<?php }?>
<tr><td colspan=3 style="background-color:black;color:white">GRAND TOTAL</td></tr>	
<TR>
<TD>--</TD>
<TD>--</TD>
<TD><?php echo $grand_total;?></TD>
</TR>

</table>
<CENTER><H5>SERVICE WISE SUMMARY</H5></CENTER>
<table class="table table-striped report_table">
<thead>
<tr>
<td>SHOP</td>
<td>SERVICE</td>
<td>AMOUNT</td>
</tr>
</thead>	
<?php 

$sql="SELECT s.service_name, h.shop_name,sum(total) total FROM service_trans_det d, service_trans t,service s,shop h where d.trans_id=t.trans_id and d.active_status='A' and s.service_id=d.service_id and ifnull(t.shop,'1000002')=h.shop_id ";

if(isset($from_date) && $from_date!="")
		$sql.="  AND IFNULL(t.trans_date,t.created_on) BETWEEN  STR_TO_DATE('".$from_date."', '%d-%m-%Y') AND  STR_TO_DATE('".$to_date."', '%d-%m-%Y') ";
if(isset($customer) && $customer!="")
	$sql.="  AND COALESCE(NULLIF(t.customer, ''),'') in (".$customer.") ";
if(isset($shop_param) && $shop_param!="")
	$sql.="  AND ifnull(t.shop,'1000002')='".$shop_param."'";
$sql.=" group by s.service_name, h.shop_name order by 1,2";
//echo $sql;
$result = $conn->query($sql);
		$slno=1;
		$prev_service="";
		$arr_total=[];
while ($row = $result->fetch_assoc()) {	
$amount= $row["total"];
$shop_name=$row["shop_name"];
$type_service=$row["service_name"];
if (isset($arr_total[$shop_name."~".$type_service])){
	$arr_total[$shop_name."~".$type_service]+=$amount;
}else{
	$arr_total[$shop_name."~".$type_service]=$amount;
}

if($prev_service!=$type_service)	{?>
<tr><td colspan=3 style="background-color:black;color:white"><?php echo $type_service;?></td></tr>	
<?php $prev_service=$type_service;}
	
	?>	
<TR>
<TD><?php echo $shop_name;?> </TD>
<TD><?php echo $type_service;?> </TD>
<TD><?php echo $amount;?> </TD>
</TR>	
<?php }?>

<?php 
$payTypeSum = [];

foreach ($arr_total as $key => $amount) {
    // Split key into Shop and Pay Type
   $tmp = explode('~', $key);
$shop = $tmp[0];
$payType = $tmp[1];
//echo $shop;
    // Initialize if not exists
    if (!isset($payTypeSum[$payType])) {
        $payTypeSum[$payType] = 0;
    }

    // Add amount (cast to int for safety)
    $payTypeSum[$payType] += (int)$amount;
}
//var_dump($payTypeSum);
?>
<tr><td colspan=3 style="background-color:black;color:white">TOTAL SERVICE TYPE WISE</td></tr>	

<?php foreach ($payTypeSum as $key => $value) {?>
<TR>
<TD>-- </TD>
<TD><?php echo $key;?> </TD>
<TD><?php echo $value;?> </TD>
</TR>
<?php } $payTypeSum = [];
$grand_total=0;
foreach ($arr_total as $key => $amount) {
    // Split key into Shop and Pay Type
   $tmp = explode('~', $key);
$shop = $tmp[0];
$payType = $tmp[1];
//echo $shop;
    // Initialize if not exists
    if (!isset($payTypeSum[$shop])) {
        $payTypeSum[$shop] = 0;
    }

    // Add amount (cast to int for safety)
    $payTypeSum[$shop] += (int)$amount;
	$grand_total+=(int)$amount;
}
//var_dump($payTypeSum);
?>
<tr><td colspan=3 style="background-color:black;color:white">TOTAL SHOP WISE</td></tr>	

<?php foreach ($payTypeSum as $key => $value) {?>
<TR>
<TD><?php echo $key;?> </TD>
<TD>--</TD>
<TD><?php echo $value;?> </TD>
</TR>
<?php }?>
<tr><td colspan=3 style="background-color:black;color:white">GRAND TOTAL</td></tr>	
<TR>
<TD>--</TD>
<TD>--</TD>
<TD><?php echo $grand_total;?></TD>
</TR>

</table>	
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
		function print_table() {
    TableToExcel.convert(document.getElementById("export_area"), {
        name: "Report.xlsx"
    });
}
		</script>


</body>

</html>