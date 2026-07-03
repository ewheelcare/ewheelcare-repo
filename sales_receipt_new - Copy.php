<html>
<style>
table tbody tr td{border-collapse:collapse;}
	table{border:0px!important}
</style>
<body id="content-to-pdf">
<?php
include "db_config.php";
$trans_id=$_GET["trans_id"];
$shop=$_COOKIE["shop"];
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
$sql="select trans_id, DATE_FORMAT(m.trans_date, '%d-%m-%Y') AS trans_date, details, customer, trans_amount, pending, gst, tally, created_by, created_on, modified_by, modified_on, active_status,  COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, customer_mobile FROM sales_trans  m where m.active_status='A' and m.trans_id='".$trans_id."' order by 1";
//echo $sql;
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
	$customer= $row["customer"];
$gst=$row["gst"];
$company_name= $row["COMPANY_NAME"];
$company_address= $row["CUSTOMER_ADDRESS"];
$owner_name= $row["CUSTOMER_NAME"];
$owner_gst= $row["CUSTOMER_GST"];
$owner_mobile= $row["cusomer_mobile"];
$trans_date =$row["trans_date"];
$pending = $row["pending"];

}
?>
<table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a;min-height:610px">
<tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a;">
<tr><td style="width:20%;border:1px solid #4a4a4a"><img src="img/expert_logo.png" style="width:90px"/></td><td style="width:70%;text-align:center;font-size:190%;background-color:#f0f0f0">
<?php echo $company_name1;?><BR> <SPAN style="font-size:70%"><?php echo $company_address1;?><br> <?php echo $company_gst;?></SPAN></td></tr>
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
 Invoice No :SALES/<?php echo $trans_id;?><br>
Date  : <?php echo $trans_date;?><br>
 </td></tr>
<tr><td style="width:50%;border:1px solid #4a4a4a">Customer :<?php echo $company_name;?><br>
<?php if($gst=="Y"){?>
Customer GSTIN : <?php echo $owner_gst;?><br>
<?PHP }?>


 Owner :<?php echo $owner_name;?><br>

 </td><td style="width:50%;border:1px solid #4a4a4a">
Address : <?php echo $company_address;?><br>
 Mobile : <?php echo $owner_mobile;?>
 </td></tr></table></td></tr><tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a;font-size:55%;font-weight:bold">
<?php  $sql="SELECT trans_id,subtrans_id, i.item_id,i.item_name,i.item_description,i.hsn,d.cost,d.qty,d.tax,d.tax_amount,d.total,d.tax_sgst,d.tax_amount_sgst, (d.tax_amount+d.total+d.tax_amount_sgst) full_amount FROM sales_trans_det d,item i where i.item_id=d.item_id and d.trans_id='".$trans_id."' and d.active_status='A'";
				//echo $sql;
				$result = $conn->query($sql);
$tax_amount=0;
$total_amount=0;
$colspan=5;
?>
<tr>
<td style="border:1px solid #4a4a4a" >Sub Trans</td>
<td style="border:1px solid #4a4a4a;width:160px">Item</td>
<td style="border:1px solid #4a4a4a;width:160px">HSN</td>
<td style="border:1px solid #4a4a4a">Unit Price</td>
<td style="border:1px solid #4a4a4a">Qty</td>
<td style="border:1px solid #4a4a4a">Cost</td>
<?php if($gst=="Y"){ ;?>
<?PHP if(strncasecmp($company_gst, "37", strlen("37")) === 0){ $colspan=10?>
<td style="border:1px solid #4a4a4a">CGST<br>(%)</td>
<td style="border:1px solid #4a4a4a">CGST <br>Amount</td>
<td style="border:1px solid #4a4a4a">SGST<br>(5)</td>
<td style="border:1px solid #4a4a4a">SGST <br>Amount</td>
<?php }else{ $colspan=8?>
<td style="border:1px solid #4a4a4a">IGST<br>(%)</td>
<td style="border:1px solid #4a4a4a">IGST<br> Amount</td>
<?php }?>

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
<TD style="border:1px solid #4a4a4a;font-size:90%">
(<b><?php echo $row["subtrans_id"];?></b>)</td>
<td style="border:1px solid #4a4a4a;width:160px"><b><?php echo $row["item_name"];?></b>
, <?php echo $row["item_description"];?>


</TD>
<td style="border:1px solid #4a4a4a;width:160px"><?php echo $row["hsn"];?></td>
<td style="border:1px solid #4a4a4a;text-align:right"> <?php echo $row["cost"];?></td>
<td style="border:1px solid #4a4a4a;text-align:right"> <?php echo $row["qty"];?></td>
<td style="border:1px solid #4a4a4a;text-align:right"> <?php echo $row["total"];?></td>
<?php if($gst=="Y"){ if (strncasecmp($company_gst, "37", strlen("37")) === 0){?>
<td style="border:1px solid #4a4a4a;text-align:right"> <?php echo $row["tax"];?></td>
<td style="border:1px solid #4a4a4a;text-align:right"> <?php echo $row["tax_amount"];?></td>
<td style="border:1px solid #4a4a4a;text-align:right"> <?php echo $row["tax_sgst"];?></td>
<td style="border:1px solid #4a4a4a;text-align:right"> <?php echo $row["tax_amount_sgst"];?></td>
<?php }else {?>
<td style="border:1px solid #4a4a4a;text-align:right"> <?php echo $row["tax"];?></td>
<td style="border:1px solid #4a4a4a;text-align:right"> <?php echo $row["tax_amount"];?></td>
<?php }}?>
<td style="border:1px solid #4a4a4a;text-align:right"> Rs.<?php echo $row["total"]+$row["tax_sgst"]+$row["tax_amount_sgst"];?></td>
</TR>
<?php }?>
<tr><td style="border:0px solid #4a4a4a;text-align:right" colspan="<?php echo $colspan;?>">Total</td><td  style="border:0px solid #4a4a4a"><?php echo $total_amount;?></td></tr>
<?php if($gst=="Y"){ if(strncasecmp($company_gst, "37", strlen("37")) === 0){ ?>
<tr><td  style="border:0px solid #4a4a4a;text-align:right" colspan="<?php echo $colspan;?>">CGST Tax (<?php echo $tax ;?> %)</td><td  style="border:0px solid #4a4a4a"><?php echo $tax_amount;?></td></tr>
<tr><td  style="border:0px solid #4a4a4a;text-align:right" colspan="<?php echo $colspan;?>">SGST Tax (<?php echo $tax_sgst ;?> %)</td><td  style="border:0px solid #4a4a4a"><?php echo $tax_amount_sgst;?></td></tr>
<?php }else{?>
<tr><td  style="border:0px solid #4a4a4a;text-align:right" colspan="<?php echo $colspan;?>">IGST Tax (<?php echo $tax ;?> %)</td><td  style="border:0px solid #4a4a4a"><?php echo $tax_amount;?></td></tr>

<?php }
$total_amount+=$tax_amount+$tax_amount_sgst; }?>
<tr><td style="border:0px solid #4a4a4a;;text-align:right" colspan="<?php echo $colspan;?>">Total Amount </td><td style="border:0px solid #4a4a4a"><?php echo $total_amount;?></td></tr>
<!--<tr><td style="border:0px solid #4a4a4a;;text-align:right" colspan="<?php echo $colspan;?>">Amount Paid </td><td style="border:0px solid #4a4a4a"><?php echo $total_amount-$pending;?></td></tr>
<tr><td style="border:0px solid #4a4a4a;;text-align:right" colspan="<?php echo $colspan;?>">Amount Pending </td><td style="border:0px solid #4a4a4a"><?php echo  $pending;?></td></tr>-->
<!--<tr><td style="border:1px solid #4a4a4a">Transaction ID : <?php echo $trans_id;?> </td><td style="border:1px solid #4a4a4a"> Trans Date <?php echo  $trans_date ;?></td></tr>-->
</table></td></tr>
<tr><td>
<?php if($gst=="Y"){?>
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
<?php }?>
</td></tr>


</table>
<table style="width:100%;margin-top:25px">

<tr>
<td colspan=3 style="border:1px solid #4a4a4a">
Terms & Conditions<br>
1. Goods once sold cannot be taken back. 2. Manufacturingdefects will be replaced by the manufacturing company only. 3. No dealer guarantee manufacturing defect.<br>
4. All disputes are subject to Anakapalli jurisdiction.
</td>
</tr>
<tr>
<td style="text-align:center">___________________________<br>Customer/ Authorised Signatory</td>
<td style="text-align:center">___________________________<br>Service/Advisor Signature</td>
<td style="text-align:center">___________________________<br>Cashier/Authorised Signature</td>

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