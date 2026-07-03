<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Receipt</title>
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
            color: #003366; /* Dark blue for address like in screenshot */
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

        .totals-section {
            display: flex;
            justify-content: flex-end;
            border-top: none;
        }

        .totals-box {
            width: 300px;
        }

        .total-row {
            display: flex;
            border: 1px solid #000;
            border-top: none;
        }

        .total-label {
            flex: 1;
            padding: 6px 10px;
            font-weight: 700;
            background: #f9f9f9;
            text-align: right;
            border-right: 1px solid #000;
        }

        .total-value {
            width: 100px;
            padding: 6px 10px;
            text-align: right;
            font-weight: 700;
        }

        .amount-words {
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
            width: 30%;
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
        <button type="button" onclick="window.location.href='sales_trans_new.php'"
            style="background: white; color: #b31217; border: none; padding: 5px 15px; border-radius: 4px; font-weight: bold; cursor: pointer;">
            &larr; BACK TO SALES LIST
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
    <?php
    function numberToWords($number)
    {
        if ($number == 0) return "Zero";
        $words = array(0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
        $levels = array(10000000 => 'Crore', 100000 => 'Lakh', 1000 => 'Thousand', 100 => 'Hundred');
        $result = '';
        foreach ($levels as $value => $name) {
            if ($number >= $value) {
                $count = floor($number / $value);
                $number = $number % $value;
                $result .= numberToWords($count) . ' ' . $name . ' ';
            }
        }
        if ($number > 0) {
            if ($number < 20) $result .= $words[$number];
            else {
                $result .= $words[floor($number / 10) * 10];
                if ($number % 10) $result .= ' ' . $words[$number % 10];
            }
        }
        return trim($result);
    }

    include "db_config.php";
    $trans_id = $_GET["trans_id"];
    $action = $_GET["action"] ?? "";
    $shop_id = $_COOKIE["shop"] ?? '';

    $sql_c = "SELECT * FROM company";
    $res_c = $conn->query($sql_c);
    $company = $res_c->fetch_assoc();

    $sql_s = "SELECT * FROM shop WHERE shop_id='$shop_id'";
    $res_s = $conn->query($sql_s);
    if($res_s->num_rows > 0) {
        $shop = $res_s->fetch_assoc();
        $print_address = !empty($shop["address"]) ? $shop["address"] : $company["company_address"];
    } else {
        $print_address = $company["company_address"];
    }

    $sql = "select trans_id, DATE_FORMAT(m.trans_date, '%d-%m-%Y') AS trans_date, details,ver, customer, trans_amount, pending, gst, tally, created_by, created_on, modified_by, modified_on, active_status, COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, customer_mobile,year_part FROM sales_trans m where m.active_status='A' and m.trans_id='" . $trans_id . "' order by 1";
    $res_m = $conn->query($sql);
    $row_m = $res_m->fetch_assoc();

    $gst_type = $row_m["gst"]; // Y or N
    $ver = $row_m["ver"];
    ?>

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
                    <?php echo $print_address; ?>
                </div>
                <?php if(!empty($company["company_gst"])) { ?>
                <div style="color: #003366; margin-top: 5px;">GSTIN : <?php echo $company["company_gst"]; ?></div>
                <?php } ?>
                <div style="margin-top: 2px;">&#9742; <?php echo $company["company_mobile"]; ?></div>
            </div>
        </div>
        <div class="invoice-type-banner"><?php echo ($gst_type == "Y") ? "TAX INVOICE" : "CASH BILL"; ?></div>

        <div class="details-grid">
            <div class="details-box">
                <div class="section-title">CUSTOMER DETAILS</div>
                <div class="info-row">
                    <div class="info-label">COMPANY:</div>
                    <div class="info-value"><?php echo $row_m["COMPANY_NAME"] ? $row_m["COMPANY_NAME"] : "NA - Not Provided"; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">OWNER:</div>
                    <div class="info-value"><?php echo $row_m["CUSTOMER_NAME"] ? $row_m["CUSTOMER_NAME"] : "NA - Not Provided"; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">MOBILE:</div>
                    <div class="info-value"><?php echo $row_m["customer_mobile"] ? $row_m["customer_mobile"] : "NA - Not Provided"; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">ADDRESS:</div>
                    <div class="info-value"><?php echo $row_m["CUSTOMER_ADDRESS"] ? $row_m["CUSTOMER_ADDRESS"] : "NA - Not Provided"; ?></div>
                </div>
                <?php if ($gst_type == "Y" && !empty($row_m["CUSTOMER_GST"])) { ?>
                    <div class="info-row">
                        <div class="info-label">GSTIN:</div>
                        <div class="info-value"><?php echo $row_m["CUSTOMER_GST"]; ?></div>
                    </div>
                <?php } ?>
            </div>
            <div class="details-box">
                <div class="section-title">INVOICE INFO</div>
                <div class="info-row">
                    <div class="info-label">INVOICE NO:</div>
                    <div class="info-value"><strong><?php echo $row_m["year_part"].$row_m["trans_id"]; ?></strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">DATE:</div>
                    <div class="info-value"><?php echo $row_m["trans_date"]; ?></div>
                </div>
            </div>
        </div>

        <?php
        $tax_percent_text = "%";
        if ($gst_type == "Y") {
            $sql_tax = "SELECT tax, tax_sgst, tax_igst FROM sales_trans_det WHERE trans_id='" . $trans_id . "' AND active_status='A' AND ver='" . $ver . "' LIMIT 1";
            $res_tax = $conn->query($sql_tax);
            if ($res_tax && $row_tax = $res_tax->fetch_assoc()) {
                if (empty($row_m["CUSTOMER_GST"]) && $row_tax["tax_igst"] > 0) {
                    $row_tax["tax"] = $row_tax["tax_igst"] / 2;
                    $row_tax["tax_sgst"] = $row_tax["tax_igst"] / 2;
                    $row_tax["tax_igst"] = 0;
                }
                if ($row_tax["tax_igst"] > 0) {
                    $tax_percent_text = floatval($row_tax["tax_igst"]) . "%";
                } else {
                    $tax_percent_text = floatval($row_tax["tax"] + $row_tax["tax_sgst"]) . "%";
                }
            }
        }
        ?>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="250">ITEM DESCRIPTION</th>
                    <th width="70">HSN</th>
                    <th width="50">QTY</th>
                    <th width="80">UNIT PRICE</th>
                    <th width="80">TAXABLE AMOUNT</th>
                    <?php if ($gst_type == "Y") { ?>
                        <th width="80">GST (<?php echo $tax_percent_text; ?>)</th>
                    <?php } ?>
                    <th width="90">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_d = "SELECT trans_id,subtrans_id,d.price, i.item_id,i.item_name,i.item_description,i.hsn,d.cost,d.qty,d.tax,d.tax_amount,d.total,d.tax_sgst,d.tax_amount_sgst,d.tax_igst, d.tax_amount_igst FROM sales_trans_det d,item i where i.item_id=d.item_id and d.trans_id='" . $trans_id . "' and d.active_status='A' and ver='" . $ver . "' and account='Y' and qty>0 and total>0 order by subtrans_id";
                $res_d = $conn->query($sql_d);
                $sl = 1;
                $total_gross = 0;
                $total_tax = 0;
                $grand_total = 0;

                while ($row = $res_d->fetch_assoc()) {
                    if (empty($row_m["CUSTOMER_GST"]) && $row["tax_igst"] > 0) {
                        $row["tax"] = $row["tax_igst"] / 2;
                        $row["tax_amount"] = $row["tax_amount_igst"] / 2;
                        $row["tax_sgst"] = $row["tax_igst"] / 2;
                        $row["tax_amount_sgst"] = $row["tax_amount_igst"] / 2;
                        $row["tax_igst"] = 0;
                        $row["tax_amount_igst"] = 0;
                    }

                    $gross = $row["price"]; // d.price is amount without tax
                    $line_total = $row["total"]; // d.total is amount with tax
                    $tax = $row["tax_amount"] + $row["tax_amount_sgst"] + $row["tax_amount_igst"];

                    $total_gross += $gross;
                    $total_tax += $tax;
                    $grand_total += $line_total;
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $sl++; ?></td>
                        <td><strong><?php echo $row["item_name"]; ?></strong></td>
                        <td class="text-center"><?php echo $row["hsn"]; ?></td>
                        <td class="text-center"><?php echo floatval($row["qty"]); ?></td>
                        <td class="text-right"><?php echo number_format($row["cost"], 2); ?></td>
                        <td class="text-right"><?php echo number_format($gross, 2); ?></td>
                        <?php if ($gst_type == "Y") { ?>
                            <td class="text-right">
                            <?php 
                                echo number_format($tax, 2); 
                            ?>
                            </td>
                        <?php } ?>
                        <td class="text-right"><strong><?php echo number_format($line_total, 2); ?></strong></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="<?php echo ($gst_type == 'Y') ? '7' : '6'; ?>" class="text-right" style="font-size: 14px; font-weight: bold;">GRAND TOTAL</td>
                    <td class="text-right" style="font-size: 14px; font-weight: bold;"><?php echo number_format($grand_total, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="amount-words">
            TOTAL AMOUNT IN WORDS: INR. <?php echo numberToWords($grand_total); ?> ONLY
        </div>

        <?php if($gst_type == "Y") { ?>
            <!-- GST Summary Table -->
            <table class="items-table" style="margin-top: 5px; font-size: 10px;">
                <thead>
                    <tr>
                        <th>HSN</th>
                        <th>Taxable Value</th>
                        <th>CGST (%)</th>
                        <th>CGST Amt</th>
                        <th>SGST (%)</th>
                        <th>SGST Amt</th>
                        <th>IGST (%)</th>
                        <th>IGST Amt</th>
                        <th>Total Tax</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res_d->data_seek(0);
                    $tax_amount_sgst = 0;
                    $tax_amount_cgst = 0;
                    $tax_amount_igst = 0;
                    $total_taxable = 0;
                    $total_all_tax = 0;
                    
                    while ($row = $res_d->fetch_assoc()) {
                        if (empty($row_m["CUSTOMER_GST"]) && $row["tax_igst"] > 0) {
                            $row["tax"] = $row["tax_igst"] / 2;
                            $row["tax_amount"] = $row["tax_amount_igst"] / 2;
                            $row["tax_sgst"] = $row["tax_igst"] / 2;
                            $row["tax_amount_sgst"] = $row["tax_amount_igst"] / 2;
                            $row["tax_igst"] = 0;
                            $row["tax_amount_igst"] = 0;
                        }

                        $tax_amount_sgst += $row["tax_amount_sgst"];
                        $tax_amount_cgst += $row["tax_amount"];
                        $tax_amount_igst += $row["tax_amount_igst"];
                        $total_taxable += $row["price"];
                        $row_total_tax = $row["tax_amount"] + $row["tax_amount_sgst"] + $row["tax_amount_igst"];
                        $total_all_tax += $row_total_tax;
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $row["hsn"]; ?></td>
                            <td class="text-right"><?php echo number_format($row["price"], 2); ?></td>
                            
                            <td class="text-center"><?php echo ($row["tax"] > 0) ? floatval($row["tax"]) . "%" : "-"; ?></td>
                            <td class="text-right"><?php echo ($row["tax_amount"] > 0) ? number_format($row["tax_amount"], 2) : "-"; ?></td>
                            
                            <td class="text-center"><?php echo ($row["tax_sgst"] > 0) ? floatval($row["tax_sgst"]) . "%" : "-"; ?></td>
                            <td class="text-right"><?php echo ($row["tax_amount_sgst"] > 0) ? number_format($row["tax_amount_sgst"], 2) : "-"; ?></td>
                            
                            <td class="text-center"><?php echo ($row["tax_igst"] > 0) ? floatval($row["tax_igst"]) . "%" : "-"; ?></td>
                            <td class="text-right"><?php echo ($row["tax_amount_igst"] > 0) ? number_format($row["tax_amount_igst"], 2) : "-"; ?></td>
                            
                            <td class="text-right"><strong><?php echo number_format($row_total_tax, 2); ?></strong></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td class="text-center"><strong>Total</strong></td>
                        <td class="text-right"><strong><?php echo number_format($total_taxable, 2); ?></strong></td>
                        <td class="text-right"></td>
                        <td class="text-right"><strong><?php echo ($tax_amount_cgst > 0) ? number_format($tax_amount_cgst, 2) : "-"; ?></strong></td>
                        <td class="text-right"></td>
                        <td class="text-right"><strong><?php echo ($tax_amount_sgst > 0) ? number_format($tax_amount_sgst, 2) : "-"; ?></strong></td>
                        <td class="text-right"></td>
                        <td class="text-right"><strong><?php echo ($tax_amount_igst > 0) ? number_format($tax_amount_igst, 2) : "-"; ?></strong></td>
                        <td class="text-right"><strong><?php echo number_format($total_all_tax, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>

            <!-- Bank Details -->
            <table class="items-table" style="margin-top: 10px; font-size: 10px;">
                <thead>
                    <tr>
                        <th class="text-left" style="padding-left: 5px;">Bank Name</th>
                        <th class="text-left" style="padding-left: 5px;">Bank A/C</th>
                        <th class="text-left" style="padding-left: 5px;">IFSC</th>
                        <th class="text-left" style="padding-left: 5px;">Branch</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left" style="padding-left: 5px;"><?php echo $company["company_bank"]; ?></td>
                        <td class="text-left" style="padding-left: 5px;"><?php echo $company["company_ac"]; ?></td>
                        <td class="text-left" style="padding-left: 5px;"><?php echo $company["company_ifsc"]; ?></td>
                        <td class="text-left" style="padding-left: 5px;"><?php echo $company["company_branch"]; ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>

        <div style="margin-top: 15px; font-size: 10px;">
            <div class="section-title">Terms & Conditions</div>
            <ol style="margin: 5px 0 10px 20px; padding: 0;">
                <li>Goods once sold cannot be taken back.</li>
                <li>Manufacturing defects will be replaced by the manufacturing company only.</li>
                <li>No dealer guarantee for manufacturing defects.</li>
                <li>All disputes are subject to Anakapalli jurisdiction only.</li>
            </ol>
        </div>

        <div class="signature-section">
            <div style="width: 30%;"></div>
            <div class="sig-box">AUTHORISED SIGNATORY</div>
        </div>
    </div>

    <script src="js/jquery-3.6.0.js"></script>
    <script src="js/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            var element = document.getElementById('content-to-pdf');
            var opt = {
                margin: 0.2,
                filename: 'Sales_Receipt_<?php echo $trans_id; ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, scrollY: 0 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }

        $(document).ready(function () {
            var action = "<?php echo $action; ?>";
            if (action == "DOWNLOAD") {
                downloadPDF();
            } else {
                window.print();
            }
        });
    </script>
</body>
</html>