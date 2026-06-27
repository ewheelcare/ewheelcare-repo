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
					<button class="btn btn-danger" id="invoice"  onclick="get_dc()">Get Challan</button>&nbsp;
				
					
					<?php 
					
					
				$trans_id=""; $trans_date=""; $details=""; $customer=""; $trans_amount=""; $pending=""; $gst=""; $tally=""; $created_by=""; $created_on=""; $modified_by=""; $modified_on=""; $active_status=""; $VEHICLE_NO=""; $VEHICLE_MODEL=""; $NO_OF_WHEELS=""; $COMPANY_NAME=""; $CUSTOMER_NAME=""; $CUSTOMER_ADDRESS=""; $CUSTOMER_GST=""; $VEHICLE_ODOMETER=""; $VEHICLE="";
	$dc_id=isset($_GET["dc_id"])?$_GET["dc_id"]:"";
					$sql="SELECT dc_id, dc_date, COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, CUSTOMER_MOBILE,active_status,customer  FROM delivery_challan where dc_id='".$dc_id."'";
//echo $sql;
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {$trans_id=$row["trans_id"]; 
$trans_date=$row["dc_date"]; 
$dc_id=$row["dc_id"]; 
 $customer=$row["customer"]; 
 $COMPANY_NAME=$row["COMPANY_NAME"]; 
 $CUSTOMER_NAME=$row["CUSTOMER_NAME"]; 
 $CUSTOMER_ADDRESS=$row["CUSTOMER_ADDRESS"]; 
 $CUSTOMER_GST=$row["CUSTOMER_GST"]; 
 $CUSTOMER_MOBILE=$row["CUSTOMER_MOBILE"]; 
 $customer_id=$row["customer"];

 //echo "===";
 //echo $trans_date."====". $customer."====". $CUSTOMER_NAME."===".$CUSTOMER_MOBILE;
}else{
	
	echo "good jmsm";
}
			
?>
<div class="col-md-12">
					
<br><span class="alert alert-info">Customer Details</span><br>
<form action="#" method="post">
<table class="table">
<tr>
<td>Search Customer<input class="form-control" name="customer_search" id="customer_search" value="" list="cust_list">
<datalist id="cust_list">
 <?php $sql="SELECT customer_id,company_name,owner_name,owner_mobile FROM customer";
$result = $conn->query($sql);
while ($row1 = $result->fetch_assoc()) {
	/*$customer_id=$row1["customer_id"];
$COMPANY_NAME=$row1["company_name"];
$CUSTOMER_NAME=$row1["owner_name"];
$CUSTOMER_MOBILE=$row1["owner_mobile"];*/

	?>
<option value="<?php echo $row1["customer_id"]?>~<?php echo $row1["company_name"]?>~<?php echo $row1["owner_name"]?>~<?php echo $row1["owner_mobile"]?>"></option>
<?php }?>
</datalist>

</td>
<td><br><input type="submit" name="submit" value="Submit" class="btn btn-sm btn-danger"></td>
</tr>
</table>
</form>
</div>

<div class="col-md-6">
Challan ID :<INPUT CLASS="form-control" name="trans_id" id="trans_id" value="<?php echo $dc_id;?>">
</div><div class="col-md-6">Challan Date :<INPUT CLASS="form-control datepicker" name="trans_date" id="datepicker">
</div>
<div class="col-md-12">

<?php 
$customer = $_POST["customer_search"];
echo $customer;
if(isset($customer)){
$customer_id=explode("~",$customer)[0];
$COMPANY_NAME=explode("~",$customer)[1];
$CUSTOMER_NAME=explode("~",$customer)[2];
$CUSTOMER_MOBILE=explode("~",$customer)[3];

echo $customer_id;
}
?>
<table class="table">
<tr><td>Company Name<input class="form-control" name="company_name" id="company_name" value="<?php echo $COMPANY_NAME;?>"></td>
<td>Customer Name <input class="form-control" name="customer_name" id="customer_name" value="<?php echo $CUSTOMER_NAME;?>"></td>
<td>Mobile 
<input class="form-control" 
       name="customer_mobile" 
       id="customer_mobile" 
       value="<?php echo $CUSTOMER_MOBILE;?>"
       maxlength="10"
       pattern="[0-9]{10}"
}

</td>

<input class="form-control" name="customer" id="customer" value="<?php echo $customer_id;?>" type="hidden">
</td></tr></table>
<div class="col-md-12">
<h5>Already included in Delivery Challan</h5>
<table class="table table-striped"><tr>
<td>Item</td>
<td>Nos to deliver</td>
<td>Vehicle</td>
<td>Odometer</td>

<td>#</td>
</tr>
<?php $sql_old="SELECT d.trans_id, d.subtrans_id, d.item_id,d.despatched,d.vehicle,d.odometer,i.item_name FROM delivery_challan_det d,item i where dc_id='".$dc_id."' and d.item_id=i.item_id AND d.ACTIVE_STATUS='A'";
	//echo $sql_old;
$result_old = $conn->query($sql_old);
while ($row_old = $result_old->fetch_assoc()) {
?>
<tr>
<td><?php echo $row_old["item_name"];?></td>
<td><?php echo $row_old["despatched"];?></td>
<td><?php echo $row_old["vehicle"];?></td>
<td><?php echo $row_old["odometer"];?></td>
<td><button class="btn btn-sm btn-danger" onclick="delete_det('<?php echo $row_old["trans_id"];?>','<?php echo $row_old["subtrans_id"];?>','<?php echo $row_old["item_id"];?>','<?php echo $row_old["despatched"];?>')">X</button></td>
</tr>
<?php }?>
</table>
</div>
<?php $sql = "select s.trans_id,s.trans_date,s.COMPANY_NAME,s.customer,s.trans_amount,s.pending from sales_trans s where s.trans_id in (SELECT distinct d.trans_id FROM sales_trans_det d where ifnull(d.pending,d.qty)>0 and active_status='A') and s.customer='".$customer_id."'"; 
					//echo $sql;
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
?>
<div class="col-md-12">
<div class="alert alert-info">
<b>Trans Date :  <?php echo $row["trans_date"];?>, ID  : <?php echo $row["trans_id"];?></b>
Company :  <?php echo $row["COMPANY_NAME"];?>, Amount : <?php echo $row["trans_amount"];?>, paid :  <?php echo $row["trans_amount"]-$row["pending"];?></div>



<table class="table table-striped"><tr>
<td>Item</td>
<td>Pending</td>
<td>Nos to deliver</td>
<td>Vehicle</td>
<td>Odometer</td>

<td>#</td>
</tr>
<?php $sql_det="SELECT d.trans_id,d.subtrans_id,d.item_id,ifnull(d.pending,d.qty) PENDING,i.item_name FROM sales_trans_det d ,item i where ifnull(d.pending,d.qty)>0 and  d.active_status='A' AND d.trans_id='". $row["trans_id"]."' and i.item_id=d.item_id and concat(d.trans_id,d.subtrans_id,d.item_id ) not in (select concat(trans_id,subtrans_id,item_id) from delivery_challan_det where dc_id='".$dc_id."' and active_status='A')";
$result_det = $conn->query($sql_det);
while ($row_det = $result_det->fetch_assoc()) {
?>
<tr>
<td><?php echo $row_det["item_name"];?> </td>
<td><?php echo $row_det["PENDING"];?></td>
<td><INPUT TYPE="text" class="form-control" name="qty_<?php echo $row_det["trans_id"];?>_<?php echo $row_det["subtrans_id"];?>_<?php echo $row_det["item_id"];?>" id="qty_<?php echo $row_det["trans_id"];?>_<?php echo $row_det["subtrans_id"];?>_<?php echo $row_det["item_id"];?>" min=0 max="<?php echo $row_det["PENDING"];?>"></td>
<td><INPUT TYPE="text" class="form-control" name="vehicle_<?php echo $row_det["trans_id"];?>_<?php echo $row_det["subtrans_id"];?>_<?php echo $row_det["item_id"];?>" id="vehicle_<?php echo $row_det["trans_id"];?>_<?php echo $row_det["subtrans_id"];?>_<?php echo $row_det["item_id"];?>" list="vehicle_<?php echo $row_det["trans_id"];?>_<?php echo $row_det["subtrans_id"];?>_<?php echo $row_det["item_id"];?>_list" >

<datalist id="vehicle_<?php echo $row_det["trans_id"];?>_<?php echo $row_det["subtrans_id"];?>_<?php echo $row_det["item_id"];?>_list">
<?php  

// $sql_veh="SELECT vehicle_no FROM vehicle where customer_id";

$sql_veh="SELECT vehicle_no
          FROM vehicle
          WHERE customer_id='".$customer_id."'";

$result_veh = $conn->query($sql_veh);
while ($row_veh = $result_veh->fetch_assoc()) {?>
<option value="<?php echo $row_veh["vehicle_no"]?>"></option>
<?php }?>
</datalist>

</td>
<td><INPUT TYPE="text" class="form-control" name="odo_<?php echo $row_det["trans_id"];?>_<?php echo $row_det["subtrans_id"];?>_<?php echo $row_det["item_id"];?>" id="odo_<?php echo $row_det["trans_id"];?>_<?php echo $row_det["subtrans_id"];?>_<?php echo $row_det["item_id"];?>"></td>

<td><button class="btn btn-secondary" onclick="save_dc_det('<?php echo $row_det["trans_id"];?>','<?php echo $row_det["subtrans_id"];?>','<?php echo $row_det["item_id"];?>')">Save</button></td>
</tr>
<?php }?>
</table>
</div>
<?php }?>
</div>

</div>
 <div class="card-body"></div>
                            
									</div>
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

<script>

document.getElementById("customer_search").addEventListener("input", function () {
  console.log(document.getElementById("customer_search").value);
  let parts= document.getElementById("customer_search").value.split("~");
  $("#customer").val(parts[0]);
   $("#company_name").val(parts[1]);
     $("#customer_name").val(parts[2]);
	   $("#customer_mobile").val(parts[3]);
});

 $(function () {
    $("#datepicker").datepicker({
        dateFormat: "dd-mm-yy"  // Set format to yyyy-mm-dd
      }).datepicker("setDate", new Date());;
	  $("#edit_datepicker").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
      });
  });
  
function save_dc_det(trans_id,sub_trans_id,item_id){
    
    let mobile = $("#customer_mobile").val();

	let dc_id = document.getElementById("trans_id").value;
	if (!isValidMobile(mobile)) {
    alert("Enter valid 10 digit mobile number");
    $("#customer_mobile").focus();
    return;
}
	if(dc_id=="" || dc_id==null){
			$.post("add_dc.php",
  {
   
	 customer_mobile: $("#customer_mobile").val(),
	 company_name: $("#company_name").val(),
	  customer_name: $("#customer_name").val(),
	    customer: $("#customer").val(),
		trans_date:$("#datepicker").val()
  },
  function(data, status){
   
document.getElementById("trans_id").value = $.trim(data);

save_dc_det_only(trans_id,sub_trans_id,item_id);
  });
	}else{
	save_dc_det_only(trans_id,sub_trans_id,item_id);	
	}
	
	
}
function save_dc_det_only(trans_id,sub_trans_id,item_id){
	let selector_qty="qty_"+trans_id+"_"+sub_trans_id+"_"+item_id;
	let selector_vehicle="vehicle_"+trans_id+"_"+sub_trans_id+"_"+item_id;
	let selector_odo="odo_"+trans_id+"_"+sub_trans_id+"_"+item_id;
	let  qty = document.getElementById(selector_qty).value;
	let  vehicle = document.getElementById(selector_vehicle).value;
	let  odo = document.getElementById(selector_odo).value;
	let min_qty =  document.getElementById(selector_qty).min;
		let max_qty =  document.getElementById(selector_qty).max;
		if(parseInt(min_qty)<=parseInt(qty) && parseInt(max_qty)>=parseInt(qty)){
			$.post("add_dc_det.php",
  {
   
	 dc_id: $("#trans_id").val(),
	 trans_id: trans_id,
	  sub_trans_id: sub_trans_id,
	    item_id: item_id,
		qty:qty,
		vehicle:vehicle,
		odometer:odo
  },
  function(data, status){
   
location.href="generate_dc.php?dc_id="+$.trim(data);

//save_dc_det_only(trans_id,sub_trans_id,item_id);
  });
			
		}
	
}
function delete_det(trans_id,sub_trans_id,item_id,despatched){
		$.post("del_dc_det.php",
  {
   
	 dc_id: $("#trans_id").val(),
	 trans_id: trans_id,
	  sub_trans_id: sub_trans_id,
	    item_id: item_id,
		qty:despatched
  },
  function(data, status){
   
location.href="generate_dc.php?dc_id="+$.trim(data);

//save_dc_det_only(trans_id,sub_trans_id,item_id);
  });
	
}
function get_dc(){
	let trans_id= $("#trans_id").val();
	let url="delivery_receipt.php?trans_id="+trans_id;
	window.open(url, '_blank');
	//window.location.href=url;
}
function isValidMobile(mobile) {
    return /^[6-9]\d{9}$/.test(mobile);
}
</script>




  

</body>

</html>