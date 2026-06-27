<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "header_include.php";
include "db_config.php";
?>
<style>
    .filter-card {
        background: #fff; border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 18px 22px; margin-bottom: 20px;
    }
    .filter-label {
        font-size: 11px; font-weight: 700; letter-spacing: 0.6px;
        text-transform: uppercase; color: #6c757d; margin-bottom: 4px;
    }
    .filter-card .form-control {
        font-size: 13px; height: 34px; border-radius: 6px;
        border: 1.5px solid #dee2e6;
    }
    .filter-card .form-control:focus {
        border-color: #e74c3c; box-shadow: 0 0 0 3px rgba(231,76,60,0.1);
    }
    .btn-search {
        background: #e74c3c; color: #fff; border: none; border-radius: 6px;
        padding: 7px 22px; font-size: 13px; font-weight: 600;
        margin-top: 22px; cursor: pointer; transition: background .2s;
    }
    .btn-search:hover { background: #c0392b; }

    .report-card { border: none; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); overflow: hidden; }
    .report-card .card-header {
        background: linear-gradient(135deg, #2c3e50, #34495e); color: #fff;
        padding: 13px 20px; font-size: 14px; font-weight: 700;
        display: flex; justify-content: space-between; align-items: center;
    }
    #report_table thead th {
        background: #2c3e50; color: #ecf0f1; font-size: 11px;
        font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;
        padding: 13px 14px; border: none; white-space: nowrap;
    }
    #report_table tbody tr { transition: background .15s; }
    #report_table tbody tr:hover { background: #f8f9fa !important; }
    #report_table tbody td { padding: 10px 14px; font-size: 13px; vertical-align: middle; border-color: #f1f1f1; color: #2d3436; }

    .trans-id   { font-weight: 700; color: #2c3e50; }
    .sub-id     { display: inline-block; background: #eaf2ff; color: #2980b9; border-radius: 10px; padding: 1px 8px; font-size: 11px; font-weight: 600; }
    .trans-date { font-size: 11px; color: #6c757d; }
    .vendor-name { font-weight: 600; color: #2c3e50; }
    .item-name  { font-weight: 600; }
    .total-val  { font-weight: 700; color: #e67e22; }
    .tax-val    { font-size: 12px; color: #636e72; }

    .btn-excel { background: #27ae60; color: #fff; border: none; border-radius: 6px; padding: 5px 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background .2s; }
    .btn-excel:hover { background: #1e8449; }
    .dataTables_filter input { border-radius: 20px !important; border: 1.5px solid #dee2e6 !important; padding: 4px 14px !important; font-size: 13px !important; }
    .tfoot-row td { background: #2c3e50 !important; color: #ecf0f1 !important; font-weight: 700; font-size: 13px; padding: 11px 14px; }
</style>
</head>

<body id="page-top">
<div id="wrapper">
    <?php include "sidemenu.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include "topmenu.php"; ?>
            <div class="container-fluid py-3">

                <!-- Filter Bar -->
                <form action="#" method="post">
                    <div class="filter-card">
                        <div class="row align-items-end">
                            <div class="col-md-2">
                                <div class="filter-label">From Date</div>
                                <input type="text" name="from_date" id="from_date" class="form-control" placeholder="YYYY-MM-DD" autocomplete="off" value="<?php echo $_POST['from_date'] ?? ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <div class="filter-label">To Date</div>
                                <input type="text" name="to_date" id="to_date" class="form-control" placeholder="YYYY-MM-DD" autocomplete="off" value="<?php echo $_POST['to_date'] ?? ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <div class="filter-label">Vendor</div>
                                <select class="form-control" name="vendor">
                                    <option value="">-- All Vendors --</option>
                                    <?php $sql = "SELECT vendor_id, company_name FROM vendor ORDER BY company_name";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['vendor_id']; ?>" <?php echo (isset($_POST['vendor']) && $_POST['vendor'] == $row['vendor_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['company_name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="filter-label">Item</div>
                                <select class="form-control" name="item">
                                    <option value="">-- All Items --</option>
                                    <?php $sql = "SELECT item_id, item_name FROM item ORDER BY item_name";
                                    $result = $conn->query($sql);
                                    while ($row1 = $result->fetch_assoc()) { ?>
                                        <option value="<?php echo $row1['item_id']; ?>" <?php echo (isset($_POST['item']) && $_POST['item'] == $row1['item_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row1['item_name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn-search w-100">Search</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Report Table -->
                <?php
                $from_date = $_POST["from_date"] ?? '';
                $to_date   = $_POST["to_date"] ?? '';
                $item      = $_POST["item"] ?? '';
                $vendor    = $_POST["vendor"] ?? '';
                $shop_id   = $_COOKIE["shop"] ?? '';

                $sql = "SELECT s.trans_id, d.subtrans_id, s.trans_date,
                               c.company_name, i.item_name, d.total, d.tax_amount
                        FROM receipt_trans s
                        JOIN receipt_trans_det d ON s.trans_id = d.trans_id
                        JOIN vendor c ON s.vendor = c.vendor_id
                        JOIN item i ON d.item_id = i.item_id
                        WHERE s.active_status = 'A' AND d.active_status = 'A'
                        AND d.total > 0";
                
                if (!empty($shop_id))   $sql .= " AND d.shop_id = '$shop_id'";
                if (!empty($from_date)) $sql .= " AND s.trans_date BETWEEN '$from_date' AND '$to_date'";
                if (!empty($vendor))    $sql .= " AND s.vendor = '$vendor'";
                if (!empty($item))      $sql .= " AND d.item_id = '$item'";
                $sql .= " ORDER BY s.trans_id DESC, d.subtrans_id";

                $result = $conn->query($sql);
                $rows = [];
                $grand_total = $grand_tax = 0;
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                    $grand_total   += $row['total'];
                    $grand_tax     += $row['tax_amount'];
                }
                ?>

                <div class="card report-card">
                    <div class="card-header">
                        <span><i class="fas fa-file-invoice mr-2"></i>Purchase Receipt Report — Stock Inwards</span>
                        <button class="btn-excel" onclick="print_table()"><i class="fas fa-file-excel mr-1"></i> Export Excel</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="report_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Trans ID</th>
                                        <th>Date</th>
                                        <th>Vendor</th>
                                        <th>Item</th>
                                        <th style="text-align:right">Taxable Amt</th>
                                        <th style="text-align:right">Tax</th>
                                        <th style="text-align:right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $sl = 1; foreach ($rows as $row):
                                    $taxable = $row['total'] - $row['tax_amount'];
                                ?>
                                <tr>
                                    <td style="color:#aaa;font-size:12px"><?php echo $sl++; ?></td>
                                    <td>
                                        <span class="trans-id"><?php echo htmlspecialchars($row['trans_id']); ?></span>
                                        <span class="sub-id ml-1">#<?php echo $row['subtrans_id']; ?></span>
                                    </td>
                                    <td><span class="trans-date"><?php echo date('d-M-Y', strtotime($row['trans_date'])); ?></span></td>
                                    <td><span class="vendor-name"><?php echo htmlspecialchars($row['company_name']); ?></span></td>
                                    <td><span class="item-name"><?php echo htmlspecialchars($row['item_name']); ?></span></td>
                                    <td style="text-align:right"><?php echo number_format($taxable, 2); ?></td>
                                    <td style="text-align:right"><span class="tax-val"><?php echo number_format($row['tax_amount'], 2); ?></span></td>
                                    <td style="text-align:right"><span class="total-val"><?php echo number_format($row['total'], 2); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="tfoot-row">
                                        <td colspan="5" style="text-align:right">GRAND TOTAL</td>
                                        <td style="text-align:right">₹<?php echo number_format($grand_total - $grand_tax, 2); ?></td>
                                        <td style="text-align:right">₹<?php echo number_format($grand_tax, 2); ?></td>
                                        <td style="text-align:right">₹<?php echo number_format($grand_total, 2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>

<?php include "modals.php"; ?>
<?php include "footer_include.php"; ?>
<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="js/demo/datatables-demo.js"></script>
<script src="js/tableToExcel.js"></script>

<script>
$(function () {
    $("#from_date").datepicker({ dateFormat: "yy-mm-dd" });
    $("#to_date").datepicker({ dateFormat: "yy-mm-dd" });
});
function print_table() {
    TableToExcel.convert(document.getElementById("report_table"));
}
</script>

</body>
</html>