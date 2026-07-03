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

.table-ledger{
    font-size:85%;
    font-weight:bold;
    text-transform:uppercase;
}

.opening-row{
    background:#fff3cd;
    color:#856404;
    font-weight:bold;
}

.sales-row{
    background:#d4edda;
}

.receipt-row{
    background:#d1ecf1;
}

.total-row{
    background:#e9ecef;
    font-weight:bold;
}

.balance-dr{
    color:#dc3545;
    font-weight:bold;
}

.balance-cr{
    color:#28a745;
    font-weight:bold;
}

#customer_info{
    display:none;
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

CUSTOMER LEDGER

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3">

<label><b>From Date</b></label>

<input
type="date"
id="from_date"
class="form-control"
value="<?php echo date('Y-m-01');?>">

</div>

<div class="col-md-3">

<label><b>To Date</b></label>

<input
type="date"
id="to_date"
class="form-control"
value="<?php echo date('Y-m-d');?>">

</div>

<div class="col-md-4">

<label><b>Customer</b></label>

<select
id="customer_id"
class="form-control">

<option value="">Select Customer</option>

<?php

$sql="

SELECT DISTINCT
    A.customer_id,
    A.owner_name,
    A.owner_mobile
FROM customer A
INNER JOIN customer_ledger B
    ON A.customer_id = B.customer_id
WHERE A.owner_name IS NOT NULL
  AND TRIM(A.owner_name) <> ''
ORDER BY A.owner_name;

";

$result=$conn->query($sql);

while($row=$result->fetch_assoc())
{

?>

<option
value="<?php echo $row["customer_id"];?>">

<?php

echo strtoupper($row["owner_name"]);

echo " (";

echo $row["owner_mobile"];

echo ")";

?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-2">

<label>&nbsp;</label>

<button
class="btn btn-primary btn-block"
id="btn_show">

Show Report

</button>

</div>

</div>

<hr>

<div id="customer_info">

<div class="row">

<div class="col-md-6">

<table class="table table-bordered">

<tr>

<th width="35%">Customer Name</th>

<td id="customer_name"></td>

</tr>

<tr>

<th>Mobile</th>

<td id="customer_mobile"></td>

</tr>

<tr>

<th>Address</th>

<td id="customer_address"></td>

</tr>

</table>

</div>

<div class="col-md-6">

<table class="table table-bordered">

<tr>

<th width="40%">Period</th>

<td id="report_period"></td>

</tr>

<tr>

<th>Opening Balance</th>

<td
id="opening_balance"
class="balance-dr">

</td>

</tr>

</table>

</div>

</div>

</div>

<button
class="btn btn-success btn-sm mb-3"
onclick="print_table()">

Export To Excel

</button>

<table
class="table table-bordered table-hover table-ledger"
id="ledger_table">

<thead>

<tr style="background:#343a40;color:#fff;">

<th>Date</th>

<th>Voucher Type</th>

<th>Voucher No</th>

<th>Remarks</th>

<th class="text-right">Debit</th>

<th class="text-right">Credit</th>

<th class="text-right">Running Balance</th>

</tr>

</thead>

<tbody>

<!-- AJAX Loads Here -->

</tbody>

<tfoot>

<tr class="total-row">

<th colspan="4">

TOTAL

</th>

<th
id="total_debit"
class="text-right">

0.00

</th>

<th
id="total_credit"
class="text-right">

0.00

</th>

<th
id="closing_balance"
class="text-right">

0.00

</th>

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

var ledgerTable = null;

$(document).ready(function () {

    // If you use searchableOptionList.js in your project,
    // uncomment the next line.
    //
    // $("#customer_id").searchableOptionList();

    ledgerTable = $("#ledger_table").DataTable({

        pageLength:25,

        searching:true,

        ordering:true,

        paging:true,

        info:true,

        responsive:true,

        order:[[0,'asc']],

        columnDefs:[
            {targets:[4,5,6], className:'text-right'}
        ]

    });

});


$("#btn_show").click(function(){

    loadLedger();

});


function loadLedger()
{

    var customer_id=$("#customer_id").val();
    var from_date=$("#from_date").val();
    var to_date=$("#to_date").val();

    if(customer_id=="")
    {
        alert("Please select Customer");
        return;
    }

    if(from_date=="")
    {
        alert("Please select From Date");
        return;
    }

    if(to_date=="")
    {
        alert("Please select To Date");
        return;
    }

    $.ajax({

        url:"get_customer_ledger.php",

        type:"POST",

        dataType:"json",

        data:
        {
            customer_id:customer_id,
            from_date:from_date,
            to_date:to_date
        },

        success:function(response)
        {

            if(response.status=="ERROR")
            {
                alert(response.message);
                return;
            }

            $("#customer_info").show();

            $("#customer_name").html(response.customer_name);

            $("#customer_mobile").html(response.customer_mobile);

            $("#customer_address").html(response.customer_address);

            $("#report_period").html(response.period);

            $("#opening_balance").html(response.opening_balance);

            $("#total_debit").html(response.total_debit);

            $("#total_credit").html(response.total_credit);

            $("#closing_balance").html(response.closing_balance);

            ledgerTable.destroy();

            $("#ledger_table tbody").html(response.rows);

ledgerTable = $("#ledger_table").DataTable({

    pageLength:25,
    searching:true,
    ordering:true,
    paging:true,
    info:true,
    responsive:true,
    order:[[0,'asc']],
    columnDefs:[
        {targets:[4,5,6], className:'text-right'}
    ]

});

        },

        error:function(xhr)
        {

            alert(xhr.responseText);

        }

    });

}



function print_table()
{

    TableToExcel.convert(

        document.getElementById("ledger_table"),

        {

            name:"SalesLedger.xlsx"

        }

    );

}


$("#from_date").change(function(){

    if($("#customer_id").val()!="")
    {
        loadLedger();
    }

});


$("#to_date").change(function(){

    if($("#customer_id").val()!="")
    {
        loadLedger();
    }

});


$("#customer_id").change(function(){

    loadLedger();

});


</script>

</body>

</html>