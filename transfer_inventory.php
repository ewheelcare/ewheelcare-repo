<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "header_include.php";
    include "db_config.php";
    ?>
    <style>
        .transfer-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .transfer-card .card-header {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            color: white;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #dataTable thead tr th {
            background: #2c3e50;
            color: #ecf0f1;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 14px 16px;
            border: none;
            white-space: nowrap;
        }
        #dataTable tbody tr { transition: background 0.2s; }
        #dataTable tbody tr:hover { background-color: #f8f9fa; }
        #dataTable tbody td {
            vertical-align: middle;
            padding: 11px 16px;
            font-size: 13px;
            color: #2d3436;
            border-color: #f0f0f0;
        }
        .item-name-cell { font-weight: 600; color: #2c3e50; }
        .stock-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
            min-width: 60px;
            text-align: center;
        }
        .stock-high { background: #d4edda; color: #155724; }
        .stock-low  { background: #fff3cd; color: #856404; }
        .stock-zero { background: #f8d7da; color: #721c24; }
        .transfer-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .transfer-group input[type=number] {
            width: 90px;
            height: 34px;
            border: 1.5px solid #dee2e6;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 13px;
            text-align: center;
            transition: border-color 0.2s;
        }
        .transfer-group input[type=number]:focus {
            border-color: #e74c3c;
            outline: none;
            box-shadow: 0 0 0 3px rgba(231,76,60,0.15);
        }
        .btn-transfer {
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            transition: background 0.2s;
        }
        .btn-transfer:hover { background: #c0392b; color: white; }
        .dropdown-menu {
            min-width: 160px;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }
        .dropdown-item {
            font-size: 13px;
            padding: 9px 16px;
        }
        .dropdown-item:hover { background: #fdf2f2; color: #c0392b; }
        .dataTables_filter input {
            border-radius: 20px !important;
            border: 1.5px solid #dee2e6 !important;
            padding: 4px 14px !important;
            font-size: 13px !important;
        }
        .page-subtitle {
            font-size: 13px;
            color: #636e72;
            margin-bottom: 16px;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include "sidemenu.php"; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "topmenu.php"; ?>
                <div class="container-fluid py-3">

                    <p class="page-subtitle"><i class="fas fa-exchange-alt mr-1"></i> Transfer stock from the Global Warehouse to a specific shop.</p>

                    <div class="card transfer-card">
                        <div class="card-header">
                            <i class="fas fa-warehouse"></i>
                            Stock Transfer &mdash; Warehouse &rarr; Shop
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width:5%">Item Id</th>
                                            <th style="width:45%">Item Name</th>
                                            <th style="width:20%;text-align:center">Warehouse Stock</th>
                                            <th style="width:30%">Transfer to Shop</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql = "SELECT SUM(inv.qty) as total_qty, it.ITEM_NAME ,it.item_id
                                            FROM inventory inv 
                                            JOIN item it ON inv.item_id = it.ITEM_ID
                                            GROUP BY it.ITEM_NAME,it.ITEM_ID
                                            HAVING total_qty > 0
                                            ORDER BY it.ITEM_NAME";
                                    $result = $conn->query($sql);
                                    $row_count = 0;
                                    while ($row = $result->fetch_assoc()) {
                                        $item_name = $row["ITEM_NAME"];
                                        $item_id = $row["item_id"];
                                        $qty = $row["total_qty"];
                                        $safe_id = "item_" . $row_count++;
                                        $badge_class = $qty >= 10 ? 'stock-high' : ($qty >= 3 ? 'stock-low' : 'stock-zero');
                                    ?>
                                        <tr>
                                            <td class="item-name-cell"><?php echo $item_id; ?></td>
                                            <td class="item-name-cell"><?php echo htmlspecialchars($item_name); ?></td>
                                            <td style="text-align:center">
                                                <span class="stock-badge <?php echo $badge_class; ?>"><?php echo number_format($qty, 0); ?></span>
                                            </td>
                                            <td>
                                                <div class="transfer-group">
                                                    <input type="number" id="qty_<?php echo $safe_id; ?>" placeholder="Qty" min="1" max="<?php echo $qty; ?>" step="1">
                                                    <div class="dropdown">
                                                        <button class="btn-transfer dropdown-toggle" type="button" data-toggle="dropdown">
                                                            <i class="fas fa-truck fa-sm"></i> Transfer To
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                        onclick="transferStockByName(
                                                        '<?php echo $item_id; ?>',
                                                        '<?php echo $safe_id; ?>',
                                                        '1000002',
                                                        'Sirasapalli'
                                                        )">    

                                                        <i class="fas fa-store mr-1"></i> 
                                                            Sirasapalli Shop
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                            onclick="transferStockByName(
                                                            '<?php echo $item_id; ?>',
                                                            '<?php echo $safe_id; ?>',
                                                            '1000003',
                                                            'Gajuwaka'
                                                            )">
                                                               <i class="fas fa-store mr-1"></i> 
                                                               Gajuwaka Shop
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
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

    <?php include "modals.php"; ?>
    <?php include "footer_include.php"; ?>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>

    <script>

        function transferStockByName(
    itemid,
    elementId,
    toShopId,
    ShopName
) {

    // =========================================
    // GET QUANTITY INPUT
    // =========================================
    
    let qtyInput = document.getElementById(
        "qty_" + elementId
    );

    let qty = parseFloat(qtyInput.value);

    let maxQty = parseFloat(
        qtyInput.getAttribute("max")
    );

    // =========================================
    // VALIDATION
    // =========================================
    if (!qty || qty <= 0 || isNaN(qty)) {

        alert("Please enter valid quantity");

        qtyInput.focus();

        return;
    }

    if (qty > maxQty) {

        alert(
            "Cannot transfer " +
            qty +
            ". Available stock is only " +
            maxQty
        );

        qtyInput.focus();

        return;
    }

    // =========================================
    // CONFIRMATION
    // =========================================
    let confirmTransfer = confirm(
        "Transfer " +
        qty +
        " × [" +
        itemid +
        "] to " +
        ShopName +
        " shop ?"
    );

    if (!confirmTransfer) {
        return;
    }

    // =========================================
    // DISABLE INPUT DURING REQUEST
    // =========================================
    qtyInput.disabled = true;

    // =========================================
    // AJAX CALL
    // =========================================
    $.ajax({

        url: "draw_deposit.php",

        type: "POST",

        dataType: "json",

        data: {

            from_shop: "WAREHOUSE",

            from_qty: qty,

            to_shop: toShopId,

            item_id: itemid,

            Shop_Name:ShopName
        },

        // =====================================
        // BEFORE SEND
        // =====================================
        beforeSend: function () {

            console.log(
                "Starting transfer..."
            );
        },

        // =====================================
        // SUCCESS RESPONSE
        // =====================================
        success: function (response) {

            console.log(response);

            /*
            Expected JSON:

            {
                "success": true,
                "message": "Transfer successful",
                "trans_id": "TRN123"
            }
            */

            if (response.success === true) {

                let msg =
                    "✅ " +
                    response.message;

                // optional trans id
                if (response.trans_id) {

                    msg +=
                        "\nTransaction ID : " +
                        response.trans_id;
                }

                alert(msg);

                // reload page
                location.reload();

            } else {

                alert(
                    "⚠️ " +
                    response.message
                );

                qtyInput.disabled = false;
            }
        },

        // =====================================
        // AJAX ERROR
        // =====================================
        error: function (
            xhr,
            status,
            error
        ) {

            console.log(
                "AJAX ERROR"
            );

            console.log(xhr);

            console.log(
                xhr.responseText
            );

            alert(
                "Server Error : " +
                error
            );

            qtyInput.disabled = false;
        }
    });
}
    </script>

</body>
</html>