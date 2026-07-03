<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include "db_config.php";

$dc_id = $_GET["dc_id"];
$shop = $_COOKIE["shop"];

$sql = "select * from company";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $company_name1 = $row["company_name"];
    $company_address1 = $row["company_address"];
    $company_gst = $row["company_gst"];
    $company_mobile = $row["company_mobile"];
    $company_email = $row["company_email"];
}

$sql = "select * from shop where shop_id='" . $shop . "'";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $company_address1 = $row["address"];
}

$sql = "SELECT * FROM delivery_challan WHERE dc_id='" . $dc_id . "'";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $trans_id = $row["trans_id"];
}

$sql = "SELECT DATE_FORMAT(trans_date,'%d-%m-%Y') trans_date,
      gst,COMPANY_NAME,CUSTOMER_NAME,CUSTOMER_ADDRESS,
      CUSTOMER_GST,customer_mobile, year_part
      FROM sales_trans
      WHERE active_status!='Z' AND trans_id='" . $trans_id . "'";
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
    $gst = $row["gst"];
    $company_name = $row["COMPANY_NAME"];
    $company_address = $row["CUSTOMER_ADDRESS"];
    $owner_name = $row["CUSTOMER_NAME"];
    $owner_gst = $row["CUSTOMER_GST"];
    $owner_mobile = $row["customer_mobile"];
    $trans_date = $row["trans_date"];
    $year_part = $row["year_part"];
}

$sql_veh = "SELECT MAX(vehicle) as vehicle, MAX(run_km) as run_km FROM sales_trans_det WHERE trans_id='" . $trans_id . "'";
$res_veh = $conn->query($sql_veh);
$vehicle = '';
$odometer = '';
if ($row_veh = $res_veh->fetch_assoc()) {
    $vehicle = $row_veh["vehicle"];
    $odometer = $row_veh["run_km"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Challan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            text-transform: uppercase;
            font-size: 11px;
            line-height: 1.4;
        }

        .receipt-container {
            max-width: 850px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 0;
            background: #fff;
        }

        .header-section {
            display: flex;
            border-bottom: 1px solid #000;
            padding: 10px;
            align-items: center;
            justify-content: space-between;
        }

        .logo-left {
            width: 20%;
            text-align: left;
        }

        .logo-left img {
            max-width: 120px;
            height: auto;
        }

        .logo-center {
            width: 40%;
            text-align: center;
        }

        .logo-center img {
            max-width: 280px;
            height: auto;
        }

        .header-details {
            width: 40%;
            text-align: right;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.4;
            color: #000;
        }

        .header-details .address-text {
            color: #003366;
        }

        .invoice-type-banner {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            text-decoration: underline;
            letter-spacing: 2px;
            padding: 10px 0;
            border-bottom: 1px solid #000;
            color: #000;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #000;
        }

        .details-box {
            padding: 10px 15px;
            border-right: 1px solid #000;
        }

        .details-box:last-child {
            border-right: none;
        }

        .section-title {
            font-weight: 700;
            background: #f2f2f2;
            padding: 3px 8px;
            margin-bottom: 8px;
            display: inline-block;
            border: 1px solid #000;
            font-size: 10px;
        }

        .info-row {
            display: flex;
            margin-bottom: 3px;
        }

        .info-label {
            width: 100px;
            font-weight: 600;
        }

        .info-value {
            flex: 1;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            border-bottom: 1px solid #000;
        }

        .items-table th {
            background: #f2f2f2;
            border: 1px solid #000;
            padding: 8px 5px;
            font-weight: 700;
            text-align: center;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 6px 5px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .goods-condition {
            padding: 15px;
            text-align: center;
            font-weight: 700;
            border-bottom: 1px solid #000;
            font-size: 12px;
            background: #fff;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            padding: 40px 15px 10px;
            text-align: center;
        }

        .sig-box {
            width: 25%;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 10px;
            font-weight: 600;
        }

        @media print {
            body {
                padding: 0;
            }

            .receipt-container {
                border: 1px solid #000;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="no-print"
        style="background: #e52d27; padding: 10px; display: flex; justify-content: space-between; align-items: center; color: white;">
        <button type="button" onclick="window.location.href='delivery_challan.php'"
            style="background: white; color: #b31217; border: none; padding: 5px 15px; border-radius: 4px; font-weight: bold; cursor: pointer;">
            &larr; BACK TO DC LIST
        </button>
        <div style="font-weight: bold; font-size: 16px;">PRINT PREVIEW</div>
        <div>
            <button type="button" onclick="window.print()"
                style="background: white; color: #b31217; border: none; padding: 5px 15px; border-radius: 4px; font-weight: bold; cursor: pointer; margin-right: 10px;">
                PRINT
            </button>
            <button type="button" onclick="downloadPDF()"
                style="background: white; color: #b31217; border: none; padding: 5px 15px; border-radius: 4px; font-weight: bold; cursor: pointer;">
                DOWNLOAD PDF
            </button>
        </div>
    </div>

    <div class="receipt-container" id="content-to-pdf">
        <div class="header-section">
            <div class="logo-left">
                <img src="img/expert_logo.png" alt="Expert Wheel Care">
            </div>
            <div class="logo-center">
                <img src="img/EWC-logo-removebg-preview.png" alt="Expert Wheel Care">
            </div>
            <div class="header-details">
                <div class="address-text">
                    <?php echo $company_address1; ?>
                </div>
                <?php if (!empty($company_gst)) { ?>
                    <div style="color: #003366; margin-top: 5px;">GSTIN : <?php echo $company_gst; ?></div>
                <?php } ?>
                <div style="margin-top: 2px;">&#9742; <?php echo $company_mobile; ?></div>
            </div>
        </div>
        <div class="invoice-type-banner">DELIVERY CHALLAN</div>

        <div class="details-grid">
            <div class="details-box">
                <div class="section-title">CUSTOMER DETAILS</div>
                <div class="info-row">
                    <div class="info-label">COMPANY:</div>
                    <div class="info-value"><?php echo $company_name ? $company_name : "NA - Not Provided"; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">OWNER:</div>
                    <div class="info-value"><?php echo $owner_name ? $owner_name : "NA - Not Provided"; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">MOBILE:</div>
                    <div class="info-value"><?php echo $owner_mobile ? $owner_mobile : "NA - Not Provided"; ?></div>
                </div>
                <?php if ($gst == "Y" && !empty($owner_gst)) { ?>
                    <div class="info-row">
                        <div class="info-label">GSTIN:</div>
                        <div class="info-value"><?php echo $owner_gst; ?></div>
                    </div>
                <?php } ?>
                <div class="info-row">
                    <div class="info-label">ADDRESS:</div>
                    <div class="info-value"><?php echo $company_address ? $company_address : "NA - Not Provided"; ?>
                    </div>
                </div>
            </div>
            <div class="details-box">
                <div class="section-title">DELIVERY DETAILS</div>
                <div class="info-row">
                    <div class="info-label">DC NO:</div>
                    <div class="info-value"><strong><?php echo "DC/" . $dc_id; ?></strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">INVOICE NO:</div>
                    <div class="info-value"><strong><?php echo $year_part . $trans_id; ?></strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">DATE:</div>
                    <div class="info-value"><?php echo $trans_date; ?></div>
                </div>
                <?php if (!empty($vehicle)) { ?>
                    <div class="info-row">
                        <div class="info-label">VEHICLE:</div>
                        <div class="info-value"><?php echo $vehicle; ?></div>
                    </div>
                <?php } ?>
                <?php if (!empty($odometer)) { ?>
                    <div class="info-row">
                        <div class="info-label">ODOMETER:</div>
                        <div class="info-value"><?php echo $odometer; ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="100">ITEM ID</th>
                    <th width="500">ITEM DESCRIPTION</th>
                    <th width="70">QTY</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_items = "SELECT d.item_id as ITEM_ID, d.despatched, i.item_name, i.item_description, i.tyre_type_name, s.parent, s.account
                  FROM delivery_challan_det d
                  JOIN item i ON i.item_id=d.item_id
                  LEFT JOIN sales_trans_det s ON s.trans_id=d.trans_id AND s.subtrans_id=d.subtrans_id AND s.item_id=d.item_id
                  WHERE d.dc_id='" . $dc_id . "'
                  AND d.active_status!='Z'
                  GROUP BY d.subtrans_id, d.item_id";
                $result_items = $conn->query($sql_items);

                $slno = 1;
                $total_qty = 0;
                while ($row_item = $result_items->fetch_assoc()) {
                    $qty = floatval($row_item["despatched"]);
                    $total_qty += $qty;
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $slno++; ?></td>
                        <td class="text-center"><?php echo $row_item["ITEM_ID"]; ?></td>
                        <td><strong><?php echo $row_item["item_name"]; ?></strong></td>
                        <td class="text-center"><?php echo $qty; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="3" class="text-right" style="font-size: 14px; font-weight: bold;">TOTAL QTY</td>
                    <td class="text-center" style="font-size: 14px; font-weight: bold;"><?php echo $total_qty; ?></td>
                </tr>
            </tbody>
        </table>

        <?php
        // Reset query pointer to generate separate tyre numbers section
        if ($result_items->num_rows > 0) {
            $result_items->data_seek(0);
            $has_tyres = false;
            $tyre_html = '';

            while ($row_item = $result_items->fetch_assoc()) {
                $qty = floatval($row_item["despatched"]);
                $parent = isset($row_item["parent"]) ? trim($row_item["parent"]) : '';
                $item_id = isset($row_item["ITEM_ID"]) ? trim($row_item["ITEM_ID"]) : '';

                // Identify if this is a "master" item (not a child or sub-component like a tube or flap)
                $is_master = ($parent === '' || $parent === '0' || $parent === $item_id);

                $item_name = isset($row_item["item_name"]) ? $row_item["item_name"] : '';
                $item_desc = isset($row_item["item_description"]) ? $row_item["item_description"] : '';
                $tyre_type = isset($row_item["tyre_type_name"]) ? $row_item["tyre_type_name"] : '';

                // Convert details to uppercase for case-insensitive checks
                $upper_name = strtoupper($item_name);
                $upper_desc = strtoupper($item_desc);
                $upper_type = strtoupper($tyre_type);

                // Check if the item is a Tyre (contains "TYRE" or "OTR", but excludes "TUBE" or "FLAP")
                $is_tyre = (strpos($upper_name, 'TYRE') !== false || strpos($upper_desc, 'TYRE') !== false || strpos($upper_type, 'TYRE') !== false || strpos($upper_type, 'OTR') !== false || strpos($upper_desc, 'OTR') !== false) &&
                    (strpos($upper_name, 'TUBE') === false && strpos($upper_desc, 'TUBE') === false && strpos($upper_type, 'TUBE') === false) &&
                    (strpos($upper_name, 'FLAP') === false && strpos($upper_desc, 'FLAP') === false && strpos($upper_type, 'FLAP') === false);

                // Only generate serial number blanks for master tyre items with a quantity > 0
                if ($qty > 0 && $is_master && $is_tyre) {
                    $has_tyres = true;
                    $tyre_html .= '<div style="margin-bottom: 30px;">';
                    $tyre_html .= '<strong>' . htmlspecialchars($row_item["item_name"]) . ' (QTY: ' . $qty . '):</strong>';
                    $tyre_html .= '<div style="display: flex; flex-direction: column; gap: 28px; margin-top: 25px; font-family: monospace; font-size: 12px;">';

                    // Generate one write-in line for each unit of quantity
                    for ($i = 1; $i <= $qty; $i++) {
                        $tyre_html .= '<div>' . $i . '. ________________________________________</div>';
                    }
                    $tyre_html .= '</div>';
                    $tyre_html .= '</div>';
                }
            }

            // If any master tyre items exist, output the Tyre/Serial Numbers section
            if ($has_tyres) {
                ?>
                <div style="padding: 15px; border-bottom: 1px solid #000; background: #fff;">
                    <div class="section-title" style="margin-bottom: 15px;">TYRE / SERIAL NUMBERS</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start;">
                        <?php echo $tyre_html; ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>

        <div class="goods-condition">
            GOODS HANDED OVER IN GOOD CONDITION
        </div>

        <div class="signature-section">
            <div class="sig-box">Prepared By / Authorised Signatory</div>
            <div class="sig-box">Customer / Receiver</div>
        </div>
    </div>

    <script src="js/jquery-3.6.0.js"></script>
    <script src="js/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            var element = document.getElementById('content-to-pdf');
            var opt = {
                margin: 0.2,
                filename: 'Delivery_Challan_<?php echo $dc_id; ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, scrollY: 0 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }

        $(document).ready(function () {
            var action = "<?php echo $_GET["action"] ?? ''; ?>";
            if (action == "DOWNLOAD") {
                downloadPDF();
            } else {
                window.print();
            }
        });
    </script>
</body>

</html>