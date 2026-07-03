<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "header_include.php"; ?>
    <?php include "db_config.php"; ?>
    <title>User Management</title>
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

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users mr-2"></i> User Management</h1>
                        <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal"
                            data-target="#addModal" id="add_opener">
                            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Add New User
                        </button>
                    </div>

                    <!-- Content Row -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">System Users</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%"
                                    cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>User Name</th>
                                            <th>User ID</th>
                                            <th>Password</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Inline decrypt function so we don't need db_config.php modified
                                        function inline_decrypt($hash) {
                                            if (ctype_xdigit($hash)) {
                                                $decoded = base64_decode(hex2bin($hash));
                                                if ($decoded) return str_rot13($decoded);
                                            }
                                            return $hash; // fallback
                                        }

                                        $sql = "SELECT distinct user_id, user_name, user_pwd FROM expert_login WHERE active_status='A'";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) {
                                            ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($row["user_name"]); ?></strong></td>
                                                <td><strong><?php echo htmlspecialchars($row["user_id"]); ?></strong></td>
                                                <td><span
                                                        class="text-muted"><?php echo htmlspecialchars(inline_decrypt($row["user_pwd"])); ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                        data-target="#editModal"
                                                        onclick="edit_it('<?php echo htmlspecialchars($row["user_id"]); ?>','<?php echo htmlspecialchars($row["user_name"]); ?>','<?php echo htmlspecialchars(inline_decrypt($row["user_pwd"])); ?>')">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <a href="assign_role.php?user_id=<?php echo urlencode($row["user_id"]); ?>&user_name=<?php echo urlencode($row["user_name"]); ?>"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-user-tag"></i> Roles
                                                    </a>
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="delete_it('<?php echo htmlspecialchars($row["user_id"]); ?>')">
                                                        <i class="fas fa-trash"></i>
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus mr-1"></i> Add New User</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>User Name</strong></label>
                        <input type="text" id="user_name" class="form-control" placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label><strong>User ID</strong></label>
                        <input type="text" id="user_id" class="form-control" placeholder="Enter username/login ID">
                    </div>
                    <div class="form-group">
                        <label><strong>Generated Password</strong></label>
                        <input type="text" id="user_pwd" class="form-control" readonly>
                        <small class="form-text text-muted">A secure password is automatically generated.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="add">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-edit mr-1"></i> Edit User Details</h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>User ID</strong></label>
                        <input type="text" id="edit_user_id" class="form-control bg-light" readonly>
                    </div>
                    <div class="form-group">
                        <label><strong>User Name</strong></label>
                        <input type="text" id="edit_user_name" class="form-control" placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label><strong>Current Password</strong></label>
                        <input type="text" id="edit_user_pwd" class="form-control bg-light" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="edit">Update</button>
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
        function generateRandomString(length = 7) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            for (let i = 0; i < length; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }

        $(function () {
            // Initialize datatable
            $('#dataTable').DataTable();

            $('#add_opener').on('click', function () {
                // Auto-generate password when Add button is clicked
                $("#user_pwd").val(generateRandomString());
            });

            $('#add').on('click', function (e) {
                let user_id = $("#user_id").val().trim();
                let user_name = $("#user_name").val().trim();
                let user_pwd = $("#user_pwd").val().trim();

                if (!user_id || !user_name) {
                    alert("Please fill out both User ID and User Name.");
                    return;
                }

                $.post("add_user.php", {
                    user_id: user_id,
                    user_name: user_name,
                    user_pwd: user_pwd
                }, function (data, status) {
                    alert($.trim(data));
                    location.reload();
                });
            });

            $('#edit').on('click', function (e) {
                let user_id = $("#edit_user_id").val().trim();
                let user_name = $("#edit_user_name").val().trim();
                let user_pwd = $("#edit_user_pwd").val().trim();

                if (!user_name) {
                    alert("User Name cannot be empty.");
                    return;
                }

                $.post("edit_user.php", {
                    user_id: user_id,
                    user_name: user_name,
                    user_pwd: user_pwd
                }, function (data, status) {
                    alert($.trim(data));
                    location.reload();
                });
            });
        });

        function delete_it(user_id) {
            if (confirm("Are you sure you want to permanently delete user '" + user_id + "'?")) {
                $.post("delete_user.php", {
                    user_id: user_id
                }, function (data, status) {
                    alert($.trim(data));
                    location.reload();
                });
            }
        }

        function edit_it(user_id, user_name, user_pwd) {
            $("#edit_user_id").val(user_id);
            $("#edit_user_name").val(user_name);
            $("#edit_user_pwd").val(user_pwd);
        }
    </script>
</body>

</html>