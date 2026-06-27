<html>
<style>

body{
    font-family:Arial,sans-serif;
    margin:0;
    padding:4px;
    color:#000;
}

table{
    width:100%;
    border-collapse:collapse;
}

td{
    vertical-align:top;
    padding:3px 5px;
}

.main-border{
    border:1px dotted #666;
}

.dotted-border{
    border:1px dotted #888;
}

.header-title{
    font-size:24px;
    font-weight:bold;
    text-align:center;
}

.header-sub{
    font-size:18px;
    line-height:1.5;
    text-align:center;
}

.section-title{
    font-weight:bold;
    background:#f3f3f3;
    padding:2px;
}

.signature-box{
    height:70px;
    vertical-align:bottom;
    text-align:center;
    padding-top:20px;
}

.signature-line{
    border-top:1px dotted #555;
    width:80%;
    margin:auto;
    padding-top:6px;
    font-weight:bold;
}

.terms-list{
    margin-top:4px;
    padding-left:16px;
    line-height:1.2;
    font-size:12px;
}

.no-break{
    page-break-inside:avoid;
}

@media print{

    body{
        margin:0;
        padding:5px;
    }

    .no-break{
        page-break-inside:avoid;
    }

    /* table,tr,td{
        page-break-inside:avoid !important;
    } */

}

</style>
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
$sql="select trans_id, DATE_FORMAT(m.trans_date, '%d-%m-%Y') AS trans_date, details,ver, customer, trans_amount, pending, gst, tally, created_by, created_on, modified_by, modified_on, active_status,  COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, customer_mobile,year_part FROM sales_trans  m where m.active_status='A' and m.trans_id='".$trans_id."' order by 1";
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
$pending = $row["pending"];
$year_part=$row["year_part"];
$ver=$row["ver"];

$company_name =
trim($company_name) != ''
? $company_name
: 'NA - Not Provided';

$company_address =
trim($company_address) != ''
? $company_address
: 'NA - Not Provided';

$owner_name =
trim($owner_name) != ''
? $owner_name
: 'NA - Not Provided';

$owner_mobile =
trim($owner_mobile) != ''
? $owner_mobile
: 'NA - Not Provided';

$owner_gst =
trim($owner_gst) != ''
? $owner_gst
: 'NA - Not Provided';

}
?>
<!-- <table style="width:100%; table-layout:fixed;border:1px dotted #888;min-height:610px"> -->
<table class="main-border no-break"
style="
table-layout:fixed;
min-height:auto;
">
<tr><td><table style="width:100%; table-layout:fixed;border:1px dotted #888;">
<!-- <tr><td style="width:20%;border:1px dotted #888"><img src="img/expert_logo_final.png" style="height:90px"/></td><td style="width:70%;text-align:center;font-size:190%;background-color:#f0f0f0">
<?php echo $company_name1;?><BR> <SPAN style="font-size:70%"><?php echo $company_address1;?><br> <?php echo $company_gst;?></SPAN></td></tr> -->

<tr>

<td
style="
width:25%;
border:1px dotted #666;
text-align:center;
padding:4px;
">

<img
src="img/expert_logo_final.png"
style="
width:140px;
height:auto;
display:block;
margin:auto;
margin-top:8px;
">

</td>

<td
style="
width:75%;
border:1px dotted #666;
background:#f7f7f7;
padding:4px;
">

<div class="header-title">
<?php echo $company_name1;?>
</div>

<div class="header-sub">
<?php echo $company_address1;?>
<br style="line-height:4px">
<?php echo $company_gst;?>
</div>

</td>

</tr>

</table></td></tr>
<tr><td><table style="width:100%; table-layout:fixed;border:1px dotted #888">
<!-- <tr><td style="width:50%;border:1px dotted #888">Company :<?php echo $company_name1;?><br style="line-height:4px">
Address : <?php echo $company_address1;?><br style="line-height:4px">
<?php if($gst=="Y"){?>
GSTIN : <?php echo $company_gst;?><br style="line-height:4px">
<?PHP }?>
Contact :<?php echo $company_mobile;?><br style="line-height:4px">
Email:<?php echo $company_email;?><br style="line-height:4px">
 </td><td style="width:50%;border:1px dotted #888">
 Invoice No :<?php echo $year_part;?>/<?php echo $trans_id;?><br style="line-height:4px">
Date  : <?php echo $trans_date;?><br style="line-height:4px">
 </td></tr>
<tr><td style="width:50%;border:1px dotted #888">Customer :<?php echo $company_name;?><br style="line-height:4px">
<?php if($gst=="Y"){?>
Customer GSTIN : <?php echo $owner_gst;?><br style="line-height:4px">
<?PHP }?>


 Owner :<?php echo $owner_name;?><br style="line-height:4px">

 </td><td style="width:50%;border:1px dotted #888">
Address : <?php echo $company_address;?><br style="line-height:4px">
 Mobile : <?php echo $owner_mobile;?>
 </td></tr> -->

 <tr>

<td
style="
width:60%;
border:1px dotted #888;
padding:8px;
vertical-align:top;
">

<div
style="
font-size:20px;
font-weight:bold;
border-bottom:1px dotted #999;
padding-bottom:5px;
margin-bottom:4px;
">
Company Details
</div>

<table style="width:100%; font-size:14px; line-height:1.5;">

<tr>
<td style="width:22%; font-weight:bold;">
Company
</td>

<td>
: <?php echo $company_name1;?>
</td>
</tr>

<tr>
<td style="font-weight:bold;">
Address
</td>

<td>
: <?php echo $company_address1;?>
</td>
</tr>

<?php if($gst=="Y"){ ?>

<tr>
<td style="font-weight:bold;">
GSTIN
</td>

<td>
: <?php echo $company_gst;?>
</td>
</tr>

<?php } ?>

<tr>
<td style="font-weight:bold;">
Contact
</td>

<td>
: <?php echo $company_mobile;?>
</td>
</tr>

<tr>
<td style="font-weight:bold;">
Email
</td>

<td>
: <?php echo $company_email;?>
</td>
</tr>

</table>

</td>

<td
style="
width:35%;
border:1px dotted #888;
padding:8px;
vertical-align:top;
">

<div
style="
font-size:20px;
font-weight:bold;
border-bottom:1px dotted #999;
padding-bottom:5px;
margin-bottom:8px;
">
Invoice Details
</div>

<table style="width:100%; font-size:14px; line-height:1.7;">

<tr>
<td style="width:45%; font-weight:bold;">
Invoice No
</td>

<td style="white-space:nowrap;">
: <?php echo $year_part;?><?php echo $trans_id;?>
</td>
</tr>

<tr>
<td style="font-weight:bold;">
Invoice Date
</td>

<td>
: <?php echo $trans_date;?>
</td>
</tr>

</table>

</td>

</tr>

<tr>

<td colspan="2"
style="
border:1px dotted #888;
padding:8px;
">

<div
style="
font-size:17px;
font-weight:bold;
border-bottom:1px dotted #999;
padding-bottom:5px;
margin-bottom:8px;
">
Customer Details
</div>

<table style="width:100%; font-size:14px; line-height:1.2;">

<tr>

<td
style="
width:15%;
font-weight:bold;
padding:2px 4px;
">
Customer
</td>

<td style="width:35%;">
: <?php echo $company_name;?>
</td>

<td
style="
width:15%;
font-weight:bold;
padding:2px 4px;
">
Mobile
</td>

<td
style="
width:15%;
font-weight:bold;
padding:2px 4px;
">
: <?php echo $owner_mobile;?>
</td>

</tr>

<tr>

<td
style="
width:15%;
font-weight:bold;
padding:2px 4px;
">
Cust. GSTIN
</td>

<td>
: <?php echo $owner_gst;?>
</td>

<td
style="
width:15%;
font-weight:bold;
padding:2px 4px;
">
Owner
</td>

<td>
: <?php echo $owner_name;?>
</td>

</tr>

<tr>

<td
style="
width:15%;
font-weight:bold;
padding:2px 4px;
">
Address
</td>

<td colspan="3">
: <?php echo $company_address;?>
</td>

</tr>

</table>

</td>

</tr>

</table></td></tr><tr><td><table style="width:100%; table-layout:fixed;border:1px dotted #888;font-size:12px;font-weight:bold">
<?php  $sql="SELECT trans_id,subtrans_id,d.price, i.item_id,i.item_name,i.item_description,i.hsn,d.cost,d.qty,d.tax,d.tax_amount,d.total,d.tax_sgst,d.tax_amount_sgst,d.tax_igst, d.tax_amount_igst FROM sales_trans_det d,item i where i.item_id=d.item_id and d.trans_id='".$trans_id."' and d.active_status='A' and ver='".$ver."' and account='Y' and qty>0 and total>0 order by subtrans_id";
				//echo $sql;
				$result = $conn->query($sql);
$tax_amount=0;
$total_amount=0;
$colspan=5;
?>
<tr>
<td style="border:1px dotted #888;width:10%" >SLNO</td>
<td style="border:1px dotted #888;width:160px">Item</td>
<td style="border:1px dotted #888;width:160px">HSN</td>

<td style="border:1px dotted #888">Qty</td>
<td style="border:1px dotted #888">Rate</td>
<td style="border:1px dotted #888">Amount</td>
</tr>
<?php 
$slno=1;
while ($row = $result->fetch_assoc()) {
	$tax=$row["tax"]	; 
	$tax_sgst=$row["tax_sgst"]	; 
	$tax_igst=$row["tax_igst"]	; 
	$total_price+=$row["price"]	; 
$tax_amount+=$row["tax_amount"]	;
$tax_amount_sgst+=$row["tax_amount_sgst"]	;
$tax_amount_igst+=$row["tax_amount_igst"]	;
$total_amount+=$row["total"];
$total_qty+=$row["qty"];
	?>
<TR>
<TD style="border:1px dotted #888;font-size:90%">
<?php echo $slno++;?>
</td>
<td style="border:1px dotted #888;width:160px"><b><?php echo $row["item_name"];?></b>
, <?php echo $row["item_description"];?>


</TD>
<td style="border:1px dotted #888;width:160px"><?php echo $row["hsn"];?></td>
<td style="border:1px dotted #888;text-align:right"> <?php echo $row["qty"];?></td>
<td style="border:1px dotted #888;text-align:right"> <?php echo $row["cost"];?></td>

<td style="border:1px dotted #888;text-align:right"> <?php echo $row["price"];?></td>
</TR>
<?php }?>
<tr><td style="border:1px dotted #888;text-align:right" colspan="3">Total</td><td  style="border:1px dotted #888"><?php echo $total_qty;?></td><td  style="border:1px dotted #888"></td><td  style="border:none"><?php echo $total_price;?></td></tr>
<?php if($gst=="Y"){ if(strncasecmp($company_gst, "37", strlen("37")) === 0){ ?>
<tr><td  style="border:1px dotted #888;text-align:right" colspan="5">CGST Tax (<?php echo $tax ;?> %)</td><td  style="border:1px dotted #888"><?php echo $tax_amount;?></td></tr>
<tr><td  style="border:1px dotted #888;text-align:right" colspan="5">SGST Tax (<?php echo $tax_sgst ;?> %)</td><td  style="border:1px dotted #888"><?php echo $tax_amount_sgst;?></td></tr>
<?php }else{?>
<tr><td  style="border:1px dotted #888;text-align:right" colspan="5">IGST Tax (<?php echo $tax ;?> %)</td><td  style="border:1px dotted #888"><?php echo $tax_amount;?></td></tr>

<?php }
//$total_amount+=$tax_amount+$tax_amount_sgst; 
}?>
<tr><td style="border:1px dotted #888;;text-align:right" colspan="5">Total Amount </td><td style="border:1px dotted #888"><?php echo $total_amount;?></td></tr>
<!--<tr><td style="border:none;;text-align:right" colspan="<?php echo $colspan;?>">Amount Paid </td><td style="border:none"><?php echo $total_amount-$pending;?></td></tr>
<tr><td style="border:none;;text-align:right" colspan="<?php echo $colspan;?>">Amount Pending </td><td style="border:none"><?php echo  $pending;?></td></tr>-->
<!--<tr><td style="border:1px dotted #888">Transaction ID : <?php echo $trans_id;?> </td><td style="border:1px dotted #888"> Trans Date <?php echo  $trans_date ;?></td></tr>-->
</table>
	<br style="line-height:4px"><CENTER><span style="text-align:center;font-size:14px;font-weight:bold;">AMOUNT CHARGEABLE : INR. <?php echo numberToWords($total_amount);	?> ONLY</span></CENTER>
	
<br style="line-height:4px">
<table style="width:100%; table-layout:fixed;border:1px dotted #888;font-size:12px;font-weight:bold">
<tr>
<td style="border:1px dotted #888;width:25%">HSN</td>
<td style="border:1px dotted #888;">Taxable Amount</td>
<?php if(strncasecmp($company_gst, "37", strlen("37")) === 0){?>
<td style="border:1px dotted #888;">CGST(%)</td>
<td style="border:1px dotted #888;">CGST Amount</td>
<td style="border:1px dotted #888;">SGST(%)</td>
<td style="border:1px dotted #888;">SGST Amount</td>

<?php }else{?>
<td style="border:1px dotted #888;">IGST(%)</td>
<td style="border:1px dotted #888;">IGST Amount</td>

<?php }?>
<td style="border:1px dotted #888;">Total Tax</td>

</tr>
<?PHP  $sql="SELECT trans_id,subtrans_id,d.price, i.item_id,i.item_name,i.item_description,i.hsn,d.cost,d.qty,d.tax,d.tax_amount,d.total,d.tax_sgst,d.tax_amount_sgst,d.tax_igst, d.tax_amount_igst FROM sales_trans_det d,item i where i.item_id=d.item_id and d.trans_id='".$trans_id."' and d.active_status='A' and ver='".$ver."' and account='Y' and qty>0 and total>0 order by subtrans_id";
				//echo $sql;
				$result = $conn->query($sql);
$tax_amount=0;
$total_amount=0;
$tax_amount_sgst=0;
$tax_amount_igst=0;
$total_price=0;
$total_tax=0;
while ($row = $result->fetch_assoc()) {
	$tax=$row["tax"]	; 
	$tax_sgst=$row["tax_sgst"]	; 
	$tax_igst=$row["tax_igst"]	; 
	$total_price+=$row["price"]	; 
$tax_amount+=$row["tax_amount"]	;
$tax_amount_sgst+=$row["tax_amount_sgst"]	;
$tax_amount_igst+=$row["tax_amount_igst"]	;
$total_amount+=$row["total"];
$total_qty+=$row["qty"];
$total_tax+= $row["tax_amount"]	+$row["tax_amount_sgst"]+$row["tax_amount_igst"];
?>
<TR>
<td style="border:1px dotted #888;width:160px"><?php echo $row["hsn"];?></td>
<td style="border:1px dotted #888;width:160px"><?php echo $row["price"];?></td>
<?php if(strncasecmp($company_gst, "37", strlen("37")) === 0){?>
<td style="border:1px dotted #888;width:160px"><?php echo $row["tax"];?></td>
<td style="border:1px dotted #888;width:160px"><?php echo $row["tax_amount"];?></td>
<td style="border:1px dotted #888;width:160px"><?php echo $row["tax_sgst"];?></td>
<td style="border:1px dotted #888;width:160px"><?php echo $row["tax_amount_sgst"];?></td>

<?PHP }ELSE{?>
<td style="border:1px dotted #888;width:160px"><?php echo $row["tax_igst"];?></td>
<td style="border:1px dotted #888;width:160px"><?php echo $row["tax_amount_igst"];?></td>

<?PHP }?>
<td style="border:1px dotted #888;width:160px"><?php echo $row["tax_amount"]+$row["tax_amount_sgst"]+$row["tax_amount_igst"];?></td>

</TR>
<?PHP }?>
<tr>
<td style="border:1px dotted #888;width:160px"></td>
<td style="border:1px dotted #888;width:160px"><?php echo $total_price;?></td>
<?php if(strncasecmp($company_gst, "37", strlen("37")) === 0){?>
<td style="border:1px dotted #888;width:160px"></td>
<td style="border:1px dotted #888;width:160px"><?php echo $tax_amount;?></td>
<td style="border:1px dotted #888;width:160px"></td>
<td style="border:1px dotted #888;width:160px"><?php echo $tax_amount_sgst;?></td>
<?php }else{?>
<td style="border:1px dotted #888;width:160px"></td>
<td style="border:1px dotted #888;width:160px"><?php echo $tax_amount_igst;?></td>
<?php }?>
<td style="border:1px dotted #888;width:160px"><?php echo $total_tax;?></td>
</tr>
<table>
	<br style="line-height:4px"><CENTER><span style="text-align:center;font-size:14px;font-weight:bold;">TOTAL TAX : INR. <?php echo numberToWords($total_tax);	?> ONLY</span></CENTER>

</td></tr>
<tr><td>
<?php if($gst=="Y"){?>
<!-- <table style="width:100%; table-layout:fixed;border:1px dotted #888">
<tr>
<td style="border:1px dotted #888">Bank name</td>
<td style="border:1px dotted #888">Bank A/c </td>
<td style="border:1px dotted #888">IFSC</td>
<td style="border:1px dotted #888">Branch</td>
</tr>
<tr>
<td style="border:1px dotted #888"><?php echo $company_bank;?></td>
<td style="border:1px dotted #888"><?php echo $company_ac;?></td>
<td style="border:1px dotted #888"><?php echo $company_ifsc;?></td>
<td style="border:1px dotted #888"><?php echo $company_branch;?></td>
</tr>
</table> -->
<?php }?>
</td></tr>


</table>
<table style="width:100%;margin-top:5px">
<tr>

<td colspan="3"
class="dotted-border"
style="padding:2px;">

<div class="section-title">
Terms & Conditions
</div>

<ol class="terms-list">

<li>
Goods once sold cannot be taken back.
</li>

<li>
Manufacturing defects will be replaced
by the manufacturing company only.
</li>

<li>
No dealer guarantee for manufacturing defects.
</li>

<li>
All disputes are subject to
Anakapalli jurisdiction only.
</li>

</ol>

</td>

</tr>

<tr>

<td class="signature-box">

<div class="signature-line">
Customer / Authorised Signatory
</div>

</td>

<td class="signature-box">

<div class="signature-line">
Service Advisor Signature
</div>

</td>

<td class="signature-box">

<div class="signature-line">
Cashier / Authorised Signature
</div>

</td>

</tr>
<!-- <tr>
<td style="text-align:center">___________________________<br>Customer/ Authorised Signatory</td>
<td style="text-align:center">___________________________<br>Service/Advisor Signature</td>
<td style="text-align:center">___________________________<br>Cashier/Authorised Signature</td>

</tr> -->
</table>
<script src="js/jquery-3.6.0.js"></script>
  <script src="js/jquery-ui.js"></script>
 <script src="js/html2pdf.bundle.min.js"></script>

 <script>

$(document).ready(function () {

    var element =
    document.getElementById('content-to-pdf');

    var opt = {

        margin: 0.2,

        filename: 'Trans_Receipt_CCRS.pdf',

        image: {
            type: 'jpeg',
            quality: 0.98
        },

        html2canvas: {
            scale: 1.2,
            scrollY: 0
        },

        jsPDF: {
            unit: 'in',
            format: 'a4',
            orientation: 'portrait'
        },

        //
        // REMOVE avoid-all
        //
        pagebreak: {
            mode: ['css', 'legacy']
        }

    };

    html2pdf()
    .set(opt)
    .from(element)
    .save();

});

</script>
 <!-- <script>
  $(document).ready(function () {
   
      var element = document.getElementById('content-to-pdf');

      var opt = {
        margin:       0.2,
        filename:     'Trans_Receipt_CCRS.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  {
          scale: 1.5,
          scrollY: 0
        },
        jsPDF:        {
          unit: 'in',
          format: 'a4',
          orientation: 'portrait'
        },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
      };

      html2pdf().set(opt).from(element).save();
    
  });
 </script> -->

</body>
</html>