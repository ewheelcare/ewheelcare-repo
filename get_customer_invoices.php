<?php

include "db_config.php";

$mobile = $_POST["mobile"];

$sql = "
SELECT
    trans_id,
    trans_date,
    trans_amount,
    pending,
    DATEDIFF(CURDATE(), trans_date) AS age_days
FROM sales_trans
WHERE CUSTOMER_MOBILE = ?
AND pending > 0
ORDER BY trans_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$mobile);
$stmt->execute();

$result = $stmt->get_result();
?>

<table
class="table table-sm table-bordered invoice-table"
id="invoice_<?php echo md5($mobile); ?>">

<thead>

<tr>

<th>Invoice</th>
<th>Date</th>
<th>Age</th>
<th>Amount</th>
<th>Pending</th>

</tr>

</thead>
<tbody>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td>
<a href="#"
class="invoice-link"
data-trans="<?php echo $row['trans_id']; ?>">
<?php echo $row['trans_id']; ?>
</a>
</td>

<td><?php echo date('d-m-Y', strtotime($row['trans_date'])); ?></td>

<td>
<?php echo $row['age_days']; ?> Days
</td>

<td><?php echo number_format($row['trans_amount'],2); ?></td>

<td><?php echo number_format($row['pending'],2); ?></td>

</tr>


<?php } ?>
</tbody>
</table>
<script>

var invoiceTable =
$("#invoice_<?php echo md5($mobile); ?>").DataTable({

    paging:false,
    searching:false,
    ordering:false,
    info:false,
    destroy:true

});

</script>
<script>

$(".invoice-table tbody").on(
'click',
'a.invoice-link',
function(e){

e.preventDefault();

let tr=$(this).closest("tr");

let row = invoiceTable.row(tr);
let trans=$(this).data("trans");

loadInvoiceDetails(row,trans);

});

</script>