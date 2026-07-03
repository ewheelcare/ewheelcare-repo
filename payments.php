<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['shop'])) {
  header("Location: login.php");
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
    /* Hide number input arrows */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield; /* Firefox */
    }

    /* Select2 Theme Customization */
    .select2-container--default .select2-selection--single {
      height: 38px !important;
      border: 1px solid #4a4a4a !important;
      border-radius: 4px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 36px !important;
      font-weight: bold;
      color: #4a4a4a;
      text-transform: uppercase;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 36px !important;
    }

    .select2-dropdown {
      border: 1px solid #b31217 !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
      background-color: #f8f9fa !important;
      color: #e52d27 !important;
    }

    .select2-results__option[aria-selected="true"] {
      background-color: #f1f1f1 !important;
    }

    .select2-result-item {
      padding: 12px 16px;
      border-bottom: 1px solid #eee;
    }

    .select2-result-item:last-child {
      border-bottom: none;
    }

    .select2-result-title {
      font-weight: 800;
      display: block;
      color: #000;
      font-size: 18px;
      margin-bottom: 4px;
    }

    .select2-result-mobile {
      font-size: 15px;
      color: #e52d27;
      font-weight: bold;
      display: block;
      margin-bottom: 8px;
    }

    .select2-result-pending {
      background: #fff3f3;
      color: #d63031;
      padding: 4px 10px;
      border-radius: 4px;
      font-weight: 800;
      font-size: 16px;
      display: inline-block;
      margin-top: 5px;
      border: 1px solid #ffcccc;
    }

    .select2-result-row {
      display: flex;
      font-size: 13px;
      color: #444;
      margin-top: 4px;
      text-transform: none;
    }

    .select2-result-label {
      font-weight: bold;
      color: #888;
      margin-right: 8px;
      text-transform: uppercase;
      min-width: 80px;
    }

    .select2-result-val {
      flex: 1;
      font-weight: 600;
    }

    /* Make dropdown wider */
    .select2-container--open .select2-dropdown {
      min-width: 450px !important;
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            <div class="col-md-12">
              <?php $now_nature = $_GET["nature"]; ?>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"
                id="add_opener">
                Add
              </button>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover table-sm" id="dataTable" width="100%" cellspacing="0"
                    style="font-size:90%">
                    <thead class="btn-primary">
                      <tr>
                        <th>Trans ID</th>
                        <th>Trans Date</th>
                        <th>Vendor / Customer Det</th>

                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Bank</th>
                        <th>Reference</th>
                        <th>#</th>

                      </tr>
                    </thead>
                    <tfoot class="btn-primary">
                      <tr>
                        <th>Trans ID</th>
                        <th>Trans Date</th>
                        <th>Vendor / Customer Det</th>

                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Bank</th>
                        <th>Reference</th>
                        <th>#</th>

                      </tr>
                    </tfoot>
                    <tbody>
                      <?php $sql = "SELECT p.*, a.account_name FROM payments p INNER JOIN account a ON p.account = a.account_id WHERE p.active_status = 'A' AND ('all' = 'all' OR p.nature = 'all') ORDER BY p.payment_id DESC LIMIT 100";

                      $result = $conn->query($sql);

                      while ($row = $result->fetch_assoc()) {
                        $mode_class = ($row["mode"] == "CASH") ? "badge-success" : (($row["mode"] == "UPI") ? "badge-primary" : "badge-info");
                        $display_name = trim(($row["vendor_name"] ?? '') . " " . ($row["customer_name"] ?? ''));
                        $display_date = !empty($row["payment_date"]) ? date("d-M-Y", strtotime($row["payment_date"])) : "<span class='badge badge-warning text-dark'>NO DATE</span>";
                        ?>
                        <TR>
                          <td class="align-middle"><span
                              class="text-primary font-weight-bold">#<?php echo $row["payment_id"]; ?></span></td>
                          <td class="align-middle text-nowrap"><?php echo $display_date; ?></td>
                          <td class="align-middle"><span
                              class="font-weight-bold text-dark"><?php echo $display_name; ?></span></td>
                          <td class="align-middle"><span
                              class="font-weight-bold">₹<?php echo number_format($row["amount"], 0); ?></span></td>
                          <td class="align-middle"><span
                              class="badge <?php echo $mode_class; ?>"><?php echo $row["mode"]; ?></span></td>
                          <td class="align-middle text-muted" style="font-size: 0.9em;">
                            <?php echo $row["account_name"]; ?>
                          </td>
                          <td class="align-middle text-muted" style="font-size: 0.9em;"><?php echo $row["reference"]; ?>
                          </td>
                          <td class="align-middle text-nowrap">
                            <button class="btn btn-outline-danger btn-sm border-0" title="Delete"
                              onclick="delete_it('<?php echo $row["payment_id"]; ?>','<?php echo $row["nature"]; ?>','<?php echo $row["account"]; ?>','<?php echo $row["account_to"]; ?>','<?php echo $row["amount"]; ?>','<?php echo $row["mode"]; ?>')">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                            <?php if (($row["nature"] == "DEBIT" || $row["nature"] == "CREDIT") && ($row["verified"] == "N")) { ?>
                              <button class="btn btn-success btn-sm ml-1" title="Approve"
                                onclick="approve_pay('<?php echo $row["payment_id"]; ?>','<?php echo $row["nature"]; ?>','<?php echo $row["amount"]; ?>')">
                                <i class="fas fa-check-circle"></i>
                              </button>
                            <?php } ?>
                          </td>
                        </TR>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
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


  <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content shadow">

        <div class="modal-header">
          <h5 class="modal-title font-weight-bold">Add Transaction</h5>
          <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"
            aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body p-4" style="overflow-y: auto; max-height: 80vh;">
          <div class="row">
            <!-- Date of Transaction -->
            <div class="col-md-12 mb-3">
              <label class="form-label font-weight-bold text-dark">Date of Transaction</label>
              <input type="text" id="payment_datepicker" class="form-control" style="height: 38px;">
              <input type="hidden" id="nature" value="CREDIT">
            </div>
          </div>

          <div class="row">
            <!-- Receipt Type -->
            <div class="col-md-12 mb-3" id="receipt_type_row" style="display:none;">
              <label class="form-label font-weight-bold text-dark">Receipt Type</label>
              <select class="form-control" id="receipt_type" style="height: 38px;">
                <option value="sales">Sales Bills</option>
                <option value="service">Service Bills</option>
              </select>
            </div>

            <!-- Vendor -->
            <div class="col-md-12 mb-3" id="vendor_row">
              <label class="form-label font-weight-bold text-dark">Vendor</label>
              <select class="form-control select2-vendor" id="vendor" style="width: 100%;">
                <option value="">--Select Vendor--</option>
              </select>
              <div id="vendor_pending_balance" class="mt-2" style="font-weight: bold; display: none;"></div>
            </div>

            <!-- Customer -->
            <div class="col-md-12 mb-3" id="customer_row">
              <label class="form-label font-weight-bold text-dark">Customer</label>
              <select class="form-control select2-customer" id="customer" style="width: 100%;">
                <option value="">--Select Customer--</option>
              </select>
              <div id="customer_pending_balance" class="mt-2" style="font-weight: bold; display: none;"></div>
            </div>
          </div>

          <div class="row">
            <!-- Mode -->
            <div class="col-md-6 mb-3" id="mode_row">
              <label class="form-label font-weight-bold text-dark">Mode</label>
              <select class="form-control" id="mode" onchange="filter_account()" style="height: 38px;">
                <option value="">--Select--</option>
                <?php $sql = "SELECT paytype_id,paytype_name FROM paytype order by 1";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                  ?>
                  <option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['paytype_name']; ?>">
                    <?php echo $row['paytype_name']; ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <!-- Account / From Account -->
            <div class="col-md-6 mb-3" id="account_row">
              <label class="form-label font-weight-bold text-dark" id="account_label">Account</label>
              <select class="form-control" id="account" style="height: 38px;">
                <option value="">--Select--</option>
                <?php $sql = "SELECT account_id,account_name,paytype_id FROM account order by 1";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                  ?>
                  <option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['account_id']; ?>">
                    <?php echo $row['account_name']; ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <!-- Account To (Transfer) -->
            <div class="col-md-6 mb-3" id="account_to_row">
              <label class="form-label font-weight-bold text-dark">To Account</label>
              <select class="form-control" id="account_to" style="height: 38px;">
                <option value="">--Select--</option>
                <?php $sql = "SELECT account_id,account_name,paytype_id FROM account order by 1";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                  ?>
                  <option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['account_id']; ?>">
                    <?php echo $row['account_name']; ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="row">
            <!-- Amount -->
            <div class="col-md-6 mb-3">
              <label class="form-label font-weight-bold text-dark">Amount</label>
              <input type="number" id="amount" name="amount" class="form-control" placeholder="Enter Amount"
                style="height: 38px;">
            </div>

            <!-- Reference -->
            <div class="col-md-6 mb-3">
              <label class="form-label font-weight-bold text-dark">Reference / Remarks</label>
              <input type="text" id="reference" name="reference" class="form-control"
                placeholder="Enter Reference/Remarks" style="height: 38px;">
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary px-4" data-dismiss="modal"
            data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success px-4" id="add">Save</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content shadow">

        <div class="modal-header">
          <h5 class="modal-title font-weight-bold">Edit Transaction</h5>
          <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"
            aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body p-4">
          <div class="form-group mb-3">
            <label class="form-label font-weight-bold text-dark">Date of Transaction</label>
            <input type="text" id="edit_datepicker" class="form-control" style="height: 38px;">
          </div>
          <div class="form-group mb-3">
            <label class="form-label font-weight-bold text-dark">Details</label>
            <input type="text" class="form-control" id="edit_details" style="height: 38px;">
          </div>
          <div class="form-group mb-3">
            <label class="form-label font-weight-bold text-dark">Vendor</label>
            <input class="form-control" id="edit_vendor" list="edit_vendor_list" style="height: 38px;">
            <?php $sql = "SELECT vendor_id,company_name FROM vendor order by 1";
            $result = $conn->query($sql); ?>
            <datalist id="edit_vendor_list">
              <?php while ($row = $result->fetch_assoc()) { ?>
                <option value="<?php echo $row['vendor_id']; ?>~<?php echo $row['company_name']; ?>"></option>
              <?php } ?>
            </datalist>
            <input type='HIDDEN' name="trans_id" id="trans_id">
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary px-4" data-dismiss="modal"
            data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success px-4" id="edit">Save</button>
        </div>
      </div>
    </div>
  </div>
  <?php include "footer_include.php"; ?>
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/jquery-ui.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="js/demo/datatables-demo.js"></script>

  <script>
    var addModal = new bootstrap.Modal(document.getElementById('addModal'));
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    $(document).ready(function () {
      // Disable scroll wheel and up/down arrows on number inputs
      $(document).on('wheel', 'input[type=number]', function (e) {
          $(this).blur();
      });
      $(document).on('keydown', 'input[type=number]', function(e) {
          if (e.which == 38 || e.which == 40) {
              e.preventDefault();
          }
      });

      document.getElementById("nature").value = "<?php echo $now_nature; ?>";
      set_vendor_customer();

      // Initialize Select2 for Customers with AJAX
      $('.select2-customer').select2({
        dropdownParent: $('#addModal'),
        ajax: {
          url: 'search_customers_ajax.php',
          dataType: 'json',
          delay: 300,
          data: function (params) {
            return {
              q: params.term,
              page: params.page || 1,
              receipt_type: $('#receipt_type').is(':visible') ? $('#receipt_type').val() : ''
            };
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: data.results,
              pagination: {
                more: data.pagination.more
              }
            };
          },
          cache: true
        },
        placeholder: '--Select Customer--',
        templateResult: formatCustomerResult,
        templateSelection: formatSelection
      });

      // Initialize Select2 for Vendors with AJAX
      $('.select2-vendor').select2({
        dropdownParent: $('#addModal'),
        ajax: {
          url: 'search_vendors_ajax.php',
          dataType: 'json',
          delay: 300,
          data: function (params) {
            return {
              q: params.term,
              page: params.page || 1
            };
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: data.results,
              pagination: {
                more: data.pagination.more
              }
            };
          },
          cache: true
        },
        placeholder: '--Select Vendor--',
        templateResult: formatVendorResult,
        templateSelection: formatSelection
      });

      function formatCustomerResult(state) {
        if (state.loading) return state.text;
        var $state = $(
          '<div class="select2-result-item">' +
          '<span class="select2-result-title">' + state.text + '</span>' +
          (state.mobile ? '<span class="select2-result-mobile"><i class="fas fa-phone-alt mr-1"></i> ' + state.mobile + '</span>' : '') +
          (function () {
            if (state.pending === undefined || state.pending === null) return '';
            let num = parseFloat(String(state.pending).replace(/,/g, ''));
            if (num === 0) return '<div class="select2-result-pending" style="background:#f0f0f0;color:#555;">No Pending Balance</div>';
            if (num < 0) return '<div class="select2-result-pending" style="color:red;">Pending Balance: ₹ ' + state.pending + '</div>';
            return '<div class="select2-result-pending">Pending Balance: ₹ ' + state.pending + '</div>';
          })() +
          '</div>'
        );
        return $state;
      }

      function formatVendorResult(state) {
        if (state.loading) return state.text;
        var $state = $(
          '<div class="select2-result-item">' +
          '<span class="select2-result-title">' + state.text + '</span>' +
          (function () {
            if (!state.pending) return '';
            let num = parseFloat(state.pending.replace(/,/g, ''));
            if (num === 0) return '';
            if (num < 0) return '<div class="select2-result-pending" style="color:red;">Pending Balance: ₹ ' + state.pending + '</div>';
            return '<div class="select2-result-pending">Pending Balance: ₹ ' + state.pending + '</div>';
          })() +
          (state.details ? '<div class="select2-result-row" style="margin-top:8px"><span class="select2-result-label">Owner:</span><span class="select2-result-val">' + state.details + '</span></div>' : '') +
          '</div>'
        );
        return $state;
      }

      function formatSelection(state) {
        return state.text;
      }

      $('#receipt_type').on('change', function () {
        $('#customer').val(null).trigger('change');
      });

      // Handle customer selection event to show pending balance
      $('.select2-customer').on('select2:select', function (e) {
        var data = e.params.data;
        if (data && data.pending !== undefined && data.pending !== null) {
          let num = parseFloat(String(data.pending).replace(/,/g, ''));
          let color, label;
          if (num === 0) {
            color = '#555'; label = 'No Pending Balance';
            $('#customer_pending_balance').html('<span style="color:' + color + '; font-weight: 800;">' + label + '</span>').show();
          } else {
            color = num < 0 ? 'red' : 'green';
            $('#customer_pending_balance').html('Pending Balance: <span style="color:' + color + '; font-weight: 800;">₹ ' + data.pending + '</span>').show();
          }
        } else {
          $('#customer_pending_balance').html('<span style="color:#555; font-weight: 800;">Pending Balance: ₹ 0.00</span>').show();
        }
      });

      $('.select2-customer').on('change', function () {
        if (!$(this).val()) {
          $('#customer_pending_balance').hide().html('');
        }
      });

      // Handle vendor selection event to show pending balance
      $('.select2-vendor').on('select2:select', function (e) {
        var data = e.params.data;
        if (data && data.pending) {
          let num = parseFloat(data.pending.replace(/,/g, ''));
          let color = num < 0 ? 'red' : 'green';
          $('#vendor_pending_balance').html('Pending Balance: <span style="color:' + color + '; font-weight: 800;">₹ ' + data.pending + '</span>').show();
        } else {
          $('#vendor_pending_balance').hide().html('');
        }
      });

      $('.select2-vendor').on('change', function () {
        if (!$(this).val()) {
          $('#vendor_pending_balance').hide().html('');
        }
      });
    });



    $(function () {
      $("#payment_datepicker").datepicker({
        dateFormat: "dd-mm-yy"  // Set format to yyyy-mm-dd
      }).datepicker("setDate", new Date());;
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

    $('#add').on('click', function (e) {
      let vendor = "", vendor_name = "", customer = "", customer_name = "";
      let payment_date = $("#payment_datepicker").val();
      let nature = $("#nature").val();

      let v_val = $("#vendor").val();
      if (v_val) {
        vendor = v_val.split("~")[0];
        vendor_name = v_val.split("~")[1];
      }

      let c_val = $("#customer").val();
      if (c_val) {
        customer = c_val.split("~")[0];
        customer_name = c_val.split("~")[1];
      }

      let mode = "";
      try { mode = $("#mode").val().split("~")[1]; } catch (err) { mode = ""; }
      let account = "";
      try { account = $("#account").val().split("~")[1]; } catch (err) { account = ""; }
      let account_to = "";
      try { account_to = $("#account_to").val().split("~")[1]; } catch (err) { account_to = ""; }

      let amount = $("#amount").val();
      let reference = $("#reference").val();
      let receipt_type = $("#receipt_type").val();
      $.post(
        "add_payment.php",
        {
          payment_date: payment_date,
          nature: nature,
          vendor: vendor,
          vendor_name: vendor_name,
          customer: customer,
          customer_name: customer_name,
          mode: mode,
          account: account,
          account_to: account_to,
          amount: amount,
          reference: reference,
          receipt_type: receipt_type
        },
        function (response) {

          if (response.status === "success") {

            alert(
              response.message +
              "\nPayment ID : " + response.payment_id
            );

            location.reload();

          } else {

            alert(response.message);
          }

        },
        "json"
      ).fail(function (xhr, status, error) {

        console.error(xhr.responseText);

        alert(
          "Error while saving payment.\n\n" +
          "Status : " + status + "\n" +
          "Error : " + error
        );

      });


    });

    $('#edit').on('click', function (e) {
      let trans_date = $("#edit_datepicker").val();
      let details = $("#edit_details").val();
      let vendor = $("#edit_vendor").val().split("~")[0];
      let trans_id = $("#trans_id").val();
      $.post("update_receipt.php",
        {
          trans_date: trans_date,
          details: details,
          vendor: vendor,
          trans_id: trans_id
        },
        function (data, status) {
          alert($.trim(data));
          location.reload();
        });


    });
    function set_vendor_customer() {
      let nature = document.getElementById("nature").value;

      // Reset pending balance displays
      $('#customer_pending_balance').hide().html('');
      $('#vendor_pending_balance').hide().html('');

      // Default: hide/show sections
      $("#receipt_type_row").hide();
      $("#vendor_row").hide();
      $("#customer_row").hide();
      $("#mode_row").show();
      $("#account_row").show();
      $("#account_to_row").hide();

      // Reset disabled states for form inputs
      document.getElementById("vendor").disabled = true;
      document.getElementById("customer").disabled = true;
      document.getElementById("account_to").disabled = true;
      document.getElementById("mode").disabled = false;
      document.getElementById("account").disabled = false;

      // Reset Account label
      document.getElementById("account_label").innerText = "Account";

      if (nature == "DEBIT") {
        $("#vendor_row").show();
        document.getElementById("vendor").disabled = false;
        document.getElementById("customer").disabled = true;
        document.getElementById("account_to").disabled = true;
      } else if (nature == "CREDIT") {
        $("#receipt_type_row").show();
        $("#customer_row").show();
        document.getElementById("vendor").disabled = true;
        document.getElementById("customer").disabled = false;
        document.getElementById("account_to").disabled = true;
      } else {
        // Transfer natures (CASHTOBANK, BANKTOCASH, BANKTOBANK)
        $("#mode_row").hide();
        $("#account_to_row").show();

        document.getElementById("vendor").disabled = true;
        document.getElementById("customer").disabled = true;
        document.getElementById("mode").disabled = true;
        document.getElementById("account_to").disabled = false;

        // Dynamically change account label to "From Account"
        document.getElementById("account_label").innerText = "From Account";
      }
    }
    function filter_account() {
      let mode_val = document.getElementById("mode").value;
      if (!mode_val) {
        $('#account option, #account_to option').prop('disabled', false);
        return;
      }
      let pay_type = mode_val.split("~")[0];

      // Reset disabled status first to allow re-filtering
      $('#account option, #account_to option').prop('disabled', false);

      $('#account option, #account_to option').each(function () {
        let val = $(this).val();
        if (val !== "") {
          let acc_pay_type = val.split("~")[0];
          if (acc_pay_type !== pay_type) {
            $(this).prop('disabled', true);
          }
        }
      });

      // Clear selection if now disabled
      if ($('#account option:selected').is(':disabled')) {
        $('#account').val("");
      }
      if ($('#account_to option:selected').is(':disabled')) {
        $('#account_to').val("");
      }

      // Auto-select account if only one option matches the selected mode
      let enabledAccounts = $('#account option:enabled').not('[value=""]');
      if (enabledAccounts.length === 1) {
        $('#account').val(enabledAccounts.first().val());
      }

      // Auto-select account_to if only one option matches
      let enabledAccountsTo = $('#account_to option:enabled').not('[value=""]');
      if (enabledAccountsTo.length === 1) {
        $('#account_to').val(enabledAccountsTo.first().val());
      }
    }

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
    function delete_it(trans_id, nature, account, account_to, amount, mode) {
      var r = confirm("Are you sure that you want to delete the transaction");
      if (r) {
        $.post("delete_payment.php",
          {
            payment_id: trans_id,
            nature: nature,
            account: account,
            account_to: account_to,
            amount: amount,
            mode: mode
          },
          function (data, status) {
            let response = typeof data === "object" ? data : JSON.parse(data);
            alert(response.message || response);
            location.reload();
          });
      }
    }
    function approve_pay(trans_id, nature, amount) {
      var r = confirm("Are you sure that you want to approve the transaction");
      if (r) {
        $.post("approve_pay.php",
          {
            payment_id: trans_id,
            nature: nature,

            amount: amount

          },
          function (data, status) {
            alert($.trim(data));
            location.reload();
          });
      }

    }
    function edit_it(trans_id, trans_date, vendor, details) {

      //alert("====");
      $("#trans_id").val(trans_id);
      $("#edit_datepicker").val(trans_date);

      $("#edit_details").val(details);
      let datalist = document.getElementById("edit_vendor_list");
      const options = datalist.getElementsByTagName('option');

      // Set partial value


      // Try to auto-complete
      for (let i = 0; i < options.length; i++) {
        const optionVal = options[i].value.toLowerCase();
        if (optionVal.startsWith(vendor)) {
          // input.value = options[i].value;
          $("#edit_vendor").val(options[i].value);
          break;
        }
      }
      editModal.show();

    }
  </script>


  </script>



</body>

</html>