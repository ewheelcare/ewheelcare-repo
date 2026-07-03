<html>
<body id="content-to-pdf">
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
$sql="SELECT trans_id,trans_date,details,trans_amount,pending,m.customer,m.gst,v.company_name,v.company_address,v.owner_name,v.owner_mobile,v.owner_aadhar FROM sales_trans m,customer v where v.customer_id=m.customer and m.active_status='A' and m.trans_id='".$trans_id."' order by 1";
//echo $sql;
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
	$customer= $row["customer"];
$gst=$row["gst"];
$company_name= $row["company_name"];
$company_address= $row["company_address"];
$owner_name= $row["owner_name"];
$owner_mobile= $row["owner_mobile"];
$owner_aadhar= $row["owner_aadhar"];
$trans_date =$row["trans_date"];
}
?>
<table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr><td style="width:10%;border:1px solid #4a4a4a">aaaa</td><td style="width:70%;text-align:center;font-size:160%;background-color:#f0f0f0">
<?php echo $company_name1;?><BR> <?php echo $company_address1;?><br> <?php echo $company_gst;?></td></tr>
</table></td></tr>
<tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr><td style="width:50%;border:1px solid #4a4a4a">Company :<?php echo $company_name1;?><br>
Address : <?php echo $company_address1;?><br>
GSTIN : <?php echo $company_gst;?><br>
Contact :<?php echo $company_mobile;?><br>
Email:<?php echo $company_email;?><br>
 </td><td style="width:50%;border:1px solid #4a4a4a">
 Invoice No :SALES/<?php echo $trans_id;?><br>
Date  : <?php echo $trans_date;?><br>
 </td></tr>
<tr><td style="width:50%;border:1px solid #4a4a4a">Customer :<?php echo $company_name;?><br>
Address : <?php echo $company_address;?><br>
 </td><td style="width:50%;border:1px solid #4a4a4a">
 Name :<?php echo $owner_name;?><br>
Mobile : <?php echo $owner_mobile;?><br>
Aadhar : <?php echo $owner_aadhar;?><br>
 </td></tr></table></td></tr><tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a;font-size:70%;font-weight:bold">
<?php  $sql="SELECT trans_id,subtrans_id, i.item_id,i.item_name,i.item_description,i.hsn,d.cost,d.qty,d.tax,d.tax_amount,d.total,d.tax_sgst,d.tax_amount_sgst, (d.tax_amount+d.total+d.tax_amount_sgst) full_amount FROM sales_trans_det d,item i where i.item_id=d.item_id and d.trans_id='".$trans_id."' and d.active_status='A'";
				//echo $sql;
				$result = $conn->query($sql);
$tax_amount=0;
$total_amount=0;
$colspan=5;
?>
<tr>
<td style="border:1px solid #4a4a4a" >Sub Trans</td>
<td style="border:1px solid #4a4a4a">Service</td>
<td style="border:1px solid #4a4a4a">HSN</td>
<td style="border:1px solid #4a4a4a">Unit Price</td>
<td style="border:1px solid #4a4a4a">Qty</td>
<td style="border:1px solid #4a4a4a">Cost</td>
<?php if($gst=="Y"){ $colspan=10;?>
<td style="border:1px solid #4a4a4a">CGST(%)</td>
<td style="border:1px solid #4a4a4a">CGST Amount</td>
<td style="border:1px solid #4a4a4a">SGST</td>
<td style="border:1px solid #4a4a4a">CGST Amount</td>
<?php }?>
<td style="border:1px solid #4a4a4a">Total</td>
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
<td style="border:1px solid #4a4a4a"><b><?php echo $row["item_name"];?></b>
, <?php echo $row["item_description"];?>


</TD>
<td style="border:1px solid #4a4a4a"><?php echo $row["hsn"];?></td>
<td style="border:1px solid #4a4a4a"> <?php echo $row["cost"];?></td>
<td style="border:1px solid #4a4a4a"> <?php echo $row["qty"];?></td>
<td style="border:1px solid #4a4a4a"> <?php echo $row["total"];?></td>
<?php if($gst=="Y"){?>
<td style="border:1px solid #4a4a4a"> <?php echo $row["tax"];?></td>
<td style="border:1px solid #4a4a4a"> <?php echo $row["tax_amount"];?></td>
<td style="border:1px solid #4a4a4a"> <?php echo $row["tax_sgst"];?></td>
<td style="border:1px solid #4a4a4a"> <?php echo $row["tax_amount_sgst"];?></td>
<?php }?>
<td style="border:1px solid #4a4a4a"> Rs.<?php echo $row["total"]+$row["tax_amount"]+$row["tax_amount_sgst"];?></td>
</TR>
<?php }?>
<tr><td style="border:0px solid #4a4a4a;text-align:right" colspan="<?php echo $colspan;?>">Total</td><td  style="border:0px solid #4a4a4a"><?php echo $total_amount;?></td></tr>
<?php if($gst=="Y"){?>
<tr><td  style="border:0px solid #4a4a4a;text-align:right" colspan="<?php echo $colspan;?>">CGST Tax (<?php echo $tax ;?> %)</td><td  style="border:0px solid #4a4a4a"><?php echo $tax_amount;?></td></tr>
<tr><td  style="border:0px solid #4a4a4a;text-align:right" colspan="<?php echo $colspan;?>">SGST Tax (<?php echo $tax_sgst ;?> %)</td><td  style="border:0px solid #4a4a4a"><?php echo $tax_amount_sgst;?></td></tr>

<?php $total_amount=$total_amount+$tax_amount+$tax_amount_sgst; }?>
<tr><td style="border:0px solid #4a4a4a;;text-align:right" colspan="<?php echo $colspan;?>">Total Amount payable </td><td style="border:0px solid #4a4a4a"><?php echo $total_amount;?></td></tr>
<!--<tr><td style="border:1px solid #4a4a4a">Transaction ID : <?php echo $trans_id;?> </td><td style="border:1px solid #4a4a4a"> Trans Date <?php echo  $trans_date ;?></td></tr>-->
</table></td></tr>
<tr><td>
<table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr>
<td style="border:1px solid #4a4a4a">Bank name</td>
<td style="border:1px solid #4a4a4a">Bank A/c </td>
<td style="border:1px solid #4a4a4a">IFSC</td>
<td style="border:1px solid #4a4a4a">Branch</td>
</tr>
<tr>
<td style="border:1px solid #4a4a4a"><?php echo $company_bank;?></td>
<td style="border:1px solid #4a4a4a"><?php echo $company_ac;?></td>
<td style="border:1px solid #4a4a4a"><?php echo $company_ifsc;?></td>
<td style="border:1px solid #4a4a4a"><?php echo $company_branch;?></td>
</tr>
</table>
</td></tr>


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