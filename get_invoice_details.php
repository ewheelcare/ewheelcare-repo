<?php

include "db_config.php";

$trans_id = $_POST["trans_id"];

$sql = "
SELECT
    i.ITEM_NAME,
    d.qty,
    d.price,
    d.tax,
    d.tax_amount,
    d.total
FROM sales_trans_det d
LEFT JOIN item i
ON i.ITEM_ID = d.item_id
WHERE d.trans_id = ?
AND d.active_status <> 'Z'
ORDER BY d.id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$trans_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<table class="table table-sm table-bordered table-striped mb-0">

<thead class="table-dark">

<tr>

<th>Item Name</th>
<th class="text-center">Qty</th>
<th class="text-right">Price</th>
<th class="text-right">Tax %</th>
<th class="text-right">Tax Amt</th>
<th class="text-right">Total</th>

</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><?php echo htmlspecialchars($row['ITEM_NAME']); ?></td>

<td class="text-center">
<?php echo number_format($row['qty'],2); ?>
</td>

<td class="text-right">
<?php echo number_format($row['price'],2); ?>
</td>

<td class="text-right">
<?php echo number_format($row['tax'],2); ?>
</td>

<td class="text-right">
<?php echo number_format($row['tax_amount'],2); ?>
</td>

<td class="text-right font-weight-bold">
<?php echo number_format($row['total'],2); ?>
</td>

</tr>

<?php } ?>

</tbody>

</table>