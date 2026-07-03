<?php
session_start();

$user_id = $_SESSION["user_id"] ?? '';
$shop = $_SESSION["shop"] ?? '';

if ($user_id === '' || $shop === '') {
  header("Location: login.php?redirect=" . urlencode($_SERVER["REQUEST_URI"]));
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "header_include.php";
  include "db_config.php";
  ?>
  <style>
    .tooltip-wrapper {
      position: relative;
      display: inline-block;
      margin-bottom: 20px;
    }

    .amount-box {
      text-align: right !important;
      padding-right: 8px;
    }

    .qty-box {
      text-align: center;
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
      background: #333;
      color: #fff;
      padding: 6px 10px;
      border-radius: 4px;
      white-space: nowrap;
      font-size: 12px;
      opacity: 0;
      visibility: hidden;
      transition: opacity .3s;
      z-index: 1000;
    }

    .form-control {
      font-size: 12px;
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

    .invoice-summary {
      font-size: 13px;
      background: #fafafa;
    }

    .invoice-summary td:first-child {
      font-weight: 600;
    }

    .invoice-summary td {
      padding: 6px 10px;
    }

    .discount {
      display: none !important;
    }

    .purchase-table {
      width: 100% !important;
    }

    .purchase-table .form-control {
      font-size: 12px;
      padding: 2px 4px;
      height: 28px;
    }
  </style>
  <link href="multi/searchableOptionList.css" rel="stylesheet">
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
            <!--<button class="btn btn-danger" id="invoice"  onclick="get_invoice()">Get Invoice</button>&nbsp;
          <button class="btn btn-primary" id="jobcard"  onclick="get_jobcard()">Get JobCard</button>-->

            <?php
            //echo $trans_id;
            $gst = "Y";
            //echo $_COOKIE["gst"];
            //echo $gst;
            
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
            $trans_id = isset($_GET["trans_id"]) ? $conn->real_escape_string($_GET["trans_id"]) : "";
            $sql = "SELECT trans_id, trans_date, details, vendor, trans_amount, pending, gst, tally, created_by, created_on, modified_by, modified_on, active_status, COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, customer_mobile,invoice_no,discount FROM receipt_trans where trans_id='" . $trans_id . "'";
            //echo $sql;
            $result = $conn->query($sql);

            if ($row = $result->fetch_assoc()) {
              $trans_id = $row["trans_id"];
              $trans_date = $row["trans_date"];
              $details = $row["trans_id"];
              $customer = $row["vendor"];
              $trans_amount = $row["trans_amount"];
              $pending = $row["pending"];
              $gst = $row["gst"];
              $tally = $row["tally"];
              $invoice_no = $row["invoice_no"];
              $created_by = $row["created_by"];
              $created_on = $row["created_on"];
              $modified_by = $row["modified_by"];
              $modified_on = $row["modified_on"];
              $active_status = $row["active_status"];

              // $NO_OF_WHEELS=$row["NO_OF_WHEELS"]; 
              $COMPANY_NAME = $row["COMPANY_NAME"];
              $CUSTOMER_NAME = $row["CUSTOMER_NAME"];
              $CUSTOMER_ADDRESS = $row["CUSTOMER_ADDRESS"];
              $CUSTOMER_GST = $row["CUSTOMER_GST"];
              $CUSTOMER_MOBILE = $row["customer_mobile"];
              $active_status = $row["active_status"];
              $grand_discount = $row["discount"];
              if ($active_status == "A") {
                $final_disabler = "disabled";
              }
              //echo "===";
// echo $trans_date."====". $customer."====". $CUSTOMER_NAME;
            

            }

            $gst = "Y";
            ?>
            <div class="col-md-12">
              <div class="alert alert-warning" style="text-align:center;font-weight:bold"><b>PURCHASE
                </b><BR>GST:<?PHP echo $gst; ?></div><br><br>

              <span class="alert alert-info">Vendor Details</span>
              <table class="table">
                <tr>
                  <td>Date<input type="text" id="datepicker" class="form-control" onchange="saveHeader()"
                      value="<?php echo !empty($trans_date) ? date('d-m-Y', strtotime($trans_date)) : ''; ?>"><br>
                    Invoice No <input class="form-control" name="invoice_no" id="invoice_no"
                      value="<?php echo $invoice_no; ?>" onblur="saveHeader()">

                  </td>
                  <td>Trans No <input class="form-control" readonly name="trans_id" id="trans_id"
                      value="<?php echo $trans_id; ?>"><br>
                    Search vendor<br><select class="form-control" name="customer_search" id="customer_search"
                      multiple="multiple" style="max-width:300px!important">
                      <option value=""></option>

                      <?php $sql = "SELECT vendor_id,company_name,owner_name,owner_mobile FROM vendor ORDER BY UPPER(company_name)";
                      $result = $conn->query($sql);
                      while ($row1 = $result->fetch_assoc()) { ?>
                        <option
                          value="<?php echo $row1["company_name"] ?>~<?php echo $row1["owner_name"] ?>~<?php echo $row1["owner_mobile"] ?>~<?php echo $row1["vendor_id"] ?>">
                          <?php echo $row1["company_name"] ?>,<?php echo $row1["owner_name"] ?>
                        </option>
                      <?php } ?>
                    </select></td>

                  <!--<a href="module.php?param=customer" class="btn btn-success" target="_blank">Add Customer, If not exists</a>
<a href="module.php?param=vehicle" class="btn btn-danger" target="_blank">Add Vehicle, If not exists</a>-->
                  <input class="form-control" name="customer" id="customer" value="<?php echo $customer; ?>"
                    type="hidden">
                  </td>
                </tr>
              </table>
            </div>
            <div class="col-md-12">
              <span class="alert alert-success">Vendor Details</span>
              <table class="table">
                <tr>
                  <td>Company Name<input class="form-control" name="company_name" id="company_name"
                      value="<?php echo $COMPANY_NAME; ?>">
                    <br>Mobile <input class="form-control" name="customer_mobile" id="customer_mobile"
                      value="<?php echo $CUSTOMER_MOBILE; ?>">
                  </td>

                  <?php $visibility = ""; ?>
                  <td style="display:<?php echo $visibility; ?>">GST <input class="form-control" name="customer_gst"
                      id="customer_gst" value="<?php echo $CUSTOMER_GST; ?>" list="gst_list" onfocus="get_gst()"
                      autocomplete="off" onblur="set_gst();saveHeader();">
                    <datalist id="gst_list">
                    </datalist><br>Address <input class="form-control" name="customer_address" id="customer_address"
                      value="<?php echo $CUSTOMER_ADDRESS; ?>" list="address_list" onfocus="get_address()"
                      autocomplete="off" onblur="saveHeader()">
                    <datalist id="address_list">
                    </datalist>
                    <input class="form-control" name="customer" id="customer" value="<?php echo $customer; ?>"
                      type="hidden">
                  </td>
                </tr>
              </table>
            </div>


            <div class="col-md-12">
              <div class="card shadow mb-4">
                <div class="card-header py-2" style="background-color: #2c3e50; color: white;">
                  <h6 class="m-0 font-weight-bold"><i class="fas fa-search-plus mr-2"></i> Add Items
                    to Receipt</h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <label style="font-weight:bold; color:#e74c3c;"><i class="fas fa-search mr-1"></i> Search
                        Individual Item</label>
                      <select class="form-control" name="item_search" id="item_search" multiple="multiple" <?php echo $final_disabler; ?> style="max-width:700px!important">
                        <?php $sql = "SELECT item_id,upper(item_name) item_name,item_description,cost,ifnull(purchase_tax_cgst,'9') purchase_tax_cgst,ifnull(purchase_tax_igst,'9') purchase_tax_igst,hsn FROM item order by upper(item_name)";
                        $result = $conn->query($sql);
                        while ($row1 = $result->fetch_assoc()) { 
                            $item_name = str_replace("~", "-", $row1["item_name"] ?? '');
                            $item_desc = str_replace("~", "-", $row1["item_description"] ?? '');
                            $hsn = str_replace("~", "-", $row1["hsn"] ?? '');
                        ?>
                          <option
                            value="<?php echo htmlspecialchars($item_name, ENT_QUOTES) ?>~<?php echo htmlspecialchars($item_desc, ENT_QUOTES) ?>~<?php echo $row1["cost"] ?>~<?php echo $row1["purchase_tax_cgst"] ?>~<?php echo $row1["purchase_tax_igst"] ?>~<?php echo $row1["item_id"] ?>~<?php echo htmlspecialchars($hsn, ENT_QUOTES) ?>">
                            <?php echo htmlspecialchars($item_name, ENT_QUOTES) ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <!--  this is the place where one need to tinker with for getting a better table -->
              <div class="table-responsive">
                <table class="table table-striped purchase-table">
                  <thead>
                    <tr>
                      <td>Item</td>
                      <td>HSN</td>
                      <td>Qty</td>
                      <td>Total<br>Cost</td>

                      <td>Unit Cost</td>
                      <td style="display:none">%CGST</td>
                      <td style="display:none">%SGST</td>
                      <td style="display:none">%IGST</td>
                      <td>CGST</td>
                      <td>SGST</td>
                      <td>IGST</td>
                      <td>Discount</td>
                      <td>Total</td>
                      <td>Extra<br>Charges</td>
                      <!--<td>Remarks</td>-->
                      <td>Taxable Amount</td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql_det = "SELECT t.subtrans_id,t.item_id,t.cost,price,ifnull(t.discount,'0') discount,t.qty,t.total, t.tax,t.tax_amount,t.tax_sgst,t.total,t.remarks,t.tax_amount_sgst,t.tax_igst,t.tax_amount_igst,i.item_name,t.price,t.roundoff,hsn FROM receipt_trans_det t,item i where i.item_id=t.item_id and t.trans_id='" . $trans_id . "' and t.trans_id!='' and t.active_status='A' order by t.subtrans_id";
                    $result_det = $conn->query($sql_det);
                    //echo $sql_det;
                    $display = "none";
                    $readonly = "";
                    $slno = 0;
                    while ($row_det = $result_det->fetch_assoc()) {
                      $det_price = $row_det["price"];
                      $det_roundoff = $row_det["roundoff"];
                      $det_item_name = $row_det["item_name"];
                      $det_hsn = $row_det["hsn"];

                      if (substr($CUSTOMER_GST, 0, strlen("37")) === "37") {
                        $tax_pc = $row_det["tax"];
                        $tax_pc_sgst = $row_det["tax_sgst"];
                        $det_tax = $row_det["tax_amount"];
                        $det_tax_sgst = $row_det["tax_amount_sgst"];
                      } else {
                        $tax_pc_igst = $tax_pc_igst;
                        $det_tax_igst = $row_det["tax_amount_igst"];

                      }
                      $det_qty = $row_det["qty"];
                      $det_discount = $row_det["discount"];
                      $det_remarks = $row_det["remarks"];
                      $det_cost = $row_det["cost"];
                      $det_total = $row_det["total"];
                      $det_subtrans_id = $row_det["subtrans_id"];
                      $display = "";
                      $grand_total += $det_total;

                      $discount_amount += $row_det["discount"];
                      $roundoff_amount += $row_det["roundoff"];
                      // COMMENTED LINES FOR TESTING THE FUNCTIONALITY OF ROW TOTALS 26-04-2026
                      $price_amount += $row_det["price"] - $row_det["discount"] + $row_det["roundoff"];
                      $cgst_amount += $row_det["tax_amount"];
                      $sgst_amount += $row_det["tax_amount_sgst"];
                      $igst_amount += $row_det["tax_amount_igst"];
                      $readonly = "readonly";



                      ?>
                      <tr class="purchase_rows" id="row_<?php echo $slno; ?>" style="display:<?php echo $display; ?>"
                        onfocusout="add_service_det('<?php echo $slno; ?>')">
                        <td><?php echo $det_item_name; ?>
                          <input type="hidden" id="item_id_<?php echo $slno; ?>"
                            value="<?php echo $row_det["item_id"]; ?>">
                          <input type="hidden" id="subtrans_id_<?php echo $slno; ?>"
                            value="<?php echo $det_subtrans_id; ?>">
                        </td>
                        <td><?php echo $det_hsn; ?></td>

                        <td>
                          <div class="tooltip-wrapper"><input class="form-control tooltip-input" <?php echo $final_disabler; ?> id="qty_<?php echo $slno; ?>" onblur="calculate('<?php echo $slno; ?>')"
                              value="<?php echo $det_qty; ?>">
                            <div class="tooltip-text">Once saved cannot be edited. If required,
                              delete and reenter.</div>
                          </div>
                        </td>
                        <td><input class="form-control" <?php echo $final_disabler; ?> id="price_<?php echo $slno; ?>"
                            value="<?php echo $det_price; ?>" onblur="calculate('<?php echo $slno; ?>')"></td>

                        <td><input class="form-control" <?php echo $final_disabler; ?> readonly
                            value="<?php echo $row_det['cost']; ?>" id="cost_<?php echo $slno; ?>"
                            value="<?php echo $det_cost; ?>" onblur="calculate('<?php echo $slno; ?>')"></td>
                        <td style="display:none"><input style="text-align:right" <?php echo $final_disabler; ?>
                            class="form-control" readonly value="<?php echo $tax_pc; ?>" id="gst_<?php echo $slno; ?>">
                        </td>
                        <td style="display:none"><input style="text-align:right" <?php echo $final_disabler; ?>
                            class="form-control" readonly value="<?php echo $tax_pc_sgst; ?>"
                            id="sgst_<?php echo $slno; ?>">
                        </td>
                        <td style="display:none"><input style="text-align:right" <?php echo $final_disabler; ?>
                            class="form-control" readonly value="<?php echo $tax_pc_igst; ?>"
                            id="igst_<?php echo $slno; ?>">
                        </td>

                        <td style="display:<?php echo $visibility; ?>"><input class="form-control" readonly
                            style="text-align:right" id="tax_gst_<?php echo $slno; ?>" value="<?php echo $det_tax; ?>">
                        </td>
                        <td style="display:<?php echo $visibility; ?>"><input class="form-control"
                            style="text-align:right" readonly id="tax_sgst_<?php echo $slno; ?>"
                            value="<?php echo $det_tax_sgst; ?>"></td>
                        <td style="display:<?php echo $visibility; ?>"><input class="form-control"
                            style="text-align:right" readonly id="tax_igst_<?php echo $slno; ?>"
                            value="<?php echo $det_tax_igst; ?>"></td>
                        <td><input class="form-control" <?php echo $final_disabler; ?> id="discount_<?php echo $slno; ?>"
                            value="<?php echo $det_discount; ?>" onblur="calculate('<?php echo $slno; ?>')"></td>
                        <td><input class="form-control" <?php echo $final_disabler; ?> readonly
                            id="total_<?php echo $slno; ?>" value="<?php echo $det_total; ?>">
                          <input class="form-control" <?php echo $final_disabler; ?> type="hidden"
                            id="id_<?php echo $slno; ?>" value="<?php echo $det_subtrans_id; ?>">
                        </td>
                        <td><input class="form-control" <?php echo $final_disabler; ?> id="roundoff_<?php echo $slno; ?>"
                            value="<?php echo $det_roundoff; ?>" onblur="calculate('<?php echo $slno; ?>')"></td>

                        <!--<td><input class="form-control" <?php echo $final_disabler; ?>   id="remarks_<?php echo $slno; ?>" value="<?php echo $det_remarks; ?>" onblur="calculate('<?php echo $slno; ?>')"></td>-->
                        <td><input class="form-control" <?php echo $final_disabler; ?>
                            id="show_total_<?php echo $slno; ?>"
                            value="<?php echo $det_price - $det_discount + $det_roundoff; ?>"
                            onblur="calculate('<?php echo $slno; ?>')"></td>
                        <td>
                          <button class="btn btn-xs btn-danger" <?php echo $final_disabler; ?>
                            onclick="del_service_det('<?php echo $slno; ?>')" id="del_<?php echo $slno; ?>">X</button>
                        </td>
                      </tr>


                      <?php $slno++;
                    } ?>
                    <?php $max_rows = 10; ?>

                    <!-- Blank Rows creation Starts -->
                    <?php while ($slno < $max_rows) { ?>
                      <tr class="purchase_rows" id="row_<?php echo $slno; ?>" style="display:none"
                        onfocusout="add_service_det('<?php echo $slno; ?>')">

                        <td>
                          <span id="item_name_<?php echo $slno; ?>"></span>
                          <input type="hidden" id="item_id_<?php echo $slno; ?>">
                          <input type="hidden" id="subtrans_id_<?php echo $slno; ?>">
                        </td>

                        <td><span id="hsn_<?php echo $slno; ?>"></span></td>

                        <td>
                          <div class="tooltip-wrapper">
                            <input class="form-control tooltip-input qty-box" id="qty_<?php echo $slno; ?>"
                              onblur="calculate('<?php echo $slno; ?>')">
                          </div>
                        </td>

                        <td><input class="form-control amount-box" id="price_<?php echo $slno; ?>"
                            onblur="calculate('<?php echo $slno; ?>')"></td>

                        <td><input class="form-control amount-box" readonly id="cost_<?php echo $slno; ?>"></td>

                        <td style="display:none"><input class="form-control amount-box" readonly
                            id="gst_<?php echo $slno; ?>"></td>

                        <td style="display:none"><input class="form-control amount-box" readonly
                            id="sgst_<?php echo $slno; ?>"></td>

                        <td style="display:none"><input class="form-control amount-box" readonly
                            id="igst_<?php echo $slno; ?>"></td>

                        <td style="display:<?php echo $visibility; ?>"><input class="form-control amount-box" readonly
                            id="tax_gst_<?php echo $slno; ?>"></td>

                        <td style="display:<?php echo $visibility; ?>"><input class="form-control amount-box" readonly
                            id="tax_sgst_<?php echo $slno; ?>"></td>

                        <td style="display:<?php echo $visibility; ?>"><input class="form-control amount-box" readonly
                            id="tax_igst_<?php echo $slno; ?>"></td>

                        <td><input class="form-control amount-box" id="discount_<?php echo $slno; ?>"
                            onblur="calculate('<?php echo $slno; ?>')"></td>

                        <td>
                          <input class="form-control amount-box" readonly id="total_<?php echo $slno; ?>">
                          <input type="hidden" id="id_<?php echo $slno; ?>">
                        </td>

                        <td><input class="form-control amount-box" id="roundoff_<?php echo $slno; ?>"
                            onblur="calculate('<?php echo $slno; ?>')"></td>

                        <td><input class="form-control amount-box" readonly id="show_total_<?php echo $slno; ?>"></td>

                        <td>
                          <button class="btn btn-xs btn-danger" onclick="del_service_det('<?php echo $slno; ?>')"
                            id="del_<?php echo $slno; ?>">X</button>
                        </td>

                      </tr>
                      <?php $slno++;
                    } ?>
                    <!--</tbody>
</table>-->
                    <!-- Blank row creation ends Purchase table also ends here -->

                    <!-- Summary Row creation starts  -->
                    <tr>
                      <TD></TD>
                      <TD></TD>
                      <TD></TD>
                      <TD><input id="price_amount" class="form-control" readonly value="<?php echo $price_amount; ?>">
                      </TD>

                      <TD></TD>

                      <TD><input id="cgst_amount" class="form-control" readonly value="<?php echo $cgst_amount; ?>">
                      </TD>
                      <TD><input id="sgst_amount" class="form-control" readonly value="<?php echo $sgst_amount; ?>">
                      </TD>

                      <TD><input id="igst_amount" class="form-control" readonly value="<?php echo $igst_amount; ?>">
                      </TD>
                      <TD><input id="discount_amount" class="form-control" readonly
                          value="<?php echo $discount_amount; ?>"></TD>
                      <!--  <TD><input id="discount_amount" class="form-control" readonly value="999"></TD> -->
                      <TD><input id="trans_amount" class="form-control" readonly value="<?php echo $grand_total; ?>">
                      </TD>

                      <TD><input id="roundoff_amount" class="form-control" readonly
                          value="<?php echo $roundoff_amount; ?>"></TD>
                      <!--   <TD><input id="roundoff_amount" class="form-control" readonly value="999"></TD> -->

                      <td></td>
                      <TD>
                        <?php //echo ($grand_total - floor($grand_total)) > 0.5 ? ceil($grand_total) : $grand_total; ?>
                      </td>
                      <TD></TD>


                    </tr>

                </table>
              </div>
              <!-- Summary Row creation ends  -->

              <!-- Bottom Summary rows creation starts here -->
              <div class="row mt-3">
                <div class="col-md-8"></div>

                <div class="col-md-4">
                  <table class="table table-bordered table-sm invoice-summary">
                    <tr>
                      <!--    <td>Subtotal</td>
    <td class="text-right" id="sum_subtotal"><?php echo number_format($price_amount, 2); ?></td>
    </tr>

    <tr>
    <td>Merchandise Value</td>
    <td class="text-right" id="sum_merchandise"><?php echo number_format($price_amount, 2); ?></td>
    </tr>  -->

                    <tr>
                      <td>Taxable Value</td>
                      <td class="text-right" id="sum_taxable">
                        <?php echo number_format($price_amount, 2); ?>
                      </td>
                    </tr>

                    <tr>
                      <td>CGST</td>
                      <td class="text-right" id="sum_cgst">
                        <?php echo number_format($cgst_amount, 2); ?>
                      </td>
                    </tr>

                    <tr>
                      <td>SGST</td>
                      <td class="text-right" id="sum_sgst">
                        <?php echo number_format($sgst_amount, 2); ?>
                      </td>
                    </tr>
                    <tr>
                      <td>IGST</td>
                      <td class="text-right" id="sum_igst">
                        <?php echo number_format($igst_amount, 2); ?>
                      </td>
                    </tr>

                    <tr>
                      <td><b>Total Invoice Value</b></td>
                      <td class="text-right" id="sum_total">
                        <?php echo number_format($grand_total, 2); ?>
                      </td>
                    </tr>

                    <tr>
                      <td><b>Total Invoice Value (R/OFF)</b></td>
                      <td class="text-right" id="sum_round">
                        <?php
                        $rounded = round($grand_total);
                        echo number_format($rounded, 2);
                        ?>
                      </td>
                    </tr>

                  </table>
                </div>
              </div>
              <!--  Bottom summary ends here -->
              <?php if ($active_status == "A") { ?>
                <hr>
                <center><button class="btn btn-success" disabled>Saved already</button>&nbsp;<button
                    class="btn btn-primary" id="jobcard" onclick="unlock()">Edit</button>
                </center>
                <hr>

              <?php } else { ?>
                <hr>
                <center><button class="btn btn-success" onclick="save_dummy()">Save</button></center>
                <hr>
              <?php } ?>

            </div>
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

  <!-- Scroll to Top Button-->
  <?php include "modals.php"; ?>



  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
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
                    <option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['paytype_name']; ?>" <?php echo $selected; ?>><?php echo $row['paytype_name']; ?></option>
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
                    <option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['account_id']; ?>" <?php echo $selected; ?>><?php echo $row['account_name']; ?></option>
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
          <button type="button" class="btn btn-success" id="save_pay_btn" onclick="save_pay1()">Save</button>
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


                <input type='HIDDEN' name="trans_id" id="trans_id">
                <input type='HIDDEN' name="trans_id" id="subtrans_id">
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

  <?php include "footer_include.php"; ?>
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/jquery-ui.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="js/demo/datatables-demo.js"></script>
  <script src="multi/searchableOptionList.js"></script>

  <script type="text/javascript">
    $(function () {

      $('#customer_search').searchableOptionList({
        maxHeight: '350px',
        showSelectAll: true,

        onChange: function () {
          select_customer();
        }
      });
      /*$('#item_search').searchableOptionList({
          maxHeight: '350px',
          showSelectAll: true,
           onChange: function () {
             console.log("ekhane elem");
          
          setTimeout(function () {
      select_item();
    }, 2000);
        }
         // alert("jaimaa");
      });*/
      $('#item_search').searchableOptionList({
        maxHeight: '750px',
        showSelectAll: true,

        onChange: function () {
          $('.sol-container').removeClass('sol-active');
          setTimeout(function () {
            select_item();
          }, 200);
        }
      });

      // 🔥 ADDed on 27-04-20206 by PcS



    });

  </script>
  <script>
    var del_mode = 0;
    var addModal = new bootstrap.Modal(document.getElementById('addModal'));
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    /*document.getElementById("customer_search").addEventListener("input", function () {
      console.log(document.getElementById("customer_search").value);
      let parts= document.getElementById("customer_search").value.split("~");
      $("#customer").val(parts[3]);
       $("#company_name").val(parts[0]);
       $("#customer_name").val(parts[1]);
         $("#customer_mobile").val(parts[2]);
        get_gst();
       get_address();
    });*/
    document.getElementById("item_search").addEventListener("blur", function () {
      console.log(document.getElementById("customer_search").value);
      let parts = document.getElementById("item_search").value.split("~");
      document.getElementById("item_search").value = "";
      console.log(parts[5]);
      //load_service(parts[5],parts[4],parts[5],parts[0]);
    });

    $(function () {
      $("#datepicker").datepicker({
        dateFormat: "dd-mm-yy"  // Set format to yyyy-mm-dd
      }).datepicker("setDate", new Date());;
      $("#edit_datepicker").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
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
      //alert(parts[0]+"++"+parts[1]+"===="+parts[2]);
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
      //alert(parts[0]+"++"+parts[1]+"===="+parts[2]);
      show_edit_amount();
    }
    function populate_edit_vehicle_det() {
      let parts = (document.getElementById("edit_vehicle").value).split("~");
      $("#edit_vehicle_id").val(parts[0]);

    }
    $(function () {
      $("#datepicker").datepicker({
        dateFormat: "dd-mm-yy"  // Set format to yyyy-mm-dd
      });
      $("#edit_datepicker").datepicker({
        dateFormat: "dd-mm-yy"  // Set format to yyyy-mm-dd
      });
    });
    $('#add_opener').on('click', function () {
      //alert('Button was clicked!');
      // You can run any function here


      addModal.show();
  /*  let module="<?php echo $module; ?>";
      $.post("generic_form.php", { module: module })
        .done(function (data) {
          $("#add_block").html($.trim(data));
          addModal.show();


        });*/
    });
    function add_service_det(service_id) {

      let service = service_id;

      let price_selector = "#price_" + service;
      let discount_selector = "#discount_" + service;
      let roundoff_selector = "#roundoff_" + service;
      let qty_selector = "#qty_" + service;
      let cost_selector = "#cost_" + service;
      let gst_selector = "#gst_" + service;
      let sgst_selector = "#sgst_" + service;
      let igst_selector = "#igst_" + service;
      let tax_gst_selector = "#tax_gst_" + service;
      let tax_sgst_selector = "#tax_sgst_" + service;
      let tax_igst_selector = "#tax_igst_" + service;
      let total_selector = "#total_" + service;
      let remarks_selector = "#remarks_" + service;
      let item_id_selector = "#item_id_" + service;
      let subtrans_id_selector = "#subtrans_id_" + service;

      let price = $(price_selector).val();
      let roundoff = $(roundoff_selector).val();
      let cost = $(cost_selector).val();
      let tax_pc = $(gst_selector).val();
      let tax_pc_sgst = $(sgst_selector).val();
      let tax_pc_igst = $(igst_selector).val();
      let total = $(total_selector).val();
      let tax = $(tax_gst_selector).val();
      let tax_sgst = $(tax_sgst_selector).val();
      let tax_igst = $(tax_igst_selector).val()
      let qty = $(qty_selector).val();
      let discount = $(discount_selector).val();
      let remarks = $(remarks_selector).val();
      let item_id = $(item_id_selector).val();
      let subtrans_id = $(subtrans_id_selector).val();
      if (del_mode == 0) {
        $(qty_selector).prop('disabled', true);
        $(price_selector).prop('disabled', true);
        $(discount_selector).prop('disabled', true);
        $(roundoff_selector).prop('disabled', true);
        $(remarks_selector).prop('disabled', true);
        $.ajax({
          url: "add_receipt_det.php",
          type: "POST",
          data: {

            cost: isFinite(cost) ? cost : 0,
            tax_pc: tax_pc,
            tax_pc_sgst: tax_pc_sgst,
            tax_pc_igst: tax_pc_igst,
            total: total,
            tax: tax,
            tax_sgst: tax_sgst,
            tax_igst: tax_igst,
            qty: qty,
            item_id: item_id,
            price: price,
            subtrans_id: subtrans_id,
            trans_id: $("#trans_id").val(),

            discount: discount,
            roundoff: roundoff,
            remarks: remarks


          },
          dataType: "json",
          success: function (res) {
            let qty_selector = "#qty_" + service;
            let discount_selector = "#discount_" + service;
            let price_selector = "#price_" + service;
            let roundoff_selector = "#roundoff_" + service;
            let remarks_selector = "#remarks_" + service;

            if (res.status === "success") {
              let data = res.data;
              let id_selector = "#subtrans_id_" + service;
              let del_selector = "#del_" + service;
              $(id_selector).val(data.subtrans_id);

              let amount_paid = parseFloat(data.grand_total);
              $("#trans_amount").val(amount_paid);
              $("#cgst_amount").val(parseFloat(data.tax));
              $("#sgst_amount").val(parseFloat(data.sgst));
              $("#igst_amount").val(parseFloat(data.igst));
              $("#price_amount").val(parseFloat(data.price));
              $("#discount_amount").val(parseFloat(data.discount));
              $("#roundoff_amount").val(parseFloat(data.roundoff));
              update_summary();

              const paid_amount = document.getElementById("paid_amount");
              if (paid_amount) {
                paid_amount.setAttribute("min", "0");
                paid_amount.setAttribute("max", amount_paid);
              }
              $(del_selector).prop('disabled', false);
            } else {
              alert(res.message);
              $(discount_selector).val("0");
              $(qty_selector).val("0");
              $("#row_" + service).hide();
            }
          },
          error: function (xhr) {
            alert("Server error occurred while saving the row.");
          },
          complete: function () {
            $(qty_selector).prop('disabled', false);
            $(price_selector).prop('disabled', false);
            $(discount_selector).prop('disabled', false);
            $(roundoff_selector).prop('disabled', false);
            $(remarks_selector).prop('disabled', false);
          }
        });
      }

    }
    function update_summary() {

      let subtotal = (parseFloat($("#price_amount").val()) || 0) - (parseFloat($("#discount_amount").val()) || 0) + (parseFloat($("#roundoff_amount").val()) || 0);
      let cgst = parseFloat($("#cgst_amount").val()) || 0;
      let sgst = parseFloat($("#sgst_amount").val()) || 0;
      let igst = parseFloat($("#igst_amount").val()) || 0;

      let total = subtotal + cgst + sgst + igst;

      // $("#sum_subtotal").text(subtotal.toFixed(2));
      //  $("#sum_merchandise").text(subtotal.toFixed(2));
      $("#sum_taxable").text(subtotal.toFixed(2));

      $("#sum_cgst").text(cgst.toFixed(2));
      $("#sum_sgst").text(sgst.toFixed(2));
      $("#sum_igst").text(igst.toFixed(2));

      $("#sum_total").text(total.toFixed(2));
      $("#sum_round").text(Math.round(total).toFixed(2));
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
          //alert($.trim(data));
          console.log(data);
          location.reload();
        });


    });


/*$(document).ready(function() {
   let module="<?php echo $module; ?>"; 
    $.post("generic_fetch.php", { module: module })
      .done(function (data) {
        let jsonData = (data);
        if (jsonData.length === 0) {
          $('#myTable').html("<p>No data available</p>");
          return;
        }

        // 1. Generate columns dynamically based on keys of first object
        const columns = Object.keys(jsonData[0]).map(key => ({
          title: key,   // column header
          data: key     // property name to pull from
        }));

        // 2. Initialize DataTable with dynamic columns and data
        $('#myTable').DataTable({
          data: jsonData,
          columns: columns
        });
      });
});*/
    function delete_it(trans_id, subtrans_id) {
      var r = confirm("Are you sure that you want to delete the transaction");
      if (r) {
        $.post("delete_receipt_trans_det.php",
          {
            trans_id: trans_id,
            subtrans_id: subtrans_id
          },
          function (data, status) {
            //alert($.trim(data));
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
      $.post("get_gst_vendor.php",
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
            //document.getElementById("customer_gst").value=part;
          });
          // Assign the LAST value to the input field
          if (parts.length > 0) {
            document.getElementById("customer_gst").value = parts[0];
            console.log(parts[0]);
            set_gst();
          }
        });


    }

    function get_address() {
      let customer = $("#customer").val();
      $.post("get_address_vendor.php",
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
            //document.getElementById("customer_address").value=part;
          });
          // Assign the LAST value to the input field
          if (parts.length > 0) {
            document.getElementById("customer_address").value = parts[0];
          }
        });


    }

    function set_gst() {
      let str = document.getElementById("customer_gst").value;
      //alert(str);
      if (str.startsWith("37")) {
        $("#cgst_igst").html("CGST");
        $("#cgst_igst_amount").html("CGST");
        // alert("CGST");

        document.querySelectorAll('.hide_sgst').forEach(el => {
          el.style.display = '';
        });

      } else {
        $("#cgst_igst").html("IGST");
        $("#cgst_igst_amount").html("IGST");

        document.querySelectorAll('.hide_sgst').forEach(el => {
          el.style.display = 'none';
        });
        //alert("IGST"); 
      }
    }
    function edit_it(trans_id, subtrans_id, service, vehicle, qty) {

      //alert("====");
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

    function load_service(service, price, gst, igst, name, hsn) {

      let next_row = $(".purchase_rows:hidden").first();
      if (next_row.length === 0) {
        alert("Max items (10) reached for this transaction. Please finalize this and start a new one if needed.");
        return;
      }
      var slno = next_row.attr('id').split('_')[1];
      let trans_id = document.getElementById("trans_id").value;
      if ($("#customer").val() == "" || $("#customer").val() == null) {
        alert("Please enter customer Details");
        return false;
      }
      let str = document.getElementById("customer_gst").value;

      //alert(str);
      let gst_selector = "gst_" + slno;
      let sgst_selector = "sgst_" + slno
      let igst_selector = "igst_" + slno;
      let price_selector = "cost_" + slno;
      let item_selector = "item_id_" + slno;
      let item_name_selector = "item_name_" + slno;
      let hsn_selector = "hsn_" + slno;
      document.getElementById(price_selector).value = parseInt(price);
      document.getElementById(item_selector).value = service;
      document.getElementById(hsn_selector).innerHTML = hsn;
      document.getElementById(item_name_selector).innerHTML = name;

      if (str.startsWith("37")) {
        document.getElementById(sgst_selector).value = parseInt(gst);
        document.getElementById(gst_selector).value = parseInt(gst);
      } else {
        document.getElementById(igst_selector).value = parseInt(gst) + parseInt(gst);
        //document.getElementById(sgst_selector).value=0;
      }
      if (trans_id == "" || trans_id == null) {
        $.post("add_receipt.php",
          {

            customer_mobile: $("#customer_mobile").val(),
            company_name: $("#company_name").val(),
            customer_name: $("#customer_name").val(),
            customer_address: $("#customer_address").val(),
            customer_gst: $("#customer_gst").val(),
            customer: $("#customer").val(),
            trans_date: $("#datepicker").val(),
            gst: "<?php echo $_COOKIE["gst"]; ?>",
            discount: $("#discount").val(),
            invoice_no: $("#invoice_no").val()
          },
          function (data, status) {
            let selector = "row_" + slno;
            document.getElementById(selector).style.display = "";
            if (typeof data === "object") {
              if (data.status === "error") {
                alert(data.message);
                location.reload();
              } else {
                document.getElementById("trans_id").value = data.trans_id;
              }
            } else {
              if ($.trim(data).indexOf("Error") != -1) {
                alert($.trim(data));
                location.reload();
              } else {
                document.getElementById("trans_id").value = $.trim(data);
              }
            }
          });
      } else {
        let selector = "row_" + slno;
        document.getElementById(selector).style.display = "";
      }


    }
    function calculate(row_no) {
      console.log(row_no);
      let price_selector = "price_" + row_no;
      let cost_selector = "cost_" + row_no;
      let discount_selector = "discount_" + row_no;
      let roundoff_selector = "roundoff_" + row_no;
      let qty_selector = "qty_" + row_no;
      let discount = (document.getElementById(discount_selector).value == "" || document.getElementById(discount_selector).value == null) ? 0 : document.getElementById(discount_selector).value;
      let roundoff = (document.getElementById(roundoff_selector).value == "" || document.getElementById(roundoff_selector).value == null) ? 0 : document.getElementById(roundoff_selector).value;

      console.log(price_selector);
      console.log(document.getElementById(price_selector).value);
      let price = (document.getElementById(price_selector).value == "") ? 0 : document.getElementById(price_selector).value;
      let cost = (document.getElementById(cost_selector).value == "") ? 0 : document.getElementById(cost_selector).value;

      let qty = (document.getElementById(qty_selector).value == "") ? 0 : document.getElementById(qty_selector).value;
      /*if(row_no=="1000002"){
      document.getElementById(qty_selector).value=$("#no_of_wheels").val();}*/
      if (price > 0 && document.getElementById(price_selector).readOnly == false) {
        (price) = parseFloat(price);
        document.getElementById(price_selector).value = price;
        document.getElementById(cost_selector).value = (parseFloat(price)) / parseFloat(qty);
      }
      else
        document.getElementById(price_selector).value = ((parseFloat(cost)) * parseFloat(qty)).toFixed(2);

      document.getElementById(price_selector).value = parseFloat(document.getElementById(price_selector).value);
      let show_total_selector = "show_total_" + row_no;

      document.getElementById(show_total_selector).value = (parseFloat(document.getElementById(price_selector).value) || 0) - (parseFloat(document.getElementById(discount_selector).value) || 0) + (parseFloat(document.getElementById(roundoff_selector).value) || 0);



      /*	if(row_no=="1000002"){	
      if(parseFloat(document.getElementById(cost_selector).value)<800) 	{document.getElementById(cost_selector).value="800";}
        }*/
      let gst_selector = "gst_" + row_no;
      let sgst_selector = "sgst_" + row_no;
      let igst_selector = "igst_" + row_no;
      let tax_gst_selector = "tax_gst_" + row_no;
      let tax_sgst_selector = "tax_sgst_" + row_no;
      let tax_igst_selector = "tax_igst_" + row_no;
      let tax_perc_gst = "";
      try { tax_perc_gst = parseFloat(document.getElementById(gst_selector).value); } catch (err) { tax_perc_gst = 0; }
      let tax_perc_sgst = "";
      try { tax_perc_sgst = parseFloat(document.getElementById(sgst_selector).value); } catch (err) { tax_perc_sgst = 0; }
      let tax_perc_igst = "";
      try { tax_perc_igst = parseFloat(document.getElementById(igst_selector).value); } catch (err) { tax_perc_igst = 0; }
      if (Number.isNaN(tax_perc_gst)) {
        tax_perc_gst = 0;
      }
      if (Number.isNaN(tax_perc_sgst)) {
        tax_perc_sgst = 0;
      }
      if (Number.isNaN(tax_perc_igst)) {
        tax_perc_igst = 0;
      }
      console.log(tax_perc_gst + "==" + tax_perc_sgst + "==" + tax_perc_igst);
      document.getElementById(tax_gst_selector).value = ((parseFloat(document.getElementById(price_selector).value) - (parseFloat(discount)) + (parseFloat(roundoff))) * tax_perc_gst / 100.0).toFixed(2);
      document.getElementById(tax_sgst_selector).value = ((parseFloat(document.getElementById(price_selector).value) - (parseFloat(discount)) + (parseFloat(roundoff))) * tax_perc_sgst / 100.0).toFixed(2);
      document.getElementById(tax_igst_selector).value = ((parseFloat(document.getElementById(price_selector).value) - (parseFloat(discount)) + (parseFloat(roundoff))) * tax_perc_igst / 100.0).toFixed(2);

      let total_selector = "total_" + row_no;
      document.getElementById(total_selector).value = (parseFloat(document.getElementById(tax_gst_selector).value) + parseFloat(document.getElementById(price_selector).value) + parseFloat(document.getElementById(tax_sgst_selector).value) + parseFloat(document.getElementById(tax_igst_selector).value) - (parseFloat(discount)) + (parseFloat(roundoff))).toFixed(2);;
    }

    function del_service_det(service) {
      let id_selector = "subtrans_id_" + service;
      let subtrans_id = document.getElementById(id_selector).value;
      let item_id_selector = "item_id_" + service;
      let item_id = document.getElementById(item_id_selector).value;

      // If it's a new row not yet saved in DB, just remove from DOM
      if (subtrans_id === "" || subtrans_id === null || subtrans_id === "undefined") {
        $("#row_" + service).fadeOut(200, function () {
          $(this).remove();
        });
        return;
      }

      var r = confirm("Are you sure that you want to delete the transaction");

      if (r) {
        del_mode = 1;

        $.ajax({
          url: "delete_trans_det_receipt.php",
          type: "POST",
          data: {
            trans_id: $("#trans_id").val(),
            subtrans_id: subtrans_id,
            item_id: item_id
          },
          dataType: "json",
          success: function (res) {
            if (res.status === "error") {
              alert(res.message);
              return;
            }

            let data = res.data;
            $("#row_" + service).fadeOut(200, function () {
              $(this).hide();
            });

            $("#trans_amount").val(data.grand_total || 0);
            $("#cgst_amount").val(data.tax || 0);
            $("#sgst_amount").val(data.sgst || 0);
            $("#igst_amount").val(data.igst || 0);
            $("#price_amount").val(data.price || 0);
            $("#discount_amount").val(data.discount || 0);
            $("#roundoff_amount").val(data.roundoff || 0);

            update_summary();

            setTimeout(() => {
              del_mode = 0;
            }, 500);

          },
          error: function (xhr) {
            alert("Server error occurred while deleting the row.");
            del_mode = 0;
          }
        });

      }
    }
    function save_pay() {
      let paid_amount = $("#paid_amount").val();
      let pay_type = $("#pay_type").val();
      let ref_no = $("#ref_no").val();
      let trans_amount = $("#trans_amount").val();
      let account = "";
      try { account = $("#account").val().split("~")[1]; } catch (err) { account = ""; }
      $.post("update_pay_receipt.php",
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
          console.log($.trim(data));
          //location.href="sales_trans_new.php?trans_id="+$("#trans_id").val();

        });
    }
    function save_pay1() {
      let paid_amount = $("#paid_amount").val();
      let pay_type_raw = $("#pay_type1").val();
      let account_raw = $("#account1").val();
      let ref_no = $("#ref_no1").val();
      let trans_amount = $("#trans_amount1").val();

      if (!paid_amount || parseFloat(paid_amount) <= 0) {
        alert("Please enter a valid amount.");
        return;
      }
      if (!pay_type_raw) {
        alert("Please select a Payment Mode.");
        return;
      }
      if (!account_raw) {
        alert("Please select an Account.");
        return;
      }

      let pay_type = pay_type_raw.split("~")[1];
      let account = account_raw.split("~")[1];

      let max_amount = parseFloat("<?php echo $grand_total; ?>") - parseFloat("<?php echo $total_amount; ?>");
      if (parseFloat(paid_amount) > max_amount + 0.01 && max_amount > 0) {
        alert("Amount should not exceed pending amount");
        return;
      }

      // Disable button
      let btn = $("#save_pay_btn");
      btn.prop("disabled", true).text("Saving...");

      $.post("update_pay_receipt.php",
        {
          trans_id: $("#trans_id").val(),
          paid_amount: paid_amount,
          pay_type: pay_type,
          account: account,
          ref_no: ref_no,
          trans_amount: trans_amount,
          customer_name: $("#customer_name").val(),
          customer: $("#customer").val(),
          trans_date: $("#datepicker").val(),
          gst: "<?php echo $gst; ?>"
        },
        function (data, status) {
          alert("Payment Saved Successfully");
          window.location.href = "receipt_trans_new.php?trans_id=" + $("#trans_id").val();
        }).fail(function () {
          alert("Error saving payment.");
          btn.prop("disabled", false).text("Save");
        });
    }
    function get_invoice() {
      let trans_id = $("#trans_id").val();
      let url = "sales_receipt_new.php?trans_id=" + trans_id;
      window.open(url, '_blank');
      //window.location.href=url;
    }
    function get_jobcard() {
      let trans_id = $("#trans_id").val();
      let url = "job_card.php?trans_id=" + trans_id;
      window.open(url, '_blank');
      //window.location.href=url;
    }
    function filter_account() {
      let mode_val = document.getElementById("pay_type").value;
      if (!mode_val) {
        $('#account option').prop('disabled', false);
        return;
      }
      let pay_type = mode_val.split("~")[0];

      // Re-enable all first
      $('#account option').prop('disabled', false);

      $('#account option').each(function () {
        let val = $(this).val();
        if (val !== "") {
          let acc_pay_type = val.split("~")[0];
          if (acc_pay_type !== pay_type) {
            $(this).prop('disabled', true);
          }
        }
      });

      if ($('#account option:selected').is(':disabled')) {
        $('#account').val("");
      }
    }
    function filter_account1() {
      let mode_val = document.getElementById("pay_type1").value;
      if (!mode_val) {
        $('#account1 option').prop('disabled', false);
        return;
      }
      let pay_type = mode_val.split("~")[0];

      // Re-enable all first
      $('#account1 option').prop('disabled', false);

      $('#account1 option').each(function () {
        let val = $(this).val();
        if (val !== "") {
          let acc_pay_type = val.split("~")[0];
          if (acc_pay_type !== pay_type) {
            $(this).prop('disabled', true);
          }
        }
      });

      if ($('#account1 option:selected').is(':disabled')) {
        $('#account1').val("");
      }
    }

    const input = document.getElementById('paid_amount');

    input.addEventListener('input', function () {
      const min = parseInt(input.min);
      const max = parseInt(input.max);
      const value = parseInt(input.value);

      if (value > max) {
        input.value = max;
      } else if (value < min) {
        input.value = min;
      }
    });
    const tooltipWrappers = document.querySelectorAll('.tooltip-wrapper');

    tooltipWrappers.forEach(wrapper => {
      const input = wrapper.querySelector('input');
      const tooltip = wrapper.querySelector('.tooltip-text');

      function showTooltip() {
        if (input.readOnly) {
          wrapper.classList.add('show');
        }
      }

      function hideTooltip() {
        wrapper.classList.remove('show');
      }

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
      $("#add_opener").prop("disabled", true);
      $.ajax({
        url: "finalize_receipt.php",
        type: "POST",
        data: {
          trans_id: $("#trans_id").val()
        },
        dataType: "json",
        success: function (res) {
          if (res.status === "error") {
            alert(res.message);
            $("#add_opener").prop("disabled", false);
            return;
          }
          alert(res.message || "Saved Transaction Successfully");
          location.href = "receipt_trans_new.php?trans_id=" + $("#trans_id").val();
        },
        error: function (xhr) {
          alert("Server error occurred");
          $("#add_opener").prop("disabled", false);
        }
      });
    }
    function save_dummy_back() {
      $.post("save_draft_receipt.php",
        {
          value: $("#trans_id").val(),


        },
        function (data, status) {

        });
    }
    function select_customer() {

      set_customer();
      get_gst();
      get_address();
    };


    function select_item() {
      var checkedInputs = $('input[name^="item_search"]:checked');
      var myvar = "";

      checkedInputs.each(function () {
        let val = $(this).val();
        if (val && val !== "") {
          myvar = val;
        }
        // Clean up the checkbox state and its display item immediately
        $(this).prop('checked', false);
        let item = $(this).data('sol-item');
        if (item && item.displaySelectionItem) {
          item.displaySelectionItem.remove();
        }
      });

      $('.sol-option').removeClass("sol-selected");

      if (!myvar || myvar.trim() === "") {
        return;
      }

      let parts = myvar.split("~");
      let item_id = parts[5];
      let item_name = parts[0];

      // Load the main item
      load_service(item_id, parts[2], parts[3], parts[4], item_name, parts[6]);

      // Auto-check for sub-items (Kit)
      setTimeout(function () {
        $.post("fetch_items.php", { itemgroup_id: item_id }, function (data, status) {
          if (data && $.trim(data) !== "" && $.trim(data) !== "0") {
            let list = $.trim(data).split("@");
            for (let i = 0; i < list.length - 1; i++) {
              let sub = list[i].split("~");
              // sub[0]=item_id, sub[1]=item_name, sub[2]=tax_pc, sub[3]=tax_pc_sgst, sub[4]=perc, sub[5]=hsn
              // Only add if it's NOT the main item itself (to avoid duplicates)
              if (sub[0] != item_id) {
                load_service(sub[0], 0, sub[2], sub[2], sub[1], sub[5]);
              }
            }
          } else {
            // Try searching by name if ID doesn't match a group
            $.post("fetch_items_by_name.php", { item_name: item_name }, function (data2, status2) {
              if (data2 && $.trim(data2) !== "" && $.trim(data2) !== "0") {
                let list = $.trim(data2).split("@");
                for (let i = 0; i < list.length - 1; i++) {
                  let sub = list[i].split("~");
                  if (sub[0] != item_id) {
                    load_service(sub[0], 0, sub[2], sub[2], sub[1], sub[5]);
                  }
                }
              }
            });
          }
        });
      }, 500);
    }


    function set_customer() {
      var checkedInputs = $('input[name^="customer"]:checked');
      var myvar = "";

      checkedInputs.each(function () {
        let val = $(this).val();
        if (val && val !== "") {
          myvar = val;
        }
        // Clean up the checkbox state and its display item immediately
        $(this).prop('checked', false);
        let item = $(this).data('sol-item');
        if (item && item.displaySelectionItem) {
          item.displaySelectionItem.remove();
        }
      });

      if (!myvar || myvar.trim() === "") {
        return;
      }

      let parts = myvar.split("~");

      $("#customer").val(parts[3]);
      $("#company_name").val(parts[0]);
      $("#customer_name").val(parts[1]);
      $("#customer_mobile").val(parts[2]);
    }
    function unlock() {
      $.post("unlock_receipt.php",
        {
          trans_id: $("#trans_id").val()


        },
        function (data, status) {
          alert("Transaction unlocked with ID: " + $("#trans_id").val());
          //$('#invoice').prop('disabled', false);
          window.location.href = "receipt_trans_new.php?trans_id=" + $("#trans_id").val();

        });
    }

    let headerTimer;
    function saveHeader() {
      clearTimeout(headerTimer);
      headerTimer = setTimeout(function () {
        let trans_id = $("#trans_id").val();
        if (trans_id === "") return;
        $.ajax({
          url: "update_receipt.php",
          type: "POST",
          dataType: "json",
          data: {
            trans_id: trans_id,
            company_name: $("#company_name").val(),
            customer_name: $("#customer_name").val(),
            customer_mobile: $("#customer_mobile").val(),
            customer_address: $("#customer_address").val(),
            customer_gst: $("#customer_gst").val(),
            invoice_no: $("#invoice_no").val(),
            trans_date: $("#datepicker").val()
          },
          success: function (res) {
            if (res.status === "error") {
              console.log(res.message);
            }
          },
          error: function (xhr) {
            console.log("Header update error:", xhr.responseText);
          }
        });
      }, 400);
    }
  </script>






</body>

</html>