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

    // ── PRIVILEGE CHECK ──────────────────────────────────────────
// Fetch all privilege codes granted to the logged-in user.
// Use $user_privileges['CODE'] throughout the page to gate features.
    $user_privileges = [];
    $_logged_user = isset($_COOKIE["user_id"]) ? $_COOKIE["user_id"] : "";
    if (!empty($_logged_user)) {
        $priv_sql = "SELECT pm.privilege_code
                 FROM user_privilege up
                 JOIN privilege_master pm ON pm.privilege_id = up.privilege_id
                 WHERE up.user_id = '" . $conn->real_escape_string($_logged_user) . "'
                   AND pm.active_status = 'A'";
        $priv_res = $conn->query($priv_sql);
        while ($priv_res && $pr = $priv_res->fetch_assoc()) {
            $user_privileges[$pr["privilege_code"]] = true;
        }
    }
    // Helper: true if user has the given privilege
    function has_priv($code)
    {
        global $user_privileges;
        return isset($user_privileges[$code]);
    }
    // ── END PRIVILEGE CHECK ──────────────────────────────────────
    ?>
    <link href="multi/searchableOptionList.css" rel="stylesheet">
    <style>
        .master-item-bold {
            font-weight: bold !important;
            color: #000 !important;
        }

        .tooltip-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .tooltip-input {
            padding: 6px 8px;
            font-size: 14px;
            width: 100px;
        }

        .tooltip-text {
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: #fff;
            padding: 6px 10px;
            border-radius: 4px;
            white-space: nowrap;
            font-size: 12px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s;
            z-index: 1000;
        }

        .tooltip-wrapper.show .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }

        .multi-item-set td {
            background: #eef6ff !important;
        }

        .multi-item-set-start td {
            border-top: 3px solid #2563eb !important;
        }

        .multi-item-set-end td {
            border-bottom: 3px solid #2563eb !important;
        }

        .multi-item-set td:first-child {
            border-left: 6px solid #2563eb !important;
        }

        /* ── CHANGE 1: Custom customer dropdown styles ── */
        #customer_dropdown {
            position: absolute;
            z-index: 9999;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            max-height: 280px;
            overflow-y: auto;
            min-width: 340px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            display: none;
        }

        .cust-option {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }

        .cust-option:hover {
            background: #f0f6ff;
        }

        .cust-name {
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
        }

        .cust-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        #create_customer_btn {
            padding: 10px 12px;
            cursor: pointer;
            color: #2563eb;
            font-weight: 700;
            font-size: 14px;
            border-top: 2px solid #e5e7eb;
            background: #f8faff;
            display: flex;
            align-items: center;
            gap: 6px;
            position: sticky;
            bottom: 0;
        }

        #create_customer_btn:hover {
            background: #dbeafe;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include "sidemenu.php"; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include "topmenu.php"; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Content Row -->
                    <div class="row">

                        <?php
                        $gst = isset($_COOKIE["gst"]) ? $_COOKIE["gst"] : "";

                        $trans_id = "";
                        $trans_date = "";
                        $details = "";
                        $customer = "";
                        $trans_amount = "";
                        $pending = "";
                        $gst = "";
                        $tally = "";
                        $created_by = "";
                        $created_on = "";
                        $modified_by = "";
                        $modified_on = "";
                        $active_status = "";
                        $VEHICLE_NO = "";
                        $VEHICLE_MODEL = "";
                        $NO_OF_WHEELS = "";
                        $COMPANY_NAME = "";
                        $CUSTOMER_NAME = "";
                        $CUSTOMER_ADDRESS = "";
                        $CUSTOMER_GST = "";
                        $VEHICLE_ODOMETER = "";
                        $VEHICLE = "";
                        $CUSTOMER_MOBILE = "";
                        $trans_id = isset($_GET["trans_id"]) ? $_GET["trans_id"] : "";

                        $sql = "SELECT trans_id, trans_date, details, customer, trans_amount, pending, gst, tally, created_by, created_on, modified_by, modified_on, active_status, COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, customer_mobile,discount,roundoff,ver,pay_type FROM sales_trans where trans_id='" . $trans_id . "'";
                        $result = $conn->query($sql);

                        $grand_total = 0;
                        $price_amount = 0;
                        $cgst_amount = 0;
                        $sgst_amount = 0;
                        $igst_amount = 0;
                        $final_disabler = "";
                        $ver = "1";

                        if ($result && $row = $result->fetch_assoc()) {
                            $trans_id = $row["trans_id"];
                            $trans_date = $row["trans_date"];
                            $details = $row["trans_id"];
                            $customer = $row["customer"];
                            $final_amount = $row["trans_amount"];
                            $discount_amount = $row["discount"];
                            $roundoff_amount = $row["roundoff"];
                            $pending = $row["pending"];
                            $gst = $row["gst"];
                            $tally = $row["tally"];
                            $created_by = $row["created_by"];
                            $created_on = $row["created_on"];
                            $modified_by = $row["modified_by"];
                            $modified_on = $row["modified_on"];
                            $active_status = $row["active_status"];

                            $NO_OF_WHEELS = $row["NO_OF_WHEELS"];
                            $COMPANY_NAME = $row["COMPANY_NAME"];
                            $CUSTOMER_NAME = $row["CUSTOMER_NAME"];
                            $CUSTOMER_ADDRESS = $row["CUSTOMER_ADDRESS"];
                            $CUSTOMER_GST = $row["CUSTOMER_GST"];
                            $CUSTOMER_MOBILE = $row["customer_mobile"];
                            $ver = $row["ver"];
                            $active_status = $row["active_status"];
                            $pay_type = $row["pay_type"];

                            if ($active_status == "A") {
                                $final_disabler = "disabled";
                            } else {
                                $ver = $row["ver"];
                            }
                        }

                        if (isset($gst) && $gst != "") {
                        } else {
                            $gst = isset($_COOKIE["gst"]) ? $_COOKIE["gst"] : "";
                        }
                        $customer_locked = !empty($trans_id);
                        $trans_date = isset($_GET["trans_date"]) ? $_GET["trans_date"] : date("Y-m-d");
                        ?>
                        <div class="col-md-12">
                            <div class="alert alert-warning" style="text-align:center;font-weight:bold">Sales :
                                GST:<?PHP echo $gst; ?></div><br><br>

                            <span class="alert alert-info">Customer Details</span>
                            <table class="table">
                                <tr>
                                    <td>Date <input type="text" id="datepicker" class="form-control"
                                            value="<?php echo !empty($trans_date) ? date('d-m-Y', strtotime($trans_date)) : ''; ?>">
                                    </td>
                                    <td>Invoice No <span id="year_part"></span><input class="form-control" readonly
                                            name="trans_id" id="trans_id" value="<?php echo $trans_id; ?>">
                                        <input class="form-control" hidden name="ver" id="ver"
                                            value="<?php echo $ver; ?>">
                                    </td>

                                    <!-- ── CHANGE 2: Replace old customer_search <td> with new custom dropdown ── -->
                                    <td id="customer_search_cell">
                                        <?php if ($customer_locked) { ?>
                                            <label style="font-weight:bold;">Customer</label>
                                            <div class="form-control" id="customer_display" style="background:#f8f9fa;">
                                                <?php echo !empty($COMPANY_NAME) ? $COMPANY_NAME : $CUSTOMER_NAME; ?>
                                                <?php if (!empty($CUSTOMER_NAME) && !empty($COMPANY_NAME)) {
                                                    echo " (" . $CUSTOMER_NAME . ")";
                                                } ?>
                                            </div>
                                            <input type="hidden" id="customer" name="customer"
                                                value="<?php echo $customer; ?>">
                                        <?php } else { ?>
                                            <label style="font-weight:bold;">Search Customer</label>
                                            <div style="position:relative; max-width:360px;">
                                                <input type="text" id="customer_search_input" class="form-control"
                                                    placeholder="Type name, company or mobile..." autocomplete="off"
                                                    oninput="filterCustomerDropdown()" onfocus="showCustomerDropdown()">
                                                <div id="customer_dropdown">
                                                    <?php
                                                    $sql_cust = "SELECT customer_id, company_name, owner_name, owner_mobile FROM customer ORDER BY company_name, owner_name";
                                                    $result_cust = $conn->query($sql_cust);
                                                    while ($rc = $result_cust->fetch_assoc()) {
                                                        $val = $rc["customer_id"] . "~" . $rc["company_name"] . "~" . $rc["owner_name"] . "~" . $rc["owner_mobile"];
                                                        $label = htmlspecialchars($rc["company_name"] ?: $rc["owner_name"]);
                                                        $sub = htmlspecialchars($rc["owner_name"] ?: "");
                                                        $mobile = htmlspecialchars($rc["owner_mobile"] ?: "");
                                                        $search = strtolower($rc["company_name"] . " " . $rc["owner_name"] . " " . $rc["owner_mobile"]);
                                                        ?>
                                                        <div class="cust-option"
                                                            data-val="<?php echo htmlspecialchars($val); ?>"
                                                            data-search="<?php echo htmlspecialchars($search); ?>"
                                                            onclick="pickCustomer(this)">
                                                            <div class="cust-name"><?php echo $label; ?></div>
                                                            <div class="cust-sub">
                                                                <?php echo $sub; ?>
                                                                <?php echo $mobile ? " · " . $mobile : ""; ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (has_priv('CREATE_CUSTOMER')) { ?>
                                                        <div id="create_customer_btn" onclick="openCreateCustomerModal()">
                                                            <span style="font-size:18px;line-height:1;">+</span> Create New
                                                            Customer
                                                        </div>
                                                    <?php } else { ?>
                                                        <div id="create_customer_btn"
                                                            title="You don't have permission to create customers"
                                                            style="opacity:0.45; cursor:not-allowed; pointer-events:none;">
                                                            <span style="font-size:18px;line-height:1;">+</span> Create New
                                                            Customer
                                                            <span style="font-size:11px; margin-left:4px;">🔒</span>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" id="customer" name="customer"
                                                value="<?php echo $customer; ?>">
                                        <?php } ?>
                                    </td>
                                    <!-- ── END CHANGE 2 ── -->

                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <span class="alert alert-success">Customer Details</span>
                            <table class="table">
                                <tr>
                                    <td>Company Name<input class="form-control" name="company_name" id="company_name"
                                            value="<?php echo $COMPANY_NAME; ?>" <?php echo $customer_locked ? "readonly" : ""; ?>></td>
                                    <td>Customer Name <input class="form-control" name="customer_name"
                                            id="customer_name" value="<?php echo $CUSTOMER_NAME; ?>" <?php echo $customer_locked ? "readonly" : ""; ?>></td>
                                    <td>Mobile <input class="form-control" name="customer_mobile" id="customer_mobile"
                                            value="<?php echo $CUSTOMER_MOBILE; ?>" <?php echo $customer_locked ? "readonly" : ""; ?>></td>
                                </tr>
                                <?php if ((isset($_COOKIE["gst"]) && $_COOKIE["gst"] == "Y") || ($gst == "Y")) {
                                    $visibility = "";
                                } else {
                                    $visibility = "none";
                                } ?>
                                <tr>
                                    <td style="display:<?php echo $visibility; ?>">GST <input class="form-control"
                                            name="customer_gst" id="customer_gst" value="<?php echo $CUSTOMER_GST; ?>"
                                            list="gst_list" onfocus="get_gst()" autocomplete="off" onblur="set_gst()">
                                        <datalist id="gst_list">
                                        </datalist>
                                    </td>
                                    <td>Address <input class="form-control" name="customer_address"
                                            id="customer_address" value="<?php echo $CUSTOMER_ADDRESS; ?>"
                                            list="address_list" onfocus="get_address()" autocomplete="off">
                                        <datalist id="address_list">
                                        </datalist>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <select class="form-control font-weight-bold" name="item_search" id="item_search"
                                list="item_list" <?php echo $final_disabler; ?> multiple="multiple"
                                style="max-width:600px!important; font-weight: bold; font-size: 1.1em; color: #000;">
                                <?php
                                $shop_id_cookie = isset($_COOKIE["shop"]) ? $_COOKIE["shop"] : "";
                                $sql = "SELECT i.item_id,i.item_name,i.item_description,i.cost,ifnull(i.tax_pc,9) tax_pc,ifnull(i.tax_pc_sgst,9) tax_pc_sgst, ifnull(inv.qty, 0) as available_qty,
                                        (SELECT COUNT(*) FROM item_association ia WHERE ia.item_group_id = i.item_id AND ia.item_id != i.item_id) as has_subitems
                                        FROM item i LEFT JOIN inventory_shop inv ON i.item_id = inv.item_id AND inv.shop_name = '" . $shop_id_cookie . "' WHERE ifnull(inv.qty, 0) > 0 AND i.status='A' ORDER BY i.item_id";
                                $result = $conn->query($sql);

                                while ($row1 = $result->fetch_assoc()) {
                                    $is_master = ($row1['has_subitems'] > 0) ? '1' : '0';
                                    ?>
                                    <option data-master="<?php echo $is_master; ?>" <?php echo $final_disabler; ?>
                                        value="<?php echo $row1["item_id"] ?>~<?php echo $row1["item_name"] ?>~<?php echo $row1["item_description"] ?>~<?php echo $row1["cost"] ?>~<?php echo $row1["tax_pc"] ?>~<?php echo $row1["tax_pc_sgst"] ?>~<?php echo $row1["available_qty"] ?>">
                                        <?php if ($is_master == '1') { ?>
                                            <span class="master-item-bold"><?php echo $row1["item_name"] ?></span>
                                        <?php } else { ?>
                                            <?php echo $row1["item_name"] ?>
                                        <?php } ?>
                                    </option>
                                <?php } ?>
                            </select>

                            <table class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <td>Item</td>
                                        <td>Available Qty</td>
                                        <td>Qty</td>
                                        <td>Taxable Price</td>
                                        <td>Unit Cost</td>
                                        <td style="display:none">%CGST</td>
                                        <td style="display:none">%SGST</td>
                                        <td style="display:none">%IGST</td>
                                        <td>CGST</td>
                                        <td>SGST</td>
                                        <td>IGST</td>
                                        <td style="display:none">Discount</td>
                                        <td>Total</td>
                                        <td style="display:none">Round Off</td>
                                        <td style="display:none">Remarks</td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $shop_id_cookie = isset($_COOKIE["shop"]) ? $_COOKIE["shop"] : "";
                                    $sql_det = "SELECT t.subtrans_id,t.item_id,t.cost,t.price,ifnull(t.discount,'0') discount,t.qty,t.total, t.tax,t.tax_amount,t.tax_sgst,t.total,t.remarks,t.tax_amount_sgst,t.tax_igst,t.tax_amount_igst,i.item_name,t.roundoff,t.account,t.parent,t.perc, ifnull(inv.qty, 0) as available_qty FROM sales_trans_det t JOIN item i ON i.item_id=t.item_id LEFT JOIN inventory_shop inv ON t.item_id = inv.item_id AND inv.shop_name = '" . $shop_id_cookie . "' where t.trans_id='" . $trans_id . "' and t.trans_id!='' and t.subtrans_id!='' and t.qty>0 and t.total>0 and t.active_status='A'  and t.ver='" . $ver . "'
ORDER BY
CASE
    WHEN IFNULL(t.account,'Y')='N'
    THEN t.item_id
    ELSE t.parent
END,
CASE
    WHEN IFNULL(t.account,'Y')='N'
    THEN 0
    ELSE 1
END,
t.subtrans_id";

                                    $result_det = $conn->query($sql_det);
                                    $display = "none";
                                    $readonly = "";
                                    $slno = 0;
                                    $tax_pc = 0;
                                    $tax_pc_sgst = 0;
                                    $tax_pc_igst = 0;
                                    $det_tax = 0;
                                    $det_tax_sgst = 0;
                                    $det_tax_igst = 0;

                                    while ($row_det = $result_det->fetch_assoc()) {
                                        $det_price = $row_det["price"];
                                        $det_roundoff = $row_det["roundoff"];
                                        $det_item_name = $row_det["item_name"];
                                        $det_account = $row_det["account"];
                                        $det_parent = $row_det["parent"];
                                        $det_perc = $row_det["perc"];
                                        $det_available_qty = $row_det["available_qty"];

                                        if (substr($CUSTOMER_GST, 0, strlen("37")) === "37") {
                                            $tax_pc = $row_det["tax"];
                                            $tax_pc_sgst = $row_det["tax_sgst"];
                                            $det_tax = $row_det["tax_amount"];
                                            $det_tax_sgst = $row_det["tax_amount_sgst"];
                                            $tax_pc_igst = 0;
                                            $det_tax_igst = 0;
                                        } else {
                                            $tax_pc_igst = $row_det["tax_igst"];
                                            $det_tax_igst = $row_det["tax_amount_igst"];
                                            $tax_pc = 0;
                                            $tax_pc_sgst = 0;
                                            $det_tax = 0;
                                            $det_tax_sgst = 0;
                                        }
                                        $det_qty = $row_det["qty"];
                                        $det_discount = $row_det["discount"];
                                        $det_remarks = $row_det["remarks"];
                                        $det_cost = $row_det["cost"];
                                        $det_total = $row_det["total"];
                                        $det_subtrans_id = $row_det["subtrans_id"];
                                        $display = "";

                                        $counts_toward_total = true;
                                        if ($det_account == "N") {
                                            $has_active_child_y = false;
                                            $sql_chk = "SELECT 1 FROM sales_trans_det WHERE trans_id='" . $trans_id . "' AND ver='" . $ver . "' AND parent='" . $row_det["item_id"] . "' AND active_status='A' AND IFNULL(account,'Y')='Y' LIMIT 1";
                                            $res_chk = $conn->query($sql_chk);
                                            $has_active_child_y = ($res_chk && $res_chk->num_rows > 0);
                                            $counts_toward_total = !$has_active_child_y;
                                        }

                                        if ($counts_toward_total) {
                                            $grand_total += $det_total;
                                            $price_amount += ($det_price - $det_discount + $det_roundoff);
                                            $cgst_amount += $det_tax;
                                            $sgst_amount += $det_tax_sgst;
                                            $igst_amount += $det_tax_igst;
                                        }
                                        $readonly = "readonly";
                                        ?>
                                        <tr class="purchase_rows" id="row_<?php echo $slno; ?>"
                                            style="display:<?php echo $display; ?>"
                                            onfocusout="add_service_det('<?php echo $slno; ?>')">
                                            <td><?php echo $det_item_name; ?>
                                                <input type="hidden" id="item_id_<?php echo $slno; ?>"
                                                    value="<?php echo $row_det["item_id"]; ?>">
                                                <input type="hidden" id="subtrans_id_<?php echo $slno; ?>"
                                                    value="<?php echo $det_subtrans_id; ?>">
                                                <input type="hidden" id="account_<?php echo $slno; ?>"
                                                    value="<?php echo $det_account; ?>">
                                                <input type="hidden" id="parent_<?php echo $slno; ?>"
                                                    value="<?php echo $det_parent; ?>">
                                                <input type="hidden" id="perc_<?php echo $slno; ?>"
                                                    value="<?php echo $det_perc; ?>">
                                            </td>
                                            <td>
                                                <input class="form-control" readonly id="available_qty_<?php echo $slno; ?>"
                                                    value="<?php echo $det_available_qty; ?>">
                                            </td>
                                            <td>
                                                <div class="tooltip-wrapper"><input class="form-control tooltip-input" <?php echo $final_disabler; ?> id="qty_<?php echo $slno; ?>"
                                                        onblur="calculate('<?php echo $slno; ?>')"
                                                        value="<?php echo $det_qty; ?>">
                                                </div>
                                            </td>
                                            <td><input class="form-control" <?php echo $final_disabler; ?>
                                                    id="price_<?php echo $slno; ?>" value="<?php echo $det_price; ?>"
                                                    onblur="calculate('<?php echo $slno; ?>')"></td>
                                            <td><input class="form-control" <?php echo $final_disabler; ?> readonly
                                                    value="<?php echo $det_cost; ?>" id="cost_<?php echo $slno; ?>"
                                                    onblur="calculate('<?php echo $slno; ?>')"></td>
                                            <td style="display:none"><input style="text-align:right" <?php echo $final_disabler; ?> class="form-control" readonly
                                                    value="<?php echo $tax_pc; ?>" id="gst_<?php echo $slno; ?>"></td>
                                            <td style="display:none"><input style="text-align:right" <?php echo $final_disabler; ?> class="form-control" readonly
                                                    value="<?php echo $tax_pc_sgst; ?>" id="sgst_<?php echo $slno; ?>"></td>
                                            <td style="display:none"><input style="text-align:right" <?php echo $final_disabler; ?> class="form-control" readonly
                                                    value="<?php echo $tax_pc_igst; ?>" id="igst_<?php echo $slno; ?>"></td>
                                            <td style="display:<?php echo $visibility; ?>"><input class="form-control"
                                                    readonly style="text-align:right" id="tax_gst_<?php echo $slno; ?>"
                                                    value="<?php echo $det_tax; ?>"></td>
                                            <td style="display:<?php echo $visibility; ?>"><input class="form-control"
                                                    style="text-align:right" readonly id="tax_sgst_<?php echo $slno; ?>"
                                                    value="<?php echo $det_tax_sgst; ?>"></td>
                                            <td style="display:<?php echo $visibility; ?>"><input class="form-control"
                                                    style="text-align:right" readonly id="tax_igst_<?php echo $slno; ?>"
                                                    value="<?php echo $det_tax_igst; ?>"></td>
                                            <td style="display:none"><input class="form-control" <?php echo $final_disabler; ?> id="discount_<?php echo $slno; ?>"
                                                    value="<?php echo $det_discount; ?>"
                                                    onblur="calculate('<?php echo $slno; ?>')"></td>
                                            <td>
                                                <input class="form-control" <?php echo $final_disabler; ?>
                                                    id="total_<?php echo $slno; ?>" value="<?php echo $det_total; ?>"
                                                    onblur="calculate('<?php echo $slno; ?>')">
                                                <input class="form-control" <?php echo $final_disabler; ?> type="hidden"
                                                    id="id_<?php echo $slno; ?>" value="<?php echo $det_subtrans_id; ?>">
                                            </td>
                                            <td style="display:none"><input class="form-control" <?php echo $final_disabler; ?> id="roundoff_<?php echo $slno; ?>"
                                                    value="<?php echo $det_roundoff; ?>"
                                                    onblur="calculate('<?php echo $slno; ?>')"></td>
                                            <td style="display:none"><input class="form-control" <?php echo $final_disabler; ?> id="remarks_<?php echo $slno; ?>" value="<?php echo $det_remarks; ?>"
                                                    onblur="calculate('<?php echo $slno; ?>')"></td>
                                            <td>
                                                <button class="btn btn-xs btn-danger" <?php echo $final_disabler; ?>
                                                    onclick="del_service_det('<?php echo $slno; ?>')"
                                                    id="del_<?php echo $slno; ?>">X</button>
                                            </td>
                                        </tr>

                                        <?php $slno++;
                                    } ?>
                                    <?php while ($slno < 100) { ?>
                                        <tr class="purchase_rows" id="row_<?php echo $slno; ?>" style="display:none"
                                            onfocusout="add_service_det('<?php echo $slno; ?>')">
                                            <td><span id="item_name_<?php echo $slno; ?>"></span>
                                                <input type="hidden" id="item_id_<?php echo $slno; ?>">
                                                <input type="hidden" id="subtrans_id_<?php echo $slno; ?>">
                                                <input type="hidden" id="account_<?php echo $slno; ?>" value="">
                                                <input type="hidden" id="parent_<?php echo $slno; ?>" value="">
                                                <input type="hidden" id="perc_<?php echo $slno; ?>" value="">
                                            </td>
                                            <td>
                                                <input class="form-control" readonly id="available_qty_<?php echo $slno; ?>"
                                                    value="">
                                            </td>
                                            <td>
                                                <div class="tooltip-wrapper"><input class="form-control tooltip-input"
                                                        id="qty_<?php echo $slno; ?>"
                                                        onblur="calculate('<?php echo $slno; ?>')">
                                                    <div class="tooltip-text"></div>
                                                </div>
                                            </td>
                                            <td><input class="form-control" id="price_<?php echo $slno; ?>" readonly
                                                    onblur="calculate('<?php echo $slno; ?>')"></td>
                                            <td><input class="form-control" <?php echo $final_disabler; ?> readonly value=""
                                                    id="cost_<?php echo $slno; ?>"
                                                    onblur="calculate('<?php echo $slno; ?>')">
                                            </td>
                                            <td style="display:none"><input style="text-align:right;" <?php echo $final_disabler; ?> class="form-control" readonly
                                                    id="gst_<?php echo $slno; ?>"></td>
                                            <td style="display:none"><input style="text-align:right" <?php echo $final_disabler; ?> class="form-control" readonly
                                                    id="sgst_<?php echo $slno; ?>"></td>
                                            <td style="display:none"><input style="text-align:right" <?php echo $final_disabler; ?> class="form-control" readonly
                                                    id="igst_<?php echo $slno; ?>"></td>
                                            <td style="display:<?php echo $visibility; ?>"><input class="form-control"
                                                    readonly style="text-align:right" id="tax_gst_<?php echo $slno; ?>"
                                                    value=""></td>
                                            <td style="display:<?php echo $visibility; ?>"><input class="form-control"
                                                    style="text-align:right" readonly id="tax_sgst_<?php echo $slno; ?>"
                                                    value=""></td>
                                            <td style="display:<?php echo $visibility; ?>"><input class="form-control"
                                                    style="text-align:right" readonly id="tax_igst_<?php echo $slno; ?>"
                                                    value=""></td>
                                            <td style="display:none"><input class="form-control"
                                                    id="discount_<?php echo $slno; ?>" value=""
                                                    onblur="calculate('<?php echo $slno; ?>')"></td>
                                            <td><input class="form-control" <?php echo $final_disabler; ?>
                                                    id="total_<?php echo $slno; ?>" value=""
                                                    onblur="calculate('<?php echo $slno; ?>')">
                                                <input class="form-control" <?php echo $final_disabler; ?> type="hidden"
                                                    id="id_<?php echo $slno; ?>" value="">
                                            </td>
                                            <td style="display:none"><input class="form-control" <?php echo $final_disabler; ?> id="roundoff_<?php echo $slno; ?>" value=""
                                                    onblur="calculate('<?php echo $slno; ?>')"></td>
                                            <td style="display:none"><input class="form-control" <?php echo $final_disabler; ?> id="remarks_<?php echo $slno; ?>" value=""
                                                    onblur="calculate('<?php echo $slno; ?>')"></td>
                                            <td>
                                                <button class="btn btn-xs btn-danger" <?php echo $final_disabler; ?>
                                                    onclick="del_service_det('<?php echo $slno; ?>')"
                                                    id="del_<?php echo $slno; ?>">X</button>
                                            </td>
                                        </tr>
                                        <?php $slno++;
                                    } ?>

                                    <tr>
                                        <TD></TD>
                                        <TD></TD>
                                        <TD><input id="price_amount" class="form-control" readonly
                                                value="<?php echo $price_amount; ?>"></TD>
                                        <TD></TD>
                                        <TD><input id="cgst_amount" class="form-control" readonly
                                                value="<?php echo $cgst_amount; ?>"></TD>
                                        <TD><input id="sgst_amount" class="form-control" readonly
                                                value="<?php echo $sgst_amount; ?>"></TD>
                                        <TD><input id="igst_amount" class="form-control" readonly
                                                value="<?php echo $igst_amount; ?>"></TD>
                                        <TD><input id="trans_amount" class="form-control" readonly
                                                value="<?php echo $grand_total; ?>"
                                                style="font-weight: bold; font-size: 1.1em; color: #000;"></TD>
                                        <TD>Discount : <input id="discount_amount" class="form-control"
                                                value="<?php echo $discount_amount; ?>" onblur="calculate_final()"><br>
                                            Round Off <input id="roundoff_amount" class="form-control"
                                                value="<?php echo $roundoff_amount; ?>"
                                                onblur="calculate_final()"><br><b style="font-size: 1.1em; text-transform: uppercase;">Total Invoice Amount :</b> <input
                                                id="final_amount" class="form-control" readonly
                                                value="<?php echo $grand_total - ($discount_amount) + ($roundoff_amount); ?>"
                                                style="font-weight: 900; font-size: 1.5em; color: #000; background-color: #d1ecf1; border: 2px solid #17a2b8; text-align: center; height: auto; padding: 10px;">
                                        </TD>
                                    </tr>

                                </tbody>
                            </table>
                            <?php if ($active_status == "A") { ?>
                                <hr>
                                <center>
                                    <button class="btn btn-success" disabled><?php echo !empty($pay_type) ? "Paid" : "Saved already"; ?></button>
                                    &nbsp;
                                    <button class="btn btn-danger" id="invoice" onclick="get_invoice()">Get Invoice</button>
                                    &nbsp;
                                    <?php if (empty($pay_type)) { ?>
                                        <button class="btn btn-primary" id="jobcard" onclick="unlock()">Edit</button>
                                    <?php } ?>
                                </center>
                                <hr>

                            <?php } else { ?>

                                <hr>
                                <center>
                                    <button class="btn btn-success" onclick="save_dummy()">Save</button>
                                </center>
                                <hr>

                            <?php } ?>
                        </div>
                        <div class="col-md-12">

                            <table class="table">
                                <?php $sql_pay = "SELECT p.payment_id, p.mode, p.account, p.reference, amount, a.account_name FROM payments p, pay_track i, account a where i.trans_id='" . $trans_id . "' and i.trans_id!='' and i.payment_id=p.payment_id and a.account_id=p.account and i.mode='sales'";
                                $result_pay = $conn->query($sql_pay);
                                $pay_ind = 0;
                                $total_amount = 0;
                                $mode = "";
                                $account = "";
                                $reference = "";
                                while ($row_pay = $result_pay->fetch_assoc()) {
                                    $mode = $row_pay["mode"];
                                    $account = $row_pay["account"];
                                    $reference = $row_pay["reference"];
                                    $amount = $row_pay["amount"];
                                    $total_amount += $amount;
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td><?php echo $row_pay['mode']; ?></td>
                                        <td><?php echo $row_pay['account_name']; ?></td>
                                        <td><?php echo $reference; ?></td>
                                        <td><?php echo $amount; ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="delete_it_pay('<?php echo $row_pay['payment_id']; ?>','CREDIT','<?php echo $row_pay['account']; ?>','','<?php echo $row_pay['amount']; ?>','<?php echo $row_pay['mode']; ?>')">X</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <?php if (($grand_total > $total_amount || $total_amount == 0) && ($active_status == "A")) {
                                $disabled = "";
                            } else {
                                $disabled = "disabled";
                            } ?>

                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addModal" id="add_opener" <?php echo $disabled; ?>>
                                Add Payments
                            </button>

                        </div>
                        <div class="card-body"></div>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include "footer.php"; ?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <?php include "modals.php"; ?>

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="">
                    <table class="table">
                        <tr>
                            <td>Pay Type <select id="pay_type1" class="form-control" onchange="filter_account1()">
                                    <option value="">--Select--</option>
                                    <?php $sql = "SELECT paytype_id,paytype_name FROM paytype order by 1";
                                    $result = $conn->query($sql);
                                    $selected = "";
                                    while ($row = $result->fetch_assoc()) {
                                        if ($row['paytype_name'] == $mode)
                                            $selected = "selected";
                                        else
                                            $selected = "";
                                        ?>
                                        <option
                                            value="<?php echo $row['paytype_id']; ?>~<?php echo $row['paytype_name']; ?>"
                                            <?php echo $selected; ?>><?php echo $row['paytype_name']; ?></option>
                                    <?php } ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td>Account<br><select type="text" class="form-control" id="account1">
                                    <option value="">--Select--</option>
                                    <?php $sql = "SELECT account_id,account_name,paytype_id FROM account order by 1";
                                    $result = $conn->query($sql);
                                    $selected = "";
                                    while ($row = $result->fetch_assoc()) {
                                        if ($row['account_id'] == $account)
                                            $selected = "selected";
                                        else
                                            $selected = "";
                                        ?>
                                        <option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['account_id']; ?>"
                                            <?php echo $selected; ?>><?php echo $row['account_name']; ?></option>
                                    <?php } ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td>Ref No <input id="ref_no1" class="form-control"></td>
                        </tr>
                        <tr>
                            <td>Paid Amount <input id="paid_amount" class="form-control" type="number"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="add" onclick="save_pay1()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="">
                    <table class="table table-striped">
                        <tr>
                            <td>Vehicle</td>
                            <td><select class="form-control" id="edit_vehicle" onchange="populate_edit_vehicle_det()">
                                    <option value="">--select---</option>
                                    <?php $sql = "SELECT vehicle_id,vehicle_no,vehicle_model,vehicle_brand FROM vehicle where customer_id='" . $customer . "' order by 1";
                                    $result = $conn->query($sql);
                                    while ($row1 = $result->fetch_assoc()) { ?>
                                        <option value="<?php echo $row1['vehicle_id']; ?>">
                                            <?php echo $row1['item_name']; ?>(<?php echo $row1['vehicle_no']; ?>,Model:
                                            <?php echo $row1['vehicle_model']; ?>, Brand:
                                            <?php echo $row1['vehicle_brand']; ?>)
                                        </option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" id="edit_vehicle_id">
                            </td>
                        </tr>
                        <tr>
                            <td>Service</td>
                            <td><select class="form-control" id="edit_service" onchange="populate_edit_det()">
                                    <option value="">--select---</option>
                                    <?php $sql = "SELECT service_id,service_name,service_description,cost,tax_pc,tax_pc_sgst FROM service order by 1";
                                    $result = $conn->query($sql);
                                    while ($row1 = $result->fetch_assoc()) { ?>
                                        <option
                                            value="<?php echo $row1['service_id']; ?>~<?php echo $row1['cost']; ?>~<?php echo $row1['tax_pc']; ?>~<?php echo $row1['tax_pc_sgst']; ?>">
                                            <?php echo $row1['service_name']; ?>(<?php echo $row1['service_description']; ?>,Cost:
                                            <?php echo $row1['cost']; ?>, Tax (%): <?php echo $row1['tax_pc']; ?>)
                                        </option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" id="edit_cost">
                                <input type="hidden" id="edit_tax_pc">
                                <input type="hidden" id="edit_tax_pc_sgst">
                                <input type="hidden" id="edit_service_id">
                            </td>
                        </tr>
                        <tr>
                            <td>Qty</td>
                            <td><input type="number" id="edit_qty" class="form-control" oninput="show_edit_amount()">
                            </td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td><input type="number" class="form-control" id="edit_amount"></td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td><input type="number" class="form-control" id="edit_tax" readonly></td>
                        </tr>
                        <tr>
                            <td>Tax (SGST)</td>
                            <td><input type="number" class="form-control" id="edit_tax_sgst" readonly></td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td><input type="number" class="form-control" id="edit_total" readonly>
                                <input type='HIDDEN' name="edit_trans_id" id="edit_trans_id">
                                <input type='HIDDEN' name="edit_subtrans_id" id="subtrans_id">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="edit">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CHANGE 3: Create New Customer Modal ── -->
    <div class="modal fade" id="createCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width:480px;">
            <div class="modal-content">
                <div class="modal-header" style="background:#2563eb; color:#fff;">
                    <h5 class="modal-title" style="font-weight:700;">+ Create New Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="color:#fff;opacity:1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless" style="margin-bottom:0;">
                        <tr>
                            <td style="width:38%;font-weight:600;vertical-align:middle;">Company Name</td>
                            <td><input type="text" id="nc_company_name" class="form-control"
                                    placeholder="e.g. ABC Pvt Ltd"></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;vertical-align:middle;">Customer Name <span
                                    style="color:red;">*</span></td>
                            <td><input type="text" id="nc_customer_name" class="form-control"
                                    placeholder="Owner / Contact name"></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;vertical-align:middle;">Mobile <span style="color:red;">*</span>
                            </td>
                            <td><input type="text" id="nc_mobile" class="form-control"
                                    placeholder="10-digit mobile number" maxlength="10"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;vertical-align:middle;">Address</td>
                            <td><input type="text" id="nc_address" class="form-control" placeholder="Full address"></td>
                        </tr>
                        <?php
                        $gst_row_display = ((isset($_COOKIE["gst"]) && $_COOKIE["gst"] == "Y") || $gst == "Y") ? "table-row" : "none";
                        ?>
                        <tr style="display:<?php echo $gst_row_display; ?>">
                            <td style="font-weight:600;vertical-align:middle;">GST Number</td>
                            <td><input type="text" id="nc_gst" class="form-control" placeholder="e.g. 37AAAAA0000A1Z5"
                                    maxlength="15"></td>
                        </tr>
                    </table>
                    <div id="nc_error_msg" style="color:red;font-size:13px;padding:6px 8px;margin-top:4px;
                     background:#fff5f5;border-radius:4px;border:1px solid #fca5a5;display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="nc_save_btn" onclick="saveNewCustomer()"
                        style="background:#2563eb;border-color:#2563eb;font-weight:600;min-width:130px;">
                        Save Customer
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- ── END CHANGE 3 ── -->

    <?php include "footer_include.php"; ?>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    <script src="multi/searchableOptionList.js"></script>
    <script>
        var itemStockMap = {
            <?php
            $shop_id_cookie = isset($_COOKIE["shop"]) ? $_COOKIE["shop"] : "";
            $sql_stock = "SELECT item_id, ifnull(qty, 0) as qty FROM inventory_shop WHERE shop_name = '" . $conn->real_escape_string($shop_id_cookie) . "'";
            $res_stock = $conn->query($sql_stock);
            if ($res_stock) {
                while ($row_s = $res_stock->fetch_assoc()) {
                    echo "'" . $row_s['item_id'] . "': " . $row_s['qty'] . ",\n";
                }
            }
            ?>
        };

        var del_mode = 0;
        var addModal = new bootstrap.Modal(document.getElementById('addModal'));
        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        var createCustomerModal = new bootstrap.Modal(document.getElementById('createCustomerModal'));

        // Privilege flags (set server-side, used client-side as safety net)
        var CAN_CREATE_CUSTOMER = <?php echo has_priv('CREATE_CUSTOMER') ? 'true' : 'false'; ?>;

        function restoreMultiItemBorders() {
            var parentMap = {};

            document.querySelectorAll(".purchase_rows").forEach(function (row) {
                if (row.style.display === "none") return;

                var rowId = row.id;
                if (!rowId || !rowId.startsWith("row_")) return;

                var slno = rowId.split("_")[1];
                var parentEl = document.getElementById("parent_" + slno);
                if (!parentEl) return;

                var parentVal = parentEl.value;
                if (parentVal === "" || parentVal === null) return;

                if (!parentMap[parentVal]) {
                    parentMap[parentVal] = [];
                }
                parentMap[parentVal].push(slno);
            });

            Object.keys(parentMap).forEach(function (parentVal) {
                var group = parentMap[parentVal];
                if (group.length < 2) return;

                group.forEach(function (slno) {
                    var row = document.getElementById("row_" + slno);
                    if (row) row.classList.add("multi-item-set");
                });

                var firstRow = document.getElementById("row_" + group[0]);
                if (firstRow) firstRow.classList.add("multi-item-set-start");

                var lastRow = document.getElementById("row_" + group[group.length - 1]);
                if (lastRow) lastRow.classList.add("multi-item-set-end");
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            restoreMultiItemBorders();
            recalc_invoice_totals();
        });

        /* ── CHANGE 3 JS: Customer dropdown functions (replaces old searchableOptionList for customer) ── */
        <?php if (!$customer_locked) { ?>

            function showCustomerDropdown() {
                document.getElementById("customer_dropdown").style.display = "block";
                setTimeout(function () {
                    document.addEventListener("mousedown", hideCustomerDropdownOutside);
                }, 100);
            }

            function hideCustomerDropdownOutside(e) {
                var dd = document.getElementById("customer_dropdown");
                var inp = document.getElementById("customer_search_input");
                if (dd && !dd.contains(e.target) && e.target !== inp) {
                    dd.style.display = "none";
                    document.removeEventListener("mousedown", hideCustomerDropdownOutside);
                }
            }

            function filterCustomerDropdown() {
                var query = (document.getElementById("customer_search_input").value || "").toLowerCase().trim();
                document.querySelectorAll("#customer_dropdown .cust-option").forEach(function (opt) {
                    var s = opt.getAttribute("data-search") || "";
                    opt.style.display = (!query || s.includes(query)) ? "" : "none";
                });
                document.getElementById("customer_dropdown").style.display = "block";
            }

            function pickCustomer(el) {
                var parts = (el.getAttribute("data-val") || "").split("~");
                var customerId = parts[0] || "";
                var companyName = parts[1] || "";
                var customerName = parts[2] || "";
                var mobile = parts[3] || "";

                document.getElementById("customer").value = customerId;
                document.getElementById("customer_search_input").value =
                    companyName ? companyName + (customerName ? " (" + customerName + ")" : "") : customerName;

                document.getElementById("company_name").value = companyName;
                document.getElementById("customer_name").value = customerName;
                document.getElementById("customer_mobile").value = mobile;

                document.getElementById("customer_dropdown").style.display = "none";
                document.removeEventListener("mousedown", hideCustomerDropdownOutside);

                get_gst();
                get_address();
            }

            function openCreateCustomerModal() {
                if (!CAN_CREATE_CUSTOMER) {
                    alert("You don't have permission to create customers. Please contact your administrator.");
                    return;
                }
                var typed = document.getElementById("customer_search_input").value || "";
                document.getElementById("nc_company_name").value = typed;
                document.getElementById("nc_customer_name").value = "";
                document.getElementById("nc_mobile").value = "";
                document.getElementById("nc_address").value = "";
                if (document.getElementById("nc_gst")) document.getElementById("nc_gst").value = "";
                document.getElementById("nc_error_msg").style.display = "none";
                document.getElementById("nc_error_msg").innerText = "";
                document.getElementById("customer_dropdown").style.display = "none";
                createCustomerModal.show();
            }

            function saveNewCustomer() {
                var companyName = document.getElementById("nc_company_name").value.trim();
                var customerName = document.getElementById("nc_customer_name").value.trim();
                var mobile = document.getElementById("nc_mobile").value.trim();
                var address = document.getElementById("nc_address").value.trim();
                var gstNo = document.getElementById("nc_gst") ? document.getElementById("nc_gst").value.trim() : "";
                
                // Set the hidden fields so add_sales.php handles it upon invoice save
                document.getElementById("customer").value = ""; 
                document.getElementById("company_name").value = companyName;
                document.getElementById("customer_name").value = customerName;
                document.getElementById("customer_mobile").value = mobile;
                
                if (document.getElementById("customer_address")) {
                    document.getElementById("customer_address").value = address;
                }
                
                if (document.getElementById("customer_gst")) {
                    document.getElementById("customer_gst").value = gstNo;
                }
                
                // Update the search input to show the selected name
                var displayName = companyName ? companyName + (customerName ? " (" + customerName + ")" : "") : customerName;
                if (!displayName && mobile) displayName = mobile;
                if (!displayName) displayName = "New Customer"; 
                
                document.getElementById("customer_search_input").value = displayName;

                // Close the modal
                createCustomerModal.hide();
            }
        <?php } ?>
        /* ── END CHANGE 3 JS ── */

        $('#item_search').searchableOptionList({
            maxHeight: '350px',
            showSelectAll: true,
            onChange: function () {
                $('.sol-container').removeClass('sol-active');
                setTimeout(function () {
                    $('#item_search').val(null);
                    $('#item_search option').prop("selected", false);
                    $('.sol-checkbox').prop("checked", false);
                    $('.sol-option').removeClass("sol-selected");
                }, 50);
                select_item();
            }
        });

        // Make master items bold via HTML span in the option directly
        // (searchableOptionList parses option.html() into the label)

        $('#group_search').searchableOptionList({
            maxHeight: '350px',
            showSelectAll: true,
            onChange: function () {
                $('.sol-container').removeClass('sol-active');
                setTimeout(function () {
                    $('#item_search').val(null);
                    $('#item_search option').prop("selected", false);
                    $('.sol-checkbox').prop("checked", false);
                    $('.sol-option').removeClass("sol-selected");
                }, 50);
                select_group();
            }
        });

        let sectionCounter = 0;

        // ============================================================
        function select_item() {
            sectionCounter++;
            var a = document.getElementsByClassName("sol-selected-display-item");
            var myvar = "";
            console.log(a.length);
            for (var i = 0; i < a.length; i++) {
                myvar = myvar + (a[i].getAttribute("data-sol-item-val"));
                a[i].setAttribute("data-sol-item-val", "");
                a[i].innerHTML = "";
            }
            console.log(myvar);
            let parts = myvar.split("~");

            let main_slno = load_service(parts[0], parts[3], parts[4], parts[5], parts[1], "", "N", "100", parts[6]);
            console.log("main_slno" + main_slno);
            setTimeout(function () {
                $.post("fetch_items.php",
                    {
                        itemgroup_id: parts[0]
                    },
                    function (data, status) {
                        console.log("data is" + data);

                        if (!data || $.trim(data) === "" || $.trim(data) === "null" || $.trim(data) === "0") {
                            console.log("no mapped items - treat as standalone (Scenario 2)");
                            if (typeof main_slno === "undefined" || main_slno === null) {
                                main_slno = 0;
                            }
                            document.getElementById("account_" + main_slno).value = "Y";
                            calculate(main_slno);
                        } else {
                            // Scenario 1/3: mapped items found
                            let list = $.trim(data).split("@");
                            let isMultiSet = list.length > 2;
                            let createdRows = [];
                            for (let i = 0; i < list.length - 1; i++) {
                                let parts1 = list[i].split("~");
                                console.log("part" + parts1[0] + "==" + parts1[1]);
                                let sub_avail_qty = itemStockMap[parts1[0]] || 0;
                                let rowNo = load_service(
                                    parts1[0],
                                    "",
                                    parts1[2],
                                    parts1[3],
                                    parts1[1],
                                    parts[0],
                                    "Y",
                                    parts1[4],
                                    sub_avail_qty
                                );
                                createdRows.push(rowNo);
                                setTimeout(function (rn) { return function () { calculate(rn); add_service_det(rn); }; }(rowNo), 300);
                            }
                            if (isMultiSet) {
                                createdRows.forEach(function (slno) {
                                    let row = document.getElementById("row_" + slno);
                                    if (row) { row.classList.add("multi-item-set"); }
                                });
                                let firstRow = document.getElementById("row_" + createdRows[0]);
                                if (firstRow) { firstRow.classList.add("multi-item-set-start"); }
                                let lastRow = document.getElementById("row_" + createdRows[createdRows.length - 1]);
                                if (lastRow) { lastRow.classList.add("multi-item-set-end"); }
                            }
                        }
                        recalc_invoice_totals();
                    });
            }, 200);
        }

        function select_group() {
            var a = document.getElementsByClassName("sol-selected-display-item");
            var myvar = "";
            console.log(a.length);
            for (var i = 0; i < a.length; i++) {
                myvar = myvar + (a[i].getAttribute("data-sol-item-val"));
                a[i].setAttribute("data-sol-item-val", "");
                a[i].innerHTML = "";
            }
            console.log(myvar);
            var mainParts = myvar.split("~");
            load_service(mainParts[0], "", "9", "9", mainParts[1], "", "N", "100");
            console.log("group" + mainParts[0] + "==" + mainParts[1]);
            setTimeout(function () {
                $.post("fetch_items.php",
                    {
                        itemgroup_id: mainParts[0]
                    },
                    function (data, status) {
                        let list = $.trim(data).split("@");
                        for (let i = 0; i < list.length - 1; i++) {
                            let parts = list[i].split("~");
                            console.log("part" + parts[0] + "==" + parts[1]);
                            let sub_avail_qty = itemStockMap[parts[0]] || 0;
                            let rn = load_service(
                                parts[0],
                                "",
                                parts[2],
                                parts[3],
                                parts[1],
                                mainParts[0],
                                "Y",
                                parts[4],
                                sub_avail_qty
                            );
                            setTimeout(function (rowNo) { return function () { calculate(rowNo); add_service_det(rowNo); }; }(rn), 300);
                        }
                        recalc_invoice_totals();
                    });
            }, 2000);
        }

        $(function () {

            $("#datepicker").datepicker({
                dateFormat: "dd-mm-yy",
                maxDate: 0
            });
            if ($("#datepicker").val() == "") {
                $("#datepicker").datepicker("setDate", new Date());
            }
            $("#edit_datepicker").datepicker({
                dateFormat: "yy-mm-dd"
            });
        });

        function show_amount() {
            let cost = $("#cost").val() == "" ? 0 : $("#cost").val();
            let qty = $("#qty").val() == "" ? 0 : $("#qty").val();
            $("#amount").val(cost * qty);
            $("#tax").val(($("#amount").val() * $("#tax_pc").val()) / 100);
            $("#tax_sgst").val(($("#amount").val() * $("#tax_pc_sgst").val()) / 100);
            let total = parseFloat($("#amount").val()) + parseFloat($("#tax").val()) + parseFloat($("#tax_sgst").val());
            $("#total").val(total);
        }
        function show_edit_amount() {
            let cost = $("#edit_cost").val() == "" ? 0 : $("#edit_cost").val();
            let qty = $("#edit_qty").val() == "" ? 0 : $("#edit_qty").val();
            $("#edit_amount").val(cost * qty);
            $("#edit_tax").val(($("#edit_amount").val() * $("#edit_tax_pc").val()) / 100);
            $("#edit_tax_sgst").val(($("#edit_amount").val() * $("#edit_tax_pc_sgst").val()) / 100);
            let total = parseFloat($("#edit_amount").val()) + parseFloat($("#edit_tax").val()) + parseFloat($("#edit_tax_sgst").val());
            $("#edit_total").val(total);
        }
        function populate_det() {
            let gst = "<?php echo $gst; ?>";
            let parts = (document.getElementById("service").value).split("~");
            $("#service_id").val(parts[0]);
            $("#cost").val(parts[1]);
            if (gst == "Y") {
                $("#tax_pc").val(parts[2]);
                $("#tax_pc_sgst").val(parts[3]);
            }
            else {
                $("#tax_pc").val("0");
                $("#tax_pc_sgst").val("0");
            }
            show_amount();
        }
        function populate_vehicle_det() {
            let parts = (document.getElementById("vehicle").value).split("~");
            $("#vehicle_id").val(parts[0]);
        }
        function populate_edit_det() {
            let gst = "<?php echo $gst; ?>";
            let parts = (document.getElementById("edit_service").value).split("~");
            $("#edit_service_id").val(parts[0]);
            $("#edit_cost").val(parts[1]);
            if (gst == "Y") { $("#edit_tax_pc").val(parts[2]); $("#edit_tax_pc_sgst").val(parts[3]); }
            else { $("#edit_tax_pc").val("0"); $("#edit_tax_pc_sgst").val("0"); }
            show_edit_amount();
        }
        function populate_edit_vehicle_det() {
            let parts = (document.getElementById("edit_vehicle").value).split("~");
            $("#edit_vehicle_id").val(parts[0]);
        }

        $('#add_opener').on('click', function () {
            addModal.show();
        });

        function recalc_invoice_totals() {
            let grand_total = 0, cgst = 0, sgst = 0, igst = 0, price_amount = 0;

            let mainItemsWithChildren = new Set();
            document.querySelectorAll(".purchase_rows").forEach(function (row) {
                if (row.style.display === "none") return;
                let slno = row.id.split("_")[1];
                let parent = document.getElementById("parent_" + slno).value;
                let account = document.getElementById("account_" + slno).value;
                if (account === "Y" && parent !== "" && parent !== null) {
                    mainItemsWithChildren.add(parent);
                }
            });

            document.querySelectorAll(".purchase_rows").forEach(function (row) {
                if (row.style.display === "none") return;
                let slno = row.id.split("_")[1];
                let account = document.getElementById("account_" + slno).value;
                let itemId = document.getElementById("item_id_" + slno).value;

                let countsToward = true;
                if (account === "N" && mainItemsWithChildren.has(itemId)) {
                    countsToward = false;
                }

                if (countsToward) {
                    let total = parseFloat(document.getElementById("total_" + slno).value || 0);
                    let price = parseFloat(document.getElementById("price_" + slno).value || 0);
                    let discount = parseFloat(document.getElementById("discount_" + slno).value || 0);
                    let roundoff = parseFloat(document.getElementById("roundoff_" + slno).value || 0);
                    let tg = parseFloat(document.getElementById("tax_gst_" + slno).value || 0);
                    let ts = parseFloat(document.getElementById("tax_sgst_" + slno).value || 0);
                    let ti = parseFloat(document.getElementById("tax_igst_" + slno).value || 0);

                    grand_total += total;
                    cgst += tg;
                    sgst += ts;
                    igst += ti;
                    price_amount += (price - discount + roundoff);
                }
            });

            $("#trans_amount").val(grand_total.toFixed(2));
            $("#cgst_amount").val(cgst.toFixed(2));
            $("#sgst_amount").val(sgst.toFixed(2));
            $("#igst_amount").val(igst.toFixed(2));
            $("#price_amount").val(price_amount.toFixed(2));

            calculate_final();
        }

        function add_service_det(service_id) {
            let service = service_id;

            let item_check = document.getElementById("item_id_" + service);
            if (!item_check || item_check.value === "" || item_check.value === null) {
                return;
            }

            let total_check = document.getElementById("total_" + service);
            if (!total_check || parseFloat(total_check.value || 0) <= 0) {
                return;
            }

            let price_selector = "#price_" + service;
            let discount_selector = "#discount_" + service;
            let qty_selector = "#qty_" + service;
            let item_selector = "#item_id_" + service;
            let cost_selector = "#cost_" + service;
            let gst_selector = "#gst_" + service;
            let sgst_selector = "#sgst_" + service;
            let igst_selector = "#igst_" + service;
            let tax_gst_selector = "#tax_gst_" + service;
            let tax_sgst_selector = "#tax_sgst_" + service;
            let tax_igst_selector = "#tax_igst_" + service;
            let total_selector = "#total_" + service;
            let parent_selector = "#parent_" + service;
            let account_selector = "#account_" + service;
            let perc_selector = "#perc_" + service;
            let roundoff_selector = "#roundoff_" + service;
            let remarks_selector = "#remarks_" + service;
            let subtrans_id_selector = "#subtrans_id_" + service;
            let cost = $(cost_selector).val();
            let tax_pc = $(gst_selector).val();
            let tax_pc_sgst = $(sgst_selector).val();
            let tax_pc_igst = $(igst_selector).val();
            let total = $(total_selector).val();
            let tax = $(tax_gst_selector).val();
            let tax_sgst = $(tax_sgst_selector).val();
            let tax_igst = $(tax_igst_selector).val();
            let qty = $(qty_selector).val();
            let discount = $(discount_selector).val();
            let parent = $(parent_selector).val();
            let account = $(account_selector).val();
            let price = $(price_selector).val();
            let perc = $(perc_selector).val();
            let subtrans_id = $(subtrans_id_selector).val();

            if (del_mode == 0) {
                $(qty_selector).prop('disabled', true);
                $(total_selector).prop('disabled', true);
                $(discount_selector).prop('disabled', true);
                $(roundoff_selector).prop('disabled', true);
                $(remarks_selector).prop('disabled', true);

                $.post("add_sales_det.php",
                    {
                        cost: cost,
                        tax_pc: tax_pc,
                        tax_pc_sgst: tax_pc_sgst,
                        tax_pc_igst: tax_pc_igst,
                        total: total,
                        tax: tax,
                        tax_sgst: tax_sgst,
                        tax_igst: tax_igst,
                        qty: qty,
                        subtrans_id: subtrans_id,
                        item_id: $(item_selector).val(),
                        price: price,
                        roundoff: $(roundoff_selector).val(),
                        parent: parent,
                        account: account,
                        trans_id: $("#trans_id").val(),
                        discount: discount,
                        ver: $("#ver").val(),
                        perc: perc,
                        invoice_discount: $("#discount_amount").val(),
                        invoice_roundoff: $("#roundoff_amount").val(),
                        invoice_final_amount: $("#final_amount").val()
                    },
                    function (data, status) {
                        try {
                            if (typeof data !== 'object') data = JSON.parse(data);
                        } catch (e) { }

                        if (data && data.status === 'success') {
                            let id_selector = "#subtrans_id_" + service;
                            let del_selector = "#del_" + service;
                            $(id_selector).val(data.subtrans_id);

                            recalc_invoice_totals();

                            const paid_amount = document.getElementById("paid_amount");
                            if (paid_amount) {
                                paid_amount.setAttribute("min", "0");
                                paid_amount.setAttribute("max", $("#trans_amount").val());
                            }

                            $(del_selector).prop('disabled', false);
                        } else {
                            let errorMsg = (data && data.message) ? data.message : $.trim(data);
                            alert(errorMsg);
                            document.getElementById("qty_" + service).value = "";
                            document.getElementById("total_" + service).value = "";
                            document.getElementById("price_" + service).value = "";
                            document.getElementById("tax_gst_" + service).value = "";
                            document.getElementById("tax_sgst_" + service).value = "";
                            document.getElementById("tax_igst_" + service).value = "";
                            document.getElementById("discount_" + service).value = "0";
                            document.getElementById("roundoff_" + service).value = "0";
                            document.getElementById("qty_" + service).focus();
                        }
                        $(qty_selector).prop('disabled', false);
                        $(total_selector).prop('disabled', false);
                        $(discount_selector).prop('disabled', false);
                        $(roundoff_selector).prop('disabled', false);
                        $(remarks_selector).prop('disabled', false);
                    });
            }
        }

        $('#edit').on('click', function (e) {
            let service = $("#edit_service_id").val();
            let vehicle = $("#edit_vehicle_id").val();
            let cost = $("#edit_cost").val();
            let tax_pc = $("#edit_tax_pc").val();
            let tax_pc_sgst = $("#edit_tax_pc_sgst").val();
            let total = $("#edit_amount").val();
            let tax = $("#edit_tax").val();
            let tax_sgst = $("#edit_tax_sgst").val();
            let qty = $("#edit_qty").val();
            let subtrans_id = $("#subtrans_id").val();
            $.post("edit_service_det.php",
                {
                    cost: cost,
                    tax_pc: tax_pc,
                    tax_pc_sgst: tax_sgst,
                    total: total,
                    tax: tax,
                    tax_sgst: tax_sgst,
                    qty: qty,
                    service_id: service,
                    vehicle: vehicle,
                    trans_id: "<?php echo $trans_id; ?>",
                    subtrans_id: subtrans_id
                },
                function (data, status) {
                    console.log(data);
                    location.reload();
                });
        });

        function delete_it(trans_id, subtrans_id) {
            var r = confirm("Are you sure that you want to delete the transaction");
            if (r) {
                $.post("delete_trans_det.php",
                    {
                        trans_id: trans_id,
                        subtrans_id: subtrans_id,
                        ver: $("#ver").val()
                    },
                    function (data, status) {
                        location.reload();
                    });
            }
        }

        function get_details() {
            let vehicle_no = $("#vehicle_no").val();
            $.post("get_details.php",
                {
                    vehicle_no: vehicle_no
                },
                function (data, status) {
                    let parts = $.trim(data).split("~");
                    $("#vehicle").val(parts[0]);
                    $("#vehicle_model").val(parts[1]);
                    $("#no_of_wheels").val(parts[2]);
                    $("#customer").val(parts[3]);
                    $("#company_name").val(parts[4]);
                    $("#customer_name").val(parts[5]);
                });
        }

        function get_gst() {
            let customer = $("#customer").val();
            $.post("get_gst.php",
                {
                    customer_id: customer
                },
                function (data, status) {
                    let parts = $.trim(data).split("~");
                    var datalist = document.getElementById("gst_list");
                    datalist.innerHTML = "";
                    parts.forEach(part => {
                        const option = document.createElement("option");
                        option.value = part;
                        datalist.appendChild(option);
                    });
                    if (parts.length > 0) {
                        document.getElementById("customer_gst").value = parts[0];
                        console.log(parts[0]);
                        set_gst();
                    }
                });
        }

        function get_address() {
            let customer = $("#customer").val();
            $.post("get_address.php",
                {
                    customer_id: customer
                },
                function (data, status) {
                    let parts = $.trim(data).split("~");
                    var datalist = document.getElementById("address_list");
                    datalist.innerHTML = "";
                    parts.forEach(part => {
                        const option = document.createElement("option");
                        option.value = part;
                        datalist.appendChild(option);
                    });
                    if (parts.length > 0) {
                        document.getElementById("customer_address").value = parts[0];
                    }
                });
        }

        function set_gst() {
            let str = document.getElementById("customer_gst").value;
            document.querySelectorAll('.row_service').forEach(el => {
                el.style.display = 'none';
            });
            document.querySelectorAll('.qty').forEach(el => {
                el.value = '0';
            });
            if (str.startsWith("37")) {
                $("#cgst_igst").html("CGST");
                $("#cgst_igst_amount").html("CGST(9%)");
                document.querySelectorAll('.hide_sgst').forEach(el => {
                    el.style.display = '';
                });
            } else {
                $("#cgst_igst").html("IGST");
                $("#cgst_igst_amount").html("IGST(18%)");
                document.querySelectorAll('.hide_sgst').forEach(el => {
                    el.style.display = 'none';
                });
            }
        }

        function edit_it(trans_id, subtrans_id, service, vehicle, qty) {
            $("#trans_id").val(trans_id);
            $("#subtrans_id").val(subtrans_id);
            $("#edit_service").val(service);
            $("#edit_vehicle").val(vehicle);
            $("#edit_qty").val(qty);
            populate_edit_det();
            populate_edit_vehicle_det();
            show_edit_amount();
            editModal.show();
        }

        function load_service(service, price, gst, sgst, name, parent, account, perc, available_qty = 0) {
            let trans_id = document.getElementById("trans_id").value;
            // Removed mandatory check for customer and company name

            let str = document.getElementById("customer_gst").value;
            var slno = 0;
            while (true) {
                let row = document.getElementById("row_" + slno);
                if (!row) {
                    alert("No more item slots available. Please save and reload.");
                    return false;
                }

                let isHidden = (row.style.display === "none" || $(row).css("display") === "none");
                let itemId = document.getElementById("item_id_" + slno) ?
                    document.getElementById("item_id_" + slno).value : "";

                if (isHidden && (itemId === "" || itemId === null)) {
                    break;
                }
                slno++;
            }

            console.log("Loading into free row:", slno);

            let gst_selector = "gst_" + slno;
            let sgst_selector = "sgst_" + slno;
            let igst_selector = "igst_" + slno;
            let price_selector = "cost_" + slno;
            let item_selector = "item_id_" + slno;
            let item_name_selector = "item_name_" + slno;
            let item_parent_selector = "parent_" + slno;
            let item_account_selector = "account_" + slno;
            let item_perc_selector = "perc_" + slno;
            let available_qty_selector = "available_qty_" + slno;

            if (document.getElementById(available_qty_selector)) {
                document.getElementById(available_qty_selector).value = available_qty || 0;
            }

            if (document.getElementById(price_selector))
                document.getElementById(price_selector).value = parseInt(price || 0);

            if (document.getElementById(item_selector))
                document.getElementById(item_selector).value = service;

            let nameEl = document.getElementById(item_name_selector);
            if (nameEl) {
                nameEl.innerHTML = name;
            } else {
                console.log("item_name span missing for slot:", slno);
            }

            document.getElementById(item_parent_selector).value = parent;
            document.getElementById(item_perc_selector).value = perc;
            document.getElementById(item_account_selector).value = account;

            if (str.startsWith("37")) {
                document.getElementById(sgst_selector).value = parseInt(gst || 0);
                document.getElementById(gst_selector).value = parseInt(gst || 0);
            } else {
                document.getElementById(igst_selector).value = parseInt(gst || 0) + parseInt(gst || 0);
            }

            if (trans_id == "" || trans_id == null) {
                $.post("add_sales.php",
                    {
                        customer_mobile: $("#customer_mobile").val(),
                        company_name: $("#company_name").val(),
                        customer_name: $("#customer_name").val(),
                        customer_address: $("#customer_address").val(),
                        customer_gst: $("#customer_gst").val(),
                        customer: $("#customer").val(),
                        trans_date: $("#datepicker").val(),
                        gst: "<?php echo $_COOKIE["gst"]; ?>"
                    },
                    function (data, status) {
                        let selector = "row_" + slno;
                        document.getElementById(selector).style.display = "";
                        if (data.status !== "success") {
                            alert(data.message);
                            return;
                        }
                        document.getElementById("trans_id").value = data.trans_id;
                        document.getElementById("year_part").innerHTML = data.year_part;
                        document.getElementById("ver").value = "0";

                        // ── Update customer cell to locked display after first item added ──
                        let cell = document.getElementById("customer_search_cell");
                        if (cell && document.getElementById("customer_search_input")) {
                            let companyName = $("#company_name").val();
                            let customerName = $("#customer_name").val();
                            let display = companyName || customerName;
                            if (companyName && customerName) { display = companyName + " (" + customerName + ")"; }
                            cell.innerHTML = '<label style="font-weight:bold;">Customer</label>'
                                + '<div class="form-control" id="customer_display" style="background:#f8f9fa;">' + display + '</div>'
                                + '<input type="hidden" id="customer" name="customer" value="' + $("#customer").val() + '">';
                        }
                        $("#company_name").prop("readonly", true);
                        $("#customer_name").prop("readonly", true);
                        $("#customer_mobile").prop("readonly", true);

                        return slno;
                    });
            } else {
                let selector = "row_" + slno;
                document.getElementById(selector).style.display = "";
                return slno;
            }
            return slno;
        }

        function calculate(service) {
            console.log(service);
            let price_selector = "price_" + service;
            let cost_selector = "cost_" + service;
            let discount_selector = "discount_" + service;
            let roundoff_selector = "roundoff_" + service;
            let qty_selector = "qty_" + service;
            let total_selector = "total_" + service;
            let account_selector = "account_" + service;
            let account = document.getElementById(account_selector).value;

            let available_qty_el = document.getElementById("available_qty_" + service);
            if (available_qty_el) {
                let available_qty = parseFloat(available_qty_el.value || 0);
                let entered_qty = parseFloat(document.getElementById(qty_selector).value || 0);
                if (entered_qty > available_qty) {
                    alert("Entered quantity (" + entered_qty + ") exceeds available quantity (" + available_qty + ")");
                    document.getElementById(qty_selector).value = available_qty;
                }
            }

            let discount = (document.getElementById(discount_selector).value == "" || document.getElementById(discount_selector).value == null) ? 0 : parseFloat(document.getElementById(discount_selector).value);
            let roundoff = (document.getElementById(roundoff_selector).value == "" || document.getElementById(roundoff_selector).value == null) ? 0 : parseFloat(document.getElementById(roundoff_selector).value);
            let total = (document.getElementById(total_selector).value == "" || document.getElementById(total_selector).value == null) ? 0 : parseFloat(document.getElementById(total_selector).value);
            let gst_selector = "gst_" + service;
            let sgst_selector = "sgst_" + service;
            let igst_selector = "igst_" + service;
            let tax_gst_selector = "tax_gst_" + service;
            let tax_sgst_selector = "tax_sgst_" + service;
            let tax_igst_selector = "tax_igst_" + service;
            let tax_perc_gst = 0, tax_perc_sgst = 0, tax_perc_igst = 0;
            try { tax_perc_gst = parseFloat(document.getElementById(gst_selector).value || 0); } catch (err) { tax_perc_gst = 0; }
            try { tax_perc_sgst = parseFloat(document.getElementById(sgst_selector).value || 0); } catch (err) { tax_perc_sgst = 0; }
            try { tax_perc_igst = parseFloat(document.getElementById(igst_selector).value || 0); } catch (err) { tax_perc_igst = 0; }
            if (Number.isNaN(tax_perc_gst)) { tax_perc_gst = 0; }
            if (Number.isNaN(tax_perc_sgst)) { tax_perc_sgst = 0; }
            if (Number.isNaN(tax_perc_igst)) { tax_perc_igst = 0; }
            let gst_percentage = (tax_perc_gst + tax_perc_sgst + tax_perc_igst);
            let final_total_after_discount = total - discount + roundoff;
            if (final_total_after_discount < 0) { final_total_after_discount = 0; }
            let actual_price = final_total_after_discount / (1 + (gst_percentage / 100));
            if (actual_price < 0) { actual_price = 0; }
            document.getElementById(price_selector).value = actual_price.toFixed(2);

            if (account === "" || account === null || typeof account === "undefined") {
                let qty = parseFloat(document.getElementById(qty_selector).value || 0);
                if (qty > 0) {
                    document.getElementById(cost_selector).value = (actual_price / qty).toFixed(2);
                } else {
                    document.getElementById(cost_selector).value = "0";
                }
            } else {
                let qty = parseFloat(document.getElementById(qty_selector).value || 0);
                if (qty > 0) {
                    document.getElementById(cost_selector).value = (actual_price / qty).toFixed(2);
                } else {
                    document.getElementById(cost_selector).value = actual_price.toFixed(2);
                }
            }

            let cgst = (actual_price * tax_perc_gst / 100);
            let sgst = (actual_price * tax_perc_sgst / 100);
            let igst = (actual_price * tax_perc_igst / 100);
            document.getElementById(tax_gst_selector).value = cgst.toFixed(2);
            document.getElementById(tax_sgst_selector).value = sgst.toFixed(2);
            document.getElementById(tax_igst_selector).value = igst.toFixed(2);

            if (account === "" || account === null || typeof account === "undefined") {
                let final_total = actual_price + cgst + sgst + igst;
                document.getElementById(total_selector).value = final_total.toFixed(2);
            }

            let item_selector = "item_id_" + service;
            let item = document.getElementById(item_selector).value;
            if (account == "N") {
                var rows = document.querySelectorAll(".purchase_rows");
                let total_child_amount = 0;
                rows.forEach(function (row) {
                    var rowId = row.id;
                    if (rowId && rowId.startsWith("row_")) {
                        var slno = rowId.split("_")[1];
                        var parentElement = document.getElementById("parent_" + slno);
                        if (parentElement) {
                            if (parentElement.value == item) {
                                setTimeout(function () {
                                    let qtyVal = parseFloat(document.getElementById(qty_selector).value);
                                    let totalVal = parseFloat(document.getElementById(total_selector).value);
                                    let child_qty = document.getElementById("qty_" + slno);
                                    let child_total = document.getElementById("total_" + slno);
                                    let child_perc = document.getElementById("perc_" + slno);
                                    child_qty.value = qtyVal;
                                    let child_amount = (totalVal * parseFloat(child_perc.value) / 100);
                                    child_total.value = child_amount.toFixed(2);
                                    total_child_amount += child_amount;
                                    calculate(slno);
                                    add_service_det(slno);
                                }, 700);
                            }
                        }
                    }
                });
            }
            recalc_invoice_totals();
        }

        function del_service_det(service) {
            let id_selector = "subtrans_id_" + service;
            let subtrans_id = document.getElementById(id_selector).value;
            let item = document.getElementById("item_id_" + service).value;
            let parent = document.getElementById("parent_" + service).value;
            let account = document.getElementById("account_" + service).value;

            var r = confirm("Are you sure that you want to delete the transaction");
            if (r) {
                del_mode = 1;

                let isGrouped = false;
                let groupId = "";

                if (account === "N") {
                    isGrouped = true;
                    groupId = item;
                } else if (parent !== "" && parent !== null && parent !== "undefined" && parent !== "0") {
                    isGrouped = true;
                    groupId = parent;
                }

                $.post("delete_trans_det_sales.php",
                    {
                        trans_id: $("#trans_id").val(),
                        subtrans_id: subtrans_id,
                        item_id: item,
                        ver: $("#ver").val()
                    },
                    function (data, status) {

                        if (data.indexOf("Error") !== -1) {
                            alert(data);
                            del_mode = 0;
                            return;
                        }

                        if (isGrouped) {
                            document.querySelectorAll(".purchase_rows").forEach(function (row) {
                                let slno = row.id.split("_")[1];
                                let itemId = document.getElementById("item_id_" + slno)?.value;
                                let parentId = document.getElementById("parent_" + slno)?.value;
                                let rowAccount = document.getElementById("account_" + slno)?.value;

                                let belongsToGroup = false;
                                if (rowAccount === "N" && itemId == groupId) { belongsToGroup = true; }
                                if (rowAccount === "Y" && parentId == groupId) { belongsToGroup = true; }

                                if (belongsToGroup) { clearAndHideRow(slno); }
                            });

                        } else {
                            clearAndHideRow(service);
                        }

                        recalc_invoice_totals();

                        setTimeout(() => {
                            del_mode = 0;
                        }, 700);
                    });
            }
        }

        function clearAndHideRow(slno) {
            let row = document.getElementById("row_" + slno);
            if (!row) return;

            row.style.display = "none";
            row.classList.remove("multi-item-set", "multi-item-set-start", "multi-item-set-end");
            row.classList.remove("section-group-even", "section-group-odd");
            row.setAttribute("onfocusout", "add_service_det('" + slno + "')");

            $("#item_id_" + slno).val("");
            $("#parent_" + slno).val("");
            $("#subtrans_id_" + slno).val("");
            $("#account_" + slno).val("");
            $("#perc_" + slno).val("");

            let nameEl = document.getElementById("item_name_" + slno);
            if (nameEl) { nameEl.innerHTML = ""; }

            $("#qty_" + slno).val("");
            $("#cost_" + slno).val("");
            $("#price_" + slno).val("");
            $("#total_" + slno).val("");
            $("#discount_" + slno).val("");
            $("#roundoff_" + slno).val("");
            $("#remarks_" + slno).val("");

            $("#gst_" + slno).val("");
            $("#sgst_" + slno).val("");
            $("#igst_" + slno).val("");
            $("#tax_gst_" + slno).val("");
            $("#tax_sgst_" + slno).val("");
            $("#tax_igst_" + slno).val("");
        }

        function empty_str(v) {
            return (v === "" || v === null || v === undefined);
        }

        function dummy_save() {
            window.location.href = "sales_trans_new.php?trans_id=" + $("#trans_id").val();
        }

        function save_pay1() {
            let paid_amount = $("#paid_amount").val();
            let pay_type = $("#pay_type1").val();
            let ref_no = $("#ref_no1").val();
            let trans_amount = $("#trans_amount").val();
            let account = "";
            let max_amount = parseFloat($("#final_amount").val()) - 0;
            try { account = $("#account1").val().split("~")[1]; } catch (err) { account = ""; }
            if (parseFloat(paid_amount) > max_amount && max_amount > 0) {
                alert("Amount should not exceed pending amount");
                return false;
            }
            $.post("update_pay_sales.php",
                {
                    trans_id: $("#trans_id").val(),
                    paid_amount: paid_amount,
                    pay_type: pay_type.split("~")[1],
                    account: account,
                    ref_no: ref_no,
                    trans_amount: trans_amount,
                    customer_name: $("#customer_name").val(),
                    customer: $("#customer").val(),
                    trans_date: $("#datepicker").val(),
                    gst: "<?php echo $gst; ?>"
                },
                function (data, status) {
                    alert("Transaction Saved with ID: " + $("#trans_id").val());
                    $('#invoice').prop('disabled', false);
                    window.location.href = "sales_trans_new.php?trans_id=" + $("#trans_id").val();
                });
        }

        function get_invoice() {
            let trans_id = $("#trans_id").val();
            let url = "sales_receipt_new.php?trans_id=" + trans_id;
            window.open(url, '_blank');
        }
        function get_jobcard() {
            let trans_id = $("#trans_id").val();
            let url = "job_card.php?trans_id=" + trans_id;
            window.open(url, '_blank');
        }
        function filter_account1() {
            let pay_type = document.getElementById("pay_type1").value.split("~")[0];
            $('#account1 option').filter(function () {
                return !$(this).val().toLowerCase().includes(pay_type);
            }).prop('disabled', true);
        }

        $(document).ready(function () {
            const input = document.getElementById('paid_amount');
            if (input) {
                input.addEventListener('input', function () {
                    const min = parseInt(input.min);
                    const max = parseInt(input.max);
                    const value = parseInt(input.value);
                    if (value > max) { input.value = max; }
                    else if (value < min) { input.value = min; }
                });
            }
        });

        const tooltipWrappers = document.querySelectorAll('.tooltip-wrapper');
        tooltipWrappers.forEach(wrapper => {
            const input = wrapper.querySelector('input');
            const tooltip = wrapper.querySelector('.tooltip-text');
            function showTooltip() { if (input.readOnly) { wrapper.classList.add('show'); } }
            function hideTooltip() { wrapper.classList.remove('show'); }
            input.addEventListener('mouseenter', showTooltip);
            input.addEventListener('focus', showTooltip);
            input.addEventListener('mouseleave', hideTooltip);
            input.addEventListener('blur', hideTooltip);
        });

        function setwheels() {
            let qty_selector = "qty_1000002";
            let cost_selector = "cost_1000002";
            document.getElementById(qty_selector).value = $("#no_of_wheels").val();
            calculate("1000002");
            if (parseFloat(document.getElementById(cost_selector).value) < 800) { document.getElementById(cost_selector).value = "800"; }
        }

        function save_dummy() {
            if ($("#trans_id").val() === "") {
                alert("Please add at least one item before saving.");
                return;
            }
            $.post("save_draft_sale.php",
                {
                    value: $("#trans_id").val(),
                    discount: $("#discount_amount").val(),
                    roundoff: $("#roundoff_amount").val(),
                    trans_amount: $("#trans_amount").val(),
                    ver: $("#ver").val()
                },
                function (data, status) {
                    console.log(data);
                    alert("Saved Transaction Successfully");
                    location.href = "sales_trans_new.php?trans_id=" + $("#trans_id").val();
                    $("#add_opener").prop("disabled", false);

                });
        }

        function unlock() {
            console.log("TRANS ID =", $("#trans_id").val());

            $.post("unlock_sales.php",
                {
                    trans_id: $("#trans_id").val(),
                    ver: $("#ver").val()
                },
                function (data, status) {
                    console.log(data);
                    alert("Transaction unlocked with ID: " + $("#trans_id").val());
                    window.location.href = "sales_trans_new.php?trans_id=" + $("#trans_id").val();
                });
        }
        
        function delete_it_pay(payment_id, nature, account, account_to, amount, mode) {
            if (confirm("Are you sure that you want to delete this payment?")) {
                $.post(
                    "delete_payment.php",
                    {
                        payment_id: payment_id,
                        nature: nature,
                        account: account,
                        account_to: account_to,
                        amount: amount,
                        mode: mode
                    },
                    function(data) {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    }
                );
            }
        }


        function calculate_final() {
            let trans_amount = parseFloat(document.getElementById("trans_amount").value || 0);
            let discount = parseFloat(document.getElementById("discount_amount").value || 0);

            // Automatically round to nearest whole number
            let raw_amount = trans_amount - discount;
            let final_amount = Math.round(raw_amount);
            let roundoff = final_amount - raw_amount;

            if (final_amount < 0) {
                final_amount = 0;
                roundoff = 0 - raw_amount;
            }

            document.getElementById("roundoff_amount").value = roundoff.toFixed(2);
            document.getElementById("final_amount").value = final_amount.toFixed(2);
        }
    </script>

</body>

</html>