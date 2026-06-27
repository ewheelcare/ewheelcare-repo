<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>


<html>
<body id="content-to-pdf">
<?php
include "db_config.php";
$dc_id=$_GET["dc_id"];
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

					$sql="SELECT dc_id, dc_date, COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, CUSTOMER_MOBILE,active_status,customer,trans_id  FROM delivery_challan where dc_id='".$dc_id."'";
//echo $sql;
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {$trans_id=$row["trans_id"]; 
$trans_date=$row["dc_date"]; 
$dc_id=$row["dc_id"]; 
 $customer=$row["customer"]; 
 $COMPANY_NAME=$row["COMPANY_NAME"]; 
 $CUSTOMER_NAME=$row["CUSTOMER_NAME"]; 
 $CUSTOMER_ADDRESS=$row["CUSTOMER_ADDRESS"]; 
 $CUSTOMER_GST=$row["CUSTOMER_GST"]; 
 $CUSTOMER_MOBILE=$row["CUSTOMER_MOBILE"]; 
 $customer_id=$row["customer"];
}
$sql="select trans_id,  DATE_FORMAT(m.trans_date, '%d-%m-%Y') AS trans_date, details, customer, trans_amount, pending, gst, tally, created_by, created_on, modified_by, modified_on, active_status,  COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, customer_mobile FROM sales_trans  m where m.active_status='A' and m.trans_id='".$trans_id."' order by 1";
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

}
?>
<table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr><td style="width:10%;border:1px solid #4a4a4a"><img src="img/expert_logo_final.png" style="width:70px"/></td><td style="width:70%;text-align:center;font-size:160%;background-color:#f0f0f0">
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
 Delivery Challan No :DC/<?php echo $dc_id;?><br>
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
 </td></tr></table></td></tr><tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a;font-size:70%;font-weight:bold">
<?php  $sql="SELECT D.SUBTRANS_ID,D.despatched,D.VEHICLE,D.ODOMETER,i.item_name,i.item_description FROM delivery_challan_det d, item i where i.item_id=d.item_id and d.dc_id='".$dc_id."' and d.active_status='A'";
				//echo $sql;
				$result = $conn->query($sql);
$tax_amount=0;
$total_amount=0;
$colspan=5;
?>
<tr>
<td style="border:1px solid #4a4a4a" >Sub Trans</td>
<td style="border:1px solid #4a4a4a">Item</td>
<td style="border:1px solid #4a4a4a">Qty</td>
<td style="border:1px solid #4a4a4a">Vehicle</td>
<td style="border:1px solid #4a4a4a">Odometer</td>
</tr>
<?php 
while ($row = $result->fetch_assoc()) {
	?>
<TR>
<TD style="border:1px solid #4a4a4a">
<?php echo $dc_id;?>, (<b><?php echo $row["SUBTRANS_ID"];?></b>)</td>
<td style="border:1px solid #4a4a4a"><b><?php echo $row["item_name"];?></b>
, <?php echo $row["item_description"];?>


</TD>
<td style="border:1px solid #4a4a4a;text-align:right"><?php echo $row["despatched"];?></td>
<td style="border:1px solid #4a4a4a"> <?php echo $row["VEHICLE"];?></td>
<td style="border:1px solid #4a4a4a;text-align:right"> <?php echo $row["ODOMETER"];?></td>
</tr>
<?php }?>
</table></td></tr>


</table>
<table style="width:100%;margin-top:25px">
<tr>
<td style="text-align:center">___________________________<br>Customer/ Authorised Signatory</td>
<td style="text-align:center">___________________________<br>Service/Advisor Signature</td>
<td style="text-align:center">___________________________<br>Cashier/Authorised Signature</td>

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