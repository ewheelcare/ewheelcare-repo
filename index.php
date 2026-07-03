<?php require 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "header_include.php"; ?>
    <title>Dashboard</title>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include "db_config.php"; ?>
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
                        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        
                        <!-- Services Card -->
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card shadow h-100 border-left-primary">
                                <div class="card-header bg-dark text-white text-center text-uppercase font-weight-bold">
                                    <h5 class="m-0"><i class="fas fa-fw fa-wrench mr-2"></i> Services</h5>
                                </div>
                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                    <div>
                                        <a href="go_for_services.php?GSN=N" class="btn btn-danger btn-lg m-2 shadow-sm">
                                            <i class="fas fa-tools mr-1"></i> Non GST
                                        </a>
                                        <a href="go_for_services.php?GSN=Y" class="btn btn-success btn-lg m-2 shadow-sm">
                                            <i class="fas fa-file-invoice-dollar mr-1"></i> GST
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tyre Sales Card -->
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card shadow h-100 border-left-info">
                                <div class="card-header bg-dark text-white text-center text-uppercase font-weight-bold">
                                    <h5 class="m-0"><i class="fas fa-fw fa-car mr-2"></i> Tyre Sales</h5>
                                </div>
                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                    <div>
                                        <a href="go_for_sales.php?GSN=Y" class="btn btn-info btn-lg m-2 shadow-sm">
                                            <i class="fas fa-shopping-cart mr-1"></i> GST
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Procurement Card -->
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card shadow h-100 border-left-warning">
                                <div class="card-header bg-dark text-white text-center text-uppercase font-weight-bold">
                                    <h5 class="m-0"><i class="fas fa-fw fa-box-open mr-2"></i> Procurement</h5>
                                </div>
                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                    <div>
                                        <a href="go_for_purchase.php?GSN=Y" class="btn btn-warning btn-lg m-2 shadow-sm text-dark">
                                            <i class="fas fa-shopping-basket mr-1"></i> GST
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payments Card -->
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card shadow h-100 border-left-success">
                                <div class="card-header bg-dark text-white text-center text-uppercase font-weight-bold">
                                    <h5 class="m-0"><i class="fas fa-fw fa-money-bill-wave mr-2"></i> Financials</h5>
                                </div>
                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                    <div>
                                        <a href="payments.php?nature=CREDIT" class="btn btn-success btn-lg m-2 shadow-sm">
                                            <i class="fas fa-hand-holding-usd mr-1"></i> Receipts
                                        </a>
                                        <a href="payments.php?nature=DEBIT" class="btn btn-danger btn-lg m-2 shadow-sm">
                                            <i class="fas fa-money-check-alt mr-1"></i> Payments
                                        </a>
                                        <a href="module.php?param=expenditure" class="btn btn-secondary btn-lg m-2 shadow-sm">
                                            <i class="fas fa-file-invoice mr-1"></i> Expenditure
                                        </a>
                                    </div>
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
    <?php include "footer_include.php"; ?>

</body>
</html>