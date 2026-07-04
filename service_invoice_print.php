<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Service Receipt</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            text-transform: uppercase;
            font-size: 13px;
            line-height: 1.4;
        }

        .receipt-container {
            max-width: 760px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 0;
            background: #fff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 850px;
        }

        .header-section {
            display: flex;
            border-bottom: 1px solid #000;
            padding: 15px 15px;
            align-items: center;
            justify-content: space-between;
        }

        .logo-area {
            width: auto;
        }

        .logo-area img {
            max-height: 80px;
            width: auto;
        }
        
        .header-address {
            text-align: right;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.4;
            color: #000;
        }

        .invoice-type {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 2px;
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
            border-bottom: 1px solid #000;
        }

        .details-box:nth-child(even) {
            border-right: none;
        }
        
        .details-box:nth-last-child(-n+2) {
            border-bottom: none;
        }

        .section-title {
            font-weight: 700;
            background: #f2f2f2;
            padding: 3px 8px;
            margin-bottom: 8px;
            display: inline-block;
            border: 1px solid #000;
            font-size: 11px;
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
            border-top: none;
            padding: 8px 5px;
            font-weight: 700;
            text-align: center;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 6px 5px;
            vertical-align: top;
        }

        .items-table th:first-child,
        .items-table td:first-child {
            border-left: none;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            border-right: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            border-top: none;
        }

        .totals-box {
            width: 320px;
        }

        .total-row {
            display: flex;
            border-bottom: 1px solid #000;
            border-left: 1px solid #000;
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
            width: 120px;
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
            margin-top: auto;
        }

        .sig-box {
            width: 30%;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 10px;
            font-weight: 600;
        }

        @page {
            margin: 15mm 15mm 3mm 15mm; /* Top Right Bottom Left (Moves left) */
            size: auto;
        }

        @media print {
            body {
                padding: 0 !important;
                margin: 0 !important;
            }

            .receipt-container {
                margin: 0 auto !important;
                border: 1px solid #000;
                width: 100% !important;
                max-width: 100% !important;
                min-height: 900px;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body id="content-to-pdf">
    <div class="no-print"
        style="background: #e52d27; padding: 10px; display: flex; justify-content: space-between; align-items: center; color: white;">
        <div style="font-weight: bold;">PRINT PREVIEW</div>
        <button type="button" onclick="window.location.href='service.php'"
            style="background: white; color: #b31217; border: none; padding: 5px 15px; border-radius: 4px; font-weight: bold; cursor: pointer;">
            ← BACK TO SERVICE LIST
        </button>
    </div>
    <?php
    function numberToWords($number)
    {
        if ($number == 0)
            return "Zero";
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
            if ($number < 20)
                $result .= $words[$number];
            else {
                $result .= $words[floor($number / 10) * 10];
                if ($number % 10)
                    $result .= ' ' . $words[$number % 10];
            }
        }
        return trim($result);
    }

    include "db_config.php";
    $trans_id = $_GET["trans_id"];
    $action = $_GET["action"] ?? "PRINT";

    /* CHECK PAYMENT TRACKING */
    $sql_check = "
        SELECT 
            t.trans_amount, 
            IFNULL(SUM(pt.amount_settled), 0) as tracked_amount
        FROM service_trans t
        LEFT JOIN pay_track pt ON t.trans_id = pt.trans_id
        WHERE t.trans_id = ?
        GROUP BY t.trans_id
    ";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $trans_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $row_check = mysqli_fetch_assoc($result_check);
    
    $trans_amount = floatval($row_check["trans_amount"] ?? 0);
    $tracked_amount = floatval($row_check["tracked_amount"] ?? 0);

    if ($trans_amount > 0 && ($trans_amount - $tracked_amount) > 0.1) {
        die("<script>alert('Payment tracking is incomplete. Please settle the full amount (including Credit) before printing.'); window.close();</script>");
    }

    $sql_m = "SELECT * FROM service_trans WHERE trans_id='$trans_id'";
    $res_m = $conn->query($sql_m);
    $row_m = $res_m->fetch_assoc();

    $shop_id = $row_m["shop"];
    $gst_type = $row_m["gst"]; // Y or N
    
    $sql_c = "SELECT * FROM company";
    $res_c = $conn->query($sql_c);
    $company = $res_c->fetch_assoc();

    $sql_s = "SELECT * FROM shop WHERE shop_id='$shop_id'";
    $res_s = $conn->query($sql_s);
    $shop = $res_s->fetch_assoc();

    // Use shop address if available, otherwise company address
    $print_address = !empty($shop["address"]) ? $shop["address"] : $company["company_address"];

    ?>

    <div class="receipt-container">
        <div class="header-section">
            <div class="logo-area" style="display: flex; align-items: center; gap: 15px;">
                <img src="img/expert_logo.png" alt="Expert Wheel Care" style="max-height: 80px;">
                <img src="img/expert_logo2.png" alt="Expert Wheel Care" style="max-height: 80px;">
            </div>
            <div class="header-address">
                <?php echo $print_address; ?><br>
                GSTIN: <?php echo $company["company_gst"]; ?>
            </div>
        </div>

        <div class="details-grid">
            <!-- Box 1: EWC Data -->
            <div class="details-box">
                <div class="section-title">COMPANY DETAILS</div>
                <div class="info-row">
                    <div class="info-label">FIRM:</div>
                    <div class="info-value"><strong>EXPERT WHEEL CARE</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">ADDRESS:</div>
                    <div class="info-value"><?php echo $print_address; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">GSTIN:</div>
                    <div class="info-value"><?php echo $company["company_gst"]; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">CONTACT:</div>
                    <div class="info-value"><?php echo $company["company_mobile"]; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">EMAIL:</div>
                    <div class="info-value" style="text-transform: lowercase;"><?php echo $company["company_email"]; ?></div>
                </div>
            </div>

            <!-- Box 2: Invoice Data -->
            <div class="details-box">
                <div class="section-title">INVOICE DETAILS</div>
                <div class="info-row">
                    <div class="info-label">INVOICE NO:</div>
                    <div class="info-value"><strong><?php echo $trans_id; ?></strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">DATE:</div>
                    <div class="info-value"><?php echo date('d-m-Y', strtotime($row_m["trans_date"])); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">TYPE:</div>
                    <div class="info-value"><strong><?php echo ($gst_type == "Y") ? "TAX INVOICE" : "SERVICE BILL"; ?></strong></div>
                </div>
            </div>
            
            <!-- Box 3: Customer Data -->
            <div class="details-box">
                <div class="section-title">CUSTOMER DETAILS</div>
                <div class="info-row">
                    <div class="info-label">COMPANY:</div>
                    <div class="info-value"><?php echo $row_m["COMPANY_NAME"]; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">OWNER:</div>
                    <div class="info-value"><?php echo $row_m["CUSTOMER_NAME"]; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">MOBILE:</div>
                    <div class="info-value"><?php echo $row_m["CUSTOMER_MOBILE"]; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">ADDRESS:</div>
                    <div class="info-value"><?php echo $row_m["CUSTOMER_ADDRESS"]; ?></div>
                </div>
                <?php if ($gst_type == "Y") { ?>
                    <div class="info-row">
                        <div class="info-label">GSTIN:</div>
                        <div class="info-value"><?php echo $row_m["CUSTOMER_GST"]; ?></div>
                    </div>
                <?php } ?>
            </div>

            <!-- Box 4: Vehicle Data -->
            <div class="details-box">
                <div class="section-title">VEHICLE DETAILS</div>
                <div class="info-row">
                    <div class="info-label">VEHICLE NO:</div>
                    <div class="info-value"><strong><?php echo $row_m["VEHICLE_NO"]; ?></strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">MAKE/MODEL:</div>
                    <div class="info-value"><?php echo $row_m["vehicle_make"] . " / " . $row_m["VEHICLE_MODEL"]; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">RUN KM:</div>
                    <div class="info-value"><?php echo $row_m["VEHICLE_ODOMETER"]; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">MECHANIC:</div>
                    <div class="info-value"><?php echo $row_m["mech"]; ?></div>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="200">SERVICE DESCRIPTION</th>
                    <th width="70">HSN</th>
                    <th width="40">QTY</th>
                    <th width="80">UNIT PRICE</th>
                    <th width="80">GROSS COST</th>
                    <th width="70">DISCOUNT</th>
                    <?php if ($gst_type == "Y") { ?>
                        <th width="80">GST (%)</th>
                    <?php } ?>
                    <th width="90">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_d = "SELECT d.*, s.service_name, s.hsn 
                      FROM service_trans_det d 
                      JOIN service s ON d.service_id = s.service_id 
                      WHERE d.trans_id = '$trans_id' AND d.active_status = 'A' AND d.qty > 0";
                $res_d = $conn->query($sql_d);
                $sl = 1;
                $total_gross = 0;
                $total_discount = 0;
                $total_tax = 0;
                $grand_total = 0;

                while ($row = $res_d->fetch_assoc()) {
                    $gross = $row["cost"] * $row["qty"];
                    $net = $gross - $row["discount"];
                    $tax = $row["tax_amount"] + $row["tax_amount_sgst"];
                    $line_total = $net + $tax;

                    $total_gross += $gross;
                    $total_discount += $row["discount"];
                    $total_tax += $tax;
                    $grand_total += $line_total;
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $sl++; ?></td>
                        <td><strong><?php echo $row["service_name"]; ?></strong></td>
                        <td class="text-center"><?php echo $row["hsn"]; ?></td>
                        <td class="text-center"><?php echo $row["qty"]; ?></td>
                        <td class="text-right"><?php echo number_format($row["cost"], 2); ?></td>
                        <td class="text-right"><?php echo number_format($gross, 2); ?></td>
                        <td class="text-right"><?php echo number_format($row["discount"], 2); ?></td>
                        <?php if ($gst_type == "Y") { ?>
                            <td class="text-right"><?php echo number_format($tax, 2); ?>
                                (<?php echo $row["tax"] + $row["tax_sgst"]; ?>%)</td>
                        <?php } ?>
                        <td class="text-right"><strong><?php echo number_format($line_total, 2); ?></strong></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="totals-section">
            <div class="totals-box">
                <?php if ($gst_type == "Y") { ?>
                    <div class="total-row">
                        <div class="total-label">TOTAL GST</div>
                        <div class="total-value"><?php echo number_format($total_tax, 2); ?></div>
                    </div>
                <?php } ?>
                <div class="total-row" style="font-size: 14px;">
                    <div class="total-label" style="background: #000; color: #fff;">GRAND TOTAL</div>
                    <div class="total-value" style="background: #eee;">
                        <?php echo number_format($grand_total, 2); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="amount-words">
            TOTAL AMOUNT IN WORDS: INR. <?php echo numberToWords($grand_total); ?> ONLY
        </div>

        <?php if($gst_type == "Y") { ?>
            <!-- GST Summary Table -->
            <table class="items-table" style="margin-top: 15px; font-size: 10px;">
                <thead>
                    <tr>
                        <th>HSN</th>
                        <th>Taxable Value</th>
                        <?php if(strncasecmp($company["company_gst"], "37", strlen("37")) === 0) { ?>
                            <th>CGST (9%)</th>
                            <th>SGST (9%)</th>
                        <?php } else { ?>
                            <th>IGST (18%)</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res_d->data_seek(0);
                    $tax_amount_sgst = 0;
                    $tax_amount_cgst = 0;
                    $total_taxable = 0;
                    
                    while ($row = $res_d->fetch_assoc()) {
                        $tax_amount_sgst += $row["tax_amount_sgst"];
                        $tax_amount_cgst += $row["tax_amount"];
                        $total_taxable += $row["total"];
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $row["hsn"]; ?></td>
                            <td class="text-right"><?php echo number_format($row["total"], 2); ?></td>
                            <td class="text-right"><?php echo number_format($row["tax_amount"], 2); ?></td>
                            <?php if(strncasecmp($company["company_gst"], "37", strlen("37")) === 0) { ?>
                                <td class="text-right"><?php echo number_format($row["tax_amount_sgst"], 2); ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td class="text-center"><strong>Total</strong></td>
                        <td class="text-right"><strong><?php echo number_format($total_taxable, 2); ?></strong></td>
                        <td class="text-right"><strong><?php echo number_format($tax_amount_cgst, 2); ?></strong></td>
                        <?php if(strncasecmp($company["company_gst"], "37", strlen("37")) === 0) { ?>
                            <td class="text-right"><strong><?php echo number_format($tax_amount_sgst, 2); ?></strong></td>
                        <?php } ?>
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

        <div class="signature-section">
            <div class="sig-box">CUSTOMER SIGNATURE</div>
            <div class="sig-box">SERVICE ADVISOR</div>
            <div class="sig-box">AUTHORISED SIGNATORY</div>
        </div>
    </div>

    <script src="js/jquery-3.6.0.js"></script>
    <script src="js/html2pdf.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            var action = "<?php echo $action; ?>";
            if (action == "DOWNLOAD") {
                var element = document.getElementById('content-to-pdf');
                var opt = {
                    margin: 0.5,
                    filename: 'Service_Invoice_<?php echo $trans_id; ?>.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, scrollY: 0 },
                    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
                };
                html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                    var pdfBlob = pdf.output('bloburl');
                    window.open(pdfBlob, '_blank');
                });
            } else {
                window.print();
            }
        });
    </script>
</body>

</html>