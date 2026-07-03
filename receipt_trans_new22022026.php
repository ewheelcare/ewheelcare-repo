
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
.form-control{font-size:12px}
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
<link href="multi/searchableOptionList.css" rel="stylesheet">
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
					<!--<button class="btn btn-danger" id="invoice"  onclick="get_invoice()">Get Invoice</button>&nbsp;
					<button class="btn btn-primary" id="jobcard"  onclick="get_jobcard()">Get JobCard</button>-->
					
					<?php 
					//echo $trans_id;
					$gst = isset($_COOKIE["gst"]) ? $_COOKIE["gst"] : "";
					//echo $_COOKIE["gst"];
					//echo $gst;
					
				$trans_id=""; $trans_date=""; $details=""; $customer=""; $trans_amount=""; $pending=""; $gst=""; $tally=""; $created_by=""; $created_on=""; $modified_by=""; $modified_on=""; $active_status=""; $VEHICLE_NO=""; $VEHICLE_MODEL=""; $NO_OF_WHEELS=""; $COMPANY_NAME=""; $CUSTOMER_NAME=""; $CUSTOMER_ADDRESS=""; $CUSTOMER_GST=""; $VEHICLE_ODOMETER=""; $VEHICLE="";
	$trans_id=isset($_GET["trans_id"])?$_GET["trans_id"]:"";
					$sql="SELECT trans_id, trans_date, details, vendor, trans_amount, pending, gst, tally, created_by, created_on, modified_by, modified_on, active_status, COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, customer_mobile,invoice_no,discount FROM receipt_trans where trans_id='".$trans_id."'";
//echo $sql;
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {$trans_id=$row["trans_id"]; 
$trans_date=$row["trans_date"]; 
$details=$row["trans_id"]; 
 $customer=$row["vendor"]; 
 $trans_amount=$row["trans_amount"]; 
 $pending=$row["pending"]; 
 $gst=$row["gst"]; 
 $tally=$row["tally"]; 
 $invoice_no=$row["invoice_no"]; 
 $created_by=$row["created_by"]; 
 $created_on=$row["created_on"];  
 $modified_by=$row["modified_by"];  
 $modified_on=$row["modified_on"]; 
 $active_status=$row["active_status"]; 

 $NO_OF_WHEELS=$row["NO_OF_WHEELS"]; 
 $COMPANY_NAME=$row["COMPANY_NAME"]; 
 $CUSTOMER_NAME=$row["CUSTOMER_NAME"]; 
 $CUSTOMER_ADDRESS=$row["CUSTOMER_ADDRESS"]; 
 $CUSTOMER_GST=$row["CUSTOMER_GST"]; 
 $CUSTOMER_MOBILE=$row["customer_mobile"]; 
 $active_status=$row["active_status"]; 
$grand_discount=$row["discount"]; 
 if($active_status=="A"){
	 $final_disabler="disabled";
 }
 //echo "===";
// echo $trans_date."====". $customer."====". $CUSTOMER_NAME;
}

if(isset($gst) && $gst!=""){
	//echo "here";
}else{
	$gst = isset($_COOKIE["gst"]) ? $_COOKIE["gst"] : "";
}				
?>
<div class="col-md-12">
	<div class="alert alert-warning" style="text-align:center;font-weight:bold"><b>PURCHASE </b><BR>GST:<?PHP echo $gst;?></div><br><br>
					
<span class="alert alert-info">Vendor Details</span>
<table class="table">
<tr><td>Date<input type="text" id="datepicker" class="form-control"><br>
	Invoice No <input class="form-control"  name="invoice_no" id="invoice_no" value="<?php echo $invoice_no;?>">

	</td>
<td>Trans No  <input class="form-control" readonly name="trans_id" id="trans_id" value="<?php echo $trans_id;?>"><br>
Search vendor<br><select class="form-control" name="customer_search" id="customer_search" multiple="multiple" style="max-width:300px!important">
 <?php $sql="SELECT vendor_id,company_name,owner_name,owner_mobile FROM vendor ORDER BY UPPER(company_name)";
$result = $conn->query($sql);
while ($row1 = $result->fetch_assoc()) {?>
	<option value="<?php echo $row1["company_name"]?>~<?php echo $row1["owner_name"]?>~<?php echo $row1["owner_mobile"]?>~<?php echo $row1["vendor_id"]?>"><?php echo $row1["company_name"]?>,<?php echo $row1["owner_name"]?> </option>
<?php }?>
</select></td>

<!--<a href="module.php?param=customer" class="btn btn-success" target="_blank">Add Customer, If not exists</a>
<a href="module.php?param=vehicle" class="btn btn-danger" target="_blank">Add Vehicle, If not exists</a>-->
<input class="form-control" name="customer" id="customer" value="<?php echo $customer;?>" type="hidden">
</td>
</tr>
</table>
</div>
<div class="col-md-12">
<span class="alert alert-success">Vendor Details</span>
<table class="table">
<tr><td>Company Name<input class="form-control" name="company_name" id="company_name" value="<?php echo $COMPANY_NAME;?>">
<br>Mobile <input class="form-control" name="customer_mobile" id="customer_mobile" value="<?php echo $CUSTOMER_MOBILE;?>"></td>

<?php if((isset($_COOKIE["gst"]) && $_COOKIE["gst"]=="Y")||($gst=="Y")){$visibility="";}else{$visibility="none";}?>
<td style="display:<?php echo $visibility;?>">GST <input class="form-control" name="customer_gst" id="customer_gst" value="<?php echo $CUSTOMER_GST;?>" list="gst_list" onfocus="get_gst()" autocomplete="off" onblur="set_gst()">
<datalist id="gst_list">
</datalist><br>Address <input class="form-control" name="customer_address" id="customer_address" value="<?php echo $CUSTOMER_ADDRESS;?>" list="address_list" onfocus="get_address()" autocomplete="off">
<datalist id="address_list">
</datalist>
<input class="form-control" name="customer" id="customer" value="<?php echo $customer;?>" type="hidden">
</td></tr></table>
</div>


<div class="col-md-12">
<select class="form-control" name="item_search" id="item_search" list="item_list" <?php echo $final_disabler;?> multiple="multiple" style="max-width:700px!important">
<?php  $sql="SELECT item_id,upper(item_name) item_name,item_description,cost,ifnull(purchase_tax_cgst,'9') purchase_tax_cgst,ifnull(purchase_tax_igst,'9') purchase_tax_igst FROM item order by upper(item_name)";
$result = $conn->query($sql);
while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1["item_name"]?>~<?php echo $row1["item_description"]?>~<?php echo $row1["cost"]?>~<?php echo $row1["purchase_tax_cgst"]?>~<?php echo $row1["purchase_tax_igst"]?>~<?php echo $row1["item_id"]?>"><?php echo $row1["item_name"]?></option>
<?php }?>
</select>


<table class="table table-striped">
<thead>
<tr>
<tr>
<td>Item</td>
	<td>Qty</td>
<td>Taxable Price</td>

<td>Unit Cost</td>
<td style="display:none">%CGST</td>
<td style="display:none">%SGST</td>
<td style="display:none">%IGST</td>
<td>CGST</td>
<td>SGST</td>
<td>IGST</td>
<td>Discount</td>
<td>Total</td>
<td>Remarks</td>
<td></td>
</tr>
</thead>
<tbody>
<?php
$sql_det="SELECT t.subtrans_id,t.item_id,t.cost,price,ifnull(t.discount,'0') discount,t.qty,t.total, t.tax,t.tax_amount,t.tax_sgst,t.total,t.remarks,t.tax_amount_sgst,t.tax_igst,t.tax_amount_igst,i.item_name,t.price FROM receipt_trans_det t,item i where i.item_id=t.item_id and t.trans_id='".$trans_id."' and t.trans_id!='' and t.active_status='A' order by t.subtrans_id";	
$result_det = $conn->query($sql_det);
//echo $sql_det;
$display="none";
$readonly="";
$slno=0;
while ($row_det = $result_det->fetch_assoc()) {
	$det_price=$row_det["price"];
	$det_item_name=$row_det["item_name"];	
		
		if (substr($CUSTOMER_GST, 0, strlen("37")) === "37"){
			$tax_pc = $row_det["tax"];
			$tax_pc_sgst = $row_det["tax_sgst"];
			$det_tax=$row_det["tax_amount"];
			$det_tax_sgst=$row_det["tax_amount_sgst"];
		}else{
			$tax_pc_igst = $tax_pc_igst;
			$det_tax_igst=$row_det["tax_amount_igst"];
			
		}
		$det_qty=$row_det["qty"];
		$det_discount=$row_det["discount"];
	$det_remarks=$row_det["remarks"];
		$det_cost=$row_det["cost"];
		$det_total = $row_det["total"];
		$det_subtrans_id = $row_det["subtrans_id"];
		$display="";
		$grand_total+=$det_total;
		$price_amount+=$row_det["price"];
		$cgst_amount+=$row_det["tax_amount"];
		$sgst_amount+=$row_det["tax_amount_sgst"];
		$igst_amount+=$row_det["tax_amount_igst"];
		$readonly="readonly";

	
	
	?>
<tr class="purchase_rows" id="row_<?php echo $slno;?>" style="display:<?php echo $display;?>" onfocusout="add_service_det('<?php echo $slno;?>')">
<td><?php echo $det_item_name;?>
<input type="hidden" id="item_id_<?php echo $slno;?>" value="<?php echo $row_det["item_id"];?>">
<input type="hidden" id="subtrans_id_<?php echo $slno;?>" value="<?php echo $det_subtrans_id;?>" >
</td>
	<td><div class="tooltip-wrapper"><input class="form-control tooltip-input"  <?php echo $final_disabler;?>  id="qty_<?php echo $slno;?>" onblur="calculate('<?php echo $slno;?>')" value="<?php echo $det_qty;?>"> <div class="tooltip-text">Once saved cannot be edited. If required, delete and reenter.</div>
</div></td>
	<td><input class="form-control"  <?php echo $final_disabler;?>  id="price_<?php echo $slno;?>" value="<?php echo $det_price;?>" onblur="calculate('<?php echo $slno;?>')"></td>

	<td><input class="form-control" <?php echo $final_disabler;?> readonly value="<?php echo $row_det['cost'];?>" id="cost_<?php echo $slno;?>" value="<?php echo $det_cost;?>" onblur="calculate('<?php echo $slno;?>')"></td>
<td style="display:none"><input style="text-align:right" <?php echo $final_disabler;?> class="form-control" readonly value="<?php echo $tax_pc;?>" id="gst_<?php echo $slno;?>"></td>
<td style="display:none"><input style="text-align:right" <?php echo $final_disabler;?> class="form-control" readonly value="<?php echo $tax_pc_sgst;?>" id="sgst_<?php echo $slno;?>" ></td>
<td style="display:none"><input style="text-align:right" <?php echo $final_disabler;?> class="form-control" readonly value="<?php echo $tax_pc_igst;?>" id="igst_<?php echo $slno;?>" ></td>

<td style="display:<?php echo $visibility;?>"><input class="form-control" readonly style="text-align:right" id="tax_gst_<?php echo $slno;?>" value="<?php echo $det_tax;?>"></td>
<td style="display:<?php echo $visibility;?>"><input class="form-control" style="text-align:right" readonly id="tax_sgst_<?php echo $slno;?>" value="<?php echo $det_tax_sgst;?>"></td>
<td style="display:<?php echo $visibility;?>"><input class="form-control" style="text-align:right" readonly id="tax_igst_<?php echo $slno;?>" value="<?php echo $det_tax_igst;?>"></td>
<td><input class="form-control" <?php echo $final_disabler;?>   id="discount_<?php echo $slno;?>" value="<?php echo $det_discount;?>" onblur="calculate('<?php echo $slno;?>')"></td>
<td><input class="form-control" <?php echo $final_disabler;?> readonly  id="total_<?php echo $slno;?>" value="<?php echo $det_total;?>">
<input class="form-control" <?php echo $final_disabler;?> type="hidden"  id="id_<?php echo $slno;?>" value="<?php echo $det_subtrans_id;?>"></td>
	<td><input class="form-control" <?php echo $final_disabler;?>   id="remarks_<?php echo $slno;?>" value="<?php echo $det_remarks;?>" onblur="calculate('<?php echo $slno;?>')"></td>
<td>
<button class="btn btn-xs btn-danger" <?php echo $final_disabler;?> onclick="del_service_det('<?php echo $slno;?>')"  id="del_<?php echo $slno;?>;?>">X</button>
</td>
</tr>


<?php $slno++; }?>
<?php while($slno<100){?>
<tr  class="purchase_rows" id="row_<?php echo $slno;?>" style="display:none" onfocusout="add_service_det('<?php echo $slno;?>')">
<td><span id="item_name_<?php echo $slno;?>"></span>
<input type="hidden" id="item_id_<?php echo $slno;?>" >
<input type="hidden" id="subtrans_id_<?php echo $slno;?>" >
</td>
	<td><div class="tooltip-wrapper"><input class="form-control tooltip-input"  id="qty_<?php echo $slno;?>" onblur="calculate('<?php echo $slno;?>')"   <div class="tooltip-text">Once saved cannot be edited. If required, delete and reenter.</div>
</div></td>
	<td><input class="form-control"  id="price_<?php echo $slno;?>" onblur="calculate('<?php echo $slno;?>')"></td>

	<td><input class="form-control" <?php echo $final_disabler;?> readonly value="" id="cost_<?php echo $slno;?>"  onblur="calculate('<?php echo $slno;?>')"></td>
<td style="display:none"><input style="text-align:right;" <?php echo $final_disabler;?> class="form-control" readonly  id="gst_<?php echo $slno;?>"></td>
<td style="display:none"><input style="text-align:right" <?php echo $final_disabler;?> class="form-control" readonly  id="sgst_<?php echo $slno;?>" ></td>
<td style="display:none"><input style="text-align:right" <?php echo $final_disabler;?> class="form-control" readonly  id="igst_<?php echo $slno;?>" ></td>

<td style="display:<?php echo $visibility;?>"><input class="form-control" readonly style="text-align:right" id="tax_gst_<?php echo $slno;?>" value=""></td>
<td style="display:<?php echo $visibility;?>"><input class="form-control" style="text-align:right" readonly id="tax_sgst_<?php echo $slno;?>" value=""></td>
<td style="display:<?php echo $visibility;?>"><input class="form-control" style="text-align:right" readonly id="tax_igst_<?php echo $slno;?>" value=""></td>
<td><input class="form-control"    id="discount_<?php echo $slno;?>" value="" onblur="calculate('<?php echo $slno;?>')"></td>
<td><input class="form-control" <?php echo $final_disabler;?> readonly  id="total_<?php echo $slno;?>" value="">
<input class="form-control" <?php echo $final_disabler;?> type="hidden"  id="id_<?php echo $slno;?>" value=""></td>
	<td><input class="form-control" <?php echo $final_disabler;?>   id="remarks_<?php echo $slno;?>" value="" onblur="calculate('<?php echo $slno;?>')"></td>
<td>
<button class="btn btn-xs btn-danger" <?php echo $final_disabler;?> onclick="del_service_det('<?php echo $slno;?>')"  id="del_<?php echo $slno;?>;?>">X</button>
</td>
</tr>

<?php $slno++;}?>
</tbody>
</table>
	
<span class="alert alert-info" style="text-align:right:font-weight:bold;width:100%;display:block">Total Invoice Amount <input id="trans_amount" class="form-control" readonly value="<?php echo $grand_total;?>" style="width:160px;float:right"></span>
<span class="alert alert-info" style="text-align:right:font-weight:bold;width:100%;display:block">Total Taxable Amount <input id="price_amount" class="form-control" readonly value="<?php echo $price_amount;?>" style="width:160px;float:right"></span>
<span class="alert alert-info" style="text-align:right:font-weight:bold;width:100%;display:block">Total CGST Amount <input id="cgst_amount" class="form-control" readonly value="<?php echo $cgst_amount;?>" style="width:160px;float:right"></span>
<span class="alert alert-info" style="text-align:right:font-weight:bold;width:100%;display:block">Total SGST Amount <input id="sgst_amount" class="form-control" readonly value="<?php echo $sgst_amount;?>" style="width:160px;float:right"></span>
<span class="alert alert-info" style="text-align:right:font-weight:bold;width:100%;display:block">Total IGST Amount <input id="igst_amount" class="form-control" readonly value="<?php echo $igst_amount;?>" style="width:160px;float:right"></span>

<?php if($active_status=="A"){?>
<hr><center><button class="btn btn-success" disabled>Saved already</button>&nbsp;<button class="btn btn-primary" id="jobcard"  onclick="unlock()">Edit</button>
</center><hr>	

<?php }else{?>
	<hr><center><button class="btn btn-success" onclick="save_dummy()">Save</button></center><hr>	
<?php }?>
	
</span>
	
	
</div>

 <div class="card-body">
                            
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
  <table class="table"><tr>
<td>Pay Type <select id="pay_type1" class="form-control" onchange="filter_account1()">
 <option value="">--Select--</option>
	 <?php  $sql="SELECT paytype_id,paytype_name FROM paytype order by 1";
$result = $conn->query($sql);
$selected="";
while ($row = $result->fetch_assoc()) {
	if($row['paytype_name']==$mode)
		$selected="selected";
	else
		$selected="";
?>
	  <option value="<?php echo $row['paytype_id'];?>~<?php echo $row['paytype_name'];?>" <?php echo $selected;?>><?php echo $row['paytype_name'];?></option>
<?php }?>
	 </select></td></tr>
	<tr> <td>Account<br><select type="text" class="form-control" id="account1">
	 <option value="">--Select--</option>
	 <?php  $sql="SELECT account_id,account_name,paytype_id FROM account order by 1";
$result = $conn->query($sql);
$selected="";
while ($row = $result->fetch_assoc()) {
	if($row['account_id']==$account)
		$selected="selected";
	else
		$selected="";
?>
	  <option value="<?php echo $row['paytype_id'];?>~<?php echo $row['account_id'];?>" <?php echo $selected;?>><?php echo $row['account_name'];?></option>
<?php }?>
	 </select></td></tr>
<tr><td>Ref No <input id="ref_no1" class="form-control"></td></tr>

<tr><td>Paid Amount <input id="paid_amount" class="form-control" type="number"></td></tr>

</table>
    </div>
	 <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		 <button type="button" class="btn btn-success" id="add"  onclick="save_pay1()">Save</button>
		
       
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
	  <tr><td>Vehicle</td><td><select  class="form-control" id="edit_vehicle" onchange="populate_edit_vehicle_det()">
<option value="">--select---</option>
<?php  $sql="SELECT vehicle_id,vehicle_no,vehicle_model,vehicle_brand FROM vehicle where customer_id='".$customer."' order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['vehicle_id'];?>"><?php echo $row1['item_name'];?>(<?php echo  $row1['vehicle_no'];?>,Model: <?php echo $row1['vehicle_model'];?>, Brand: <?php echo $row1['vehicle_brand'];?>)</option>
<?php }?>
	  </select>
	 
	    <input type="hidden" id="edit_vehicle_id">
	  </td></tr>
<tr><td>Service</td><td><select  class="form-control" id="edit_service" onchange="populate_edit_det()">
<option value="">--select---</option>
<?php  $sql="SELECT service_id,service_name,service_description,cost,tax_pc,tax_pc_sgst FROM service order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['service_id'];?>~<?php echo $row1['cost'];?>~<?php echo $row1['tax_pc'];?>~<?php echo $row1['tax_pc_sgst'];?>"><?php echo $row1['service_name'];?>(<?php echo  $row1['service_description'];?>,Cost: <?php echo $row1['cost'];?>, Tax (%): <?php echo $row1['tax_pc'];?>)</option>
<?php }?>
	  </select>
	  <input type="hidden" id="edit_cost">
	   <input type="hidden" id="edit_tax_pc">
	   <input type="hidden" id="edit_tax_pc_sgst">
	    <input type="hidden" id="edit_service_id">
	  </td></tr>	  
     <tr><td>Qty</td><td><input type="number" id="edit_qty" class="form-control" oninput="show_edit_amount()"></td></tr>
	 <tr><td>Amount</td><td><input type="number"  class="form-control" id="edit_amount"></td></tr>
	   <tr><td>Tax</td><td><input type="number"  class="form-control" id="edit_tax" readonly></td></tr>
	     <tr><td>Tax (SGST)</td><td><input type="number"  class="form-control" id="edit_tax_sgst" readonly></td></tr>
	<tr><td>Total</td><td><input type="number"  class="form-control" id="edit_total" readonly>
	
	
	  <input type='HIDDEN'name="trans_id" id="trans_id">
	   <input type='HIDDEN'name="trans_id" id="subtrans_id">
	  </td></tr>
	</table>
    </div>
	 <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		 <button type="button" class="btn btn-success" id="edit">Save</button>
		
         
      </div>
      </div>
  </div>
</div>

<?php include "footer_include.php";?>
	<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
	 <script src="js/demo/datatables-demo.js"></script>
 <script src="multi/searchableOptionList.js"></script>
      
         <script type="text/javascript">
    $(function() {
       
$('#customer_search').searchableOptionList({
    maxHeight: '350px',
    showSelectAll: true,

    onChange: function () {

        // ✅ Close dropdown immediately
        $('.sol-container').removeClass('sol-active');

        // ✅ FULL RESET
        setTimeout(function () {

            // 1. Clear select value completely
            $('#customer_search').val(null);

            // 2. Remove selected attribute from all options
            $('#customer_search option').prop("selected", false);

            // 3. Uncheck all plugin checkboxes
            $('.sol-checkbox').prop("checked", false);

            // 4. Remove plugin "selected" highlight class
            $('.sol-option').removeClass("sol-selected");

        }, 50);

        // Your function
        select_customer();
    }
});		
	/*$('#item_search').searchableOptionList({
            maxHeight: '350px',
            showSelectAll: true,
			 onChange: function () {
				 console.log("ekhane elem");
          
			setTimeout(function () {
    select_item();
}, 2000);
        }
       // alert("jaimaa");
    });*/
$('#item_search').searchableOptionList({
    maxHeight: '750px',
    showSelectAll: true,

    onChange: function () {

        // ✅ Close dropdown immediately
        $('.sol-container').removeClass('sol-active');

        // ✅ FULL RESET
        setTimeout(function () {

            // 1. Clear select value completely
            $('#item_search').val(null);

            // 2. Remove selected attribute from all options
            $('#item_search option').prop("selected", false);

            // 3. Uncheck all plugin checkboxes
            $('.sol-checkbox').prop("checked", false);

            // 4. Remove plugin "selected" highlight class
            $('.sol-option').removeClass("sol-selected");

        }, 50);

        // Your function
        select_item();
    }
});



	
			
		
		
		
		
		
});
	
</script>
<script>
var del_mode=0;
var addModal = new bootstrap.Modal(document.getElementById('addModal'));
var editModal = new bootstrap.Modal(document.getElementById('editModal'));
/*document.getElementById("customer_search").addEventListener("input", function () {
  console.log(document.getElementById("customer_search").value);
  let parts= document.getElementById("customer_search").value.split("~");
  $("#customer").val(parts[3]);
   $("#company_name").val(parts[0]);
     $("#customer_name").val(parts[1]);
	   $("#customer_mobile").val(parts[2]);
	    get_gst();
	 get_address();
});*/
document.getElementById("item_search").addEventListener("blur", function () {
  console.log(document.getElementById("customer_search").value);
  let parts= document.getElementById("item_search").value.split("~");
  document.getElementById("item_search").value="";
  cosole.log(parts[5]);
  //load_service(parts[5],parts[4],parts[5],parts[0]);
});

 $(function () {
    $("#datepicker").datepicker({
        dateFormat: "dd-mm-yy"  // Set format to yyyy-mm-dd
      }).datepicker("setDate", new Date());;
	  $("#edit_datepicker").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
      });
  });
function show_amount(){
	let cost=$("#cost").val()==""?0:$("#cost").val();
	let qty=$("#qty").val()==""?0:$("#qty").val();
	$("#amount").val(cost*qty);
	$("#tax").val(($("#amount").val()*$("#tax_pc").val())/100);
	$("#tax_sgst").val(($("#amount").val()*$("#tax_pc_sgst").val())/100);
	let total=parseFloat($("#amount").val())+parseFloat($("#tax").val())+parseFloat($("#tax_sgst").val());
	$("#total").val(total);
}
function show_edit_amount(){
	let cost=$("#edit_cost").val()==""?0:$("#edit_cost").val();
	let qty=$("#edit_qty").val()==""?0:$("#edit_qty").val();
	$("#edit_amount").val(cost*qty);
	$("#edit_tax").val(($("#edit_amount").val()*$("#edit_tax_pc").val())/100);
		$("#edit_tax_sgst").val(($("#edit_amount").val()*$("#edit_tax_pc_sgst").val())/100);
	
	let total=parseFloat($("#edit_amount").val())+parseFloat($("#edit_tax").val())+parseFloat($("#edit_tax_sgst").val());
	$("#edit_total").val(total);
}
function populate_det(){
	let gst = "<?php echo $gst;?>";
	let parts = (document.getElementById("service").value).split("~");
	$("#service_id").val(parts[0]);
	$("#cost").val(parts[1]);
	if(gst=="Y")
	{$("#tax_pc").val(parts[2]);
$("#tax_pc_sgst").val(parts[3]);
}
	else
	{$("#tax_pc").val("0");
$("#tax_pc_sgst").val("0");
}
	//alert(parts[0]+"++"+parts[1]+"===="+parts[2]);
	show_amount();
}
function populate_vehicle_det(){
	let parts = (document.getElementById("vehicle").value).split("~");
	$("#vehicle_id").val(parts[0]);
	
}
function populate_edit_det(){
	let gst = "<?php echo $gst;?>";
	let parts = (document.getElementById("edit_service").value).split("~");
	$("#edit_service_id").val(parts[0]);
	$("#edit_cost").val(parts[1]);
	if(gst=="Y")
	{$("#edit_tax_pc").val(parts[2]);$("#edit_tax_pc_sgst").val(parts[3]);}
else
{$("#edit_tax_pc").val("0");$("#edit_tax_pc_sgst").val("0");}
	//alert(parts[0]+"++"+parts[1]+"===="+parts[2]);
	show_edit_amount();
}
function populate_edit_vehicle_det(){
	let parts = (document.getElementById("edit_vehicle").value).split("~");
	$("#edit_vehicle_id").val(parts[0]);
	
}
	 $(function () {
    $("#datepicker").datepicker({
        dateFormat: "dd-mm-yy"  // Set format to yyyy-mm-dd
      });
	  $("#edit_datepicker").datepicker({
        dateFormat: "dd-mm-yy"  // Set format to yyyy-mm-dd
      });
  });
 $('#add_opener').on('click', function() {
      //alert('Button was clicked!');
      // You can run any function here
	

	addModal.show();
	/*  let module="<?php echo $module;?>";
	 $.post( "generic_form.php", {module:module})
  .done(function( data ) {
	  $("#add_block").html($.trim(data));
	    addModal.show();

  
  });*/
    });
function add_service_det(service_id){

  let service=service_id;
  
  let price_selector = "#price_"+service;
	let discount_selector = "#discount_"+service;
	let qty_selector = "#qty_"+service;
	let cost_selector="#cost_"+service;
	let gst_selector="#gst_"+service;
	let sgst_selector="#sgst_"+service;
	let igst_selector="#igst_"+service;
	let tax_gst_selector="#tax_gst_"+service;
	let tax_sgst_selector="#tax_sgst_"+service;
	let tax_igst_selector="#tax_igst_"+service;
	let total_selector="#total_"+service;
	let remarks_selector="#remarks_"+service;
  let item_id_selector="#item_id_"+service;
  let subtrans_id_selector="#subtrans_id_"+service;
  
  let price=$(price_selector).val();
   let cost=$(cost_selector).val();
 let tax_pc=$(gst_selector).val();
  let tax_pc_sgst=$(sgst_selector).val();
  let tax_pc_igst=$(igst_selector).val();
 let total=$(total_selector).val();
 let tax=$(tax_gst_selector).val();
  let tax_sgst=$(tax_sgst_selector).val();
   let tax_igst=$(tax_igst_selector).val()
  let qty=$(qty_selector).val();
  let discount=$(discount_selector).val();
	 let remarks=$(remarks_selector).val();
	let item_id=$(item_id_selector).val();
	let subtrans_id=$(subtrans_id_selector).val();
   if(del_mode==0){
 $.post("add_receipt_det.php",
  {
    
    cost: cost,
	tax_pc:tax_pc,
	tax_pc_sgst:tax_pc_sgst,
	tax_pc_igst:tax_pc_igst,
	total:total,
	tax:tax,
	tax_sgst:tax_sgst,
	tax_igst:tax_igst,
	qty:qty,
	item_id:item_id,
	price:price,
subtrans_id:subtrans_id,
	trans_id:$("#trans_id").val(),
	
	 	discount:discount,
	 remarks:remarks
	
	
  },
  function(data, status){
    //alert($.trim(data));
	if($.trim(data).indexOf("Error")==-1){
	let id_selector="#subtrans_id_"+service;
	let del_selector="#del_"+service;
	$(id_selector).val($.trim(data).split("~")[0]);
	
		
		let amount_paid = parseFloat($.trim(data).split("~")[1]);
		console.log(amount_paid);	
	$("#trans_amount").val(amount_paid);
	$("#cgst_amount").val(parseFloat($.trim(data).split("~")[2]));
	$("#sgst_amount").val(parseFloat($.trim(data).split("~")[3]));
	$("#igst_amount").val(parseFloat($.trim(data).split("~")[4]));
	$("#price_amount").val(parseFloat($.trim(data).split("~")[5]));
		console.log(amount_paid);	
	const paid_amount = document.getElementById("paid_amount");
	paid_amount.setAttribute("min", "0");
paid_amount.setAttribute("max", $.trim(data).split("~")[1]);
	//$(qty_selector).prop('readonly', true);
	//$(discount_selector).prop('readonly', true);
	$(del_selector).prop('disabled', false);}else{
		alert($.trim(data));
		  let qty_selector = "qty_"+service_id;
	let discount_selector="discount_"+service_id;
	document.getElementById(discount_selector).value=="0";
	document.getElementById(qty_selector).value=="0";
	let row_selector = "row_"+service_id;
	document.getElementById(row_selector).style.display="none";
		
	}

  });
   }
  
}

$('#edit').on('click', function(e) {
  let service=$("#edit_service_id").val();
  let vehicle=$("#edit_vehicle_id").val();
  let cost=$("#edit_cost").val();
 let tax_pc=$("#edit_tax_pc").val();
 let tax_pc_sgst=$("#edit_tax_pc_sgst").val();
 let total=$("#edit_amount").val();
 let tax=$("#edit_tax").val();
 let tax_sgst=$("#edit_tax_sgst").val();
  let qty=$("#edit_qty").val();
  let subtrans_id=$("#subtrans_id").val();
 $.post("edit_service_det.php",
  {
    
    cost: cost,
	tax_pc:tax_pc,
	tax_pc_sgst:tax_sgst,
	total:total,
	tax:tax,
	tax_sgst:tax_sgst,
	qty:qty,
	service_id:service,
	vehicle:vehicle,
	trans_id:"<?php echo $trans_id;?>",
	subtrans_id:subtrans_id
	
	
  },
  function(data, status){
    //alert($.trim(data));
	console.log(data);
	location.reload();
  });
 
  
});


/*$(document).ready(function() {
	 let module="<?php echo $module;?>"; 
 $.post( "generic_fetch.php", { module: module })
  .done(function( data ) {
	  let jsonData=(data);
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
function delete_it(trans_id,subtrans_id){
	var r = confirm("Are you sure that you want to delete the transaction");
	if(r){
		$.post("delete_receipt_trans_det.php",
  {
    trans_id: trans_id,
	subtrans_id:subtrans_id
  },
  function(data, status){
    //alert($.trim(data));
	location.reload();
  });
	}
	
}
function get_details(){
let vehicle_no = $("#vehicle_no").val();
		$.post("get_details.php",
  {
    vehicle_no: vehicle_no
  },
  function(data, status){
   let parts = $.trim(data).split("~");
   $("#vehicle").val(parts[0]);
   $("#vehicle_model").val(parts[1]);
   $("#no_of_wheels").val(parts[2]);
   $("#customer").val(parts[3]);
   $("#company_name").val(parts[4]);
   $("#customer_name").val(parts[5]);
  });
	
	
}
function get_gst(){
let customer = $("#customer").val();
		$.post("get_gst_vendor.php",
  {
    customer_id: customer
  },
  function(data, status){
   let parts = $.trim(data).split("~");
   var datalist=document.getElementById("gst_list");
   datalist.innerHTML="";
   parts.forEach(part => {
    const option = document.createElement("option");
    option.value = part;
    datalist.appendChild(option);
	//document.getElementById("customer_gst").value=part;
});
// Assign the LAST value to the input field
    if (parts.length > 0) {
      document.getElementById("customer_gst").value = parts[0];
	  console.log(parts[0]);
	  set_gst();
    }
  });
	
	
}

function get_address(){
let customer = $("#customer").val();
		$.post("get_address_vendor.php",
  {
    customer_id: customer
  },
  function(data, status){
   let parts = $.trim(data).split("~");
   var datalist=document.getElementById("address_list");
   datalist.innerHTML="";
   parts.forEach(part => {
    const option = document.createElement("option");
    option.value = part;
    datalist.appendChild(option);
	//document.getElementById("customer_address").value=part;
});
// Assign the LAST value to the input field
    if (parts.length > 0) {
      document.getElementById("customer_address").value = parts[0];
    }
  });
	
	
}

function set_gst(){
	let str = document.getElementById("customer_gst").value;
	//alert(str);
	if (str.startsWith("37")) {
 $("#cgst_igst").html("CGST");
 $("#cgst_igst_amount").html("CGST");
// alert("CGST");
 
  document.querySelectorAll('.hide_sgst').forEach(el => {
  el.style.display = '';
});
 
}else{
 $("#cgst_igst").html("IGST");
  $("#cgst_igst_amount").html("IGST");
  
  document.querySelectorAll('.hide_sgst').forEach(el => {
  el.style.display = 'none';
});
 //alert("IGST"); 
}
}
function edit_it(trans_id,subtrans_id,service,vehicle,qty){
	
	//alert("====");
	$("#trans_id").val(trans_id);
	$("#subtrans_id").val(subtrans_id);
	$("#edit_service").val(service);
	
	$("#edit_vehicle").val(vehicle);
	$("#edit_qty").val(qty);
	populate_edit_det();
	populate_edit_vehicle_det();
	show_edit_amount();
	 editModal.show();
	
}

function load_service(service,price, gst,igst,name){
	
	let rows = $(".purchase_rows").filter(function () {
  return $(this).css("display") != "none";
});
console.log(rows.length);
var slno=rows.length;
	let trans_id=document.getElementById("trans_id").value;
	if($("#customer").val()=="" || $("#customer").val()==null){
		alert("Please enter customer Details");
		return false;
	}
	let str = document.getElementById("customer_gst").value;
	
	//alert(str);
	let gst_selector="gst_"+slno;
	let sgst_selector="sgst_"+slno
	let igst_selector="igst_"+slno;
	let price_selector="cost_"+slno;
	let item_selector="item_id_"+slno;
	let item_name_selector="item_name_"+slno;
	
	document.getElementById(price_selector).value=parseInt(price);
	document.getElementById(item_selector).value=service;
	document.getElementById(item_name_selector).innerHTML=name;
	
	if (str.startsWith("37")) {
		document.getElementById(sgst_selector).value=parseInt(gst);
		document.getElementById(gst_selector).value=parseInt(gst);
	}else{
		document.getElementById(igst_selector).value=parseInt(gst)+parseInt(gst);
		//document.getElementById(sgst_selector).value=0;
	}
	if(trans_id=="" || trans_id==null){
			$.post("add_receipt.php",
  {
   
	 customer_mobile: $("#customer_mobile").val(),
	 company_name: $("#company_name").val(),
	  customer_name: $("#customer_name").val(),
	  customer_address: $("#customer_address").val(),
	   customer_gst: $("#customer_gst").val(),
	    customer: $("#customer").val(),
		trans_date:$("#datepicker").val(),
		gst :"<?php echo $_COOKIE["gst"];?>",
				discount:$("#discount").val(),
				invoice_no:$("#invoice_no").val()
  },
  function(data, status){
   let selector = "row_"+slno;
document.getElementById(selector).style.display="";	
document.getElementById("trans_id").value = $.trim(data);


  });
	}else{let selector = "row_"+slno;
document.getElementById(selector).style.display="";	}
	
	
}
function calculate(service){
	console.log(service);
	let price_selector = "price_"+service;
	let cost_selector = "cost_"+service;
	let discount_selector = "discount_"+service;
	let qty_selector = "qty_"+service;
	let discount=(document.getElementById(discount_selector).value=="" || document.getElementById(discount_selector).value==null)?0:document.getElementById(discount_selector).value;
	console.log(price_selector);
	console.log(document.getElementById(price_selector).value);
	let price=(document.getElementById(price_selector).value=="")?0:document.getElementById(price_selector).value;
		let cost=(document.getElementById(cost_selector).value=="")?0:document.getElementById(cost_selector).value;

	let qty=(document.getElementById(qty_selector).value=="")?0:document.getElementById(qty_selector).value;
	/*if(service=="1000002"){
	document.getElementById(qty_selector).value=$("#no_of_wheels").val();}*/
	if(price>0 && document.getElementById(price_selector).readOnly==false)
	{(price)=parseFloat(price);
document.getElementById(price_selector).value=price;
		document.getElementById(cost_selector).value=(parseFloat(price))/parseFloat(qty);}
	else
	document.getElementById(price_selector).value=(parseFloat(cost))*parseFloat(qty);	
	/*	if(service=="1000002"){	
	if(parseFloat(document.getElementById(cost_selector).value)<800) 	{document.getElementById(cost_selector).value="800";}
		}*/
	let gst_selector="gst_"+service;
	let sgst_selector="sgst_"+service;
	let igst_selector="igst_"+service;
	let tax_gst_selector="tax_gst_"+service;
	let tax_sgst_selector="tax_sgst_"+service;
	let tax_igst_selector="tax_igst_"+service;
	let tax_perc_gst="";
	try{tax_perc_gst=parseFloat(document.getElementById(gst_selector).value);}catch(err){tax_perc_gst=0;}
	let tax_perc_sgst="";
	try{tax_perc_sgst=parseFloat(document.getElementById(sgst_selector).value);}catch(err){tax_perc_sgst=0;}
	let tax_perc_igst="";
	try{tax_perc_igst=parseFloat(document.getElementById(igst_selector).value);}catch(err){tax_perc_igst=0;}
	if (Number.isNaN(tax_perc_gst)) {
 tax_perc_gst=0;
}
	if (Number.isNaN(tax_perc_sgst)) {
 tax_perc_sgst=0;
}
	if (Number.isNaN(tax_perc_igst)) {
 tax_perc_igst=0;
}
	console.log(tax_perc_gst+"=="+tax_perc_sgst+"=="+tax_perc_igst);
	document.getElementById(tax_gst_selector).value=(parseFloat(document.getElementById(price_selector).value)*tax_perc_gst/100.0).toFixed(2);
document.getElementById(tax_sgst_selector).value=(parseFloat(document.getElementById(price_selector).value)*tax_perc_sgst/100.0).toFixed(2);
document.getElementById(tax_igst_selector).value=(parseFloat(document.getElementById(price_selector).value)*tax_perc_igst/100.0).toFixed(2);

	let total_selector="total_"+service;
	document.getElementById(total_selector).value=(parseFloat(document.getElementById(tax_gst_selector).value)+parseFloat(document.getElementById(price_selector).value)+parseFloat(document.getElementById(tax_sgst_selector).value)+parseFloat(document.getElementById(tax_igst_selector).value)+parseFloat(discount)).toFixed(2);;
}
function del_service_det(service){
let id_selector="subtrans_id_"+service;	
let item_id_selector="item_id_"+service;
let subtrans_id=document.getElementById(id_selector).value;	
let item_id=document.getElementById(item_id_selector).value;	
	var r = confirm("Are you sure that you want to delete the transaction");
	if(r){
		 del_mode=1;
		$.post("delete_trans_det_receipt.php",
  {
    trans_id: $("#trans_id").val(),
	subtrans_id:subtrans_id,
	item_id:item_id
  },
  function(data, status){
   let qty_selector = "qty_"+service;
	//let discount_selector="discount_"+service;
			let cost_selector = "cost_"+service;
	let total_selector="total_"+service;
			let tax_gst_selector = "tax_gst_"+service;
	let tax_sgst_selector="tax_sgst_"+service;
	//document.getElementById(discount_selector).value="0";
	document.getElementById(qty_selector).value="0";
			document.getElementById(cost_selector).value="0";
	document.getElementById(total_selector).value="0";
			document.getElementById(tax_gst_selector).value="0";
	document.getElementById(tax_sgst_selector).value="0";
	let row_selector = "row_"+service;
	document.getElementById(row_selector).style.display="none";
	$("#trans_amount").val($.trim(data).split("~")[1]);
	setTimeout(() => {
 del_mode=0;
  console.log("Variable set:", myVar);
}, 700);
	//window.location.href="service_trans_new.php?trans_id="+$("#trans_id").val();
	
  });
}
}
function save_pay(){
	let paid_amount=$("#paid_amount").val();
	let pay_type=$("#pay_type").val();
	let ref_no=$("#ref_no").val();
	let trans_amount=$("#trans_amount").val();
	let account="";
	try{account=$("#account").val().split("~")[1];}catch(err){account="";}
	$.post("update_pay_receipt.php",
  {
    trans_id: $("#trans_id").val(),
	paid_amount:paid_amount,
	
	pay_type:pay_type.split("~")[1],
	account:account,
	ref_no:ref_no,
	trans_amount:trans_amount,
	 customer_name: $("#customer_name").val(),
	    customer: $("#customer").val(),
		trans_date:$("#datepicker").val(),
		gst :"<?php echo $gst;?>"
	
  },
  function(data, status){
   alert("Transaction Saved with ID: "+$("#trans_id").val());
	$('#invoice').prop('disabled', false);
	console.log($.trim(data));
	//location.href="sales_trans_new.php?trans_id="+$("#trans_id").val();

  });
}
function save_pay1(){
	let paid_amount=$("#paid_amount").val();
	let pay_type=$("#pay_type1").val();
	let ref_no=$("#ref_no1").val();
	let trans_amount=$("#trans_amount1").val();
	let account="";
	let max_amount=parseFloat("<?php echo $grand_total;?>")-parseFloat("<?php echo $total_amount;?>");
	try{account=$("#account1").val().split("~")[1];}catch(err){account="";}
	if(parseFloat(paid_amount)>max_amount && max_amount>0){
		alert("Amount should not exceed pending amount");
		return false;
	}
	$.post("update_pay_receipt.php",
  {
    trans_id: $("#trans_id").val(),
	paid_amount:paid_amount,
	
	pay_type:pay_type.split("~")[1],
	account:account,
	ref_no:ref_no,
	trans_amount:trans_amount,
	 customer_name: $("#customer_name").val(),
	    customer: $("#customer").val(),
		trans_date:$("#datepicker").val(),
		gst :"<?php echo $gst;?>"
	
  },
  function(data, status){
   alert("Transaction Saved with ID: "+$("#trans_id").val());
	$('#invoice').prop('disabled', false);
	window.location.href="receipt_trans_new.php?trans_id="+$("#trans_id").val();

  });
}
function get_invoice(){
	let trans_id= $("#trans_id").val();
	let url="sales_receipt_new.php?trans_id="+trans_id;
	window.open(url, '_blank');
	//window.location.href=url;
}
function get_jobcard(){
	let trans_id= $("#trans_id").val();
	let url="job_card.php?trans_id="+trans_id;
	window.open(url,'_blank');
	//window.location.href=url;
}
function filter_account(){
	let pay_type=document.getElementById("pay_type").value.split("~")[0];
	//alert(pay_type);
	 $('#account option').filter(function() {
    return !$(this).val().toLowerCase().includes(pay_type);
  }).prop('disabled', true);
}
function filter_account1(){
	let pay_type=document.getElementById("pay_type1").value.split("~")[0];
	//alert(pay_type);
	 $('#account1 option').filter(function() {
    return !$(this).val().toLowerCase().includes(pay_type);
  }).prop('disabled', true);
}

const input = document.getElementById('paid_amount');

input.addEventListener('input', function () {
    const min = parseInt(input.min);
    const max = parseInt(input.max);
    const value = parseInt(input.value);

    if (value > max) {
        input.value = max;
    } else if (value < min) {
        input.value = min;
    }
});
const tooltipWrappers = document.querySelectorAll('.tooltip-wrapper');

tooltipWrappers.forEach(wrapper => {
  const input = wrapper.querySelector('input');
  const tooltip = wrapper.querySelector('.tooltip-text');

  function showTooltip() {
    if (input.readOnly) {
      wrapper.classList.add('show');
    }
  }

  function hideTooltip() {
    wrapper.classList.remove('show');
  }

  input.addEventListener('mouseenter', showTooltip);
  input.addEventListener('focus', showTooltip);
  input.addEventListener('mouseleave', hideTooltip);
  input.addEventListener('blur', hideTooltip);
});
function setwheels(){

		let qty_selector="qty_1000002";
		let cost_selector="cost_1000002";
		document.getElementById(qty_selector).value=$("#no_of_wheels").val();
		calculate("1000002");
	if(parseFloat(document.getElementById(cost_selector).value)<800) 	{document.getElementById(cost_selector).value="800";}
	
}
	function save_dummy(){
		 $.post("save_draft_receipt.php",
  {
    value: $("#trans_id").val(),
	
	
  },
  function(data, status){
	alert("Saved Transaction Successfully");
			 location.href="receipt_trans_new.php?trans_id="+$("#trans_id").val();
	$("#add_opener").prop("disabled", false);
  });
	}
	function select_customer(){
  set_customer();
	    get_gst();
	 get_address();
};
	function select_item(){
	 var a = document.getElementsByClassName("sol-selected-display-item");
            var myvar = "";
			console.log(a.length);
            for(var i=0;i<a.length;i++)
            {
                  myvar = myvar + (a[i].getAttribute("data-sol-item-val"));
				a[i].setAttribute("data-sol-item-val","");
				a[i].innerHTML="";
               // myvar = myvar + (a[i].innerHTML);
            }
			console.log(myvar);
  let parts=myvar.split("~");
//  document.getElementById("item_search").value="";
		console.log()
   load_service(parts[5],parts[2],parts[3],parts[4],parts[0]);
	}
function set_customer(){
	 var a = document.getElementsByClassName("sol-selected-display-item");
            var myvar = "";
			console.log(a.length);
            for(var i=0;i<a.length;i++)
            {
                  myvar = myvar + (a[i].getAttribute("data-sol-item-val"));
               // myvar = myvar + (a[i].innerHTML);
				a[i].setAttribute("data-sol-item-val","");
				a[i].innerHTML="";
            }
			console.log(myvar);
			let parts= myvar.split("~");
  
	 $("#customer").val(parts[3]);
   $("#company_name").val(parts[0]);
     $("#customer_name").val(parts[1]);
	   $("#customer_mobile").val(parts[2]);
	
}
	function unlock(){
		$.post("unlock_receipt.php",
  {
    trans_id: $("#trans_id").val()
	
	
  },
  function(data, status){
   alert("Transaction unlocked with ID: "+$("#trans_id").val());
	//$('#invoice').prop('disabled', false);
	window.location.href="receipt_trans_new.php?trans_id="+$("#trans_id").val();

  });
	}
</script>




  

</body>

</html>