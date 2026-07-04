<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function write_log($type, $data = [])
{
    error_log(json_encode([
        "time" => date("Y-m-d H:i:s"),
        "type" => $type,
        "data" => $data
    ]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    include "db_config.php";

    $conn->autocommit(false);

    try {
        $user_id = $_SESSION["user_id"] ?? "";
        $login_shop = $_SESSION["shop"] ?? "";

        if ($user_id == "" || $login_shop == "") {
            echo json_encode([
                "success" => false,
                "message" => "Session expired"
            ]);
            exit();
        }

        $item_id  = trim($_POST["item_id"] ?? '');
        $from_shop_name = trim($_POST["from_shop_name"] ?? '');
        $to_shop_id  = trim($_POST["to_shop_id"] ?? '');
        $to_shop_name = trim($_POST["to_shop_name"] ?? '');
        $from_qty = (float)($_POST["qty"] ?? 0);

        if ($item_id == '') {
            throw new Exception("Item ID missing");
        }
        if ($from_shop_name == '') {
            throw new Exception("Source shop missing");
        }
        if ($to_shop_name == '') {
            throw new Exception("Destination shop missing");
        }
        if ($from_qty <= 0) {
            throw new Exception("Invalid quantity");
        }

        write_log("SHOP_TRANSFER_REQUEST", [
            "item_id" => $item_id,
            "from_shop_name" => $from_shop_name,
            "to_shop_id" => $to_shop_id,
            "to_shop_name" => $to_shop_name,
            "qty" => $from_qty,
            "user_id" => $user_id
        ]);

        $conn->begin_transaction();

        $sql = "
            SELECT qty
            FROM inventory_shop
            WHERE item_id = ? AND shop_name = ?
            FOR UPDATE
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $item_id, $from_shop_name);
        $stmt->execute();
        $res = $stmt->get_result();

        if (!$row = $res->fetch_assoc()) {
            throw new Exception("Item not found in source shop inventory");
        }

        $available_qty = (float)$row['qty'];

        if ($from_qty > $available_qty) {
            throw new Exception("Insufficient stock in source shop. Available Qty = " . $available_qty);
        }

        $stmt->close();

        $sql = "
            UPDATE inventory_shop
            SET qty = qty - ?, modified_by = ?, modified_on = NOW()
            WHERE item_id = ? AND shop_name = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dsss", $from_qty, $user_id, $item_id, $from_shop_name);
        $stmt->execute();

        if ($stmt->affected_rows <= 0) {
            throw new Exception("Source shop inventory update failed");
        }
        $stmt->close();

        $sql = "
            INSERT INTO inventory_shop
            (item_id, shop_name, qty, modified_by, modified_on)
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
            qty = qty + VALUES(qty),
            modified_by = VALUES(modified_by),
            modified_on = NOW()
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssds", $item_id, $to_shop_name, $from_qty, $user_id);
        $stmt->execute();
        $stmt->close();

        $trans_id = uniqid("TRN_STS_");

        $sql = "
            INSERT INTO inventory_trans
            (trans_id, item_id, shop_id, qty, trans_type, created_by, remarks)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($sql);

        $trans_type_out = "TRANSFER_OUT";
        $remarks_out = "Transferred from " . $from_shop_name . " to " . $to_shop_name;
        $stmt->bind_param("sssdsss", $trans_id, $item_id, $from_shop_name, $from_qty, $trans_type_out, $user_id, $remarks_out);
        $stmt->execute();

        $trans_type_in = "TRANSFER_IN";
        $remarks_in = "Transferred from " . $from_shop_name . " to " . $to_shop_name;
        $stmt->bind_param("sssdsss", $trans_id, $item_id, $to_shop_name, $from_qty, $trans_type_in, $user_id, $remarks_in);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        echo json_encode([
            "success" => true,
            "message" => "Shop to shop transfer successful",
            "trans_id" => $trans_id
        ]);

    } catch (Exception $e) {
        if (isset($conn)) {
            try {
                $conn->rollback();
            } catch (Exception $rollbackError) {
                write_log("ROLLBACK_FAILED", ["error" => $rollbackError->getMessage()]);
            }
        }

        write_log("TRANSFER_ERROR", [
            "item_id" => $item_id ?? '',
            "from" => $from_shop_name ?? '',
            "to" => $to_shop_name ?? '',
            "qty" => $from_qty ?? '',
            "error" => $e->getMessage()
        ]);

        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }

    if (isset($conn)) {
        $conn->close();
    }

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
        .transfer-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .transfer-card .card-header {
            background: linear-gradient(135deg, #2980b9, #3498db);
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

                    <p class="page-subtitle"><i class="fas fa-exchange-alt mr-1"></i> Transfer stock from one shop to another.</p>

                    <div class="card transfer-card">
                        <div class="card-header">
                            <i class="fas fa-store"></i>
                            Stock Transfer &mdash; Shop &rarr; Shop
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width:25%">Shop (From)</th>
                                            <th style="width:5%">Item Id</th>
                                            <th style="width:30%">Item Name</th>
                                            <th style="width:15%;text-align:center">Available Stock</th>
                                            <th style="width:25%">Transfer to Shop</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql_shop = "SELECT s.qty as total_qty, s.shop_name, s.item_id, it.ITEM_NAME 
                                            FROM inventory_shop s 
                                            JOIN item it ON s.item_id = it.ITEM_ID
                                            WHERE s.qty > 0
                                            ORDER BY s.shop_name, it.ITEM_NAME";
                                    $result_shop = $conn->query($sql_shop);
                                    $shop_row_count = 0;
                                    while ($row_shop = $result_shop->fetch_assoc()) {
                                        $from_shop_name = $row_shop["shop_name"];
                                        $item_name = $row_shop["ITEM_NAME"];
                                        $item_id = $row_shop["item_id"];
                                        $qty = $row_shop["total_qty"];
                                        $safe_id = "shop_item_" . $shop_row_count++;
                                        $badge_class = $qty >= 10 ? 'stock-high' : ($qty >= 3 ? 'stock-low' : 'stock-zero');
                                    ?>
                                        <tr>
                                            <td class="item-name-cell"><?php echo htmlspecialchars($from_shop_name); ?></td>
                                            <td class="item-name-cell"><?php echo $item_id; ?></td>
                                            <td class="item-name-cell"><?php echo htmlspecialchars($item_name); ?></td>
                                            <td style="text-align:center">
                                                <span class="stock-badge <?php echo $badge_class; ?>"><?php echo number_format($qty, 0); ?></span>
                                            </td>
                                            <td>
                                                <div class="transfer-group">
                                                    <input type="number" id="qty_<?php echo $safe_id; ?>" placeholder="Qty" min="1" max="<?php echo $qty; ?>" step="1">
                                                    <div class="dropdown">
                                                        <button class="btn-transfer dropdown-toggle" style="background: #3498db;" type="button" data-toggle="dropdown">
                                                            <i class="fas fa-truck fa-sm"></i> Transfer To
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <?php if (strtolower($from_shop_name) !== 'sirasapalli') { ?>
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                onclick="transferShopToShop(
                                                                '<?php echo $item_id; ?>',
                                                                '<?php echo $from_shop_name; ?>',
                                                                '<?php echo $safe_id; ?>',
                                                                '1000002',
                                                                'Sirasapalli'
                                                                )">    
                                                                <i class="fas fa-store mr-1"></i> 
                                                                Sirasapalli Shop
                                                            </a>
                                                            <?php } ?>
                                                            <?php if (strtolower($from_shop_name) !== 'gajuwaka') { ?>
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                onclick="transferShopToShop(
                                                                '<?php echo $item_id; ?>',
                                                                '<?php echo $from_shop_name; ?>',
                                                                '<?php echo $safe_id; ?>',
                                                                '1000003',
                                                                'Gajuwaka'
                                                                )">
                                                                <i class="fas fa-store mr-1"></i> 
                                                                Gajuwaka Shop
                                                            </a>
                                                            <?php } ?>
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
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().destroy();
            }
            $('#dataTable').DataTable({
                "pageLength": 10,
                "lengthChange": false
            });
        });

        function transferShopToShop(itemid, fromShopName, elementId, toShopId, toShopName) {
            let qtyInput = document.getElementById("qty_" + elementId);
            let qty = parseFloat(qtyInput.value);
            let maxQty = parseFloat(qtyInput.getAttribute("max"));

            if (!qty || qty <= 0 || isNaN(qty)) {
                alert("Please enter valid quantity");
                qtyInput.focus();
                return;
            }

            if (qty > maxQty) {
                alert("Cannot transfer " + qty + ". Available stock is only " + maxQty);
                qtyInput.focus();
                return;
            }

            let confirmTransfer = confirm(
                "Transfer " + qty + " × [" + itemid + "] from " + fromShopName + " to " + toShopName + " shop ?"
            );

            if (!confirmTransfer) {
                return;
            }

            qtyInput.disabled = true;

            $.ajax({
                url: "shop_to_shop_transfer.php",
                type: "POST",
                dataType: "json",
                data: {
                    item_id: itemid,
                    from_shop_name: fromShopName,
                    to_shop_id: toShopId,
                    to_shop_name: toShopName,
                    qty: qty
                },
                beforeSend: function () {
                    console.log("Starting shop-to-shop transfer...");
                },
                success: function (response) {
                    console.log(response);
                    if (response.success === true) {
                        let msg = "✅ " + response.message;
                        if (response.trans_id) {
                            msg += "\nTransaction ID : " + response.trans_id;
                        }
                        alert(msg);
                        location.reload();
                    } else {
                        alert("⚠️ " + response.message);
                        qtyInput.disabled = false;
                    }
                },
                error: function (xhr, status, error) {
                    console.log("AJAX ERROR", xhr, xhr.responseText);
                    alert("Server Error : " + error);
                    qtyInput.disabled = false;
                }
            });
        }
    </script>
</body>
</html>
