<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
            <img src="img/expert_logo.png" class="img img-responsive" style="width:90%">
        </div>
        <div class="sidebar-brand-text mx-3">Expert <sup>Wheel Care</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <?php if (isset($_COOKIE["user_id"])) { ?>
        <?php if (isset($_COOKIE["DASHBOARD"]) || isset($_COOKIE["SA"])) { ?>
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Home</span>
                </a>
            </li>
        <?php } ?>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Interface
        </div>

        <!-- Nav Item - Configure Menu -->
        <?php if (isset($_COOKIE["MASTER"]) || isset($_COOKIE["SA"])) { ?>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true"
                    aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Configure</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Master Maintenance:</h6>
                        <a class="collapse-item" href="module.php?param=customer">Customer</a>
                        <a class="collapse-item" href="module.php?param=vendor">Vendor</a>
                        <a class="collapse-item" href="module.php?param=vehicle">Vehicle</a>
                        <a class="collapse-item" href="module.php?param=location">Location</a>
                        <a class="collapse-item" href="module.php?param=shop">Shop</a>
                        <a class="collapse-item" href="module.php?param=storagelocation">Storage Location</a>
                        <a class="collapse-item" href="module.php?param=tyre_type">Tyre Types</a>
                        <a class="collapse-item" href="module.php?param=item">Item</a>
                        <a class="collapse-item" href="module.php?param=service">Service</a>
                        <a class="collapse-item" href="module.php?param=company">Company</a>
                        <a class="collapse-item" href="module.php?param=crm">CRM</a>
                        <a class="collapse-item" href="module.php?param=paytype">Pay Type</a>
                        <a class="collapse-item" href="module.php?param=account">Account</a>
                        <a class="collapse-item" href="module.php?param=make">Make</a>
                        <a class="collapse-item" href="module.php?param=model">Model</a>
                        <a class="collapse-item" href="module.php?param=servicecost">Service Cost</a>
                        <a class="collapse-item" href="module.php?param=inventory">Inventory</a>
                        <a class="collapse-item" href="module.php?param=mech">Mechanic</a>
                        <a class="collapse-item" href="module.php?param=headofaccount">Head of Account</a>
                        <a class="collapse-item" href="privilege_config.php">Privilage Configuration</a>
                    </div>
                </div>
            </li>
        <?php } ?>

        <?php if (isset($_COOKIE["ENTRY"]) || isset($_COOKIE["SA"])) { ?>
            <!-- Nav Item - Services -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo1" aria-expanded="true"
                    aria-controls="collapseTwo1">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Services</span>
                </a>
                <div id="collapseTwo1" class="collapse" aria-labelledby="headingTwo1" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">ENTRY</h6>
                        <a class="collapse-item" href="go_for_services.php?GSN=Y">Service (GST)</a>
                        <a class="collapse-item" href="go_for_services.php?GSN=N">Service (Non-GST)</a>
                    </div>
                    <?php if (isset($_COOKIE["REPORT"]) || isset($_COOKIE["SA"])) { ?>
                        <div class="bg-white py-2 collapse-inner rounded mt-2">
                            <h6 class="collapse-header">REPORTS</h6>
                            <a class="collapse-item" href="report_service.php">Service</a>
                            <a class="collapse-item" href="report_service_new.php">Service Download</a>
                            <a class="collapse-item" href="report_service_trans.php">Service (Transaction)</a>
                            <a class="collapse-item" href="report_service_wise_summary.php">Service (Summary)</a>
                            <a class="collapse-item" href="report_overall.php">Overall Service (Summary)</a>
                            <a class="collapse-item" href="report_service_ageing.php">Ageing Service</a>
                        </div>
                    <?php } ?>
                </div>
            </li>

            <!-- Nav Item - Sales -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo2" aria-expanded="true"
                    aria-controls="collapseTwo2">
                    <i class="fas fa-fw fa-car"></i>
                    <span>Sales</span>
                </a>
                <div id="collapseTwo2" class="collapse" aria-labelledby="headingTwo2" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">ENTRY</h6>
                        <a class="collapse-item" href="go_for_sales.php?GSN=Y">Sales (GST)</a>
                        <a class="collapse-item" href="delivery_challan.php">Delivery Challan</a>
                </div>
                    <?php if (isset($_COOKIE["REPORT"]) || isset($_COOKIE["SA"])) { ?>
                        <div class="bg-white py-2 collapse-inner rounded mt-2">
                            <h6 class="collapse-header">REPORTS</h6>
                            <a class="collapse-item" href="report_sales.php">Sales</a>
                            <a class="collapse-item" href="report_sales_aging.php">Ageing Sales</a>
                            <a class="collapse-item" href="report_customer_ledger.php">Cutomer Ledger</a>
                        </div>
                    <?php } ?>
                </div>
            </li>

            <!-- Nav Item - Procurement -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo3" aria-expanded="true"
                    aria-controls="collapseTwo3">
                    <i class="fas fa-fw fa-box-open"></i>
                    <span>Procurement</span>
                </a>
                <div id="collapseTwo3" class="collapse" aria-labelledby="headingTwo3" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">ENTRY</h6>
                        <a class="collapse-item" href="go_for_purchase.php?GSN=Y">Purchases (GST)</a>
                    </div>
                    <?php if (isset($_COOKIE["REPORT"]) || isset($_COOKIE["SA"])) { ?>
                        <div class="bg-white py-2 collapse-inner rounded mt-2">
                            <h6 class="collapse-header">REPORTS</h6>
                            <a class="collapse-item" href="report_receipt.php">Purchase</a>
                        </div>
                    <?php } ?>
                </div>
            </li>

            <!-- Nav Item - Inventory -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventory"
                    aria-expanded="true" aria-controls="collapseInventory">
                    <i class="fas fa-fw fa-boxes"></i>
                    <span>Inventory</span>
                </a>
                <div id="collapseInventory" class="collapse" aria-labelledby="headingInventory" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">ENTRY</h6>
                        <?php if (isset($_COOKIE["INVENTORY"]) || isset($_COOKIE["SA"])) { ?>

                            <a class="collapse-item" href="shop_to_shop_transfer.php">Shop to Shop Transfer</a>
                        <?php } ?>
                    </div>
                    <?php if (isset($_COOKIE["REPORT"]) || isset($_COOKIE["SA"])) { ?>
                        <div class="bg-white py-2 collapse-inner rounded mt-2">
                            <h6 class="collapse-header">REPORTS</h6>
                            <a class="collapse-item" href="inventory_report.php">Inventory Report</a>
                        </div>
                    <?php } ?>
                </div>
            </li>

            <!-- Nav Item - Financials -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo4" aria-expanded="true"
                    aria-controls="collapseTwo4">
                    <i class="fas fa-fw fa-money-bill-wave"></i>
                    <span>Financials</span>
                </a>
                <div id="collapseTwo4" class="collapse" aria-labelledby="headingTwo4" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">ENTRY</h6>
                        <a class="collapse-item" href="payments.php?nature=CREDIT">Receipts</a>
                        <a class="collapse-item" href="module.php?param=expenditure">Expenditure</a>
                    </div>
                    <?php if (isset($_COOKIE["REPORT"]) || isset($_COOKIE["SA"])) { ?>
                        <div class="bg-white py-2 collapse-inner rounded mt-2">
                            <h6 class="collapse-header">REPORTS</h6>
                            <a class="collapse-item" href="report_payment.php">Payment</a>
                            <a class="collapse-item" href="report_financial_statement.php">Financial Statement</a>
                            <a class="collapse-item" href="profit_analysis.php">Profit Analysis</a>
                        </div>
                    <?php } ?>
                </div>
            </li>
        <?php } ?>

        <!-- Nav Item - User Management -->
        <?php if (isset($_COOKIE["SA"])) { ?>
            <li class="nav-item">
                <a class="nav-link" href="user.php">
                    <i class="fas fa-fw fa-users"></i>
                    <span>User Management</span>
                </a>
            </li>
        <?php } ?>

        <!-- Nav Item - Profile -->
        <li class="nav-item">
            <a class="nav-link" href="profile.php">
                <i class="fas fa-fw fa-user-cog"></i>
                <span>Profile & Password</span>
            </a>
        </li>

    <?php } ?>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>