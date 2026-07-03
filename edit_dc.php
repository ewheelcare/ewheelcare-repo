<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include "db_config.php";

$dc_id = isset($_GET['dc_id'])
    ? mysqli_real_escape_string($conn,$_GET['dc_id'])
    : '';

if($dc_id==''){
    die("DC ID Missing");
}

$sql_hdr = "
SELECT
dc.*,
st.delivery_status,
st.COMPANY_NAME,
st.CUSTOMER_NAME,
st.CUSTOMER_MOBILE
FROM delivery_challan dc
LEFT JOIN sales_trans st
    ON st.trans_id = dc.trans_id
WHERE dc.dc_id='$dc_id'
";

$res_hdr = mysqli_query($conn,$sql_hdr);

if(!$res_hdr || mysqli_num_rows($res_hdr)==0){
    die("Invalid DC");
}

$hdr = mysqli_fetch_assoc($res_hdr);

$trans_id = $hdr['trans_id'];

$sql_vehicle = "
SELECT vehicle,
       odometer
FROM delivery_challan
WHERE dc_id='$dc_id'";

$res_vehicle = mysqli_query($conn,$sql_vehicle);
$vehicle_row = mysqli_fetch_assoc($res_vehicle);

// if($hdr['delivery_status']=='C'){
//     die("Delivery Completed. Edit Not Allowed.");
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "header_include.php"; ?>
</head>

<body id="page-top">

<div id="wrapper">

<?php include "sidemenu.php"; ?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<?php include "topmenu.php"; ?>

<div class="container-fluid">

<div class="alert alert-warning text-center font-weight-bold">
EDIT DELIVERY CHALLAN - DC/<?php echo $dc_id; ?>
</div>

<input type="hidden"
       id="dc_id"
       value="<?php echo $dc_id; ?>">

       <div class="card mb-3">
<div class="card-body">

<div class="row">

<div class="col-md-3">
<label>Trans ID</label>
<input class="form-control"
       readonly
       value="<?php echo $hdr['trans_id']; ?>">
</div>

<div class="col-md-3">
<label>Company Name</label>
<input class="form-control"
       readonly
       value="<?php echo $hdr['COMPANY_NAME']; ?>">
</div>

<div class="col-md-3">
<label>Customer Name</label>
<input class="form-control"
       readonly
       value="<?php echo $hdr['CUSTOMER_NAME']; ?>">
</div>

<div class="col-md-3">
<label>Mobile</label>
<input class="form-control"
       readonly
       value="<?php echo $hdr['CUSTOMER_MOBILE']; ?>">
</div>

</div>

<br>

<div class="row">

<div class="col-md-6">
<label>Vehicle No</label>
<input type="text"
       id="vehicle_no"
       class="form-control"
       value="<?php echo $vehicle_row['vehicle']; ?>">
</div>

<div class="col-md-6">
<label>Odometer Reading</label>
<input type="text"
       id="odometer"
       class="form-control"
       value="<?php echo $vehicle_row['odometer']; ?>">
</div>

</div>

</div>
</div>

<div class="card">

<div class="card-header">
Invoice : <?php echo $hdr['trans_id']; ?>
</div>

<div class="card-body">

<table class="table table-bordered table-sm">

<thead>
<tr>
<th>Item</th>
<th>Sale Qty</th>
<th>Despatched Qty</th>
</tr>
</thead>

<tbody>

<?php

// $sql_items = "
// SELECT
// d.dc_id,
// d.item_id,
// d.subtrans_id,
// d.despatched,
// i.item_name
// FROM delivery_challan_det d
// INNER JOIN item i
//     ON i.item_id=d.item_id
// WHERE d.dc_id='$dc_id'
// AND d.active_status='A'
// ORDER BY d.subtrans_id
// ";

$sql_items = "
SELECT
s.trans_id,
s.subtrans_id,
s.item_id,
s.qty sale_qty,
IFNULL(d.despatched,0) despatched,
i.item_name
FROM sales_trans_det s
INNER JOIN item i
    ON i.item_id=s.item_id
LEFT JOIN delivery_challan_det d
    ON d.dc_id='$dc_id'
   AND d.subtrans_id=s.subtrans_id
   AND d.item_id=s.item_id
WHERE s.trans_id='$trans_id'
AND s.ver = (
    SELECT MAX(ver)
    FROM sales_trans_det
    WHERE trans_id='$trans_id'
)
AND s.account='Y'
AND s.active_status='A'
ORDER BY s.subtrans_id
";

$res_items = mysqli_query($conn,$sql_items);

while($row=mysqli_fetch_assoc($res_items))
{
?>

<tr>

<td><?php echo $row['item_name']; ?></td>

<td>
<?php echo $row['sale_qty']; ?>
</td>

<td>
<input type="number"
       class="form-control qtybox"
       value="<?php echo $row['despatched']; ?>"
       min="0"
       max="<?php echo $row['sale_qty']; ?>"
       data-item="<?php echo $row['item_id']; ?>"
       data-sub="<?php echo $row['subtrans_id']; ?>">
</td>

</tr>

<?php
}
?>

</tbody>

</table>

<div class="text-right">

<button id="btnUpdateDC"
        class="btn btn-primary"
        onclick="updateDC()">
    Save Changes
</button>

</div>

</div>

</div>

</div>

<?php include "footer.php"; ?>

</div>
</div>
</div>

<?php include "footer_include.php"; ?>

<script src="js/jquery-3.5.1.min.js"></script>

<script>

function updateDC()
{
    if($("#btnUpdateDC").prop("disabled"))
    {
        return;
    }

    $("#btnUpdateDC").prop("disabled", true);

    let rows=[];

    $(".qtybox").each(function(){

        rows.push({
            item_id: $(this).data("item"),
            subtrans_id: $(this).data("sub"),
            qty: $(this).val()
        });

    });

    let validationFailed = false;

    $(".qtybox").each(function(){

        let entered = parseInt($(this).val()) || 0;
        let maxQty  = parseInt($(this).attr("max")) || 0;

        if(entered > maxQty)
        {
            alert("Despatched Qty cannot exceed Sale Qty");
            validationFailed = true;
            return false;
        }
    });

    if(validationFailed)
    {
        $("#btnUpdateDC").prop("disabled", false);
        return;
    }

    let vehicle = $("#vehicle_no").val().trim();
    let odometer = $("#odometer").val().trim();

    if(vehicle == "")
    {
        alert("Enter Vehicle No");
        $("#btnUpdateDC").prop("disabled", false);
        return;
    }

    if(odometer == "")
    {
        alert("Enter Odometer Reading");
        $("#btnUpdateDC").prop("disabled", false);
        return;
    }

    $.ajax({
        url:"update_dc.php",
        type:"POST",
        data:{
            dc_id: $("#dc_id").val(),
            vehicle: vehicle,
            odometer: odometer,
            rows: JSON.stringify(rows)
        },
        success:function(res)
{
    //alert(res);

    let response = JSON.parse(res);
            try
            {
                let response = JSON.parse(res);

                if(response.status=="SUCCESS")
                {
                    alert("DC Updated");

                    window.location.href =
                        "dc_history.php?trans_id=" +
                        response.trans_id +
                        "&msg=updated";
                }
                else
                {
                    $("#btnUpdateDC").prop("disabled", false);

                    alert(response.message || "Update Failed");
                }
            }
            catch(e)
            {
                $("#btnUpdateDC").prop("disabled", false);

                alert("Invalid Server Response");
            }
        },
        error:function()
        {
            $("#btnUpdateDC").prop("disabled", false);

            alert("Error while updating DC");
        }
    });
}

$(document).on("change", ".qtybox", function () {

    let entered = parseFloat($(this).val()) || 0;
    let maxQty  = parseFloat($(this).attr("max")) || 0;

    if (entered > maxQty)
    {
        alert("Despatched Qty cannot exceed Sale Qty (" + maxQty + ")");
        $(this).val(maxQty);
        $(this).focus();
    }
});
</script>

</body>
</html>