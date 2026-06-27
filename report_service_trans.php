<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
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
<?php
$sql = "SELECT shop_id, shop_name FROM shop ORDER BY shop_id";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {

    $shop_short = strtoupper(trim(strtok($row['shop_name'], '- ')));
?>

<option value="<?php echo $shop_short; ?>">
    <?php echo $shop_short; ?>
</option>
<?php } ?>
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
	$shop_param = strtoupper(trim(strtok($_POST["shop"] ?? '', '- ')));

    // $shop_param = isset($_POST["shop"]) ? $_POST["shop"] : "";
		
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
		<?php
//  This is the first Query that executes to give the results of the first block

$sql = "SELECT 
            p.payment_id,
            COALESCE(NULLIF(p.customer_name,''), c.owner_name) AS customer_name,
            st.customer_mobile,
            st.customer_gst,
            st.vehicle_no,
            p.amount,
            p.mode,
            p.shop_id,
            IFNULL(p.payment_date,p.created_on) AS trans_date,
            st.trans_id
        FROM payments p
        INNER JOIN customer c 
            ON c.customer_id = p.customer
        INNER JOIN pay_track t 
            ON t.payment_id = p.payment_id
        INNER JOIN service_trans st 
            ON st.trans_id = t.trans_id
        WHERE p.active_status = 'A' and st.active_status = 'A'
        AND t.mode = 'service' ";


/* DATE FILTER */
if (!empty($from_date) && !empty($to_date)) {

    $sql .= " AND IFNULL(p.payment_date,p.created_on)
              BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y')
              AND STR_TO_DATE('$to_date','%d-%m-%Y') ";
}


/* CUSTOMER FILTER */
if (isset($customer) && $customer != "") {

    $sql .= " AND COALESCE(NULLIF(p.customer,''), st.customer)
              IN ($customer) ";
}


/* SHOP FILTER */
if (isset($shop_param) && $shop_param != "") {

    $sql .= " AND p.shop_id = '$shop_param' ";
}


/* ORDER BY */
$sql .= " ORDER BY
            p.shop_id,
			trans_date,
            COALESCE(NULLIF(p.customer_name,''), c.owner_name),
			p.mode ";


/* DEBUG */
//echo $sql;


/* EXECUTE */
// $result = $conn->query($sql);

?>
	<?php
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
$shop_name=$row["shop_id"];
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
<td>Date</td>
<td>SHOP</td>
<td>Pay Mode</td>
<td>DESCRIPTION</td>
<td>AMOUNT</td>
</tr>
</thead>	
<?php

$sql = "SELECT
    b.date,

    CASE b.shop_id
        WHEN '1000002' THEN 'SIRASAPALLI'
        WHEN '1000003' THEN 'GAJUWAKA'
        ELSE b.shop_id
    END AS shop_id,

    c.paytype_name,
    b.description,
    b.amount

FROM expenditure b

INNER JOIN paytype c
    ON c.paytype_id = b.paytype_id

WHERE 1=1";


/* DATE FILTER */
if (isset($from_date) && $from_date != "" && isset($to_date) && $to_date != "")
{
    $sql .= " AND b.date BETWEEN
              STR_TO_DATE('$from_date','%d-%m-%Y')
              AND STR_TO_DATE('$to_date','%d-%m-%Y') ";
}


/* SHOP FILTER */
if (isset($shop_param) && $shop_param != "")
{
    $sql .= " AND b.shop_id = '$shop_param' ";
}


/* ORDER BY */
$sql .= " ORDER BY
            b.shop_id,
            b.date,
            c.paytype_name ";


/* DEBUG */
// echo $sql;




?>
<?php	/* EXECUTE */
$result = $conn->query($sql);
	$slno=1;
		$prev_nature="";
		$arr_total=[];
while ($row = $result->fetch_assoc()) {	
$date = $row["date"];
$amount= $row["amount"];
$shop_name=$row["shop_id"];
$mode=$row["paytype_name"];
$description=$row["description"];
$total_expenditure+=$amount;
?>
<TR>
<TD><?php echo $date;?> </TD>
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
<TD></TD>
<TD><?php echo $total_expenditure;?> </TD>
</TR>
</TABLE>
<center><h5>SUMMARY</h5></center>

<table class="table table-striped report_table">
<thead>
<tr style="background:#343a40;color:white;text-align:center;font-weight:bold;">
    <td>SHOP</td>
    <td>MODE / TYPE</td>
    <td>AMOUNT</td>
</tr>
</thead>
<tbody>

<?php

/* ======================================================
   FINAL WORKABLE SUMMARY REPORT
   Assumption:
   payments.shop_id      = shop name
   expenditure.shop_id   = shop name
====================================================== */

$sql = "SELECT * FROM (

        /* =========================
           INFLOW (INCOME)
        ========================= */
        SELECT 
            SUM(p.amount) AS amount,
            p.shop_id AS shop_name,
            p.mode,
            'INFLOW (INCOME)' AS nature
        FROM payments p
        WHERE p.active_status='A' ";


/* DATE FILTER */
if (!empty($from_date) && !empty($to_date))
{
    $sql .= " AND COALESCE(p.payment_date,p.created_on)
              BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y')
              AND STR_TO_DATE('$to_date','%d-%m-%Y') ";
}

/* SHOP FILTER */
if (!empty($shop_param))
{
    $sql .= " AND p.shop_id='$shop_param' ";
}

/* CUSTOMER FILTER */
if (!empty($customer))
{
    $sql .= " AND p.customer IN ($customer) ";
}

$sql .= " GROUP BY p.shop_id,p.mode

        UNION ALL

        /* =========================
           OUTFLOW (EXPENDITURE)
        ========================= */
       SELECT
    SUM(e.amount) AS amount,

    CASE e.shop_id
        WHEN '1000002' THEN 'SIRASAPALLI'
        WHEN '1000003' THEN 'GAJUWAKA'
        ELSE e.shop_id
    END AS shop_name,

    pt.paytype_name AS mode,
    'OUTFLOW (EXPENDITURE)' AS nature

FROM expenditure e

INNER JOIN paytype pt
    ON pt.paytype_id = e.paytype_id

WHERE 1=1 ";


/* DATE FILTER */
if (!empty($from_date) && !empty($to_date))
{
    $sql .= " AND e.date
              BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y')
              AND STR_TO_DATE('$to_date','%d-%m-%Y') ";
}

/* SHOP FILTER */
if (!empty($shop_param))
{
    $sql .= " AND e.shop_id='$shop_param' ";
}

$sql .= " GROUP BY e.shop_id,pt.paytype_name

) x

ORDER BY
nature,
shop_name,
mode";

/* DEBUG IF NEEDED */
/* echo $sql; */

$result = $conn->query($sql);

if (!$result)
{
    die($conn->error);
}

/* ======================================================
   PROCESS REPORT
====================================================== */

$arr_total   = [];
$prev_nature = '';

while ($row = $result->fetch_assoc())
{
    $amount = (float)$row['amount'];
    $shop   = $row['shop_name'];
    $mode   = $row['mode'];
    $nature = $row['nature'];

    /* SECTION HEADINGS */
    if ($prev_nature != $nature)
    {
?>
<tr>
<td colspan="3"
style="background:#cfe2ff;
color:black;
font-weight:bold;
text-align:center;">
<?php echo $nature; ?>
</td>
</tr>
<?php
        $prev_nature = $nature;
    }
?>

<tr>
<td><?php echo $shop; ?></td>
<td><?php echo $mode; ?></td>
<td align="right"><?php echo number_format($amount,0); ?></td>
</tr>

<?php

    /* NET CALCULATION */
    $key = $shop . "~" . $mode;

    if (!isset($arr_total[$key]))
        $arr_total[$key] = 0;

    if ($nature == 'INFLOW (INCOME)')
        $arr_total[$key] += $amount;
    else
        $arr_total[$key] -= $amount;
}

/* ======================================================
   NET INCOME
====================================================== */
?>

<tr>
<td colspan="3"
style="background:#d1e7dd;
color:black;
font-weight:bold;
text-align:center;">
NET INCOME
</td>
</tr>

<?php foreach ($arr_total as $key => $value) {

$tmp = explode("~",$key);
?>

<tr>
<td><?php echo $tmp[0]; ?></td>
<td><?php echo $tmp[1]; ?></td>
<td align="right"><?php echo number_format($value,0); ?></td>
</tr>

<?php } ?>


<?php
/* ======================================================
   TOTAL MODE WISE
====================================================== */

$mode_total = [];

foreach ($arr_total as $key => $value)
{
    $tmp  = explode("~",$key);
    $mode = $tmp[1];

    if (!isset($mode_total[$mode]))
        $mode_total[$mode] = 0;

    $mode_total[$mode] += $value;
}
?>

<tr>
<td colspan="3"
style="background:#fff3cd;
color:black;
font-weight:bold;
text-align:center;">
TOTAL MODE / TYPE WISE
</td>
</tr>

<?php foreach ($mode_total as $mode => $value) { ?>

<tr>
<td>--</td>
<td><?php echo $mode; ?></td>
<td align="right"><?php echo number_format($value,0); ?></td>
</tr>

<?php } ?>


<?php
/* ======================================================
   TOTAL SHOP WISE
====================================================== */

$shop_total  = [];
$grand_total = 0;

foreach ($arr_total as $key => $value)
{
    $tmp  = explode("~",$key);
    $shop = $tmp[0];

    if (!isset($shop_total[$shop]))
        $shop_total[$shop] = 0;

    $shop_total[$shop] += $value;
    $grand_total += $value;
}
?>

<tr>
<td colspan="3"
style="background:#f8d7da;
color:black;
font-weight:bold;
text-align:center;">
TOTAL SHOP WISE
</td>
</tr>

<?php foreach ($shop_total as $shop => $value) { ?>

<tr>
<td><?php echo $shop; ?></td>
<td>--</td>
<td align="right"><?php echo number_format($value,0); ?></td>
</tr>

<?php } ?>


<tr>
<td colspan="3"
style="background:#d6d8db;
color:black;
font-weight:bold;
text-align:center;">
GRAND TOTAL
</td>
</tr>

<tr>
<td>--</td>
<td>--</td>
<td align="right"><?php echo number_format($grand_total,0); ?></td>
</tr>

</tbody>
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
$sql = "SELECT 
            s.service_name,
            t.shop,
            SUM(d.total) AS total
        FROM service_trans_det d
        INNER JOIN service_trans t
            ON d.trans_id = t.trans_id
        INNER JOIN service s
            ON s.service_id = d.service_id
        WHERE d.active_status = 'A'  and t.active_status = 'A'";

/* DATE FILTER */
if (!empty($from_date) && !empty($to_date))
{
    $sql .= " AND IFNULL(t.trans_date,t.created_on)
              BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y')
              AND STR_TO_DATE('$to_date','%d-%m-%Y') ";
}

/* SHOP FILTER */
if (!empty($shop_param))
{
    $sql .= " AND t.shop = '$shop_param' ";
}


/* CUSTOMER FILTER */
if (isset($customer) && $customer != "")
{
    $sql .= " AND COALESCE(NULLIF(t.customer,''),'')
              IN ($customer) ";
}


/* GROUP BY + ORDER BY */
$sql .= " GROUP BY
            s.service_name,
            t.shop
          ORDER BY
            s.service_name,
            t.shop ";


/* DEBUG */
// echo $sql;

?>
<?php
$result = $conn->query($sql);
		$slno=1;
		$prev_service="";
		$arr_total=[];
while ($row = $result->fetch_assoc()) {	
$amount= $row["total"];
$shop_name=$row["shop"];
$type_service=$row["service_name"];
if (isset($arr_total[$shop_name."~".$type_service])){
	$arr_total[$shop_name."~".$type_service]+=$amount;
}else{
	$arr_total[$shop_name."~".$type_service]=$amount;
}

if($prev_service!=$type_service)	{?>
<tr><td colspan="3" style="background-color:#cfe2ff;color:#000;font-weight:bold;text-align:center;"><?php echo $type_service;?></td></tr>	
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
<tr><td colspan="3" style="background-color:#cfe2ff;color:#000;font-weight:bold;text-align:center;">TOTAL SERVICE TYPE WISE</td></tr>	

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
<tr><td colspan="3" style="background-color:#cfe2ff;color:#000;font-weight:bold;text-align:center;">TOTAL SHOP WISE</td></tr>	

<?php foreach ($payTypeSum as $key => $value) {?>
<TR>
<TD><?php echo $key;?> </TD>
<TD>--</TD>
<TD><?php echo $value;?> </TD>
</TR>
<?php }?>
<tr><td colspan="3" style="background-color:#e9ecef;color:black;font-weight:bold;">GRAND TOTAL</td></tr>	
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