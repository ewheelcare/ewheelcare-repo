<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include "db_config.php";

$trans_id = isset($_GET['trans_id'])
    ? mysqli_real_escape_string($conn,$_GET['trans_id'])
    : '';

if($trans_id==''){
    die("Transaction ID Missing");
}
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

<?php
if(isset($_GET['msg']) && $_GET['msg']=="updated")
{
?>
<div class="text-success font-weight-bold" align='center'>
    Delivery Challan Updated Successfully
</div><br>
<?php
}
?>

<div class="alert alert-info text-center font-weight-bold">
DELIVERY CHALLAN HISTORY
</div>

<?php

$status = '';

$sql_status = "
SELECT delivery_status
FROM sales_trans
WHERE trans_id='$trans_id'
";

$res_status = mysqli_query($conn, $sql_status);

if($row_status = mysqli_fetch_assoc($res_status))
{
    $status = $row_status['delivery_status'];
}

$sql="
SELECT
dc_id,
dc_date,
trans_id
FROM delivery_challan
WHERE trans_id='$trans_id'
ORDER BY dc_id DESC
";

$result=$conn->query($sql);
?>

<div class="card">
<div class="card-header">
Invoice : <?php echo $trans_id; ?>
</div>

<div class="card-body">

<table class="table table-bordered table-sm">

<thead>
<tr>
<th>DC No</th>
<th>Date</th>
<th>Print</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php

if($result && $result->num_rows>0)
{
    while($row=$result->fetch_assoc())
    {
?>

<tr>

<td>
DC/<?php echo $row['dc_id']; ?>
</td>

<td>
<?php echo $row['dc_date']; ?>
</td>

<td>

<a href="delivery_receipt.php?dc_id=<?php echo $row['dc_id']; ?>"
   target="_blank"
   class="btn btn-success btn-sm">
Print DC
</a>

</td>

<td>
<!-- <?php if($status == 'P') { ?>
    <a href="edit_dc.php?dc_id=<?php echo $row['dc_id']; ?>&mode=edit"
       class="btn btn-primary btn-sm">
       Edit
    </a>
<?php } else { ?>
    <span class="badge badge-secondary">
        Locked
    </span>
<?php } ?> -->
 <a href="edit_dc.php?dc_id=<?php echo $row['dc_id']; ?>&mode=edit"
       class="btn btn-primary btn-sm">
       Edit
    </a>
</td>

</tr>

<?php
    }
}
else
{
?>

<tr>
<td colspan="4" align="center">
No DC Found
</td>
</tr>

<?php
}
?>

</tbody>

</table>

</div>
</div>

</div>

<?php include "footer.php"; ?>

</div>
</div>
</div>

<?php include "footer_include.php"; ?>

</body>
</html>