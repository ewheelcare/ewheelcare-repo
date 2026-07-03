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

                            <form method="post">
                                <table class="table table-striped">
                                    <tr>
                                        <td>From date<br><input type="text" name="from_date" id="from_date"
                                                class="form-control"></td>
                                        <td>To date<br><input type="text" name="to_date" id="to_date"
                                                class="form-control"></td>


                                        <td>Shop<br>
                                            <select class="form-control" name="shop">
                                                <option value="">--select---</option>
                                                <?php
                                                $sql = "SELECT shop_id,shop_name FROM shop ORDER BY shop_id";
                                                $res = $conn->query($sql);

                                                $shop_array = [];
                                                $shop_name_array = [];
                                                $shop_cnt = 0;

                                                while ($r = $res->fetch_assoc()) {
                                                    $shop_array[$shop_cnt] = $r['shop_id'];
                                                    $shop_name_array[$shop_cnt++] = $r['shop_name'];
                                                    $shop_short = strtoupper(trim(explode('-', $r['shop_name'])[0]));
                                                    ?>
                                                    <option value="<?= $shop_short ?>"><?= $shop_short ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>

                                        <td><br><input type="submit" class="btn btn-primary btn-sm" value="Search"></td>
                                    </tr>
                                </table>
                            </form>

                            <?php
                            $from_date = $_POST["from_date"] ?? '';
                            $to_date = $_POST["to_date"] ?? '';

                            $shop_param = strtoupper(trim(strtok($_POST["shop"] ?? '', '- ')));


                            // customer handling SAME as old logic
                            

                            // services
                            $sql = "select service_id,service_name from service";
                            $res = $conn->query($sql);

                            $serv_array = [];
                            $serv_names = [];
                            $serv_total_array = [];
                            $serv_qty_array = [];
                            $serv_cnt = 0;

                            while ($row = $res->fetch_assoc()) {
                                $serv_array[$serv_cnt] = $row['service_id'];
                                $serv_names[$row['service_id']] = $row['service_name'];
                                $serv_total_array[$serv_cnt] = 0;
                                $serv_qty_array[$serv_cnt] = 0;
                                $serv_cnt++;
                            }
                            ?>

                            <button class="btn btn-sm btn-primary" onclick="print_table()"
                                style="float:right;margin:3px;">Excel</button>

                            <div style="overflow-x:auto;">
                                <table class="table table-striped table-bordered" id="report_table"
                                    style="font-size:75%;font-weight:bold;text-transform:uppercase">

                                    <thead>
                                        <tr style="background:#343a40; color:white; font-weight:bold; font-size:11px;">
                                            <th style="padding:3px;" rowspan="2">Shop</th>
                                            <?php foreach ($serv_array as $sid) { ?>
                                                <th style="padding:3px;text-align:center;" colspan="2">
                                                    <?= $serv_names[$sid] ?>
                                                </th>
                                            <?php } ?>
                                            <th style="padding:3px;text-align:center;" colspan="2">TOTAL</th>
                                        </tr>
                                        <tr style="background:#343a40; color:white; font-weight:bold; font-size:11px;">
                                            <?php foreach ($serv_array as $sid) { ?>
                                                <th style="padding:3px;">Qty</th>
                                                <th style="padding:3px;">Amount</th>
                                            <?php } ?>
                                            <th style="padding:3px;">Qty</th>
                                            <th style="padding:3px;">Amount</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php
                                        // ----------- SINGLE OPTIMIZED QUERY (OLD LOGIC PRESERVED) ------------
                                        
                                        $sql = "SELECT IFNULL(t.shop,'1000002') as shop_id";

                                        foreach ($serv_array as $sid) {
                                            $sql .= ", SUM(CASE WHEN d.service_id='$sid' THEN (d.total + IFNULL(d.tax_amount, 0) + IFNULL(d.tax_amount_sgst, 0)) ELSE 0 END) AS s_$sid";
                                            $sql .= ", SUM(CASE WHEN d.service_id='$sid' THEN IFNULL(d.qty, 0) ELSE 0 END) AS q_$sid";
                                        }

                                        $sql .= ", SUM(d.total + IFNULL(d.tax_amount, 0) + IFNULL(d.tax_amount_sgst, 0)) as grand_total";
                                        $sql .= ", SUM(IFNULL(d.qty, 0)) as grand_qty
FROM service_trans t
JOIN service_trans_det d ON t.trans_id=d.trans_id
WHERE t.active_status='A'
AND d.active_status='A'";

                                        // SAME DATE LOGIC
                                        if ($from_date != "") {
                                            $sql .= " AND IFNULL(t.trans_date,t.trans_date) BETWEEN 
STR_TO_DATE('$from_date','%d-%m-%Y')
AND STR_TO_DATE('$to_date','%d-%m-%Y')";
                                        }


                                        // SAME SHOP LOGIC
                                        if ($shop_param != "") {
                                            $sql .= " AND IFNULL(t.shop,'SIRASAPALLI')='$shop_param'";
                                        }


                                        $sql .= " GROUP BY IFNULL(t.shop,'SIRASAPALLI')";

                                        $res = $conn->query($sql);

                                        // store data
                                        $data = [];
                                        while ($row = $res->fetch_assoc()) {
                                            $data[$row['shop_id']] = $row;
                                        }

                                        // display rows
                                        $overall = 0;
                                        $overall_qty = 0;

                                        foreach ($data as $shop_name => $row) {
                                            $gt = 0;
                                            $gqty = 0;
                                            ?>

                                            <tr>
                                                <td><?= $shop_name ?></td>
                                                <?php
                                                $i = 0;
                                                foreach ($serv_array as $sid) {

                                                    $val = $row["s_$sid"] ?? 0;
                                                    $qty = $row["q_$sid"] ?? 0;
                                                    $serv_total_array[$i] += $val;
                                                    $serv_qty_array[$i] += $qty;
                                                    $gt += $val;
                                                    $gqty += $qty;
                                                    ?>

                                                    <td><?= $qty ?></td>
                                                    <td><?= $val ?></td>

                                                    <?php $i++;
                                                } ?>

                                                <td><?= $gqty ?></td>
                                                <td><?= $gt ?></td>
                                            </tr>

                                            <?php $overall += $gt;
                                            $overall_qty += $gqty;
                                        } ?>

                                        <tr>
                                            <td>Total</td>
                                            <?php
                                            $i = 0;
                                            foreach ($serv_array as $sid) { ?>
                                                <td><?= $serv_qty_array[$i] ?></td>
                                                <td><?= $serv_total_array[$i] ?></td>
                                                <?php $i++;
                                            } ?>
                                            <td><?= $overall_qty ?></td>
                                            <td><?= $overall ?></td>
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
    </div>

    <?php include "footer_include.php"; ?>

    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>


    <script>
        $(function () {
            $("#from_date,#to_date").datepicker({ dateFormat: "dd-mm-yy" });
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