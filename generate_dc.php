<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include "db_config.php";

$trans_id = isset($_GET['trans_id']) ? mysqli_real_escape_string($conn, $_GET['trans_id']) : '';
if ($trans_id == '') {
    die("Transaction ID Missing");
}

$sql_hdr = "SELECT * FROM sales_trans WHERE trans_id='$trans_id'";
$res_hdr = $conn->query($sql_hdr);

if (!$res_hdr || $res_hdr->num_rows == 0) {
    die("Invalid Transaction ID");
}

$hdr = $res_hdr->fetch_assoc();
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
                        Order ID - <?php echo $trans_id; ?>
                    </div>

                    <input type="hidden" id="sales_trans_id" value="<?php echo $trans_id; ?>">
                    <input type="hidden" id="customer" value="<?php echo $hdr['customer']; ?>">

                    <div class="card mb-3">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-3">
                                    <label>Trans ID</label>
                                    <input class="form-control" readonly value="<?php echo $hdr['trans_id']; ?>">
                                </div>

                                <div class="col-md-3">
                                    <label>Company Name</label>
                                    <input class="form-control" readonly value="<?php echo $hdr['COMPANY_NAME']; ?>">
                                </div>

                                <div class="col-md-3">
                                    <label>Customer Name</label>
                                    <input class="form-control" readonly value="<?php echo $hdr['CUSTOMER_NAME']; ?>">
                                </div>

                                <div class="col-md-3">
                                    <label>Mobile</label>
                                    <input class="form-control" readonly value="<?php echo $hdr['CUSTOMER_MOBILE']; ?>">
                                </div>
                            </div>

                            <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Vehicle No</label>
                                    <input type="text" id="vehicle_no" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label>Odometer Reading</label>
                                    <input type="text" id="odometer" class="form-control">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            Pending Items For Delivery
                        </div>

                        <div class="card-body">

                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Sale Qty</th>
                                        <th>Pending</th>
                                        <th>Nos To Deliver</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    // $sql_items = "
// SELECT
// d.trans_id,
// d.subtrans_id,
// d.item_id,
// d.qty,
// IFNULL(d.pending,d.qty) pending_qty,
// i.item_name
// FROM sales_trans_det d
// INNER JOIN item i ON i.item_id=d.item_id
// WHERE d.trans_id='$trans_id' AND active_status='A' AND account='Y'
// AND IFNULL(d.pending,d.qty) > 0
// ORDER BY d.subtrans_id";
                                    
                                    $sql_items = "
SELECT
d.trans_id,
d.subtrans_id,
d.item_id,
d.qty,
IFNULL(d.pending,d.qty) pending_qty,
i.item_name
FROM sales_trans_det d
INNER JOIN item i ON i.item_id=d.item_id
WHERE d.trans_id='$trans_id'
AND d.ver = (
    SELECT MAX(ver)
    FROM sales_trans_det
    WHERE trans_id='$trans_id'
)
AND active_status='A'
AND account='Y'
AND IFNULL(d.pending,d.qty) > 0";

                                    $res_items = $conn->query($sql_items);

                                    while ($row = $res_items->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['item_name']; ?></td>
                                            <td><?php echo $row['qty']; ?></td>
                                            <td><?php echo $row['pending_qty']; ?></td>
                                            <td>
                                                <input type="number" class="form-control qtybox"
                                                    id="qty_<?php echo $row['subtrans_id']; ?>" min="0"
                                                    max="<?php echo $row['pending_qty']; ?>">

                                                <input type="hidden" class="item_row"
                                                    data-trans="<?php echo $row['trans_id']; ?>"
                                                    data-sub="<?php echo $row['subtrans_id']; ?>"
                                                    data-item="<?php echo $row['item_id']; ?>"
                                                    data-pending="<?php echo $row['pending_qty']; ?>">
                                            </td>
                                        </tr>
                                    <?php } ?>

                                </tbody>
                            </table>

                            <div class="text-right">

                                <button id="btnSubmitDelivery" class="btn btn-success" onclick="submitDelivery()">
                                    Submit Delivery
                                </button>

                            </div>

                            <div id="printArea" class="text-right mt-2"></div>

                        </div>
                    </div>

                </div>

                <?php include "footer.php"; ?>

            </div>
        </div>
    </div>

    <?php include "footer_include.php"; ?>

    <script src="js/jquery-3.5.1.min.js"></script>

    <script>
        function submitDelivery() {

            if ($("#btnSubmitDelivery").prop("disabled")) {
                return;
            }

            $("#btnSubmitDelivery").prop("disabled", true);

            let vehicle = $("#vehicle_no").val();
            let odometer = $("#odometer").val();

            // if(vehicle==""){
            //     alert("Enter Vehicle No");
            //     return;
            // }

            // if(odometer==""){
            //     alert("Enter Odometer");
            //     return;
            // }

            //     if(vehicle==""){
            //         alert("Enter Vehicle No");
            //         $("#btnSubmitDelivery").prop("disabled", false);
            //         return;
            //     }

            //     if(odometer==""){
            //         alert("Enter Odometer");
            //         $("#btnSubmitDelivery").prop("disabled", false);
            //         return;
            //     }

            //     if(vehicle==""){
            //     alert("Enter Vehicle No");
            //     $("#btnSubmitDelivery").prop("disabled", false);
            //     return;
            // }

            // if(odometer==""){
            //     alert("Enter Odometer");
            //     $("#btnSubmitDelivery").prop("disabled", false);
            //     return;
            // }

            if (vehicle == "") {
                alert("Enter Vehicle No");
                $("#btnSubmitDelivery").prop("disabled", false);
                return;
            }

            if (odometer == "") {
                alert("Enter Odometer");
                $("#btnSubmitDelivery").prop("disabled", false);
                return;
            }

            let hasQty = false;

            $(".qtybox").each(function () {
                if ($(this).val() != "" && parseInt($(this).val()) > 0) {
                    hasQty = true;
                }
            });

            if (!hasQty) {
                alert("Enter atleast one delivery quantity");
                $("#btnSubmitDelivery").prop("disabled", false);
                return;
            }

            let validationFailed = false;

            $(".item_row").each(function () {

                let subtrans_id = $(this).data("sub");
                let pending = $(this).data("pending");
                let qty = $("#qty_" + subtrans_id).val();

                if (qty != "" && parseInt(qty) > parseInt(pending)) {
                    alert("Cannot deliver more than pending qty");
                    validationFailed = true;
                    return false;
                }
            });

            if (validationFailed) {
                $("#btnSubmitDelivery").prop("disabled", false);
                return;
            }

            $.post("add_dc.php",
                {
                    trans_id: $("#sales_trans_id").val(),
                    customer: $("#customer").val(),
                    company_name: "<?php echo addslashes($hdr['COMPANY_NAME']); ?>",
                    customer_name: "<?php echo addslashes($hdr['CUSTOMER_NAME']); ?>",
                    customer_mobile: "<?php echo addslashes($hdr['CUSTOMER_MOBILE']); ?>",
                    trans_date: new Date().toLocaleDateString('en-GB').replace(/\//g, '-'),
                    vehicle: vehicle,
                    odometer: odometer
                },
                function (dc_id) {

                    $(".item_row").each(function () {

                        let trans_id = $(this).data("trans");
                        let subtrans_id = $(this).data("sub");
                        let item_id = $(this).data("item");
                        let pending = $(this).data("pending");

                        let qty = $("#qty_" + subtrans_id).val();

                        if (qty == "" || parseInt(qty) <= 0) {
                            return;
                        }

                        if (parseInt(qty) > parseInt(pending)) {
                            alert("Cannot deliver more than pending qty");
                            return false;
                        }

                        $.ajax({
                            url: "add_dc_det.php",
                            type: "POST",
                            async: false,
                            data: {
                                dc_id: dc_id.trim(),
                                trans_id: trans_id,
                                sub_trans_id: subtrans_id,
                                item_id: item_id,
                                qty: qty,
                                vehicle: vehicle,
                                odometer: odometer
                            }
                        });
                    });

                    //alert("Delivery Saved Successfully");
                    // window.location.href="generate_dc.php?trans_id="+$("#sales_trans_id").val();

                    alert("Delivery Saved Successfully");

                    $("#btnSubmitDelivery").hide();
                    $("#vehicle_no").prop("readonly", true);
                    $("#odometer").prop("readonly", true);
                    $(".qtybox").prop("readonly", true);
                    $("#btnSubmitDelivery").hide();

                    /*$("#printArea").html(
                        '<a href="delivery_receipt.php?dc_id='
                        + dc_id.trim() +
                        '" target="_blank" class="btn btn-primary">' +
                        'Print DC Challan' +
                        '</a>'
                    );*/

                    $("#printArea").html(
                        '<div class="alert alert-success text-center mb-2">' +
                        '<strong>Delivery Challan No : ' + dc_id.trim() + '</strong>' +
                        '</div>' +

                        '<a href="delivery_receipt.php?dc_id=' +
                        dc_id.trim() +
                        '" target="_blank" class="btn btn-primary">' +
                        'Print DC Challan' +
                        '</a>'
                    );
                    // window.location.href="delivery_receipt.php?dc_id="+dc_id.trim();
                });
        }

        $(document).on("blur", ".qtybox", function () {

            let entered = parseInt($(this).val()) || 0;
            let pending = parseInt($(this).attr("max")) || 0;

            if (entered > pending) {
                alert("Cannot deliver more than pending qty");

                $(this).val('');

                setTimeout(() => {
                    $(this).focus();
                }, 100);
            }
        });
    </script>

</body>

</html> });
    </script>

</body>

</html>