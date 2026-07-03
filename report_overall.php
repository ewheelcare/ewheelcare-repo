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
</head>

<body id="page-top">

    <div id="wrapper">

        <?php include "sidemenu.php"; ?>

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <?php include "topmenu.php"; ?>

                <div class="container-fluid">

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
                                                    $sel = ($shop_param == $row_shop["shop_id"]) ? "selected" : "";
                                                    ?>

                                                    <option
                                                        value="<?php echo strtoupper(explode(' ', trim($row_shop["shop_name"]))[0]); ?>"
                                                        <?php echo $sel; ?>>
                                                        <?php echo $row_shop["shop_name"]; ?>
                                                    </option>
                                                    <?php
                                                }
                                                ?>

                                            </select>
                                        </td>

                                        <td>
                                            <br>
                                            <input type="submit" class="btn btn-primary btn-sm" value="Search">
                                        </td>

                                        <td>
                                            <br>
                                            <button class="btn btn-success btn-sm" type="button"
                                                onclick="print_table()">Excel</button>
                                        </td>

                                    </tr>
                                </table>
                            </form>

                            <?php
                            $sql = "
SELECT 
    sh.shop_name AS shop_name_full,
    IFNULL(ob.OLD_BALANCE, 0) AS OLD_BALANCE,
    IFNULL(p.CASH, 0)   AS CASH,
    IFNULL(p.CREDIT, 0) AS CREDIT,
    IFNULL(p.BANK, 0)   AS BANK,
    IFNULL(p.UPI, 0)    AS UPI,
    IFNULL(p.OTHERS, 0) AS OTHERS,
    IFNULL(e.ECASH, 0)   AS ECASH,
    IFNULL(e.EBANK, 0)   AS EBANK,
    (IFNULL(ob.OLD_BALANCE, 0) + IFNULL(p.CASH, 0) + IFNULL(p.CREDIT, 0) + IFNULL(p.BANK, 0) + IFNULL(p.UPI, 0) + IFNULL(p.OTHERS, 0) - IFNULL(e.ECASH, 0) - IFNULL(e.EBANK, 0)) AS CLOSING_BALANCE
FROM shop sh
LEFT JOIN (
    /* --- OLD BALANCE (Opening Balance) --- */
    SELECT 
        s_sub.shop_id,
        SUM(income - exp) AS OLD_BALANCE
    FROM (
        SELECT 
            pay.shop_id, 
            (IFNULL(pt.service_amount, 0) + (pay.amount - IFNULL(pt.total_settled, 0))) AS income, 
            0 AS exp 
        FROM payments pay
        LEFT JOIN (
            SELECT 
                pt.payment_id,
                SUM(CASE WHEN pt.mode = 'service' THEN COALESCE(CAST(NULLIF(pt.amount_settled, '') AS DECIMAL(10,2)), 0) ELSE 0 END) AS service_amount,
                SUM(CASE WHEN pt.mode IN ('service', 'sales') THEN COALESCE(CAST(NULLIF(pt.amount_settled, '') AS DECIMAL(10,2)), 0) ELSE 0 END) AS total_settled
            FROM pay_track pt
            GROUP BY pt.payment_id
        ) pt ON (pay.payment_id = pt.payment_id)
        WHERE pay.payment_date < STR_TO_DATE('$from_date','%d-%m-%Y') 
          AND pay.active_status = 'A' 
          AND pay.nature = 'CREDIT'
        UNION ALL
        SELECT shop_id, 0 AS income, amount AS exp FROM expenditure WHERE `date` < STR_TO_DATE('$from_date','%d-%m-%Y')
    ) combined_old
    JOIN shop s_sub ON (combined_old.shop_id = s_sub.shop_id OR combined_old.shop_id = UPPER(SUBSTRING_INDEX(s_sub.shop_name, ' ', 1)))
    GROUP BY s_sub.shop_id
) ob ON ob.shop_id = sh.shop_id
LEFT JOIN (
    /* --- INCOME FROM PAYMENTS --- */
    SELECT 
        s_sub.shop_id,
        SUM(CASE WHEN mode = 'CASH' THEN service_total ELSE 0 END) AS CASH,
        SUM(CASE WHEN mode = 'CREDIT' THEN service_total ELSE 0 END) AS CREDIT,
        SUM(CASE WHEN mode = 'BANK' THEN service_total ELSE 0 END) AS BANK,
        SUM(CASE WHEN mode = 'UPI' THEN service_total ELSE 0 END) AS UPI,
        SUM(CASE WHEN mode NOT IN ('CASH', 'CREDIT', 'BANK', 'UPI') THEN service_total ELSE 0 END) AS OTHERS
    FROM (
        SELECT 
            pay.payment_id,
            pay.payment_date,
            pay.shop_id,
            pay.mode,
            pay.nature,
            pay.active_status,
            (IFNULL(pt.service_amount, 0) + (pay.amount - IFNULL(pt.total_settled, 0))) AS service_total
        FROM payments pay
        LEFT JOIN (
            SELECT 
                pt.payment_id,
                SUM(CASE WHEN pt.mode = 'service' THEN COALESCE(CAST(NULLIF(pt.amount_settled, '') AS DECIMAL(10,2)), 0) ELSE 0 END) AS service_amount,
                SUM(CASE WHEN pt.mode IN ('service', 'sales') THEN COALESCE(CAST(NULLIF(pt.amount_settled, '') AS DECIMAL(10,2)), 0) ELSE 0 END) AS total_settled
            FROM pay_track pt
            GROUP BY pt.payment_id
        ) pt ON (pay.payment_id = pt.payment_id)
    ) pay
    JOIN shop s_sub ON (pay.shop_id = s_sub.shop_id OR pay.shop_id = UPPER(SUBSTRING_INDEX(s_sub.shop_name, ' ', 1)))
    WHERE pay.payment_date BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y') AND STR_TO_DATE('$to_date','%d-%m-%Y') AND pay.active_status = 'A' AND pay.nature = 'CREDIT'
    GROUP BY s_sub.shop_id
) p ON p.shop_id = sh.shop_id
LEFT JOIN (
    /* --- EXPENDITURE --- */
    SELECT
        s_sub.shop_id,
        SUM(CASE WHEN paytype_id = '1000003' THEN amount ELSE 0 END) AS ECASH,
        SUM(CASE WHEN paytype_id = '1000002' THEN amount ELSE 0 END) AS EBANK
    FROM expenditure exp_t
    JOIN shop s_sub ON (exp_t.shop_id = s_sub.shop_id OR exp_t.shop_id = UPPER(SUBSTRING_INDEX(s_sub.shop_name, ' ', 1)))
    WHERE exp_t.`date` BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y') AND STR_TO_DATE('$to_date','%d-%m-%Y')
    GROUP BY s_sub.shop_id
) e ON e.shop_id = sh.shop_id
WHERE ('$shop_param' = '' OR sh.shop_id = '$shop_param')
  AND (ob.shop_id IS NOT NULL OR p.shop_id IS NOT NULL OR e.shop_id IS NOT NULL)
ORDER BY sh.shop_name;
";

                            $result = $conn->query($sql);
                            $gt = array_fill(0, 10, 0); // Corrected size
                            ?>

                            <table class="table table-striped table-bordered" id="report_table" border="1"
                                style="width:100%;font-size:75%;font-weight:bold;text-transform:uppercase">

                                <thead>
                                    <tr style="background-color:#f0f0f0 !important;">
                                        <th rowspan="2" style="color:#000 !important; vertical-align:middle;">SHOP NAME
                                        </th>
                                        <th rowspan="2" style="color:#000 !important; vertical-align:middle;">OPEN
                                            BALANCE</th>
                                        <th colspan="5" style="color:#000 !important;">INCOME</th>
                                        <th colspan="2" style="color:#000 !important;">EXPENDITURE</th>
                                        <th rowspan="2" style="color:#000 !important; vertical-align:middle;">NET INCOME
                                        </th>
                                        <th rowspan="2" style="color:#000 !important; vertical-align:middle;">CLOSING
                                            BALANCE</th>
                                    </tr>
                                    <tr style="background-color:#f0f0f0 !important;">
                                        <th style="color:#000 !important;">CASH</th>
                                        <th style="color:#000 !important;">CREDIT</th>
                                        <th style="color:#000 !important;">BANK</th>
                                        <th style="color:#000 !important;">UPI</th>
                                        <th style="color:#000 !important;">OTHERS</th>
                                        <th style="color:#000 !important;">CASH</th>
                                        <th style="color:#000 !important;">BANK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) {
                                        $income = $row["CASH"] + $row["CREDIT"] + $row["BANK"] + $row["UPI"] + $row["OTHERS"];
                                        $expense = $row["ECASH"] + $row["EBANK"];
                                        $net_income = $income - $expense;
                                        ?>
                                        <tr>
                                            <td><?php echo $row["shop_name_full"]; ?></td>
                                            <td class="text-primary">
                                                <?php echo number_format($row["OLD_BALANCE"], 2);
                                                $gt[0] += $row["OLD_BALANCE"]; ?>
                                            </td>

                                            <td><?php echo number_format($row["CASH"], 2);
                                            $gt[1] += $row["CASH"]; ?></td>
                                            <td><?php echo number_format($row["CREDIT"], 2);
                                            $gt[2] += $row["CREDIT"]; ?>
                                            </td>
                                            <td><?php echo number_format($row["BANK"], 2);
                                            $gt[3] += $row["BANK"]; ?></td>
                                            <td><?php echo number_format($row["UPI"], 2);
                                            $gt[4] += $row["UPI"]; ?></td>
                                            <td><?php echo number_format($row["OTHERS"], 2);
                                            $gt[5] += $row["OTHERS"]; ?></td>

                                            <td class="text-danger">
                                                <?php echo number_format($row["ECASH"], 2);
                                                $gt[6] += $row["ECASH"]; ?>
                                            </td>
                                            <td class="text-danger">
                                                <?php echo number_format($row["EBANK"], 2);
                                                $gt[7] += $row["EBANK"]; ?>
                                            </td>

                                            <td style="font-weight: 900;">
                                                <?php echo number_format($net_income, 2);
                                                $gt[8] += $net_income; ?>
                                            </td>
                                            <td class="bg-light" style="font-size: 1.1em;">
                                                <?php echo number_format($row["CLOSING_BALANCE"], 2);
                                                $gt[9] += $row["CLOSING_BALANCE"]; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr style="background:#e8e8e8;font-weight:bold;">
                                        <td>TOTAL</td>
                                        <?php
                                        for ($i = 0; $i < 10; $i++) {
                                            echo "<td>" . number_format($gt[$i], 2) . "</td>";
                                        }
                                        ?>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>

            <?php include "footer.php"; ?>

        </div>
    </div>

    <!-- DEBUG SECTION -->
    <div class="container-fluid mt-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Debug: Individual Payments (Used in Summary Above)</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Shop ID</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $debug_sql = "SELECT 
                                        pay.payment_id, 
                                        pay.shop_id, 
                                        (IFNULL(pt.service_amount, 0) + (pay.amount - IFNULL(pt.total_settled, 0))) AS amount, 
                                        pay.mode, 
                                        pay.payment_date 
                                      FROM payments pay
                                      LEFT JOIN (
                                          SELECT 
                                              pt.payment_id,
                                              SUM(CASE WHEN pt.mode = 'service' THEN COALESCE(CAST(NULLIF(pt.amount_settled, '') AS DECIMAL(10,2)), 0) ELSE 0 END) AS service_amount,
                                              SUM(CASE WHEN pt.mode IN ('service', 'sales') THEN COALESCE(CAST(NULLIF(pt.amount_settled, '') AS DECIMAL(10,2)), 0) ELSE 0 END) AS total_settled
                                          FROM pay_track pt
                                          GROUP BY pt.payment_id
                                      ) pt ON (pay.payment_id = pt.payment_id)
                                      WHERE pay.payment_date BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y') 
                                        AND STR_TO_DATE('$to_date','%d-%m-%Y') 
                                        AND pay.active_status = 'A'
                                        AND pay.nature = 'CREDIT'
                                        AND (IFNULL(pt.service_amount, 0) + (pay.amount - IFNULL(pt.total_settled, 0))) > 0
                                      ORDER BY pay.shop_id, pay.payment_id";
                        $debug_res = $conn->query($debug_sql);
                        while ($d = $debug_res->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$d['payment_id']}</td>
                                    <td>{$d['shop_id']}</td>
                                    <td>{$d['amount']}</td>
                                    <td>{$d['mode']}</td>
                                    <td>{$d['payment_date']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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
        function print_table() {
            TableToExcel.convert(document.getElementById("report_table"));
        }
    </script>

</body>

</html>