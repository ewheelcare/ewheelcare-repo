<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "header_include.php";
  include "db_config.php";
  ?>
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
            <?php $customer_id = $_GET["customer_id"];
            $sql = "SELECT customer_id,company_name FROM customer m where customer_id='" . $customer_id . "' order by 1";
            $result = $conn->query($sql);

            if ($row = $result->fetch_assoc()) { ?>
              <div class="col-md-6 alert alert-warning"><b><?php echo $row["customer_id"]; ?></b>
              </div>
              <div class="col-md-6 alert alert-secondary">
                <br> <b><?php echo $row["company_name"]; ?></b>
              </diV>
            <?php } ?>

            <div class="col-md-12">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"
                id="add_opener">
                Add
              </button>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                      <tr>
                        <th>Address</th>

                        <th>#</th>

                      </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th>Address</th>

                        <th>#</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      <?php $sql = "SELECT address  FROM customer_address where customer_id='" . $customer_id . "'";
                      $result = $conn->query($sql);

                      while ($row = $result->fetch_assoc()) { ?>
                        <TR>
                          <TD><b><?php echo $row["address"]; ?></b>
                          </TD>
                          <td>
                            <button class="btn btn-danger btn-sm"
                              onclick="delete_it('<?php echo $row["address"]; ?>','<?php echo $customer_id; ?>')">X</button>
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
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Add</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" id="">
          <table class="table table-striped">
            <tr>
              <td>Address</td>
              <td><input type="text" class="form-control" id="address"></td>
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


  <?php include "footer_include.php"; ?>
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/jquery-ui.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="js/demo/datatables-demo.js"></script>

  <script>
    var addModal = new bootstrap.Modal(document.getElementById('addModal'));
    $(function () {
      $("#datepicker").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
      }).datepicker("setDate", new Date());;
      $("#edit_datepicker").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
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
      let address = $("#address").val();
      let customer_id = "<?php echo $customer_id; ?>";
      $.post("add_address.php",
        {
          address: address,
          customer_id: customer_id
        },
        function (data, status) {
          alert($.trim(data));
          location.reload();
        });


    });

    function delete_it(address, customer_id) {
      var r = confirm("Are you sure that you want to delete the gst");
      if (r) {
        $.post("delete_address.php",
          {
            customer_id: customer_id,
            address: address
          },
          function (data, status) {
            alert($.trim(data));
            location.reload();
          });
      }

    }

  </script>


  </script>



</body>

</html>