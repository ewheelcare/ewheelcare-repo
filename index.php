<?php require 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>

<?php include "header_include.php";?>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
		 <?php include "db_config.php";?>
        <?php include "sidemenu.php";?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include "topmenu.php";?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                  
                    <!-- Content Row -->
                    <div class="row">
					<!--<?php if(isset($_COOKIE["user_id"])){?>-->
					<div class="container mt-4">
<div class="row">

<!-- Services Card -->
<div class="col-md-6">
<div class="card shadow-lg border-0">
<div class="card-header text-center" style="background-color:black;color:white;font-weight:bold;text-align:center;text-transform:uppercase">
<h4><i class="fas fa-fw fa-wrench"></i> Services</h4>
</div>

<div class="card-body text-center">

<a href="go_for_services.php?GSN=N" class="btn btn-danger btn-lg m-2">
<img src="img/nongst.png" style="width:35px;"> Non GST
</a>

<a href="go_for_services.php?GSN=Y" class="btn btn-success btn-lg m-2">
<img src="img/gst.png" style="width:35px;"> GST
</a>

</div>
</div>
</div>


<!-- Tyre Sales Card -->
<div class="col-md-6">
<div class="card shadow-lg border-0">
<div class="card-header text-center" style="background-color:black;color:white;font-weight:bold;text-align:center;text-transform:uppercase">
<h4><i class="fas fa-fw fa-cog"></i> Tyre Sales</h4>
</div>

<div class="card-body text-center">

<a href="go_for_sales.php?GSN=N" class="btn btn-danger btn-lg m-2">
<img src="img/gst.png" style="width:35px;"> GST
</a>

</div>
</div>
</div>


<div class="col-md-6" style="margin-top:7px">
<div class="card shadow-lg border-0">
<div class="card-header text-center" style="background-color:black;color:white;font-weight:bold;text-align:center;text-transform:uppercase">
<h4><i class="fas fa-fw fa-cog"></i> PROCUREMENT</h4>
</div>

<div class="card-body text-center">

<a href="go_for_purchase.php?GSN=N" class="btn btn-danger btn-lg m-2">
<img src="img/gst.png" style="width:35px;"> GST
</a>

</div>
</div>
</div>

<div class="col-md-6" style="margin-top:7px">
<div class="card shadow-lg border-0">
<div class="card-header text-center" style="background-color:black;color:white;font-weight:bold;text-align:center;text-transform:uppercase">
<h4><i class="fas fa-fw fa-cog"></i> PAYMENTS</h4>
</div>

<div class="card-body text-center">

<a href="payments.php?nature=CREDIT" class="btn btn-success btn-lg m-2">
<img src="img/gst.png" style="width:35px;"> RECEIPTS
</a>
<a href="payments.php?nature=DEBIT" class="btn btn-danger btn-lg m-2">
<img src="img/gst.png" style="width:35px;"> PAYMENTS
</a>
<a href="module.php?param=expenditure" class="btn btn-warning btn-lg m-2">
<img src="img/gst.png" style="width:35px;"> EXPENDITURE
</a>
</div>
</div>
</div>

</div>
</div>
					
					<!--<?php }else{?>-->
					<!--<img class="img img-responsive tyre3d" src="img/tyre.png" style="width:90%;margin:auto"/>-->
					<!--<?php }?>-->
     <!--               </div>-->

                    

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
           <?php include "footer.php";?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
   <?php include "modals.php";?>
<?php include "footer_include.php";?>
   

</body>

</html>