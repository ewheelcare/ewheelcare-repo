<head>
    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body id="content-to-pdf">
    <div class="no-print" style="background: #e52d27; padding: 10px; display: flex; justify-content: space-between; align-items: center; color: white; font-family: sans-serif;">
        <div style="font-weight: bold;">PRINT PREVIEW</div>
        <button type="button" onclick="window.location.href='sales.php'" 
            style="background: white; color: #b31217; border: none; padding: 5px 15px; border-radius: 4px; font-weight: bold; cursor: pointer;">
            ← BACK TO SALES LIST
        </button>
    </div>
<?php
include "db_config.php";
$trans_id=$_GET["trans_id"];
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
<tr><td style="width:10%;border:1px solid #4a4a4a">aaaa</td><td style="width:70%;text-align:center;font-size:160%;background-color:#f0f0f0">EXPERT  WHEEL SHOP <BR> DETAILS OF SHOP</td></tr>
</table></td></tr>
<tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr><td style="width:50%;border:1px solid #4a4a4a">Customer :<?php echo $company_name;?><br>
Address : <?php echo $company_address;?><br>
 </td><td style="width:50%;border:1px solid #4a4a4a">
 Name :<?php echo $owner_name;?><br>
Mobile : <?php echo $owner_mobile;?><br>
Aadhar : <?php echo $owner_aadhar;?><br>
 </td></tr></table></td></tr><tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<?php  $sql="SELECT trans_id,subtrans_id, i.item_id,i.item_name,i.item_description,d.cost,d.qty,d.tax,d.tax_amount,d.total,(d.tax_amount+d.total) full_amount FROM sales_trans_det d,item i where i.item_id=d.item_id and d.trans_id='".$trans_id."' and d.active_status='A'";
				//echo $sql;
				$result = $conn->query($sql);
$tax_amount=0;
$total_amount=0;
while ($row = $result->fetch_assoc()) {
	$tax=$row["tax"]	; 
$tax_amount+=$row["tax_amount"]	;
$total_amount+=$row["total"];
	?>
<TR>
<TD>
<b><?php echo $row["item_name"];?></b>
, <?php echo $row["item_description"];?>
(<b><?php echo $row["subtrans_id"];?></b>)

</TD>

<TD>
 Cost : <?php echo $row["cost"];?>* Qty :<?php echo  $row["qty"];?> = Rs.<?php echo $row["total"];?><br>

</TD>
</TR>
<?php }?>
<tr><td style="border:0px solid #4a4a4a">Total</td><td  style="border:0px solid #4a4a4a"><?php echo $total_amount;?></td></tr>
<?php if($gst=="Y"){?>
<tr><td  style="border:0px solid #4a4a4a">Tax (<?php echo $tax ;?> %)</td><td  style="border:0px solid #4a4a4a"><?php echo $tax_amount;?></td></tr>
<?php $total_amount+=$tax_amount;; }?>
<tr><td style="border:0px solid #4a4a4a">Total Amount payable </td><td style="border:0px solid #4a4a4a"><?php echo $total_amount;?></td></tr>
<tr><td style="border:1px solid #4a4a4a">Transaction ID : <?php echo $trans_id;?> </td><td style="border:1px solid #4a4a4a"> Trans Date <?php echo  $trans_date ;?></td></tr>
</table></td></tr></table>
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