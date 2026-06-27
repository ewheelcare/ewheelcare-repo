<html>
<style>
table tbody tr td{border-collapse:collapse;}
@media print {
    .no-print { display: none !important; }
}
</style>
<body id="content-to-pdf">
    <div class="no-print" style="background: #e52d27; padding: 10px; display: flex; justify-content: space-between; align-items: center; color: white; font-family: sans-serif;">
        <div style="font-weight: bold;">JOB CARD PREVIEW</div>
        <button type="button" onclick="window.location.href='service.php'" 
            style="background: white; color: #b31217; border: none; padding: 5px 15px; border-radius: 4px; font-weight: bold; cursor: pointer;">
            ← BACK TO SERVICE LIST
        </button>
    </div>
<?php
include "db_config.php";
$trans_id=$_GET["trans_id"];
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
$sql="select trans_id, DATE_FORMAT(m.trans_date, '%d-%m-%Y') AS trans_date, details, customer, trans_amount, pending, gst, tally, created_by, created_on, modified_by, modified_on, active_status, VEHICLE_NO, VEHICLE_MODEL, NO_OF_WHEELS, COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, VEHICLE_ODOMETER, VEHICLE, paid_amount, ref_no, pay_type,customer_mobile,vehicle_make FROM service_trans  m where m.active_status='A' and m.trans_id='".$trans_id."' order by 1";
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
$trans_date =$row["trans_date"];
$vehicle_no= $row["VEHICLE_NO"];
$vehicle_model= $row["VEHICLE_MODEL"];
$vehicle_make= $row["VEHICLE_MAKE"];
$no_of_wheels= $row["NO_OF_WHEELS"];
$vehicle_odometer= $row["VEHICLE_ODOMETER"];
}
?>
<table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr><td style="width:10%;border:1px solid #4a4a4a"><img src="img/expert_logo.png" style="width:70px"/></td><td style="width:70%;text-align:center;font-size:160%;background-color:#f0f0f0">
<?php echo $company_name1;?><BR> <?php echo $company_address1;?><br> <?php echo $company_gst;?></td></tr>
</table></td></tr>
<tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr><td style="width:50%;border:1px solid #4a4a4a">Company :<?php echo $company_name1;?><br>
Address : <?php echo $company_address1;?><br>
<?php if($gst=="Y"){?>
GSTIN : <?php echo $company_gst;?><br>
<?PHP }?>
Contact :<?php echo $company_mobile;?><br>
Email:<?php echo $company_email;?><br>
 </td><td style="width:50%;border:1px solid #4a4a4a">
 Invoice No :SERV/<?php echo $trans_id;?><br>
Date  : <?php echo $trans_date;?><br>
Job Card No : JOB/<?php echo $trans_id;?><br>
 </td></tr>
<tr><td style="width:50%;border:1px solid #4a4a4a">Customer :<?php echo $company_name;?><br>
Address : <?php echo $company_address;?><br>

 Owner :<?php echo $owner_name;?><br>
<?php if($gst=="Y"){?>
GST: <?php echo $owner_gst;?><br>
<?PHP }?>
Mobile : <?php echo $owner_mobile;?><br>
 </td><td style="width:50%;border:1px solid #4a4a4a">

 Vehicle : <?php echo $vehicle_no;?>, <?php echo $vehicle_model;?><br>
 Make : <?php echo $vehicle_make;?><br>
Run in km : <?php echo $vehicle_odometer;?><br>
No of Wheels : <?php echo $no_of_wheels;?><br>
 </td></tr></table></td></tr><tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a;font-size:70%;font-weight:bold">
<?php  $sql="SELECT trans_id,subtrans_id, i.service_id,i.service_name,i.service_description,i.hsn,d.cost,d.qty,d.tax,d.tax_amount,d.total,d.tax_sgst,d.tax_amount_sgst, (d.tax_amount+d.total+d.tax_amount_sgst) full_amount FROM service_trans_det d,service i where i.service_id=d.service_id and d.trans_id='".$trans_id."' and d.active_status='A'";
				//echo $sql;
				$result = $conn->query($sql);
$tax_amount=0;
$total_amount=0;
$colspan=5;
?>
<tr>
<td style="border:1px solid #4a4a4a" >Sub Trans</td>
<td style="border:1px solid #4a4a4a">Service</td>

</tr>
<?php 
while ($row = $result->fetch_assoc()) {
	$tax=$row["tax"]	; 
	$tax_sgst=$row["tax_sgst"]	; 
$tax_amount+=$row["tax_amount"]	;
$tax_amount_sgst+=$row["tax_amount_sgst"]	;
$total_amount+=$row["total"];
	?>
<TR>
<TD style="border:1px solid #4a4a4a">
(<b><?php echo $row["subtrans_id"];?></b>)</td>
<td style="border:1px solid #4a4a4a"><b><?php echo $row["service_name"];?></b>
, <?php echo $row["service_description"];?>


</TD></tr>
<?php }?>


</table>
<table style="width:100%;margin-top:25px">
<tr>
<td style="text-align:right" colspan=3>___________________________<br>Signature of Mechanic</td>

</tr>
<tr>
<td colspan=3 style="border:1px solid #4a4a4a">
Terms & Conditions<br>
1. Goods once sold cannot be taken back. 2. Manufacturingdefects will be replaced by the manufacturing company only. 3. No dealer guarantee manufacturing defect.<br>
4. All disputes are subject to Anakapalli jurisdiction.
</td>
</tr>

</table>
<script src="js/jquery-3.6.0.js"></script>
  <script src="js/jquery-ui.js"></script>
 <script src="js/html2pdf.bundle.min.js"></script>
 <script>
  $(document).ready(function () {
   
      var element = document.getElementById('content-to-pdf');

      var opt = {
        margin:       0.5,
        filename:     'Trans_Receipt_CCRS.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  {
          scale: 2,
          scrollY: 0
        },
        jsPDF:        {
          unit: 'in',
          format: 'letter',
          orientation: 'portrait'
        },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
      };

      html2pdf().set(opt).from(element).save();
    
  });
 </script>

</body>
</html>