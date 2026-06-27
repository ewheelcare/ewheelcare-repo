<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include "db_config.php";

$dc_id=$_GET["dc_id"];
$shop=$_COOKIE["shop"];

$sql="select * from company";
$result=$conn->query($sql);
if($row=$result->fetch_assoc()){
    $company_name1=$row["company_name"];
    $company_address1=$row["company_address"];
    $company_gst=$row["company_gst"];
    $company_mobile=$row["company_mobile"];
    $company_email=$row["company_email"];
}

$sql="select * from shop where shop_id='".$shop."'";
$result=$conn->query($sql);
if($row=$result->fetch_assoc()){
    $company_address1=$row["address"];
}

$vehicle='';
$odometer='';

$sql="SELECT * FROM delivery_challan WHERE dc_id='".$dc_id."'";
$result=$conn->query($sql);
if($row=$result->fetch_assoc()){
    $trans_id=$row["trans_id"];
    $vehicle=$row["vehicle"];
    $odometer=$row["odometer"];
}



// $sql="SELECT vehicle,odometer
//       FROM delivery_challan_det
//       WHERE dc_id='$dc_id'
//       LIMIT 1";

// $result=$conn->query($sql);

// if($row=$result->fetch_assoc()){
//     $vehicle=$row['vehicle'];
//     $odometer=$row['odometer'];
// }

$sql="SELECT DATE_FORMAT(trans_date,'%d-%m-%Y') trans_date,
      gst,COMPANY_NAME,CUSTOMER_NAME,CUSTOMER_ADDRESS,
      CUSTOMER_GST,customer_mobile
      FROM sales_trans
      WHERE active_status='A' AND trans_id='".$trans_id."'";
$result=$conn->query($sql);

if($row=$result->fetch_assoc()){
    $gst=$row["gst"];
    $company_name=$row["COMPANY_NAME"];
    $company_address=$row["CUSTOMER_ADDRESS"];
    $owner_name=$row["CUSTOMER_NAME"];
    $owner_gst=$row["CUSTOMER_GST"];
    $owner_mobile=$row["customer_mobile"];
    $trans_date=$row["trans_date"];
}
?>
<html>
<head>
<meta charset="utf-8">
<style>
body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#222;margin:8px}
.banner{border:2px dashed #555;background:#f4f4f4;padding:8px;text-align:center;font-size:22px;font-weight:bold;letter-spacing:3px;margin-bottom:8px}
.header-table{width:100%;border-collapse:collapse}
.header-table td{padding:8px;vertical-align:middle}
.logo-cell{width:12%}
.company-cell{width:88%}
.logo{
    width:220px;
    max-width:none;
}
.company-name{font-size:24px;font-weight:bold;margin:0}
.company-info{font-size:13px;line-height:1.5;margin-top:5px}

.dc-highlight{
border:2px dashed #444;
background:#fafafa;
text-align:center;
padding:10px;
margin:10px 0;
border-radius:8px;
}
.dc-no{font-size:28px;font-weight:bold;letter-spacing:2px}
.dc-date{font-size:13px}

.grid{width:100%;border-spacing:6px}
.grid td{padding:4px;vertical-align:top}

.card{
border:1px dashed #777;
border-radius:8px;
padding:8px;
background:#fcfcfc;
min-height:75px;
font-size:12px;
}
.card-title{font-size:14px;font-weight:bold;margin-bottom:6px}

.section-title{
font-size:16px;
padding:6px;
margin:10px 0;
text-align:center;
font-weight:bold;
border-top:2px dashed #777;
border-bottom:2px dashed #777;
background:#fafafa;
}

.tbl{width:100%;border-collapse:collapse}
.tbl th{
background:#efefef;
border:1px dotted #777;
padding:5px;
font-size:13px;
}
.tbl td{
border:1px dotted #777;
padding:5px;
font-size:12px;
}

.footer-banner{
margin-top:10px;
padding:6px;
font-size:12px;
text-align:center;
border:1px dashed #777;
font-weight:bold;
background:#fafafa;
}

.sign-row{width:100%;margin-top:20px}
.sign-row td{text-align:center}
.sign-line{
width:180px;
font-size:11px;
margin:auto;
border-top:1px dotted #444;
padding-top:6px;
}

.terms{
margin-top:10px;
padding:8px;
font-size:11px;
border:1px dashed #ccc;
background:#fafafa;
}

.dc-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 10px 0;
    gap: 20px;
    flex-wrap: wrap;
}

.dc-date,
.dc-vehicle,
.dc-odometer {
    font-size: 14px;
}
</style>
</head>
<body id="content-to-pdf">

<!-- <div class="banner">🚚 DELIVERY CHALLAN 🚚</div> -->

<table class="header-table">
<tr>
<td class="logo-cell">
<img src="img/expert_logo_final.png" class="logo">
</td>
<td class="company-cell">
<div class="company-name"><?php echo $company_name1; ?></div>
<div class="company-info">
📍 <?php echo $company_address1; ?><br>
🧾 GSTIN : <?php echo $company_gst; ?><br>
☎ <?php echo $company_mobile; ?><br>
✉ <?php echo $company_email; ?>
</div>
</td>
</tr>
</table>

<!-- <div class="dc-highlight">
<div class="dc-no">🚚 DELIVERY CHALLAN NO : DC/<?php echo $dc_id; ?></div>
<div class="dc-date">📅 Date : <?php echo $trans_date; ?></div>
</div> -->

<div class="dc-highlight">

<div class="dc-no">
🧾 INVOICE NO : <?php echo $trans_id;?>
</div>

<div class="dc-no">
🚚 DELIVERY CHALLAN NO : DC/<?php echo $dc_id; ?>
</div>

<div class="dc-info-row">
    <div class="dc-date">
        📅 Date : <?php echo $trans_date; ?>
    </div>

    <div class="dc-vehicle">
        🚚 Vehicle No :
        <b><?php echo $vehicle; ?></b>
    </div>

    <div class="dc-odometer">
        🛣️ Odometer :
        <b><?php echo $odometer; ?></b>
    </div>
</div>
</div>

<table class="grid">
<tr>
<td>
<div class="card">
<div class="card-title">🏢 Company Details</div>
<?php echo $company_name1; ?><br>
<?php echo $company_address1; ?>
</div>
</td>

<td>
<div class="card">
<div class="card-title">🚚 Delivery Details</div>
Generated Delivery Challan<br>
Reference : DC/<?php echo $dc_id; ?>
</div>
</td>
</tr>

<tr>
<td>
<div class="card">
<div class="card-title">👤 Customer</div>
<?php echo $company_name; ?><br>
Owner : <?php echo $owner_name; ?>
</div>
</td>

<td>
<div class="card">
<div class="card-title">📍 Address</div>
<?php echo $company_address; ?><br>
📱 <?php echo $owner_mobile; ?>
</div>
</td>
</tr>
</table>

<div class="section-title">📦 DELIVERED ITEMS</div>

<table class="tbl">
<tr>
<th>🔖 Item ID</th>
<th>📦 Item</th>
<th>🔢 Qty</th>
<!-- <th>🚚 Vehicle</th>
<th>🛣️ Odometer</th> -->
</tr>

<?php
$sql="SELECT D.SUBTRANS_ID,D.despatched,
      i.item_name,i.item_description,i.item_id
      FROM delivery_challan_det d,item i
      WHERE i.item_id=d.item_id
      AND d.dc_id='".$dc_id."'
      AND d.active_status='A'";
$result=$conn->query($sql);

while($row=$result->fetch_assoc()){
?>
<tr>
<!-- <td><?php echo $dc_id; ?> (<?php echo $row["item_id"]; ?>)</td> -->
<td><?php echo $row["item_id"]; ?></td>
<td><b><?php echo $row["item_name"]; ?></b><br><?php echo $row["item_description"]; ?></td>
<td align="right"><?php echo $row["despatched"]; ?></td>
<!-- <td><?php echo $row["VEHICLE"]; ?></td>
<td align="right"><?php echo $row["ODOMETER"]; ?></td> -->
</tr>
<?php } ?>
</table>

<div class="footer-banner">
🚚 GOODS HANDED OVER IN GOOD CONDITION 🚚
</div>

<table class="sign-row">
<tr>
<td><div class="sign-line">Customer / Authorised Signatory</div></td>
<td><div class="sign-line">Service / Advisor Signature</div></td>
<td><div class="sign-line">Cashier / Authorised Signature</div></td>
</tr>
</table>

<div class="terms">
<b>Terms & Conditions</b><br>
1. Goods once sold cannot be taken back.<br>
2. Manufacturing defects will be handled by manufacturer policy.<br>
3. No dealer guarantee on manufacturing defects.<br>
4. All disputes are subject to Anakapalli jurisdiction.
</div>

</body>
</html>