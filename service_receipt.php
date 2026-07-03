<html>
<style>
table tbody tr td{border-collapse:collapse;}
	table{border:0px!important}
	body{text-transform:uppercase}
	table {
  border-collapse: collapse;
}

table, th, td {
  border-color:#777!important;
	border-width:.16px!important;
  border-collapse: collapse;
	line-height:1.24em;
	padding:4px;
}
</style>
<body id="content-to-pdf">
	<?php
function numberToWords($number)
{
    if ($number == 0) {
        return "Zero";
    }

    $words = array(
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen',
        17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
        20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
        50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
        80 => 'Eighty', 90 => 'Ninety'
    );

    $levels = array(
        10000000 => 'Crore',
        100000 => 'Lakh',
        1000 => 'Thousand',
        100 => 'Hundred'
    );

    $result = '';

    foreach ($levels as $value => $name) {
        if ($number >= $value) {
            $count = floor($number / $value);
            $number = $number % $value;
            $result .= numberToWords($count) . ' ' . $name . ' ';
        }
    }

    if ($number > 0) {
        if ($number < 20) {
            $result .= $words[$number];
        } else {
            $result .= $words[floor($number / 10) * 10];
            if ($number % 10) {
                $result .= ' ' . $words[$number % 10];
            }
        }
    }

    return trim($result);
}
?>
<?php
include "db_config.php";
$trans_id=$_GET["trans_id"];
/* CHECK PENDING AMOUNT */
$sql_pending = "SELECT pending 
                FROM service_trans 
                WHERE trans_id = ?";

$stmt_pending = mysqli_prepare($conn, $sql_pending);

mysqli_stmt_bind_param($stmt_pending, "s", $trans_id);

mysqli_stmt_execute($stmt_pending);

$result_pending = mysqli_stmt_get_result($stmt_pending);

$row_pending = mysqli_fetch_assoc($result_pending);
if (floatval($row_pending["pending"] ?? 0) > 0) {
/* if ($row_pending && $row_pending["pending"] > 0) { */

    die("
    <script>
        alert('Pending amount is not zero');
        window.close();
    </script>
    ");

}
/* check pending amount ends here */
$shop = "";

$sql_tmp = "SELECT shop FROM service_trans WHERE trans_id='".$trans_id."'";
$res_tmp = $conn->query($sql_tmp);

if($row_tmp = $res_tmp->fetch_assoc()){
    $shop = $row_tmp["shop"];
}
	$action=$_GET["action"];
$sql = "select * from company";
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
$company_name1= $row["company_name"];
$company_address1= $row["company_address"];
$company_gst= $row["company_gst"];
$company_mobile= $row["company_mobile"];
$company_email= $row["company_email"];
$company_bank =$row["company_bank"];
$company_branch= $row["company_branch"];
$company_ifsc =$row["company_ifsc"];
$company_ac =$row["company_ac"];
}
$sql = "select * from shop where shop_id='".$shop."'";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
$company_address1= $row["address"];	
}
$result = $conn->query($sql);
$sql="select trans_id, DATE_FORMAT(m.trans_date, '%d-%m-%Y') AS trans_date, details, customer, trans_amount, pending, gst, tally, created_by, created_on, modified_by, modified_on, active_status, VEHICLE_NO, VEHICLE_MODEL, NO_OF_WHEELS, COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, VEHICLE_ODOMETER, VEHICLE, paid_amount, ref_no, pay_type,customer_mobile,vehicle_make,mech FROM service_trans  m where m.active_status='A' and m.trans_id='".$trans_id."'";
//echo $sql;
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
	$customer= $row["customer"];
$gst=$row["gst"];
$company_name= $row["COMPANY_NAME"];
$company_address= $row["CUSTOMER_ADDRESS"];
$owner_name= $row["CUSTOMER_NAME"];
$owner_gst= $row["CUSTOMER_GST"];
$owner_mobile= $row["customer_mobile"];
$trans_date = date('d-m-Y', strtotime($row["trans_date"]));
$vehicle_no= $row["VEHICLE_NO"];
$vehicle_model= $row["VEHICLE_MODEL"];
$vehicle_make= $row["vehicle_make"];
$no_of_wheels= $row["NO_OF_WHEELS"];
$vehicle_odometer= $row["VEHICLE_ODOMETER"];
	$mech = $row["MECH"];
}
	
?>

<table style="width:100%; table-layout:fixed;border:0.3px solid #4a4a4a;;min-height:610px;font-size:98%" id="">
<tr><td><table style="width:100%; table-layout:fixed;border:.3px solid #4a4a4a">
<tr><td style="width:20%;border:.3px solid #4a4a4a"><img src="img/expert_logo.png" style="width:140px;text-align:center"/></td><td style="width:75%;text-align:center;font-size:170%;background-color:#f0f0f0;line-height:.9em">
<?php echo $company_name1;?><BR> <SPAN style="font-size:52%;text-transform:none"><?php echo $company_address1;?><br> <?php echo $company_gst;?><br><br>
	<?php if($gst=="Y"){?>

	<SPAN style="font-size:92%;text-transform:none;text-decoration:underline;margin-top:40px">TAX INVOICE</span>
	<?php }else{?>
	<SPAN style="font-size:92%;text-transform:none;text-decoration:underline;margin-top:40px">CASH BILL</span>
	<?php }?>
	</SPAN></td></tr>
</table></td></tr>
<tr><td><table style="width:100%; table-layout:fixed;border:.3px solid #4a4a4a;font-size:95%">
<tr><td style="width:50%;border:.3px solid #4a4a4a">Company :<?php echo $company_name1;?><br>
<span style="font-size:85%;text-transform:none">Address : <?php echo $company_address1;?><br>
<?php if($gst=="Y"){?>
GSTIN : <?php echo $company_gst;?><br>
<?PHP }?>
Contact :<?php echo $company_mobile;?><br>
	Email:<?php echo $company_email;?></span>
 </td><td style="width:50%;border:.3px solid #4a4a4a;font-size:90%">
 Invoice No :<?php echo $trans_id;?><br>
Date  : <?php echo $trans_date;?><br>
Job Card No : JOB/<?php echo $trans_id;?><br>
 </td></tr>
<tr><td style="width:50%;border:.3px solid #4a4a4a">Customer :<?php echo $company_name;?><br>
<span style="font-size:85%;text-transform:none">

 Owner :<?php echo $owner_name;?><br>
<?php if($gst=="Y"){?>
GST: <?php echo $owner_gst;?><br>
<?PHP }?>
	Mobile : <?php echo $owner_mobile;?><BR>Address : <?php echo $company_address;?><br></span>
 </td><td style="width:50%;border:.3px solid #4a4a4a">

 Vehicle : <?php echo $vehicle_no;?>,<br>
	<span style="font-size:85%;text-transform:none"> Make : <?php echo $vehicle_make;?><br>Model : <?php echo $vehicle_model;?><br>

Run in km : <?php echo $vehicle_odometer;?><br>
No of Wheels : <?php echo $no_of_wheels;?><BR>
	Mechanic : <?php echo $mech;?>
	</span>
 </td></tr></table></td></tr><tr><td><table style="width:100%; table-layout:fixed;border:.3px solid #4a4a4a;font-size:79%;font-weight:bold;border-collapse:collapse">
<?php  $sql="SELECT trans_id,subtrans_id, i.service_id,i.service_name,i.service_description,i.hsn,
d.cost,d.qty,d.tax,d.tax_amount,d.total,d.tax_sgst,d.tax_amount_sgst,
(d.tax_amount+d.total+d.tax_amount_sgst) full_amount,discount 
FROM service_trans_det d,service i 
WHERE i.service_id=d.service_id 
AND d.trans_id='".$trans_id."' 
AND d.active_status='A'
AND d.qty > 0";
				//echo $sql;
				$result = $conn->query($sql);
$tax_amount=0;
$total_amount=0;
$colspan=5;
?>
<tr style="font-size:88%">
<td style="border:.3px solid #4a4a4a" >SL. No.</td>
<td style="border:.3px solid #4a4a4a;width:200px">Service</td>
<td style="border:.3px solid #4a4a4a">HSN</td>
	<td style="border:.3px solid #4a4a4a">Qty</td>
<td style="border:.3px solid #4a4a4a">Unit<br> Price</td>
	<td style="border:.3px solid #4a4a4a">Discount</td>

	
	<td style="border:.3px solid #4a4a4a">Cost</td>


<?php if($gst=="Y"){ ;?>
<?PHP if(strncasecmp($company_gst, "37", strlen("37")) === 0){ $colspan=7?>
<td style="border:.3px solid #4a4a4a">GST (18%)</td>


<?php }else{ $colspan=7?>
<td style="border:.3px solid #4a4a4a">GST (18%)</td>
<?php }?>

<?php }?>
	<?php if($gst=="Y"){ ?>
<td style="border:.3px solid #4a4a4a">Total</td>
	<?php }else{?>
	<td style="border:.3px solid #4a4a4a" colspan=2>Total</td><?php }?>
</tr>
<?php 
	$slno=1;
	$total_discount=0;
while ($row = $result->fetch_assoc()) {
	$tax=$row["tax"]	; 
	$tax_sgst=$row["tax_sgst"]	; 
$tax_amount+=$row["tax_amount"]	;
$tax_amount_sgst+=$row["tax_amount_sgst"]	;
$total_amount+=$row["total"];
	$total_discount+=$row["discount"];
	?>
<TR style="font-size:88%">
<TD style="border:.3px solid #4a4a4a">
<b><?php echo $slno++;?></b></td>
<td style="border:.3px solid #4a4a4a"><b><?php echo $row["service_name"];?></b>


</TD>
<td style="border:.3px solid #4a4a4a"><?php echo $row["hsn"];?></td>
	<td style="border:.3px solid #4a4a4a;text-align:right"> <?php 
	echo $row["qty"];
// 	if(strpos( $row["service_name"], 'WHEEL ALIGNMENT') !== false)
// 		echo "1" ; else 
// 	echo $row["qty"];?></td>
<td style="border:.3px solid #4a4a4a;text-align:right"> <?php echo $row["cost"];?></td>
<td style="border:.3px solid #4a4a4a;text-align:right"> <?php echo $row["discount"];?></td>

<td style="border:.3px solid #4a4a4a;text-align:right"> <?php echo $row["total"];?></td>
<?php if($gst=="Y"){ $col="1";if (strncasecmp($company_gst, "37", strlen("37")) === 0){ ?>
<!--<td style="border:.3px solid #4a4a4a;text-align:right"> <?php echo $row["tax"];?></td>-->
<td style="border:.3px solid #4a4a4a;text-align:right"> <?php echo $row["tax_amount"]+$row["tax_amount_sgst"];?></td>
<!--<td style="border:.3px solid #4a4a4a;text-align:right"> <?php echo $row["tax_sgst"];?></td>
<td style="border:.3px solid #4a4a4a;text-align:right"> <?php echo $row["tax_amount_sgst"];?></td>-->
<?php }else {?>
<!--<td style="border:.3px solid #4a4a4a;text-align:right"> <?php echo $row["tax"];?></td>-->
<td style="border:.3px solid #4a4a4a;text-align:right"> <?php echo $row["tax_amount"];?></td>
<?php }}else {$col="2";}?>
<td style="border:.3px solid #4a4a4a;text-align:right" colspan="<?php echo $col; ?>"> Rs.<?php echo $row["total"]+$row["tax_amount"]+$row["tax_amount_sgst"];?></td>
</TR>
<?php }?>
	<tr style="font-size:88%"><td style="border-left:.3px solid #4a4a4a;border-right:.3px solid #4a4a4a;;text-align:right" colspan="6"></td><td style="border:0px solid #4a4a4a;;text-align:right" colspan="2"><?php if($gst=="Y"){?>Taxable <?php } ?> Amount </td><td  style="border-right:.3px solid #4a4a4a;text-align:right"><?php echo $total_amount;?></td></tr>
<?php if($gst=="Y"){ if(strncasecmp($company_gst, "37", strlen("37")) === 0){ ?>
<tr style="font-size:88%"><td style="border-left:.3px solid #4a4a4a;border-right:.3px solid #4a4a4a;;text-align:right" colspan="6"></td><td style="border:0px solid #4a4a4a;;text-align:right" colspan="2">CGST  (<?php echo $tax ;?> %) </td><td style="border-right:.3px solid #4a4a4a;text-align:right"><?php echo $tax_amount;?></td></tr>
<tr style="font-size:88%"><td style="border-left:.3px solid #4a4a4a;border-right:.3px solid #4a4a4a;;text-align:right" colspan="6"></td><td style="border:0px solid #4a4a4a;;text-align:right" colspan="2">SGST  (<?php echo $tax ;?> %) </td><td  style="border-right:.3px solid #4a4a4a;text-align:right"><?php echo $tax_amount_sgst;?></td></tr>
<?php }else{?>
<tr style="font-size:88%"><td style="border-left:.3px solid #4a4a4a;border-right:.3px solid #4a4a4a;;text-align:right" colspan="6"></td><td style="border:0px solid #4a4a4a;;text-align:right" colspan="2">IGST  (<?php echo $tax ;?> %) </td><td  style="border-right:.3px solid #4a4a4a;text-align:right"><?php echo $tax_amount;?></td></tr>

<?php }
$total_amount+=$tax_amount+$tax_amount_sgst; }?>
	<tr style="font-size:88%"><td style="border-left:.3px solid #4a4a4a;border-right:.3px solid #4a4a4a;;text-align:right" colspan="6"></td><td style="border:0px solid #4a4a4a;;text-align:right" colspan="2">Discount </td><td style="border-right:.3px solid #4a4a4a;text-align:right"><?php echo $total_discount;?></td></tr>
<tr style="font-size:88%"><td style="border-left:.3px solid #4a4a4a;border-right:.3px solid #4a4a4a;border-bottom:.3px solid #4a4a4a;text-align:right" colspan="6"></td><td style="border:0px solid #4a4a4a;;text-align:right;;border-bottom:.3px solid #4a4a4a;" colspan="2">TOTAL AMOUNT </td><td style="border-right:.3px solid #4a4a4a;text-align:right;;border-bottom:.3px solid #4a4a4a;"><?php echo $total_amount;?></td></tr>
<!--<tr><td style="border:.3px solid #4a4a4a">Transaction ID : <?php echo $trans_id;?> </td><td style="border:.3px solid #4a4a4a"> Trans Date <?php echo  $trans_date ;?></td></tr>-->
</table>
	<BR><CENTER><span style="text-align:center;font-size:85%;font-weight:bold;">TOTAL AMOUNT : INR. <?php echo numberToWords($total_amount);	?> ONLY</span></CENTER>
	</td></tr>
<tr><td>
<?php if($gst=="Y"){?>
	<table class="table table-bordered" style="width:100%;font-size:75%;border-collapse:collapse" border=1><tr>
<td style="border:1px solid #c0c0c0">HSN</td>
<td style="border:1px solid #c0c0c0">Taxable Value</td>
<?php if($gst=="Y"){ ;?>
<?PHP if(strncasecmp($company_gst, "37", strlen("37")) === 0){?>
<td style="border:1px solid #c0c0c0">CGST (9%)</td>
<td style="border:1px solid #c0c0c0">SGST (9%)</td>

<?php }else{ ?>
<td style="border:1px solid #c0c0c0">IGST (18%)</td>
<?php }?>
<?php }?>
</TR>
<?php $result = $conn->query($sql);
$tax_amount_sgst=0;
$tax_amount_cgst=0;
$total_amount=0;
while ($row = $result->fetch_assoc()) {$cost=$row["cost"];
$tax_amount_sgst+=$row["tax_amount_sgst"]	;
$total_amount+= $row["total"];;
$tax_amount_cgst+=$row["tax_amount"];?><tr>
<td><?php echo $row["hsn"];?></td>
<td><?php echo  $row["total"];;?></td>

<td><?php echo $row["tax_amount"];?></td>
<?PHP if(strncasecmp($company_gst, "37", strlen("37")) === 0){?>
<td><?php echo $row["tax_amount_sgst"];?></td>
<?php }?>
</tr>
<?php 	} ?>
	
	<tr>
		<td>Total</td>
	<td><?php echo $total_amount;?></td>
		<td><?php echo $tax_amount_cgst;?></td>
		<?PHP if(strncasecmp($company_gst, "37", strlen("37")) === 0){?>
<td><?php echo $tax_amount_sgst;?></td>
<?php }?>
	</tr>
</table>
<table style="width:100%; table-layout:fixed;border:.3px solid #4a4a4a;margin-top:7px;font-size:75%;border-collapse:collapse">
<tr>
<td style="border:.3px solid #4a4a4a">Bank name</td>
<td style="border:.3px solid #4a4a4a">Bank A/c </td>
<td style="border:.3px solid #4a4a4a">IFSC</td>
<td style="border:.3px solid #4a4a4a">Branch</td>
</tr>
<tr>
<td style="border:.3px solid #4a4a4a"><?php echo $company_bank;?></td>
<td style="border:.3px solid #4a4a4a"><?php echo $company_ac;?></td>
<td style="border:.3px solid #4a4a4a"><?php echo $company_ifsc;?></td>
<td style="border:.3px solid #4a4a4a"><?php echo $company_branch;?></td>
</tr>
</table>
<?php }?>
</td></tr>


</table>
<table style="width:100%;margin-top:34px">

<!--<tr>
<td colspan=3 style="border:.3px solid #4a4a4a">
Terms & Conditions<br>
1. Goods once sold cannot be taken back. 2. Manufacturingdefects will be replaced by the manufacturing company only. 3. No dealer guarantee manufacturing defect.<br>
4. All disputes are subject to Anakapalli jurisdiction.
</td>
</tr>-->
<tr>
<td style="text-align:center;font-size:75%"><br><br><br><br>__________________________________________<br>Customer/ Authorised Signatory</td>
<td style="text-align:center;font-size:75%"><br><br><br><br>__________________________________________<br>Service/Advisor Signature</td>
<td style="text-align:center;font-size:75%"><br><br><br><br>__________________________________________<br>Cashier/Authorised Signature</td>

</tr>
</table>
<script src="js/jquery-3.6.0.js"></script>
  <script src="js/jquery-ui.js"></script>
 <script src="js/html2pdf.bundle.min.js"></script>
 <script>
	
  $(document).ready(function () {
	var action="<?php echo $action;?>";
	  if(action=="DOWNLOAD"){
    var element = document.getElementById('content-to-pdf');

    var opt = {
        margin: 0.5,
        filename: 'Service_Receipt_EWCS.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: {
            scale: 1,
            scrollY: 0
        },
        jsPDF: {
            unit: 'in',
            format: 'letter',
            orientation: 'portrait'
        },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    };

    html2pdf()
        .set(opt)
        .from(element)
        .toPdf()
        .get('pdf')
        .then(function (pdf) {
            var pdfBlob = pdf.output('bloburl'); // create blob URL
            window.open(pdfBlob, '_blank');     // open in new tab
        });
	  }else{window.print();}
});

 </script>

</body>
</html>