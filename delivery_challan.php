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
</head>
<body id="page-top">

<div id="wrapper">
<?php include "sidemenu.php"; ?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<?php include "topmenu.php"; ?>

<div class="container-fluid">

<div class="alert alert-info text-center font-weight-bold">
DELIVERY CHALLAN MANAGEMENT
</div>

<?php
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'pending';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;

$limit = 25;
$offset = ($page-1) * $limit;

$status = ($tab == 'completed') ? 'D' : 'P';

$where = " WHERE s.delivery_status='".$status."' and active_status!='Z' ";

if($search != ''){
    $search_safe = mysqli_real_escape_string($conn,$search);
    $where .= " AND s.trans_id LIKE '%".$search_safe."%' ";
}

$count_sql = "
SELECT COUNT(*) cnt
FROM sales_trans s
".$where;

$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['cnt'];
$total_pages = max(1, ceil($total_rows/$limit));

// $sql = "
// SELECT
// s.trans_id,
// s.trans_date,
// s.company_name,
// COALESCE(c.company_name,s.customer_name) customer_name
// FROM sales_trans s
// LEFT JOIN customer c ON c.customer_id = s.customer
// ".$where."
// ORDER BY s.trans_date DESC, s.trans_id DESC
// LIMIT $limit OFFSET $offset";

$sql = "
SELECT
s.trans_id,
s.trans_date,
s.company_name,
COALESCE(c.company_name,s.customer_name) customer_name,
(
    SELECT MAX(dc_id)
    FROM delivery_challan d
    WHERE d.trans_id=s.trans_id
) dc_id
FROM sales_trans s
LEFT JOIN customer c ON c.customer_id = s.customer
".$where."
ORDER BY s.trans_date DESC, s.trans_id DESC
LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link <?php echo ($tab=='pending')?'active':'';?>"
           href="?tab=pending">Pending DC</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($tab=='completed')?'active':'';?>"
           href="?tab=completed">Completed DC</a>
    </li>
</ul>

<br>

<form method="get" class="row">
    <input type="hidden" name="tab" value="<?php echo $tab;?>">

    <div class="col-md-4">
        <input type="text"
               name="search"
               value="<?php echo htmlspecialchars($search);?>"
               class="form-control"
               placeholder="Search Trans ID">
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary">
            Search
        </button>
    </div>
</form>

<br>

<div class="table-responsive">
<table class="table table-bordered table-sm">

<thead>
<tr>
<th>Trans ID</th>
<th>Trans Date</th>
<th>Company Name</th>
<th>Customer Name</th>
<th>#</th>
</tr>
</thead>

<tbody>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><b><?php echo $row['trans_id']; ?></b></td>
<td><?php echo $row['trans_date']; ?></td>
<td><?php echo $row['company_name']; ?></td>
<td><?php echo $row['customer_name']; ?></td>

<td>

<?php if($tab=='pending'){ ?>

<a href="generate_dc.php?trans_id=<?php echo urlencode($row['trans_id']); ?>"
   class="btn btn-sm btn-primary">
View / Submit
</a>



<?php } else { ?>

<!-- <a href="generate_dc.php?trans_id=<?php echo urlencode($row['trans_id']); ?>"
   class="btn btn-sm btn-primary">
View
</a> -->

<!-- <a href="delivery_receipt.php?dc_id=<?php echo urlencode($row['dc_id']); ?>"
   target="_blank"
   class="btn btn-sm btn-success">
Print DC
</a> -->

<!-- <?php

$sql_dc = "
SELECT dc_id
FROM delivery_challan
WHERE trans_id='".$row['trans_id']."'
ORDER BY dc_id
";

$res_dc = $conn->query($sql_dc);

while($dc = $res_dc->fetch_assoc())
{
?>
<div style="text-align:center;">
    <a href="delivery_receipt.php?dc_id=<?php echo $dc['dc_id']; ?>"
       target="_blank"
       class="btn btn-sm btn-success mb-1"
       style="width:120px;">
       Print DC <?php echo $dc['dc_id']; ?>
    </a>
</div>
    <br>
<?php
}
?> -->

<?php } ?>
<a href="dc_history.php?trans_id=<?php echo urlencode($row['trans_id']); ?>"
   class="btn btn-sm btn-info"
   title="Delivery History">
📜 History
</a>
</td>

</tr>

<?php } ?>

<?php if($total_rows == 0){ ?>
<tr>
<td colspan="5" align="center">
No Records Found
</td>
</tr>
<?php } ?>

</tbody>
</table>
</div>

<nav>
<ul class="pagination">

<?php if($page > 1){ ?>
<li class="page-item">
<a class="page-link"
href="?tab=<?php echo $tab;?>&search=<?php echo urlencode($search);?>&page=<?php echo ($page-1); ?>">
Previous
</a>
</li>
<?php } ?>

<?php
$start = max(1,$page-2);
$end = min($total_pages,$page+2);

for($i=$start;$i<=$end;$i++){
?>
<li class="page-item <?php echo ($i==$page)?'active':'';?>">
<a class="page-link"
href="?tab=<?php echo $tab;?>&search=<?php echo urlencode($search);?>&page=<?php echo $i;?>">
<?php echo $i; ?>
</a>
</li>
<?php } ?>

<?php if($page < $total_pages){ ?>
<li class="page-item">
<a class="page-link"
href="?tab=<?php echo $tab;?>&search=<?php echo urlencode($search);?>&page=<?php echo ($page+1); ?>">
Next
</a>
</li>
<?php } ?>

</ul>
</nav>

</div>

<?php include "footer.php"; ?>

</div>
</div>

<?php include "footer_include.php"; ?>

</body>
</html>
