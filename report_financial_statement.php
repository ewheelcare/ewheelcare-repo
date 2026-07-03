<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="multi/searchableOptionList.css" rel="stylesheet">
    <?php
    include "header_include.php";
    include "db_config.php";

    $from_date = isset($_POST["from_date"]) ? $_POST["from_date"] : date('d-m-Y');
    $to_date = isset($_POST["to_date"]) ? $_POST["to_date"] : date('d-m-Y');
    $shop_param = isset($_POST["shop"]) ? $_POST["shop"] : "";
    ?>
    <title>Comprehensive Financial Statement</title>
</head>

<body id="page-top">

    <div id="wrapper">

        <?php include "sidemenu.php"; ?>

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <?php include "topmenu.php"; ?>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Financial Cash Flow Statement</h1>
                    </div>

                    <!-- SEARCH FORM -->
                    <div class="row">
                        <div class="col-md-12">
                            <form action="#" method="post">
                                <table class="table table-striped">
                                    <tr>
                                        <td>
                                            From Date<br>
                                            <input type="text" name="from_date" id="from_date"
                                                value="<?php echo $from_date; ?>" class="form-control"
                                                autocomplete="off">
                                        </td>
                                        <td>
                                            To Date<br>
                                            <input type="text" name="to_date" id="to_date"
                                                value="<?php echo $to_date; ?>" class="form-control" autocomplete="off">
                                        </td>
                                        <td>
                                            Shop<br>
                                            <select class="form-control" name="shop">
                                                <option value="">--All Shops--</option>
                                                <?php
                                                $sql_shop = "SELECT shop_id,shop_name FROM shop ORDER BY shop_name";
                                                $res_shop = $conn->query($sql_shop);
                                                while ($row_shop = $res_shop->fetch_assoc()) {
                                                    $shop_code = strtoupper(explode(' ', trim($row_shop["shop_name"]))[0]);
                                                    $sel = ($shop_param == $shop_code) ? "selected" : "";
                                                    ?>
                                                    <option value="<?php echo $shop_code; ?>" <?php echo $sel; ?>>
                                                        <?php echo $row_shop["shop_name"]; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <br>
                                            <input type="submit" class="btn btn-primary btn-sm mt-1" value="Search">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>

                    <?php
                    // Fetch data excluding Advances
                    $sql_comp = "
SELECT 
    sh.shop_name AS shop_name_full,
    IFNULL(ob.OLD_BALANCE, 0) AS OLD_BALANCE,
    
    -- Inflow Breakdown
    IFNULL(inflow.SERVICE_CASH, 0) AS SERVICE_CASH,
    IFNULL(inflow.SERVICE_CREDIT, 0) AS SERVICE_CREDIT,
    IFNULL(inflow.SERVICE_BANK, 0) AS SERVICE_BANK,
    IFNULL(inflow.SERVICE_UPI, 0) AS SERVICE_UPI,
    IFNULL(inflow.SERVICE_OTHERS, 0) AS SERVICE_OTHERS,
    
    IFNULL(inflow.SALES_CASH, 0) AS SALES_CASH,
    IFNULL(inflow.SALES_CREDIT, 0) AS SALES_CREDIT,
    IFNULL(inflow.SALES_BANK, 0) AS SALES_BANK,
    IFNULL(inflow.SALES_UPI, 0) AS SALES_UPI,
    IFNULL(inflow.SALES_OTHERS, 0) AS SALES_OTHERS,
    
    -- Outflow Breakdown
    IFNULL(outflow.PROCUREMENT_CASH, 0) AS PROCUREMENT_CASH,
    IFNULL(outflow.PROCUREMENT_CREDIT, 0) AS PROCUREMENT_CREDIT,
    IFNULL(outflow.PROCUREMENT_BANK, 0) AS PROCUREMENT_BANK,
    IFNULL(outflow.PROCUREMENT_UPI, 0) AS PROCUREMENT_UPI,
    IFNULL(outflow.PROCUREMENT_OTHERS, 0) AS PROCUREMENT_OTHERS,
    
    IFNULL(outflow.EXP_CASH, 0) AS EXP_CASH,
    IFNULL(outflow.EXP_CREDIT, 0) AS EXP_CREDIT,
    IFNULL(outflow.EXP_BANK, 0) AS EXP_BANK,
    
    -- Closing Balance
    (IFNULL(ob.OLD_BALANCE, 0) 
     + IFNULL(inflow.SERVICE_CASH, 0) + IFNULL(inflow.SERVICE_BANK, 0) + IFNULL(inflow.SERVICE_UPI, 0) + IFNULL(inflow.SERVICE_OTHERS, 0)
     + IFNULL(inflow.SALES_CASH, 0) + IFNULL(inflow.SALES_BANK, 0) + IFNULL(inflow.SALES_UPI, 0) + IFNULL(inflow.SALES_OTHERS, 0)
     - IFNULL(outflow.PROCUREMENT_CASH, 0) - IFNULL(outflow.PROCUREMENT_BANK, 0) - IFNULL(outflow.PROCUREMENT_UPI, 0) - IFNULL(outflow.PROCUREMENT_OTHERS, 0)
     - IFNULL(outflow.EXP_CASH, 0) - IFNULL(outflow.EXP_BANK, 0)) AS CLOSING_BALANCE
FROM shop sh
LEFT JOIN (
    /* --- Opening Balance --- */
    SELECT 
        s_sub.shop_id,
        SUM(income - exp) AS OLD_BALANCE
    FROM (
        SELECT 
            pay.shop_id, 
            IFNULL(pt.allocated_amount, 0) AS income, 
            0 AS exp 
        FROM payments pay
        LEFT JOIN (
            SELECT 
                pt.payment_id,
                COALESCE(st.shop, rt.shop) AS shop_id,
                SUM(pt.amount_settled) AS allocated_amount
            FROM pay_track pt
            LEFT JOIN service_trans st ON (pt.trans_id = st.trans_id AND pt.mode = 'service')
            LEFT JOIN receipt_trans rt ON (pt.trans_id = rt.trans_id AND pt.mode = 'sales')
            WHERE pt.mode IN ('service', 'sales')
            GROUP BY pt.payment_id, shop_id
        ) pt ON (pay.payment_id = pt.payment_id AND UPPER(pay.shop_id) = UPPER(pt.shop_id))
        WHERE pay.payment_date < STR_TO_DATE('$from_date','%d-%m-%Y') 
          AND pay.active_status = 'A' 
          AND pay.nature = 'CREDIT'
          AND pay.mode != 'CREDIT'
        
        UNION ALL
        
        SELECT shop_id, 0 AS income, amount AS exp FROM payments WHERE payment_date < STR_TO_DATE('$from_date','%d-%m-%Y') AND active_status = 'A' AND nature = 'DEBIT' AND mode != 'CREDIT'
        
        UNION ALL
        
        SELECT shop_id, 0 AS income, amount AS exp FROM expenditure WHERE `date` < STR_TO_DATE('$from_date','%d-%m-%Y')
    ) combined_old
    JOIN shop s_sub ON (combined_old.shop_id = s_sub.shop_id OR combined_old.shop_id = UPPER(SUBSTRING_INDEX(s_sub.shop_name, ' ', 1)))
    GROUP BY s_sub.shop_id
) ob ON ob.shop_id = sh.shop_id
LEFT JOIN (
    /* --- Inflow --- */
    SELECT 
        s_sub.shop_id,
        SUM(CASE WHEN pay_alloc.mode = 'CASH' THEN pay_alloc.service_amount ELSE 0 END) AS SERVICE_CASH,
        SUM(CASE WHEN pay_alloc.mode = 'CREDIT' THEN pay_alloc.service_amount ELSE 0 END) AS SERVICE_CREDIT,
        SUM(CASE WHEN pay_alloc.mode = 'BANK' THEN pay_alloc.service_amount ELSE 0 END) AS SERVICE_BANK,
        SUM(CASE WHEN pay_alloc.mode = 'UPI' THEN pay_alloc.service_amount ELSE 0 END) AS SERVICE_UPI,
        SUM(CASE WHEN pay_alloc.mode NOT IN ('CASH', 'CREDIT', 'BANK', 'UPI') THEN pay_alloc.service_amount ELSE 0 END) AS SERVICE_OTHERS,
        
        SUM(CASE WHEN pay_alloc.mode = 'CASH' THEN pay_alloc.sales_amount ELSE 0 END) AS SALES_CASH,
        SUM(CASE WHEN pay_alloc.mode = 'CREDIT' THEN pay_alloc.sales_amount ELSE 0 END) AS SALES_CREDIT,
        SUM(CASE WHEN pay_alloc.mode = 'BANK' THEN pay_alloc.sales_amount ELSE 0 END) AS SALES_BANK,
        SUM(CASE WHEN pay_alloc.mode = 'UPI' THEN pay_alloc.sales_amount ELSE 0 END) AS SALES_UPI,
        SUM(CASE WHEN pay_alloc.mode NOT IN ('CASH', 'CREDIT', 'BANK', 'UPI') THEN pay_alloc.sales_amount ELSE 0 END) AS SALES_OTHERS
    FROM (
        SELECT 
            pay.shop_id,
            pay.payment_date,
            pay.active_status,
            pay.nature,
            pay.mode,
            IFNULL(pt.service_amount, 0) AS service_amount,
            IFNULL(pt.sales_amount, 0) AS sales_amount
        FROM payments pay
        LEFT JOIN (
            SELECT 
                pt.payment_id,
                COALESCE(st.shop, rt.shop) AS shop_id,
                SUM(CASE WHEN pt.mode = 'service' THEN pt.amount_settled ELSE 0 END) AS service_amount,
                SUM(CASE WHEN pt.mode = 'sales' THEN pt.amount_settled ELSE 0 END) AS sales_amount
            FROM pay_track pt
            LEFT JOIN service_trans st ON (pt.trans_id = st.trans_id AND pt.mode = 'service')
            LEFT JOIN receipt_trans rt ON (pt.trans_id = rt.trans_id AND pt.mode = 'sales')
            GROUP BY pt.payment_id, shop_id
        ) pt ON (pay.payment_id = pt.payment_id AND UPPER(pay.shop_id) = UPPER(pt.shop_id))
    ) pay_alloc
    JOIN shop s_sub ON (pay_alloc.shop_id = s_sub.shop_id OR pay_alloc.shop_id = UPPER(SUBSTRING_INDEX(s_sub.shop_name, ' ', 1)))
    WHERE pay_alloc.payment_date BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y') AND STR_TO_DATE('$to_date','%d-%m-%Y') 
      AND pay_alloc.active_status = 'A' 
      AND pay_alloc.nature = 'CREDIT'
    GROUP BY s_sub.shop_id
) inflow ON inflow.shop_id = sh.shop_id
LEFT JOIN (
    /* --- Outflow --- */
    SELECT 
        s_sub.shop_id,
        SUM(CASE WHEN outflow_combined.source = 'payments' AND outflow_combined.mode = 'CASH' THEN outflow_combined.amount ELSE 0 END) AS PROCUREMENT_CASH,
        SUM(CASE WHEN outflow_combined.source = 'payments' AND outflow_combined.mode = 'CREDIT' THEN outflow_combined.amount ELSE 0 END) AS PROCUREMENT_CREDIT,
        SUM(CASE WHEN outflow_combined.source = 'payments' AND outflow_combined.mode = 'BANK' THEN outflow_combined.amount ELSE 0 END) AS PROCUREMENT_BANK,
        SUM(CASE WHEN outflow_combined.source = 'payments' AND outflow_combined.mode = 'UPI' THEN outflow_combined.amount ELSE 0 END) AS PROCUREMENT_UPI,
        SUM(CASE WHEN outflow_combined.source = 'payments' AND outflow_combined.mode NOT IN ('CASH', 'CREDIT', 'BANK', 'UPI') THEN outflow_combined.amount ELSE 0 END) AS PROCUREMENT_OTHERS,
        
        SUM(CASE WHEN outflow_combined.source = 'expenditure' AND outflow_combined.paytype_id = '1000003' THEN outflow_combined.amount ELSE 0 END) AS EXP_CASH,
        SUM(CASE WHEN outflow_combined.source = 'expenditure' AND outflow_combined.paytype_id = '1000005' THEN outflow_combined.amount ELSE 0 END) AS EXP_CREDIT,
        SUM(CASE WHEN outflow_combined.source = 'expenditure' AND outflow_combined.paytype_id = '1000002' THEN outflow_combined.amount ELSE 0 END) AS EXP_BANK
    FROM (
        SELECT shop_id, payment_date AS `date`, amount, mode, NULL AS paytype_id, 'payments' AS source, active_status FROM payments WHERE nature = 'DEBIT'
        UNION ALL
        SELECT shop_id, `date`, amount, NULL AS mode, paytype_id, 'expenditure' AS source, 'A' AS active_status FROM expenditure
    ) outflow_combined
    JOIN shop s_sub ON (outflow_combined.shop_id = s_sub.shop_id OR outflow_combined.shop_id = UPPER(SUBSTRING_INDEX(s_sub.shop_name, ' ', 1)))
    WHERE outflow_combined.`date` BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y') AND STR_TO_DATE('$to_date','%d-%m-%Y') 
      AND outflow_combined.active_status = 'A'
    GROUP BY s_sub.shop_id
) outflow ON outflow.shop_id = sh.shop_id
WHERE ('$shop_param' = '' OR sh.shop_id = '$shop_param')
  AND (ob.shop_id IS NOT NULL OR inflow.shop_id IS NOT NULL OR outflow.shop_id IS NOT NULL)
ORDER BY sh.shop_name;
";
                    // Store query result in array to reuse across sections
                    $rows_data = [];
                    $res = $conn->query($sql_comp);
                    while ($r = $res->fetch_assoc()) {
                        $rows_data[] = $r;
                    }
                    ?>

                    <!-- SECTION 1: FINANCIAL OVERVIEW SUMMARY -->
                    <div class="card shadow mb-4 border-left-primary">
                        <div
                            class="card-header py-3 bg-primary text-white d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold">Executive Cash Flow Summary Dashboard</h6>
                            <button class="btn btn-light btn-sm text-primary font-weight-bold"
                                onclick="print_table('summary_table')">Excel Summary</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center" id="summary_table"
                                    style="font-size: 80%; font-weight: bold; text-transform: uppercase;">
                                    <thead>
                                        <tr class="bg-gray-100 text-gray-800">
                                            <th class="text-left">Shop Name</th>
                                            <th>Opening Balance</th>
                                            <th>Services Income</th>
                                            <th>Sales Income</th>
                                            <th class="table-success">Total Inflow</th>
                                            <th>Procurement</th>
                                            <th>Operational</th>
                                            <th class="table-danger">Total Outflow</th>
                                            <th>Net Cash Flow</th>
                                            <th>Closing Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sum_gt = array_fill(0, 9, 0);
                                        foreach ($rows_data as $row) {
                                            $total_serv = $row["SERVICE_CASH"] + $row["SERVICE_BANK"] + $row["SERVICE_UPI"] + $row["SERVICE_OTHERS"];
                                            $total_sales = $row["SALES_CASH"] + $row["SALES_BANK"] + $row["SALES_UPI"] + $row["SALES_OTHERS"];
                                            $total_in = $total_serv + $total_sales;

                                            $total_proc = $row["PROCUREMENT_CASH"] + $row["PROCUREMENT_BANK"] + $row["PROCUREMENT_UPI"] + $row["PROCUREMENT_OTHERS"];
                                            $total_op = $row["EXP_CASH"] + $row["EXP_BANK"];
                                            $total_out = $total_proc + $total_op;

                                            $net_flow = $total_in - $total_out;
                                            ?>
                                            <tr>
                                                <td class="text-left"><?php echo $row["shop_name_full"]; ?></td>
                                                <td><?php echo number_format($row["OLD_BALANCE"], 2);
                                                $sum_gt[0] += $row["OLD_BALANCE"]; ?>
                                                </td>
                                                <td><?php echo number_format($total_serv, 2);
                                                $sum_gt[1] += $total_serv; ?>
                                                </td>
                                                <td><?php echo number_format($total_sales, 2);
                                                $sum_gt[2] += $total_sales; ?>
                                                </td>
                                                <td class="table-success">
                                                    <?php echo number_format($total_in, 2);
                                                    $sum_gt[3] += $total_in; ?></td>
                                                <td><?php echo number_format($total_proc, 2);
                                                $sum_gt[4] += $total_proc; ?>
                                                </td>
                                                <td><?php echo number_format($total_op, 2);
                                                $sum_gt[5] += $total_op; ?></td>
                                                <td class="table-danger">
                                                    <?php echo number_format($total_out, 2);
                                                    $sum_gt[6] += $total_out; ?>
                                                </td>
                                                <td style="color: <?php echo $net_flow >= 0 ? '#1cc88a' : '#e74a3b'; ?>;">
                                                    <?php echo number_format($net_flow, 2);
                                                    $sum_gt[7] += $net_flow; ?>
                                                </td>
                                                <td class="bg-light">
                                                    <?php echo number_format($row["CLOSING_BALANCE"], 2);
                                                    $sum_gt[8] += $row["CLOSING_BALANCE"]; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr class="bg-gray-200" style="font-weight: 800;">
                                            <td class="text-left">TOTAL</td>
                                            <?php for ($i = 0; $i < 9; $i++) {
                                                echo "<td>" . number_format($sum_gt[$i], 2) . "</td>";
                                            } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: SERVICES INCOME DETAILS -->
                    <div class="card shadow mb-4 border-left-info">
                        <div
                            class="card-header py-3 bg-info text-white d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold">1. Services Inflow Mode Breakdown</h6>
                            <button class="btn btn-light btn-sm text-info font-weight-bold"
                                onclick="print_table('services_table')">Excel Services</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center" id="services_table"
                                    style="font-size: 80%; font-weight: bold; text-transform: uppercase;">
                                    <thead>
                                        <tr class="bg-gray-100 text-gray-800">
                                            <th class="text-left">Shop Name</th>
                                            <th>Cash</th>
                                            <th>Credit</th>
                                            <th>Bank</th>
                                            <th>UPI</th>
                                            <th>Others</th>
                                            <th class="table-info">Total Services</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $serv_gt = array_fill(0, 6, 0);
                                        foreach ($rows_data as $row) {
                                            $total_serv = $row["SERVICE_CASH"] + $row["SERVICE_CREDIT"] + $row["SERVICE_BANK"] + $row["SERVICE_UPI"] + $row["SERVICE_OTHERS"];
                                            ?>
                                            <tr>
                                                <td class="text-left"><?php echo $row["shop_name_full"]; ?></td>
                                                <td><?php echo number_format($row["SERVICE_CASH"], 2);
                                                $serv_gt[0] += $row["SERVICE_CASH"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["SERVICE_CREDIT"], 2);
                                                $serv_gt[1] += $row["SERVICE_CREDIT"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["SERVICE_BANK"], 2);
                                                $serv_gt[2] += $row["SERVICE_BANK"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["SERVICE_UPI"], 2);
                                                $serv_gt[3] += $row["SERVICE_UPI"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["SERVICE_OTHERS"], 2);
                                                $serv_gt[4] += $row["SERVICE_OTHERS"]; ?>
                                                </td>
                                                <td class="table-info">
                                                    <?php echo number_format($total_serv, 2);
                                                    $serv_gt[5] += $total_serv; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr class="bg-gray-200" style="font-weight: 800;">
                                            <td class="text-left">TOTAL</td>
                                            <?php for ($i = 0; $i < 6; $i++) {
                                                echo "<td>" . number_format($serv_gt[$i], 2) . "</td>";
                                            } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: SALES INCOME DETAILS -->
                    <div class="card shadow mb-4 border-left-success">
                        <div
                            class="card-header py-3 bg-success text-white d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold">2. Product/Tire Sales Inflow Mode Breakdown</h6>
                            <button class="btn btn-light btn-sm text-success font-weight-bold"
                                onclick="print_table('sales_table')">Excel Sales</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center" id="sales_table"
                                    style="font-size: 80%; font-weight: bold; text-transform: uppercase;">
                                    <thead>
                                        <tr class="bg-gray-100 text-gray-800">
                                            <th class="text-left">Shop Name</th>
                                            <th>Cash</th>
                                            <th>Credit</th>
                                            <th>Bank</th>
                                            <th>UPI</th>
                                            <th>Others</th>
                                            <th class="table-success">Total Sales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sales_gt = array_fill(0, 6, 0);
                                        foreach ($rows_data as $row) {
                                            $total_sales = $row["SALES_CASH"] + $row["SALES_CREDIT"] + $row["SALES_BANK"] + $row["SALES_UPI"] + $row["SALES_OTHERS"];
                                            ?>
                                            <tr>
                                                <td class="text-left"><?php echo $row["shop_name_full"]; ?></td>
                                                <td><?php echo number_format($row["SALES_CASH"], 2);
                                                $sales_gt[0] += $row["SALES_CASH"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["SALES_CREDIT"], 2);
                                                $sales_gt[1] += $row["SALES_CREDIT"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["SALES_BANK"], 2);
                                                $sales_gt[2] += $row["SALES_BANK"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["SALES_UPI"], 2);
                                                $sales_gt[3] += $row["SALES_UPI"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["SALES_OTHERS"], 2);
                                                $sales_gt[4] += $row["SALES_OTHERS"]; ?>
                                                </td>
                                                <td class="table-success">
                                                    <?php echo number_format($total_sales, 2);
                                                    $sales_gt[5] += $total_sales; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr class="bg-gray-200" style="font-weight: 800;">
                                            <td class="text-left">TOTAL</td>
                                            <?php for ($i = 0; $i < 6; $i++) {
                                                echo "<td>" . number_format($sales_gt[$i], 2) . "</td>";
                                            } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: PROCUREMENT OUTFLOW DETAILS -->
                    <div class="card shadow mb-4 border-left-danger">
                        <div
                            class="card-header py-3 bg-danger text-white d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold">3. Procurement / Vendor Outflow Mode Breakdown</h6>
                            <button class="btn btn-light btn-sm text-danger font-weight-bold"
                                onclick="print_table('procurement_table')">Excel Procurement</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center" id="procurement_table"
                                    style="font-size: 80%; font-weight: bold; text-transform: uppercase;">
                                    <thead>
                                        <tr class="bg-gray-100 text-gray-800">
                                            <th class="text-left">Shop Name</th>
                                            <th>Cash</th>
                                            <th>Credit</th>
                                            <th>Bank</th>
                                            <th>UPI</th>
                                            <th>Others</th>
                                            <th class="table-danger">Total Procurement</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $proc_gt = array_fill(0, 6, 0);
                                        foreach ($rows_data as $row) {
                                            $total_proc = $row["PROCUREMENT_CASH"] + $row["PROCUREMENT_CREDIT"] + $row["PROCUREMENT_BANK"] + $row["PROCUREMENT_UPI"] + $row["PROCUREMENT_OTHERS"];
                                            ?>
                                            <tr>
                                                <td class="text-left"><?php echo $row["shop_name_full"]; ?></td>
                                                <td><?php echo number_format($row["PROCUREMENT_CASH"], 2);
                                                $proc_gt[0] += $row["PROCUREMENT_CASH"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["PROCUREMENT_CREDIT"], 2);
                                                $proc_gt[1] += $row["PROCUREMENT_CREDIT"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["PROCUREMENT_BANK"], 2);
                                                $proc_gt[2] += $row["PROCUREMENT_BANK"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["PROCUREMENT_UPI"], 2);
                                                $proc_gt[3] += $row["PROCUREMENT_UPI"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["PROCUREMENT_OTHERS"], 2);
                                                $proc_gt[4] += $row["PROCUREMENT_OTHERS"]; ?>
                                                </td>
                                                <td class="table-danger">
                                                    <?php echo number_format($total_proc, 2);
                                                    $proc_gt[5] += $total_proc; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr class="bg-gray-200" style="font-weight: 800;">
                                            <td class="text-left">TOTAL</td>
                                            <?php for ($i = 0; $i < 6; $i++) {
                                                echo "<td>" . number_format($proc_gt[$i], 2) . "</td>";
                                            } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 5: OPERATIONAL EXPENDITURES DETAILS -->
                    <div class="card shadow mb-4 border-left-secondary">
                        <div
                            class="card-header py-3 bg-secondary text-white d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold" style="color: white !important;">4. Operational Expenditure
                                Mode Breakdown</h6>
                            <button class="btn btn-light btn-sm text-secondary font-weight-bold"
                                onclick="print_table('operational_table')">Excel Expenditures</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center" id="operational_table"
                                    style="font-size: 80%; font-weight: bold; text-transform: uppercase;">
                                    <thead>
                                        <tr class="bg-gray-100 text-gray-800">
                                            <th class="text-left">Shop Name</th>
                                            <th>Cash</th>
                                            <th>Credit</th>
                                            <th>Bank</th>
                                            <th class="table-secondary" style="color: white !important;">Total
                                                Operational</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $op_gt = array_fill(0, 4, 0);
                                        foreach ($rows_data as $row) {
                                            $total_op = $row["EXP_CASH"] + $row["EXP_CREDIT"] + $row["EXP_BANK"];
                                            ?>
                                            <tr>
                                                <td class="text-left"><?php echo $row["shop_name_full"]; ?></td>
                                                <td><?php echo number_format($row["EXP_CASH"], 2);
                                                $op_gt[0] += $row["EXP_CASH"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["EXP_CREDIT"], 2);
                                                $op_gt[1] += $row["EXP_CREDIT"]; ?>
                                                </td>
                                                <td><?php echo number_format($row["EXP_BANK"], 2);
                                                $op_gt[2] += $row["EXP_BANK"]; ?>
                                                </td>
                                                <td class="table-secondary">
                                                    <?php echo number_format($total_op, 2);
                                                    $op_gt[3] += $total_op; ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr class="bg-gray-200" style="font-weight: 800;">
                                            <td class="text-left">TOTAL</td>
                                            <?php for ($i = 0; $i < 4; $i++) {
                                                echo "<td>" . number_format($op_gt[$i], 2) . "</td>";
                                            } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include "footer.php"; ?>

        </div>
    </div>

    <?php include "modals.php"; ?>
    <?php include "footer_include.php"; ?>

    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>

    <script>
        $(function () {
            $("#from_date").datepicker({ dateFormat: "dd-mm-yy" });
            $("#to_date").datepicker({ dateFormat: "dd-mm-yy" });
        });
    </script>

    <script src="js/tableToExcel.js"></script>

    <script>
        function print_table(tableId) {
            TableToExcel.convert(document.getElementById(tableId));
        }
    </script>

</body>

</html>