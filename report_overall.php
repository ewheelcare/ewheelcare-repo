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
    SUM(IFNULL(ob.OLD_BALANCE, 0)) AS OLD_BALANCE,
    SUM(IFNULL(p.CASH, 0))   AS CASH,
    SUM(IFNULL(p.CREDIT, 0)) AS CREDIT,
    SUM(IFNULL(p.BANK, 0))   AS BANK,

    SUM(IFNULL(e.ECASH, 0))   AS ECASH,
    SUM(IFNULL(e.ECREDIT, 0)) AS ECREDIT,
    SUM(IFNULL(e.EBANK, 0))   AS EBANK,

    (SUM(IFNULL(ob.OLD_BALANCE, 0)) + SUM(IFNULL(p.CASH, 0)) + SUM(IFNULL(p.CREDIT, 0)) + SUM(IFNULL(p.BANK, 0)) - SUM(IFNULL(e.ECASH, 0)) - SUM(IFNULL(e.ECREDIT, 0)) - SUM(IFNULL(e.EBANK, 0))) AS CLOSING_BALANCE

FROM shop sh
LEFT JOIN (
    /* --- OLD BALANCE (Opening Balance) --- */
    SELECT 
        shop_id,
        (IFNULL(income_sum, 0) - IFNULL(exp_sum, 0)) AS OLD_BALANCE
    FROM (
        SELECT 
            shop_id, 
            SUM(amount) AS income_sum 
        FROM payments 
        WHERE payment_date < STR_TO_DATE('$from_date','%d-%m-%Y') 
          AND active_status = 'A' 
        GROUP BY shop_id
    ) p_o
    LEFT JOIN (
        SELECT 
            shop_id, 
            SUM(amount) AS exp_sum 
        FROM expenditure 
        WHERE `date` < STR_TO_DATE('$from_date','%d-%m-%Y') 
        GROUP BY shop_id
    ) e_o USING (shop_id)
) ob ON (ob.shop_id = sh.shop_id OR ob.shop_id = UPPER(SUBSTRING_INDEX(sh.shop_name, ' ', 1)))

LEFT JOIN (
    /* --- INCOME FROM PAYMENTS --- */
    SELECT 
        shop_id,
        SUM(CASE WHEN mode = 'CASH' THEN amount ELSE 0 END) AS CASH,
        SUM(CASE WHEN mode = 'CREDIT' THEN amount ELSE 0 END) AS CREDIT,
        SUM(CASE WHEN mode NOT IN ('CASH', 'CREDIT') THEN amount ELSE 0 END) AS BANK
    FROM payments
    WHERE payment_date BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y')
      AND STR_TO_DATE('$to_date','%d-%m-%Y') 
      AND active_status = 'A'
    GROUP BY shop_id
) p ON (p.shop_id = sh.shop_id OR p.shop_id = UPPER(SUBSTRING_INDEX(sh.shop_name, ' ', 1)))

LEFT JOIN (
    /* --- EXPENDITURE --- */
    SELECT
        shop_id,
        SUM(CASE WHEN paytype_id = '1000003' THEN amount ELSE 0 END) AS ECASH,
        SUM(CASE WHEN paytype_id = '1000005' THEN amount ELSE 0 END) AS ECREDIT,
        SUM(CASE WHEN paytype_id = '1000002' THEN amount ELSE 0 END) AS EBANK
    FROM expenditure
    WHERE `date` BETWEEN STR_TO_DATE('$from_date','%d-%m-%Y')
      AND STR_TO_DATE('$to_date','%d-%m-%Y')
    GROUP BY shop_id
) e ON (e.shop_id = sh.shop_id OR e.shop_id = UPPER(SUBSTRING_INDEX(sh.shop_name, ' ', 1)))

WHERE ('$shop_param' = '' OR sh.shop_id = '$shop_param' OR UPPER(SUBSTRING_INDEX(sh.shop_name, ' ', 1)) = '$shop_param')
  AND (IFNULL(p.shop_id, '') != '' OR IFNULL(e.shop_id, '') != '' OR IFNULL(ob.shop_id, '') != '')

GROUP BY sh.shop_id
ORDER BY sh.shop_name;
";

                            $result = $conn->query($sql);
                            $gt = array_fill(0, 9, 0); // Corrected size
                            ?>

                            <table class="table table-striped table-bordered" id="report_table" border="1"
                                style="width:100%;font-size:75%;font-weight:bold;text-transform:uppercase">

                                <thead>
                                    <tr style="background-color:#f0f0f0 !important;">
                                        <th rowspan="2" style="color:#000 !important; vertical-align:middle;">SHOP NAME</th>
                                        <th rowspan="2" style="color:#000 !important; vertical-align:middle;">OLD BALANCE</th>
                                        <th colspan="3" style="color:#000 !important;">INCOME</th>
                                        <th colspan="3" style="color:#000 !important;">EXPENDITURE</th>
                                        <th rowspan="2" style="color:#000 !important; vertical-align:middle;">NET INCOME</th>
                                        <th rowspan="2" style="color:#000 !important; vertical-align:middle;">CLOSING BALANCE</th>
                                    </tr>
                                    <tr style="background-color:#f0f0f0 !important;">
                                        <th style="color:#000 !important;">CASH</th>
                                        <th style="color:#000 !important;">CREDIT</th>
                                        <th style="color:#000 !important;">BANK</th>
                                        <th style="color:#000 !important;">CASH</th>
                                        <th style="color:#000 !important;">CREDIT</th>
                                        <th style="color:#000 !important;">BANK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { 
                                        $income = $row["CASH"] + $row["CREDIT"] + $row["BANK"];
                                        $expense = $row["ECASH"] + $row["ECREDIT"] + $row["EBANK"];
                                        $net_income = $income - $expense;
                                        ?>
                                        <tr>
                                            <td><?php echo $row["shop_name_full"]; ?></td>
                                            <td class="text-primary"><?php echo number_format($row["OLD_BALANCE"], 2); $gt[0] += $row["OLD_BALANCE"]; ?></td>
                                            
                                            <td><?php echo number_format($row["CASH"], 2); $gt[1] += $row["CASH"]; ?></td>
                                            <td><?php echo number_format($row["CREDIT"], 2); $gt[2] += $row["CREDIT"]; ?></td>
                                            <td><?php echo number_format($row["BANK"], 2); $gt[3] += $row["BANK"]; ?></td>

                                            <td class="text-danger"><?php echo number_format($row["ECASH"], 2); $gt[4] += $row["ECASH"]; ?></td>
                                            <td class="text-danger"><?php echo number_format($row["ECREDIT"], 2); $gt[5] += $row["ECREDIT"]; ?></td>
                                            <td class="text-danger"><?php echo number_format($row["EBANK"], 2); $gt[6] += $row["EBANK"]; ?></td>

                                            <td style="font-weight: 900;"><?php echo number_format($net_income, 2); $gt[7] += $net_income; ?></td>
                                            <td class="bg-light" style="font-size: 1.1em;"><?php echo number_format($row["CLOSING_BALANCE"], 2); $gt[8] += $row["CLOSING_BALANCE"]; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr style="background:#e8e8e8;font-weight:bold;">
                                        <td>TOTAL</td>
                                        <?php
                                        for ($i = 0; $i < 9; $i++) {
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
```