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
 </td></tr></table></td></tr>
 <tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<tr><td style="width:50%;border:1px solid #4a4a4a">Customer :<?php echo $company_name;?><br>
Address : <?php echo $company_address;?><br>
 </td><td style="width:50%;border:1px solid #4a4a4a">
 Name :<?php echo $owner_name;?><br>
Mobile : <?php echo $owner_mobile;?><br>
Aadhar : <?php echo $owner_aadhar;?><br>
 </td></tr></table></td></tr>
 <tr><td><table style="width:100%; table-layout:fixed;border:1px solid #4a4a4a">
<?php  $sql="SELECT trans_id,subtrans_id, i.item_id,i.item_name,i.item_description,d.cost,d.qty,d.tax,d.tax_amount,d.total,(d.tax_amount+d.total) full_amount,d.vehicle,v.vehicle_no,i.tyre_type_name FROM sales_trans_det d,item i,vehicle v where i.item_id=d.item_id and v.vehicle_id=d.vehicle and d.trans_id='".$trans_id."' and d.active_status='A'";
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
Item : <b><?php echo $row["item_name"];?></b>
, <?php echo $row["item_description"];?>

</TD>
<td><b>Vehicle : <?php echo $row["vehicle_no"];?></b></td></tr>
<tr><TD>
  Qty :<?php echo  $row["qty"];?> 

</TD>
<TD>
  Tyre  :<?php echo  $row["tyre_type_name"];?> 

</TD>
</TR>
<?php }?>