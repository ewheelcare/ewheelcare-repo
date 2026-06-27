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
  <style>
    .fullscreen-modal {
      width: 100vw;
      height: 100vh;
      margin: 0;
      border-radius: 0;
    }

    .modal-dialog {
      max-width: 100%;
      margin: 0;
    }

    .modal-content {
      height: 100vh;
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
                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="font-size:85%">
                    <thead class="btn-primary">
                      <tr>
                        <th>Trans ID</th>
                        <th>Trans Date</th>
                        <th>Vendor / Customer Det</th>

                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Bank</th>
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
                        <th>#</th>

                      </tr>
                    </tfoot>
                    <tbody>
                      <?php $sql = "SELECT p.*,a.account_name FROM payments p, account a where p.account=a.account_id and p.active_status='A' and p.nature='" . $now_nature . "' ORDER BY p.payment_id DESC LIMIT 100";
                      $result = $conn->query($sql);

                      while ($row = $result->fetch_assoc()) { ?>
                        <TR>
                          <td><b><?php echo $row["payment_id"]; ?></b>, Nature :<?php echo $row["nature"]; ?></td>
                          <td> <b><?php echo $row["payment_date"]; ?></b></td>
                          <td><b> <?php echo $row["vendor_name"]; ?></b>
                            <?php echo $row["customer_name"]; ?></b>
                          </td>
                          <td>₹<?php echo $row["amount"]; ?></td>
                          <td> <?php echo $row["mode"]; ?></td>
                          <td><?php echo $row["account_name"]; ?>
                          </td>
                          <td>
                            <button class="btn btn-danger btn-sm"
                              onclick="delete_it('<?php echo $row["payment_id"]; ?>','<?php echo $row["nature"]; ?>','<?php echo $row["account"]; ?>','<?php echo $row["account_to"]; ?>','<?php echo $row["amount"]; ?>','<?php echo $row["mode"]; ?>')">X</button>
                            <?php if (($row["nature"] == "DEBIT" || $row["nature"] == "CREDIT") && ($row["verified"] == "N")) { ?>

                              <button class="btn btn-success btn-sm"
                                onclick="approve_pay('<?php echo $row["payment_id"]; ?>','<?php echo $row["nature"]; ?>','<?php echo $row["amount"]; ?>')">Approve</button>
                            <?php } ?>

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
    <div class="modal-dialog modal-fullscreen" role="document">
      <div class="modal-content fullscreen-modal">

        <div class="modal-header">
          <h5 class="modal-title">Add</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" id="">
          <table class="table table-striped">
            <tr>
              <td>Date of Trnsaction</td>
              <td><input type="text" id="datepicker" class="form-control"></td>
            </tr>
            <tr>
              <td>Nature</td>
              <td><select type="text" class="form-control" id="nature" onchange="set_vendor_customer()">
                  <option value="">--Select--</option>
                  <option value="DEBIT">PAYMENT</option>
                  <option value="CREDIT">RECEIPT</option>
                  <?PHP if (isset($_COOKIE["SA"])) { ?>
                    <option value="CASHTOBANK">CASH TO BANK</option>
                    <option value="BANKTOCASH">BANK TO CASH</option>
                    <option value="BANKTOBANK">BANK TO BANK</option>
                  <?PHP } ?>
                </select></td>
            </tr>
            <tr>
              <td>Vendor</td>
              <td>
                <select class="form-control select2-vendor" id="vendor">
                  <option value="">--Select Vendor--</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>Customer</td>
              <td>
                <select class="form-control select2-customer" id="customer">
                  <option value="">--Select Customer--</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>Mode</td>
              <td><select type="text" class="form-control" id="mode" onchange="filter_account()">
                  <option value="">--Select--</option>
                  <?php $sql = "SELECT paytype_id,paytype_name FROM paytype order by 1";
                  $result = $conn->query($sql);
                  while ($row = $result->fetch_assoc()) {
                    ?>
                    <option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['paytype_name']; ?>">
                      <?php echo $row['paytype_name']; ?>
                    </option>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td>Account (From in case of Transfer)</td>
              <td><select type="text" class="form-control" id="account">
                  <option value="">--Select--</option>
                  <?php $sql = "SELECT account_id,account_name,paytype_id FROM account order by 1";
                  $result = $conn->query($sql);
                  while ($row = $result->fetch_assoc()) {
                    ?>
                    <option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['account_id']; ?>">
                      <?php echo $row['account_name']; ?>
                    </option>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td>Account (To in case of Transfer)</td>
              <td><select type="text" class="form-control" id="account_to">
                  <option value="">--Select--</option>
                  <?php $sql = "SELECT account_id,account_name,paytype_id FROM account order by 1";
                  $result = $conn->query($sql);
                  while ($row = $result->fetch_assoc()) {
                    ?>
                    <option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['account_id']; ?>">
                      <?php echo $row['account_name']; ?>
                    </option>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td>Amount</td>
              <td><input type="number" id="amount" name="amount"></td>
            </tr>
            <tr>
              <td>Reference</td>
              <td><input type="text" id="reference" name="reference"></td>
            </tr>

          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" id="add">Save</button>


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
              <td>Date of Trnsaction</td>
              <td><input type="text" id="edit_datepicker" class="form-control"></td>
            </tr>
            <tr>
              <td>Details</td>
              <td><input type="text" class="form-control" id="edit_details"></td>
            </tr>
            <tr>
              <td>Vendor</td>
              <td><input class="form-control" id="edit_vendor" list="edit_vendor_list">
                <?php $sql = "SELECT vendor_id,company_name FROM vendor order by 1";
                $result = $conn->query($sql); ?>
                <datalist id="edit_vendor_list">
                  <?php while ($row = $result->fetch_assoc()) { ?>
                    <option value="<?php echo $row['vendor_id']; ?>~<?php echo $row['company_name']; ?>"></option>
                  <?php } ?>
                </datalist>
                </select>
                <input type='HIDDEN' name="trans_id" id="trans_id">
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
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="js/demo/datatables-demo.js"></script>

  <script>
    var addModal = new bootstrap.Modal(document.getElementById('addModal'));
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    $(document).ready(function () {
      document.getElementById("nature").value = "<?php echo $now_nature; ?>";
      set_vendor_customer();

      // Initialize Select2 for Customers with AJAX
      $('.select2-customer').select2({
        dropdownParent: $('#addModal'),
        ajax: {
          url: 'search_customers_ajax.php',
          dataType: 'json',
          delay: 250,
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
          delay: 250,
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
          (state.pending && parseFloat(state.pending.replace(/,/g, '')) > 0 ? '<div class="select2-result-pending">Pending Balance: ₹ ' + state.pending + '</div>' : '') +
          (state.vehicles ? '<div class="select2-result-row" style="margin-top:8px"><span class="select2-result-label">Vehicles:</span><span class="select2-result-val">' + state.vehicles + '</span></div>' : '') +
          (state.address ? '<div class="select2-result-row"><span class="select2-result-label">Address:</span><span class="select2-result-val">' + state.address + '</span></div>' : '') +
          '</div>'
        );
        return $state;
      }

      function formatVendorResult(state) {
        if (state.loading) return state.text;
        var $state = $(
          '<div class="select2-result-item">' +
          '<span class="select2-result-title">' + state.text + '</span>' +
          (state.details ? '<div class="select2-result-row"><span class="select2-result-label">Owner:</span><span class="select2-result-val">' + state.details + '</span></div>' : '') +
          '</div>'
        );
        return $state;
      }

      function formatSelection(state) {
        return state.text;
      }
    });



    $(function () {
      $("#datepicker").datepicker({
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
      let payment_date = $("#datepicker").val();
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
      $.post("add_payment.php",
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
          reference: reference


        },
        function (data, status) {
          alert($.trim(data));
          console.log($.trim(data));
          location.reload();
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
      if (nature == "DEBIT") {
        document.getElementById("vendor").disabled = false;
        document.getElementById("customer").disabled = true;
        document.getElementById("account_to").disabled = true;
      } else if (nature == "CREDIT") {
        document.getElementById("vendor").disabled = true;
        document.getElementById("customer").disabled = false;
        document.getElementById("account_to").disabled = true;

      } else {
        document.getElementById("vendor").disabled = true;
        document.getElementById("customer").disabled = true;
        document.getElementById("mode").disabled = true;
        //if(document.getElementById("nature").value=="BANKTOBANK")
        { document.getElementById("account_to").disabled = false; }
        /*else
        {document.getElementById("account_to").disabled=true;}*/
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

      $('#account option, #account_to option').each(function() {
        let val = $(this).val();
        if (val !== "") {
          let acc_pay_type = val.split("~")[0];
          // Use exact match to avoid ID overlap issues (e.g. ID "3" matching "1000003")
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
            alert($.trim(data));
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