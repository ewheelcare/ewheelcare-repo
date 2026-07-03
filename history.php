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
    background-color: #333;
    color: #fff;
    padding: 6px 10px;
    border-radius: 4px;
    white-space: nowrap;
    font-size: 12px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s;
    z-index: 1000;
  }

  .tooltip-wrapper.show .tooltip-text {
    visibility: visible;
    opacity: 1;
  }

  /* Optional arrow */
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
</style>
</style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
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
					<div class="col-md-12">
					<form action="#" method="post">
					<table class="table"><tr><td><input class="form-control" id="vehicle" name="vehicle"> </td><td>
					<button class="bthn btn-primary" type="submit">GO</button>
					</td></tr></table>
					</Form>
					
					</div>
				<div class="col-md-12">
				<?php 
				$vehicle_no=$_POST["vehicle"];
				
				$sql="SELECT vehicle_id,vehicle_model,vehicle_tyre,c.company_name,c.owner_name,c.customer_id FROM vehicle v ,customer c where vehicle_no='".$vehicle_no."' and v.customer_id=c.customer_id";
//echo $sql;
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
	$vehicle_id=$row["vehicle_id"]; 
	$vehicle_model=$row["vehicle_model"];
	$no_of_wheels=$row["vehicle_tyre"];
	$customer_id=$row["customer_id"];
	$company_name=$row["company_name"];
	$owner_name=$row["owner_name"];
	}
				?>
<table class="table table-bordered">
<tr>
<td>Vehicle : <?php echo $vehicle_no;?>(<?php echo $vehicle_id;?>)<br>
Model : <?php echo $vehicle_model;?>, No of Wheels : <?php echo $no_of_wheels;?>
</td>
<td>
Company : <?php echo $company_name;?>(<?php echo $customer_id;?>)<br>
Owner : <?php echo $owner_name;?>
</td>
</tr>

</table>
<h4>History of Replacements</h4>
<table class="table table-striped">		
<thead>
<tr>
<td>Date</td>
<td>Item</td>
<td>Nos</td>
<td>Kms run</td>
</tr>
</thead>
<tbody>
<tbody>		
<?php $sql="SELECT c.dc_date, i.item_name,i.item_description,d.despatched,d.odometer FROM delivery_challan_det d,delivery_challan c, item i where d.dc_id=c.dc_id and d.item_id=i.item_id and d.vehicle='".$vehicle_no."'"	;			
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
	$vehicle_id=$row["vehicle_id"]; ?>				
<tr>
<td><?php echo $row["dc_date"];?></td>
<td><?php echo $row["item_name"];?>, <?php echo $row["item_description"];?></td>
<td><?php echo $row["despatched"];?></td>
<td><?php echo $row["odometer"];?></td>
</tr>				
<?php }?>				
	</tbody></table>			</div>	
					
					
					
							</div>
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
   

   
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
   <?php include "modals.php";?>
   <?php include "footer_include.php";?>
	<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
	 <script src="js/demo/datatables-demo.js"></script>
