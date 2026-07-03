<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "header_include.php"; ?>
    <?php include "db_config.php"; ?>
    <title>Assign Roles</title>
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

                    <?php 
                    $user_id = $_GET["user_id"] ?? '';
                    $user_name = $_GET["user_name"] ?? '';
                    ?>

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-user-tag mr-2"></i> Assign Roles 
                            <span class="text-primary">- <?php echo htmlspecialchars($user_name); ?> (<?php echo htmlspecialchars($user_id); ?>)</span>
                        </h1>
                        <div>
                            <a href="user.php" class="btn btn-secondary shadow-sm mr-2">
                                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Users
                            </a>
                            <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addModal">
                                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Assign New Role
                            </button>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Current Roles for <?php echo htmlspecialchars($user_name); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>User ID</th>
                                            <th>Assigned Role</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php  
                                        $sql="SELECT distinct user_id, user_role FROM expert_login_role WHERE active_status='A' AND user_id='".$conn->real_escape_string($user_id)."'";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) { 
                                        ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($row["user_id"]); ?></strong></td>
                                            <td><span class="badge badge-success px-3 py-2"><?php echo htmlspecialchars($row["user_role"]); ?></span></td>
                                            <td class="text-center">
                                                <button class="btn btn-danger btn-sm" onclick="delete_it('<?php echo htmlspecialchars($row["user_id"]); ?>', '<?php echo htmlspecialchars($row["user_role"]); ?>')">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
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

    <!-- Modals -->
    <?php include "modals.php"; ?>
   
    <!-- Add Role Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle mr-1"></i> Assign New Role</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>User ID</strong></label>
                        <input type="text" id="user_id" class="form-control bg-light" readonly value="<?php echo htmlspecialchars($user_id); ?>">
                    </div>
                    <div class="form-group">
                        <label><strong>Select Role</strong></label>
                        <select class="form-control" id="user_role">
                            <?php  
                            $sql="SELECT role FROM role_master WHERE active_status='A' ORDER BY 1";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                            ?>
                            <option value="<?php echo htmlspecialchars($row['role']); ?>"><?php echo htmlspecialchars($row['role']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="add">Save Role</button>
                </div>
            </div>
        </div>
    </div>

    <?php include "footer_include.php"; ?>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
    $(function () {
        // Initialize datatable
        $('#dataTable').DataTable();

        $('#add').on('click', function(e) {
            let user_id = $("#user_id").val();
            let user_role = $("#user_role").val();
            
            if (!user_role) {
                alert("Please select a role.");
                return;
            }

            $.post("add_role.php", {
                user_id: user_id,
                user_role: user_role
            }, function(data, status) {
                alert($.trim(data));
                location.reload();
            });
        });
    });

    function delete_it(user_id, role_id) {
        if (confirm("Are you sure you want to remove the '" + role_id + "' role from this user?")) {
            $.post("delete_role.php", {
                user_id: user_id,
                role_id: role_id
            }, function(data, status) {
                alert($.trim(data));
                location.reload();
            });
        }
    }
    </script>
</body>
</html>