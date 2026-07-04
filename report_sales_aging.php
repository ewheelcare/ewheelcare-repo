<?php

if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include "db_config.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>

<?php include "header_include.php"; ?>
<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<style>

.table-aging{
    font-size:85%;
    font-weight:bold;
    text-transform:uppercase;
}

.bucket1{
    background:#d4edda;
    color:#155724;
}

.bucket2{
    background:#d1ecf1;
    color:#0c5460;
}

.bucket3{
    background:#fff3cd;
    color:#856404;
}

.bucket4{
    background:#ffe5d0;
    color:#c05600;
}

.bucket5{
    background:#f8d7da;
    color:#721c24;
}

.bucket6{
    background:#dc3545;
    color:#fff;
}

.total-row{
    background:#e9ecef;
    font-weight:bold;
}

</style>

</head>

<body id="page-top">

<div id="wrapper">

<?php include "sidemenu.php"; ?>

<div id="content-wrapper" class="d-flex flex-column">

<div id="content">

<?php include "topmenu.php"; ?>

<div class="container-fluid">

<div class="card shadow mb-4">

<div class="card-header py-3">

<h5 class="m-0 font-weight-bold text-danger">
CUSTOMER OUTSTANDING AGING REPORT
</h5>

</div>

<div class="card-body">

<?php

$sql = "

SELECT
    customer_name,
    customer_mobile,

    SUM(pending) AS total_pending,

    SUM(CASE
        WHEN DATEDIFF(CURDATE(), trans_date) BETWEEN 1 AND 10
        THEN pending ELSE 0
    END) AS bucket_1_10,

    SUM(CASE
        WHEN DATEDIFF(CURDATE(), trans_date) BETWEEN 11 AND 20
        THEN pending ELSE 0
    END) AS bucket_11_20,

    SUM(CASE
        WHEN DATEDIFF(CURDATE(), trans_date) BETWEEN 21 AND 30
        THEN pending ELSE 0
    END) AS bucket_21_30,

    SUM(CASE
        WHEN DATEDIFF(CURDATE(), trans_date) BETWEEN 31 AND 45
        THEN pending ELSE 0
    END) AS bucket_31_45,

    SUM(CASE
        WHEN DATEDIFF(CURDATE(), trans_date) BETWEEN 46 AND 60
        THEN pending ELSE 0
    END) AS bucket_46_60,

    SUM(CASE
        WHEN DATEDIFF(CURDATE(), trans_date) > 60
        THEN pending ELSE 0
    END) AS bucket_gt_60

FROM sales_trans
WHERE pending > 0

GROUP BY customer_name, customer_mobile

ORDER BY total_pending DESC

";

$result = $conn->query($sql);

$gt_total = 0;
$gt1 = 0;
$gt2 = 0;
$gt3 = 0;
$gt4 = 0;
$gt5 = 0;
$gt6 = 0;

?>

<button class="btn btn-success btn-sm mb-3"
        onclick="print_table()">
    Export To Excel
</button>

<table class="table table-bordered table-hover table-aging"
       id="report_table">

<thead>

<tr style="background:#343a40;color:#fff;">

<th>CUSTOMER NAME</th>
<th>MOBILE</th>
<th>TOTAL PENDING</th>
<th>1-10 DAYS</th>
<th>11-20 DAYS</th>
<th>21-30 DAYS</th>
<th>31-45 DAYS</th>
<th>46-60 DAYS</th>
<th>>60 DAYS</th>

</tr>

</thead>

<tbody>

<?php

while($row = $result->fetch_assoc())
{

$gt_total += $row["total_pending"];
$gt1 += $row["bucket_1_10"];
$gt2 += $row["bucket_11_20"];
$gt3 += $row["bucket_21_30"];
$gt4 += $row["bucket_31_45"];
$gt5 += $row["bucket_46_60"];
$gt6 += $row["bucket_gt_60"];

?>

<tr>

<td>

<a href="#"
class="customer-link"
data-mobile="<?php echo $row['customer_mobile'];?>">

<?php echo strtoupper($row["customer_name"]); ?>

</a>

</td>

<td><?php echo $row["customer_mobile"]; ?></td>

<td class="text-primary">
<?php echo number_format($row["total_pending"],2); ?>
</td>

<td class="bucket1">
<?php echo number_format($row["bucket_1_10"],2); ?>
</td>

<td class="bucket2">
<?php echo number_format($row["bucket_11_20"],2); ?>
</td>

<td class="bucket3">
<?php echo number_format($row["bucket_21_30"],2); ?>
</td>

<td class="bucket4">
<?php echo number_format($row["bucket_31_45"],2); ?>
</td>

<td class="bucket5">
<?php echo number_format($row["bucket_46_60"],2); ?>
</td>

<td class="bucket6">
<?php echo number_format($row["bucket_gt_60"],2); ?>
</td>
</tr>

<?php
}
?>
</tbody>

<tfoot>

<tr class="total-row">

<td colspan="2">GRAND TOTAL</td>

<td><?php echo number_format($gt_total,2); ?></td>
<td><?php echo number_format($gt1,2); ?></td>
<td><?php echo number_format($gt2,2); ?></td>
<td><?php echo number_format($gt3,2); ?></td>
<td><?php echo number_format($gt4,2); ?></td>
<td><?php echo number_format($gt5,2); ?></td>
<td><?php echo number_format($gt6,2); ?></td>

</tr>

</tfoot>

</table>

</div>
</div>
</div>

<?php include "footer.php"; ?>

</div>
</div>

<?php include "footer_include.php"; ?>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script src="js/tableToExcel.js"></script>

<script>

function print_table()
{
    TableToExcel.convert(
        document.getElementById("report_table")
    );
}
var table;

$(document).ready(function(){

    table = $("#report_table").DataTable({

        pageLength:25,
        order:[[2,'desc']],
        searching:true,
        paging:true,
        ordering:true,
        info:true

    });

    $('#report_table tbody').on(
        'click',
        'a.customer-link',
        function(e){

            e.preventDefault();

            let tr=$(this).closest('tr');

            let row=table.row(tr);

            let mobile=$(this).data('mobile');

            loadInvoices(row,mobile);

        });

});

function loadInvoices(row,mobile)
{

    if(row.child.isShown())
    {
        row.child.hide();
        return;
    }

    $.post(
        "get_customer_invoices.php",
        {
            mobile:mobile
        },
        function(html){

         row.child(html,'bg-light').show();
        }
    );

}

function loadInvoiceDetails(row, trans_id)
{
    if (row.child.isShown()) {
        row.child.hide();
        return;
    }

    $.post(
        "get_invoice_details.php",
        {
            trans_id: trans_id
        },
        function (html) {

            row.child(html, "bg-light").show();

        }
    );
}

</script>

</body>
</html>