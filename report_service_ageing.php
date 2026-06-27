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
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 18px 22px;
            margin-bottom: 20px;
        }

        .filter-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .filter-card .form-control {
            font-size: 13px;
            height: 34px;
            border-radius: 6px;
            border: 1.5px solid #dee2e6;
        }

        .filter-card .form-control:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }

        .btn-search {
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 7px 22px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 22px;
            cursor: pointer;
            transition: background .2s;
        }

        .btn-search:hover {
            background: #c0392b;
        }

        .report-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        .report-card .card-header {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            color: #fff;
            padding: 13px 20px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #report_table thead th {
            background: #2c3e50;
            color: #ecf0f1;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 13px 14px;
            border: none;
            white-space: nowrap;
        }

        #report_table tbody tr {
            transition: background .15s;
        }

        #report_table tbody tr:hover {
            background: #fdf2f2 !important;
        }

        #report_table tbody td {
            padding: 10px 14px;
            font-size: 13px;
            vertical-align: middle;
            border-color: #f1f1f1;
            color: #2d3436;
        }

        .trans-id {
            font-weight: 700;
            color: #2c3e50;
        }

        .sub-id {
            display: inline-block;
            background: #eaf2ff;
            color: #2980b9;
            border-radius: 10px;
            padding: 1px 8px;
            font-size: 11px;
            font-weight: 600;
        }

        .trans-date {
            font-size: 11px;
            color: #6c757d;
        }

        .customer-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .service-name {
            font-weight: 600;
        }

        .total-val {
            font-weight: 700;
            color: #27ae60;
        }

        .tax-val {
            font-size: 12px;
            color: #636e72;
        }

        .overdue {
            background: #fde8e8;
            color: #c0392b;
            border-radius: 6px;
            padding: 4px 10px;
            font-weight: 700;
            font-size: 12px;
            display: inline-block;
        }

        .warning {
            background: #fff3cd;
            color: #856404;
            border-radius: 6px;
            padding: 4px 10px;
            font-weight: 700;
            font-size: 12px;
            display: inline-block;
        }

        .ok {
            background: #d4edda;
            color: #155724;
            border-radius: 6px;
            padding: 4px 10px;
            font-weight: 700;
            font-size: 12px;
            display: inline-block;
        }

        .ageing-label {
            font-size: 11px;
            color: #636e72;
            margin-top: 4px;
        }

        .btn-excel {
            background: #27ae60;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 5px 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
        }

        .btn-excel:hover {
            background: #1e8449;
        }

        .tfoot-row td {
            background: #2c3e50 !important;
            color: #ecf0f1 !important;
            font-weight: 700;
            font-size: 13px;
            padding: 11px 14px;
        }
        /* Select2 Theme Customization */
    .select2-container--default .select2-selection--single {
        height: 34px !important;
        border: 1.5px solid #dee2e6 !important;
        border-radius: 6px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        font-size: 13px;
        font-weight: 600;
        color: #2d3436;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }
    .select2-dropdown {
        border: 1px solid #e74c3c !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #e74c3c !important;
    }
    .select2-result-item { padding: 4px 0; }
    .select2-result-title { font-weight: 700; display: block; font-size: 13px; }
    .select2-result-details { font-size: 11px; color: #777; display: block; }
</style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include "sidemenu.php"; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "topmenu.php"; ?>
                <div class="container-fluid py-3">

                    <!-- Flatpickr calendar picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

                    <!-- Filter Bar -->
                    <form action="#" method="post">
                        <div class="filter-card">
                            <div class="row align-items-end">
                                <div class="col-md-2">
                                    <div class="filter-label">From Date</div>
                                    <input type="text" name="from_date" id="from_date" class="form-control"
                                        placeholder="YYYY-MM-DD" autocomplete="off" value="<?php echo $_POST['from_date'] ?? ''; ?>">
                                </div>
                                <div class="col-md-2">
                                    <div class="filter-label">To Date</div>
                                    <input type="text" name="to_date" id="to_date" class="form-control"
                                        placeholder="YYYY-MM-DD" autocomplete="off" value="<?php echo $_POST['to_date'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <div class="filter-label">Customer</div>
                                    <select class="form-control select2" name="customer">
                                        <option value="">-- All Customers --</option>
                                        <?php $sql = "SELECT c.customer_id, 
                                                            COALESCE(NULLIF(c.company_name, ''), c.owner_name) AS company_name, 
                                                            c.owner_mobile,
                                                            GROUP_CONCAT(DISTINCT v.vehicle_no SEPARATOR ', ') as vehicles
                                                     FROM customer c
                                                     LEFT JOIN vehicle v ON c.customer_id = v.customer_id
                                                     GROUP BY c.customer_id
                                                     ORDER BY company_name";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) { 
                                            $label = $row['company_name'];
                                            if ($row['owner_mobile']) $label .= " (" . $row['owner_mobile'] . ")";
                                            $details = $row['vehicles'] ? "Vehicles: " . $row['vehicles'] : "";
                                        ?>
                                            <option value="<?php echo $row['customer_id']; ?>" 
                                                    data-details="<?php echo htmlspecialchars($details); ?>"
                                                    <?php echo (isset($_POST['customer']) && $_POST['customer'] == $row['customer_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($label); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="filter-label">Service</div>
                                    <select class="form-control" name="service">
                                        <option value="">-- All Services --</option>
                                        <?php $sql = "SELECT service_id, service_name FROM service ORDER BY service_name";
                                        $result = $conn->query($sql);
                                        while ($row1 = $result->fetch_assoc()) { ?>
                                            <option value="<?php echo $row1['service_id']; ?>" <?php echo (isset($_POST['service']) && $_POST['service'] == $row1['service_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row1['service_name']); ?></option>
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
                    $to_date = $_POST["to_date"] ?? '';
                    $service = $_POST["service"] ?? '';
                    $customer = $_POST["customer"] ?? '';

                    $sql = "SELECT s.trans_id, d.subtrans_id, s.trans_date,
                                   COALESCE(NULLIF(c.company_name,''), c.owner_name) AS company_name,
                                   c.owner_mobile, s.vehicle_no,
                                   i.service_name, d.total, d.tax_amount, s.gst,
                                   DATEDIFF(NOW(), s.trans_date) AS pending_days,
                                   IFNULL(c.credit_days, 15) AS credit_days,
                                   IFNULL(c.credit_amount, 0) AS credit_amount,
                                   s.pending AS pending_amount
                            FROM service_trans s
                            JOIN service_trans_det d ON s.trans_id = d.trans_id
                            JOIN customer c ON s.customer = c.customer_id
                            JOIN service i ON d.service_id = i.service_id
                            WHERE s.active_status = 'A' AND d.active_status = 'A'
                            AND s.pending > 0";
                    if (!empty($from_date))
                        $sql .= " AND s.trans_date BETWEEN '$from_date' AND '$to_date'";
                    if (!empty($customer))
                        $sql .= " AND s.customer = '$customer'";
                    if (!empty($service))
                        $sql .= " AND d.service_id = '$service'";
                    $sql .= " ORDER BY pending_days DESC, s.trans_id DESC";

                    $result = $conn->query($sql);
                    $rows = [];
                    $grand_total = $grand_tax = $grand_pending = 0;
                    while ($row = $result->fetch_assoc()) {
                        $rows[] = $row;
                        $grand_total += $row['total'];
                        $grand_tax += $row['tax_amount'];
                        $grand_pending += $row['pending_amount'];
                    }
                    ?>

                    <div class="card report-card">
                        <div class="card-header">
                            <span><i class="fas fa-clock mr-2"></i>Service Ageing Report — Pending Payments</span>
                            <button class="btn-excel" onclick="print_table()"><i class="fas fa-file-excel mr-1"></i>
                                Export Excel</button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="report_table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Invoice No</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Mobile</th>
                                            <th>Vehicle</th>
                                            <th>Service</th>
                                            <th style="text-align:right">Amount</th>
                                            <th style="text-align:right">Tax</th>
                                            <th style="text-align:right">Total</th>
                                            <th style="text-align:right">Pending Amt</th>
                                            <th style="text-align:center">Days Pending</th>
                                            <th style="text-align:center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sl = 1;
                                        foreach ($rows as $row):
                                            $days = (int) $row['pending_days'];
                                            $allowed_days = (int) $row['credit_days'];
                                            $overdue_days = $days - $allowed_days;
                                            $taxable = $row['total'] - $row['tax_amount'];
 
                                            if ($overdue_days > 30) {
                                                $badge = 'overdue';
                                                $label = 'Overdue';
                                            } elseif ($overdue_days > 0) {
                                                $badge = 'warning';
                                                $label = 'Due';
                                            } else {
                                                $badge = 'ok';
                                                $label = 'Within Limit';
                                            }
                                            ?>
                                            <tr>
                                                <td style="color:#aaa;font-size:12px"><?php echo $sl++; ?></td>
                                                <td>
                                                    <span class="trans-id"><?php echo htmlspecialchars($row['trans_id']); ?></span>
                                                    <span class="sub-id ml-1">#<?php echo $row['subtrans_id']; ?></span>
                                                </td>
                                                <td><span
                                                        class="trans-date"><?php echo date('d-M-Y', strtotime($row['trans_date'])); ?></span>
                                                </td>
                                                <td><span
                                                        class="customer-name"><?php echo htmlspecialchars($row['company_name']); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['owner_mobile']); ?></td>
                                                <td><span class="badge badge-secondary"><?php echo htmlspecialchars($row['vehicle_no']); ?></span></td>
                                                <td><span
                                                        class="service-name"><?php echo htmlspecialchars($row['service_name']); ?></span>
                                                </td>
                                                <td style="text-align:right">
                                                    <?php echo number_format($taxable, 2); ?></td>
                                                <td style="text-align:right"><span
                                                        class="tax-val"><?php echo number_format($row['tax_amount'], 2); ?></span>
                                                </td>
                                                <td style="text-align:right"><span
                                                        class="total-val"><?php echo number_format($row['total'], 2); ?></span>
                                                </td>
                                                <td style="text-align:right">
                                                    <strong>₹<?php echo number_format($row['pending_amount'], 2); ?></strong>
                                                </td>
                                                <td style="text-align:center">
                                                    <strong><?php echo $days; ?> days</strong>
                                                    <div class="ageing-label">Allowed: <?php echo $allowed_days; ?> days
                                                    </div>
                                                </td>
                                                <td style="text-align:center">
                                                    <span class="<?php echo $badge; ?>"><?php echo $label; ?></span>
                                                    <?php if ($overdue_days > 0): ?>
                                                        <div class="ageing-label"><?php echo $overdue_days; ?> days over</div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="tfoot-row">
                                            <td colspan="9" style="text-align:right">GRAND TOTAL</td>
                                            <td style="text-align:right">₹<?php echo number_format($grand_total, 2); ?>
                                            </td>
                                            <td style="text-align:right">
                                                ₹<?php echo number_format($grand_pending, 2); ?></td>
                                            <td colspan="2"></td>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    <script src="js/tableToExcel.js"></script>

    <script>
        $(function () {
            $("#from_date").datepicker({ dateFormat: "yy-mm-dd" });
            $("#to_date").datepicker({ dateFormat: "yy-mm-dd" });
            $('.select2').select2({
                templateResult: function(state) {
                    if (!state.id) return state.text;
                    var details = $(state.element).data('details');
                    return $('<div class="select2-result-item"><span class="select2-result-title">' + state.text + '</span>' +
                           (details ? '<span class="select2-result-details">' + details + '</span>' : '') + '</div>');
                }
            });
        });
        function print_table() {
            TableToExcel.convert(document.getElementById("report_table"));
        }
    </script>

</body>

</html>