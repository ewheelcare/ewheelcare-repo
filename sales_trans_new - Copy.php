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
	<link href="multi/searchableOptionList.css" rel="stylesheet">
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
					<button class="btn btn-danger" id="invoice"  onclick="get_invoice()">Get Invoice</button>&nbsp;
					<!--<button class="btn btn-primary" id="jobcard"  onclick="get_jobcard()">Get JobCard</button>-->
					
					<?php 
					//echo $trans_id;
					$gst = isset($_COOKIE["gst"]) ? $_COOKIE["gst"] : "";
					//echo $_COOKIE["gst"];
					//echo $gst;
					
				$trans_id=""; $trans_date=""; $details=""; $customer=""; $trans_amount=""; $pending=""; $gst=""; $tally=""; $created_by=""; $created_on=""; $modified_by=""; $modified_on=""; $active_status=""; $VEHICLE_NO=""; $VEHICLE_MODEL=""; $NO_OF_WHEELS=""; $COMPANY_NAME=""; $CUSTOMER_NAME=""; $CUSTOMER_ADDRESS=""; $CUSTOMER_GST=""; $VEHICLE_ODOMETER=""; $VEHICLE="";
	$trans_id=isset($_GET["trans_id"])?$_GET["trans_id"]:"";
					$sql="SELECT trans_id, trans_date, details, customer, trans_amount, pending, gst, tally, created_by, created_on, modified_by, modified_on, active_status, COMPANY_NAME, CUSTOMER_NAME, CUSTOMER_ADDRESS, CUSTOMER_GST, customer_mobile,discount,roundoff,ver FROM sales_trans where trans_id='".$trans_id."'";
//echo $sql;
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {$trans_id=$row["trans_id"]; 
$trans_date=$row["trans_date"]; 
$details=$row["trans_id"]; 
 $customer=$row["customer"]; 
 $final_amount=$row["trans_amount"]; 
  $discount_amount=$row["discount"];
   $roundoff_amount=$row["roundoff"];
 $pending=$row["pending"]; 
 $gst=$row["gst"]; 
 $tally=$row["tally"]; 
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
 $CUSTOMER_MOBILE=$row["CUSTOMER_MOBILE"]; 
 $ver=$row["ver"]; 
 $active_status=$row["active_status"]; 
 if($active_status=="A"){
	 $final_disabler="disabled";
 }else{$ver=$row["ver"]+1; }
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
<div class="alert alert-warning" style="text-align:center;font-weight:bold">Sales : GST:<?PHP echo $gst;?></div><br><br>
					
<span class="alert alert-info">Customer Details</span>
<table class="table">
<tr><td>Date<input type="text" id="datepicker" class="form-control"></td>
<td>Invoice No  <span id="year_part"></span><input class="form-control" readonly name="trans_id" id="trans_id" value="<?php echo $trans_id;?>">
<input class="form-control" hidden name="ver" id="ver" value="<?php echo $ver;?>">
</td>
<td>Search Customer<select class="form-control" name="customer_search" id="customer_search" multiple="multiple" style="max-width:300px!important">
 <?php $sql="SELECT customer_id,company_name,owner_name,owner_mobile FROM customer";
$result = $conn->query($sql);
while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1["customer_id"]?>~<?php echo $row1["company_name"]?>~<?php echo $row1["owner_name"]?>~<?php echo $row1["owner_mobile"]?>"><?php echo $row1["company_name"]?>,<?php echo $row1["owner_name"]?></option>
<?php }?>
</select>
<!--<a href="module.php?param=customer" class="btn btn-success" target="_blank">Add Customer, If not exists</a>
<a href="module.php?param=vehicle" class="btn btn-danger" target="_blank">Add Vehicle, If not exists</a>-->

</td>
</tr>
</table>
</div>
<div class="col-md-12">
<span class="alert alert-success">Customer Details</span>
<table class="table">
<tr><td>Company Name<input class="form-control" name="company_name" id="company_name" value="<?php echo $COMPANY_NAME;?>"></td>
<td>Customer Name <input class="form-control" name="customer_name" id="customer_name" value="<?php echo $CUSTOMER_NAME;?>"></td>
<td>Mobile <input class="form-control" name="customer_mobile" id="customer_mobile" value="<?php echo $CUSTOMER_NAME;?>"></td>
</tr>
<?php if((isset($_COOKIE["gst"]) && $_COOKIE["gst"]=="Y")||($gst=="Y")){$visibility="";}else{$visibility="none";}?>
<tr><td style="display:<?php echo $visibility;?>">GST <input class="form-control" name="customer_gst" id="customer_gst" value="<?php echo $CUSTOMER_GST;?>" list="gst_list" onfocus="get_gst()" autocomplete="off" onblur="set_gst()">
<datalist id="gst_list">
</datalist></td>
<td>Address <input class="form-control" name="customer_address" id="customer_address" value="<?php echo $CUSTOMER_ADDRESS;?>" list="address_list" onfocus="get_address()" autocomplete="off">
<datalist id="address_list">
</datalist>
<input class="form-control" name="customer" id="customer" value="<?php echo $customer;?>" type="hidden">
</td></tr></table>
</div>
<div class="col-md-12">
<select class="form-control" name="item_search" id="item_search" list="item_list" <?php echo $final_disabler;?> multiple="multiple" style="max-width:600px!important">
<?php  

$sql="SELECT item_id,item_name,item_description,cost,ifnull(tax_pc,9) tax_pc,ifnull(tax_pc_sgst,9) tax_pc_sgst FROM item order by item_id";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option <?php echo $final_disabler;?> value="<?php echo $row1["item_id"]?>~<?php echo $row1["item_name"]?>~<?php echo $row1["item_description"]?>~<?php echo $row1["cost"]?>~<?php echo $row1["tax_pc"]?>~<?php echo $row1["tax_pc_sgst"]?>"><?php echo $row1["item_name"]?></option>
<?php }?>
</select>
<!--<select class="form-control" name="group_search" id="group_search" list="item_list" <?php echo $final_disabler;?> multiple="multiple" style="max-width:300px!important">
<?php  $sql="SELECT itemgroup_id,itemgroup_name FROM itemgroup order by itemgroup_id";
$result = $conn->query($sql);
while ($row1 = $result->fetch_assoc()) {?>
<option <?php echo $final_disabler;?> value="<?php echo $row1["itemgroup_id"]?>~<?php echo $row1["itemgroup_name"]?>"><?php echo $row1["itemgroup_name"]?></option>
<?php }?>
</select>-->
<?php  $sql="SELECT item_id,item_name,item_description,cost,ifnull(tax_pc,'N'),tax_pc_sgst,price_edit FROM item order by item_id";
$result = $conn->query($sql);?>
<table class="table table-striped" style="width:100%">
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
<td style="display:none">Discount</td>
<td>Total</td>
<td style="display:none">Round Off</td>
<td style="display:none">Remarks</td>
<td></td>
</tr>
</thead>
<tbody>
<?php
$sql_det="SELECT t.subtrans_id,t.item_id,t.cost,price,ifnull(t.discount,'0') discount,t.qty,t.total, t.tax,t.tax_amount,t.tax_sgst,t.total,t.remarks,t.tax_amount_sgst,t.tax_igst,t.tax_amount_igst,i.item_name,t.price,t.roundoff,t.account,t.parent,t.perc FROM sales_trans_det t,item i where i.item_id=t.item_id and t.trans_id='".$trans_id."' and t.trans_id!='' and t.subtrans_id!='' and t.qty>0 and total>0 and t.active_status='A'  and t.ver='".$ver."'order by t.subtrans_id";	
$result_det = $conn->query($sql_det);
//echo $sql_det;
$display="none";
$readonly="";
$slno=0;
while ($row_det = $result_det->fetch_assoc()) {
	$det_price=$row_det["price"];
	$det_roundoff=$row_det["roundoff"];
	$det_item_name=$row_det["item_name"];	
	$det_account = $row_det["account"];
	$det_parent = $row_det["parent"];
	$det_perc = $row_det["perc"];
		
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
		
		
		if($det_account=="Y"){
		$grand_total+=$det_total;
		
//	$discount_amount+=$row_det["discount"];
	//$roundoff_amount+=$row_det["roundoff"];
	$price_amount+=$row_det["price"]+$row_det["discount"]+$row_det["roundoff"];
	echo "".$price_amount;
		$cgst_amount+=$row_det["tax_amount"];
		$sgst_amount+=$row_det["tax_amount_sgst"];
		$igst_amount+=$row_det["tax_amount_igst"];
		}
		$readonly="readonly";

	
	
	?>
<tr class="purchase_rows" id="row_<?php echo $slno;?>" style="display:<?php echo $display;?>" onfocusout="add_service_det('<?php echo $slno;?>')">
<td><?php echo $det_item_name;?>
<input type="hidden" id="item_id_<?php echo $slno;?>" value="<?php echo $row_det["item_id"];?>">
<input type="hidden" id="subtrans_id_<?php echo $slno;?>" value="<?php echo $det_subtrans_id;?>" >
<input type="hidden" id="account_<?php echo $slno;?>" value="<?php echo $det_account;?>" >
<input type="hidden" id="parent_<?php echo $slno;?>" value="<?php echo $det_parent;?>" >
<input type="hidden" id="perc_<?php echo $slno;?>" value="<?php echo $det_perc;?>" >
</td>
	<td><div class="tooltip-wrapper"><input class="form-control tooltip-input"  <?php echo $final_disabler;?>  id="qty_<?php echo $slno;?>" onblur="calculate('<?php echo $slno;?>')" value="<?php echo $det_qty;?>"> <!--<div class="tooltip-text">Once saved cannot be edited. If required, delete and reenter.</div>-->
</div></td>
	<td><input class="form-control"  <?php echo $final_disabler;?>  id="price_<?php echo $slno;?>" value="<?php echo $det_price;?>" onblur="calculate('<?php echo $slno;?>')"></td>

	<td><input class="form-control" <?php echo $final_disabler;?> readonly value="<?php echo $row_det['cost'];?>" id="cost_<?php echo $slno;?>" value="<?php echo $det_cost;?>" onblur="calculate('<?php echo $slno;?>')"></td>

<td style="display:none"><input style="text-align:right" <?php echo $final_disabler;?> class="form-control" readonly value="<?php echo $tax_pc;?>" id="gst_<?php echo $slno;?>"></td>
<td style="display:none"><input style="text-align:right" <?php echo $final_disabler;?> class="form-control" readonly value="<?php echo $tax_pc_sgst;?>" id="sgst_<?php echo $slno;?>" ></td>
<td style="display:none"><input style="text-align:right" <?php echo $final_disabler;?> class="form-control" readonly value="<?php echo $tax_pc_igst;?>" id="igst_<?php echo $slno;?>" ></td>

<td style="display:<?php echo $visibility;?>"><input class="form-control" readonly style="text-align:right" id="tax_gst_<?php echo $slno;?>" value="<?php echo $det_tax;?>"></td>
<td style="display:<?php echo $visibility;?>"><input class="form-control" style="text-align:right" readonly id="tax_sgst_<?php echo $slno;?>" value="<?php echo $det_tax_sgst;?>"></td>
<td style="display:<?php echo $visibility;?>"><input class="form-control" style="text-align:right" readonly id="tax_igst_<?php echo $slno;?>" value="<?php echo $det_tax_igst;?>"></td>


<td style="display:none"><input class="form-control" <?php echo $final_disabler;?>   id="discount_<?php echo $slno;?>" value="<?php echo $det_discount;?>" onblur="calculate('<?php echo $slno;?>')"></td>
<td><input class="form-control" <?php echo $final_disabler;?>   id="total_<?php echo $slno;?>" value="<?php echo $det_total;?>">
<input class="form-control" <?php echo $final_disabler;?> type="hidden"  id="id_<?php echo $slno;?>" value="<?php echo $det_subtrans_id;?>"></td>
<td style="display:none"><input class="form-control" <?php echo $final_disabler;?>   id="roundoff_<?php echo $slno;?>" value="<?php echo $det_roundoff;?>" onblur="calculate('<?php echo $slno;?>')"></td>

	<td style="display:none"><input class="form-control" <?php echo $final_disabler;?>   id="remarks_<?php echo $slno;?>" value="<?php echo $det_remarks;?>" onblur="calculate('<?php echo $slno;?>')"></td>
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
<input type="hidden" id="account_<?php echo $slno;?>" value="" >
<input type="hidden" id="parent_<?php echo $slno;?>" value="" >
<input type="hidden" id="perc_<?php echo $slno;?>" value="" >
</td>
	<td><div class="tooltip-wrapper"><input class="form-control tooltip-input"  id="qty_<?php echo $slno;?>" onblur="calculate('<?php echo $slno;?>')"   <div class="tooltip-text"><!--Once saved cannot be edited. If required, delete and reenter.--></div>
</div></td>
	<td><input class="form-control"  id="price_<?php echo $slno;?>" readonly onblur="calculate('<?php echo $slno;?>')"></td>

	<td><input class="form-control" <?php echo $final_disabler;?> readonly value="" id="cost_<?php echo $slno;?>"  onblur="calculate('<?php echo $slno;?>')"></td>
	
<td style="display:none"><input style="text-align:right;" <?php echo $final_disabler;?> class="form-control" readonly  id="gst_<?php echo $slno;?>"></td>
<td style="display:none"><input style="text-align:right" <?php echo $final_disabler;?> class="form-control" readonly  id="sgst_<?php echo $slno;?>" ></td>
<td style="display:none"><input style="text-align:right" <?php echo $final_disabler;?> class="form-control" readonly  id="igst_<?php echo $slno;?>" ></td>

<td style="display:<?php echo $visibility;?>"><input class="form-control" readonly style="text-align:right" id="tax_gst_<?php echo $slno;?>" value=""></td>
<td style="display:<?php echo $visibility;?>"><input class="form-control" style="text-align:right" readonly id="tax_sgst_<?php echo $slno;?>" value=""></td>
<td style="display:<?php echo $visibility;?>"><input class="form-control" style="text-align:right" readonly id="tax_igst_<?php echo $slno;?>" value=""></td>
<td style="display:none"><input class="form-control"    id="discount_<?php echo $slno;?>" value="" onblur="calculate('<?php echo $slno;?>')"></td>
<td><input class="form-control" <?php echo $final_disabler;?>   id="total_<?php echo $slno;?>" value="" onblur="calculate('<?php echo $slno;?>')">
<input class="form-control" <?php echo $final_disabler;?> type="hidden"  id="id_<?php echo $slno;?>" value=""></td>
<td style="display:none"><input class="form-control" <?php echo $final_disabler;?>   id="roundoff_<?php echo $slno;?>" value="" onblur="calculate('<?php echo $slno;?>')"></td>
	<td style="display:none"><input class="form-control" <?php echo $final_disabler;?>   id="remarks_<?php echo $slno;?>" value="" onblur="calculate('<?php echo $slno;?>')"></td>
<td>
<button class="btn btn-xs btn-danger" <?php echo $final_disabler;?> onclick="del_service_det('<?php echo $slno;?>')"  id="del_<?php echo $slno;?>;?>">X</button>
</td>
</tr>

<?php $slno++;}?>

<tr>
<TD></TD>
<TD></TD>

<TD><input id="price_amount" class="form-control" readonly value="<?php echo $price_amount;?>"></TD>

<TD></TD>

<TD><input id="cgst_amount" class="form-control" readonly value="<?php echo $cgst_amount;?>"></TD>
<TD><input id="sgst_amount" class="form-control" readonly value="<?php echo $sgst_amount;?>"></TD>

<TD><input id="igst_amount" class="form-control" readonly value="<?php echo $igst_amount;?>"></TD>

<TD><input id="trans_amount" class="form-control" readonly value="<?php echo $grand_total;?>"></TD>
<TD>Discount : <input id="discount_amount" class="form-control"  value="<?php echo $discount_amount;?>" onblur="calculate_final()"><br>
Round Off <input id="roundoff_amount" class="form-control"  value="<?php echo $roundoff_amount;?>" onblur="calculate_final()"><br>Total Invoice AMount :   <input id="final_amount" class="form-control"  value="<?php echo $grand_total+($discount_amount)+($roundoff_amount);?>"></TD>


</tr> 

</tbody>
</table>

	<?php if($active_status=="A"){?>
<hr><center><button class="btn btn-success" disabled>Saved already</button>&nbsp;<button class="btn btn-primary" id="jobcard"  onclick="unlock()">Edit</button></center><hr>	

<?php }else{?>
	<hr><center><button class="btn btn-success" onclick="save_dummy()">Save</button></center><hr>	
<?php }?>
</div>
<div class="col-md-12">

<table class="table">

<?php $sql_pay="SELECT p.mode,p.account,p.reference,amount,a.account_name FROM payments p,pay_track i,account a where i.trans_id='".$trans_id."' and i.trans_id!='' and i.payment_id=p.payment_id and a.account_id=p.account and i.mode='sales'";
//echo $sql_pay;
$result_pay = $conn->query($sql_pay);
$pay_ind=0;
$total_amount=0;
while($row_pay = $result_pay->fetch_assoc()) {

$mode = $row_pay["mode"];
$account = $row_pay["account"];	
$reference = $row_pay["reference"];	
$amount = $row_pay["amount"];
$total_amount+=	$amount;		
?>
<tr>
<td></td>
<td><?php echo $row_pay['mode'];?></td>
<td><?php echo $row_pay['account_name'];?></td>
<td><?php echo $reference;?></td>
<td><?php echo $amount;?></td>
</tr>
<?php }?>
</table>
<?php if (($grand_total>$total_amount || $total_amount==0)&&($active_status=="A")){$disabled="";}else{$disabled="disabled";}?>

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal" id="add_opener"  <?php echo $disabled;?>>
  Add Payments
</button>

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
  
<script>
var del_mode=0;
var addModal = new bootstrap.Modal(document.getElementById('addModal'));
var editModal = new bootstrap.Modal(document.getElementById('editModal'));
document.getElementById("customer_search").addEventListener("input", function () {
  console.log(document.getElementById("customer_search").value);
  let parts= document.getElementById("customer_search").value.split("~");
 
});
	
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
	$('#item_search').searchableOptionList({
    maxHeight: '350px',
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
	$('#group_search').searchableOptionList({
    maxHeight: '350px',
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
        select_group();
    }
	});
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
  
  let main_slno = load_service(parts[0],parts[3],parts[4],parts[5],parts[1],"","N","100");
  console.log("main_slno"+main_slno);
  setTimeout(function () {

    // Second call after 5 seconds
   
    // Then AJAX call
    $.post("fetch_items.php",
    {
        itemgroup_id: parts[0]
    },
    function (data, status) {
	console.log("data is"+data);
       
		if (!data || $.trim(data) === "" || $.trim(data) === "null" || $.trim(data) === "0") {
    console.log("came here");
	if (typeof main_slno === "undefined" || main_slno === null) {
    main_slno = 0;
}
    document.getElementById("account_" + main_slno).value = "Y";
}else{
			 let list = $.trim(data).split("@");
        for (let i = 0; i < list.length-1; i++) {

            let parts1 = list[i].split("~");
console.log("part"+parts1[0]+"=="+parts1[1]);
           load_service(
                    parts1[0],
                    "",
                    parts1[2],
                    parts1[3],
                    parts1[1],
                    parts[0],
                    "Y",
                    parts1[4]
                );

	}
	
	}
    });

}, 200);


	}	
	function select_group(){
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
			 // First call
var mainParts = myvar.split("~");

load_service(mainParts[0], "", "9", "9", mainParts[1], "", "N", "100");
console.log("group"+mainParts[0]+"=="+mainParts[1]);
// Wait 5 seconds
setTimeout(function () {

    // Second call after 5 seconds
   
    // Then AJAX call
    $.post("fetch_items.php",
    {
        itemgroup_id: mainParts[0]
    },
    function (data, status) {

        let list = $.trim(data).split("@");

        for (let i = 0; i < list.length-1; i++) {

            let parts = list[i].split("~");
console.log("part"+parts[0]+"=="+parts[1]);
           load_service(
                    parts[0],
                    "",
                    parts[2],
                    parts[3],
                    parts[1],
                    mainParts[0],
                    "Y",
                    parts[4]
                );

        }
    });

}, 7000);

	}
		
	function select_customer(){
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
	 $("#customer").val(parts[0]);
   $("#company_name").val(parts[1]);
     $("#customer_name").val(parts[2]);
	   $("#customer_mobile").val(parts[3]);
	    get_gst();
	 get_address();}
/*document.getElementById("item_search").addEventListener("blur", function () {
  console.log(document.getElementById("customer_search").value);
  let parts= document.getElementById("item_search").value.split("~");
  document.getElementById("item_search").value="";
  load_service(parts[0],parts[3],parts[4],parts[5]);
});*/

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
	let item_selector = "#item_id_"+service;
	let cost_selector="#cost_"+service;
	let gst_selector="#gst_"+service;
	let sgst_selector="#sgst_"+service;
	let igst_selector="#igst_"+service;
	let tax_gst_selector="#tax_gst_"+service;
	let tax_sgst_selector="#tax_sgst_"+service;
	let tax_igst_selector="#tax_igst_"+service;
	let total_selector="#total_"+service;
  let parent_selector="#parent_"+service;
  let account_selector="#account_"+service;
  let perc_selector="#perc_"+service;
 
  let roundoff_selector="#roundoff_"+service;
  let remarks_selector="#remarks_"+service;
   let subtrans_id_selector="#subtrans_id_"+service;
  let cost=$(cost_selector).val();
 let tax_pc=$(gst_selector).val();
  let tax_pc_sgst=$(sgst_selector).val();
  let tax_pc_igst=$(igst_selector).val();
 let total=$(total_selector).val();
 let tax=$(tax_gst_selector).val();
  let tax_sgst=$(tax_sgst_selector).val();
   let tax_igst=$(tax_igst_selector).val();
  let qty=$(qty_selector).val();
  let discount=$(discount_selector).val();
  let parent=$(parent_selector).val();
   let account=$(account_selector).val();
   let price=$(price_selector).val();
   let perc=$(perc_selector).val();
   let subtrans_id=$(subtrans_id_selector).val();
   if(del_mode==0){
	    $(qty_selector).prop('disabled', true);
	$(total_selector).prop('disabled', true);
	$(discount_selector).prop('disabled', true);
	$(roundoff_selector).prop('disabled', true);
	$(remarks_selector).prop('disabled', true);
	   
 $.post("add_sales_det.php",
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
	subtrans_id:subtrans_id,
	item_id:$(item_selector).val(),
	price:price,
	roundoff:$(roundoff_selector).val(),
	parent:parent,
	account:account,
	trans_id:$("#trans_id").val(),
	discount:discount,
	ver:$("#ver").val(),
	perc:perc
	
	
  },
  function(data, status){
    //alert($.trim(data));
	// if($.trim(data).indexOf("Error")==-1){
if($.trim(data).indexOf("Error")==-1 && $.trim(data).indexOf("Alert")==-1){
	let id_selector="#subtrans_id_"+service;
	let del_selector="#del_"+service;
	$(id_selector).val($.trim(data).split("~")[0]);
	$("#trans_amount").val($.trim(data).split("~")[1]);
	const paid_amount = document.getElementById("paid_amount");
	
	
	let amount_paid = parseFloat($.trim(data).split("~")[1]);
		console.log(amount_paid);	
	$("#trans_amount").val(amount_paid);
	$("#cgst_amount").val(parseFloat($.trim(data).split("~")[2])|| 0);
	$("#sgst_amount").val(parseFloat($.trim(data).split("~")[3])|| 0);
	$("#igst_amount").val(parseFloat($.trim(data).split("~")[4]) || 0);
	$("#price_amount").val(parseFloat($.trim(data).split("~")[5])|| 0);
	
	
	paid_amount.setAttribute("min", "0");
paid_amount.setAttribute("max", $.trim(data).split("~")[1]);




	let amt = amount_paid || 0;

	// Get decimal part
	let decimalPart = amt - Math.floor(amt);

	// If decimal < 0.5 → round down
	// If decimal >= 0.5 → round up
	let roundedAmount = (decimalPart < 0.5) 
		? Math.floor(amt) 
		: Math.floor(amt) + 1;

	// Roundoff difference
	let roundoffAmount = (roundedAmount - amt).toFixed(2);

	// Set values
	document.getElementById("final_amount").value = roundedAmount.toFixed(2);
	document.getElementById("roundoff_amount").value = roundoffAmount;
	//$(qty_selector).prop('readonly', true);
	//$(discount_selector).prop('readonly', true);
	$(del_selector).prop('disabled', false);}else{
		// alert($.trim(data));
		// let qty_selector = "qty_"+service_id;
		// let discount_selector="discount_"+service_id;
		// document.getElementById(discount_selector).value=="0";
		// document.getElementById(qty_selector).value=="0";
		// let row_selector = "row_"+service_id;
		// document.getElementById(row_selector).style.display="none";

		alert($.trim(data));
		//
		// RESET INVALID VALUES
		//
		document.getElementById("qty_"+service).value = "";
		document.getElementById("total_"+service).value = "";
		document.getElementById("price_"+service).value = "";
		document.getElementById("tax_gst_"+service).value = "";
		document.getElementById("tax_sgst_"+service).value = "";
		document.getElementById("tax_igst_"+service).value = "";
		document.getElementById("discount_"+service).value = "0";
		document.getElementById("roundoff_"+service).value = "0";
		//
		// FOCUS BACK
		//
		document.getElementById("qty_"+service).focus();
		
	}
 $(qty_selector).prop('disabled', false);
	$(total_selector).prop('disabled', false);
	$(discount_selector).prop('disabled', false);
	$(roundoff_selector).prop('disabled', false);
	$(remarks_selector).prop('disabled', false);
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
		$.post("delete_trans_det.php",
  {
    trans_id: trans_id,
	subtrans_id:subtrans_id,
	ver:$("#ver").val()
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
		$.post("get_gst.php",
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
		$.post("get_address.php",
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
		document.querySelectorAll('.row_service').forEach(el => {
    el.style.display = 'none';
});
	document.querySelectorAll('.qty').forEach(el => {
    el.value = '0';
});
	//alert(str);
	if (str.startsWith("37")) {
 $("#cgst_igst").html("CGST");
 $("#cgst_igst_amount").html("CGST(9%)");
// alert("CGST");
  document.querySelectorAll('.hide_sgst').forEach(el => {
    el.style.display = '';
});
}else{
  $("#cgst_igst").html("IGST");
  $("#cgst_igst_amount").html("IGST(18%)");
  
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

function load_service(service,price, gst,sgst,name,parent,account,perc){
	let trans_id=document.getElementById("trans_id").value;
	if(($("#customer").val()==""|| $("#customer").val()==null) && ($("#company_name").val()=="" || $("#company_name").val()==null)){
	   
		alert("Please enter customer Details");
		return false;
	}
	let str = document.getElementById("customer_gst").value;
		let rows = $(".purchase_rows").filter(function () {
  return $(this).css("display") != "none";
});
console.log(rows.length);
var slno=rows.length;
	//alert(str);
	//let str = document.getElementById("customer_gst").value;
	
	//alert(str);
	let gst_selector="gst_"+slno;
	let sgst_selector="sgst_"+slno
	let igst_selector="igst_"+slno;
	let price_selector="cost_"+slno;
	let item_selector="item_id_"+slno;
	let item_name_selector="item_name_"+slno;
	let item_parent_selector="parent_"+slno;
	let item_account_selector="account_"+slno;
	let item_perc_selector="perc_"+slno;
	
	document.getElementById(price_selector).value=parseInt(price|| 0);
	document.getElementById(item_selector).value=service;
	document.getElementById(item_name_selector).innerHTML=name;
	document.getElementById(item_parent_selector).value=parent;
	document.getElementById(item_perc_selector).value=perc;
	document.getElementById(item_account_selector).value=account;	
	if (str.startsWith("37")) {
		document.getElementById(sgst_selector).value=parseInt(gst|| 0);
		document.getElementById(gst_selector).value=parseInt(gst|| 0);
	}else{
		document.getElementById(igst_selector).value=parseInt(gst|| 0)+parseInt(gst|| 0);
		//document.getElementById(sgst_selector).value=0;
	}
	if(trans_id=="" || trans_id==null){
			$.post("add_sales.php",
  {
   
	 customer_mobile: $("#customer_mobile").val(),
	 company_name: $("#company_name").val(),
	  customer_name: $("#customer_name").val(),
	  customer_address: $("#customer_address").val(),
	   customer_gst: $("#customer_gst").val(),
	    customer: $("#customer").val(),
		trans_date:$("#datepicker").val(),
		gst :"<?php echo $_COOKIE["gst"];?>"
			
  },
  function(data, status){
   let selector = "row_"+slno;
document.getElementById(selector).style.display="";	
document.getElementById("trans_id").value = $.trim(data.split("~")[1]);
document.getElementById("year_part").innerHTML = $.trim(data.split("~")[0]);
document.getElementById("ver").value = $.trim(data.split("~")[2]);
return slno;

  });
	}else{let selector = "row_"+slno;
document.getElementById(selector).style.display="";	
return slno;
}
	
	
}
function calculate(service){
	console.log(service);
	let price_selector = "price_"+service;
	let cost_selector = "cost_"+service;
	let discount_selector = "discount_"+service;
	let roundoff_selector = "roundoff_"+service;
	let qty_selector = "qty_"+service;
	let total_selector="total_"+service;
	let discount=(document.getElementById(discount_selector).value=="" || document.getElementById(discount_selector).value==null)?0:document.getElementById(discount_selector).value;
	let roundoff=(document.getElementById(roundoff_selector).value=="" || document.getElementById(roundoff_selector).value==null)?0:document.getElementById(roundoff_selector).value;
	let total=(document.getElementById(total_selector).value=="" || document.getElementById(total_selector).value==null)?0:document.getElementById(total_selector).value;
	
	
	let gst_selector="gst_"+service;
	let sgst_selector="sgst_"+service;
	let igst_selector="igst_"+service;
	let tax_gst_selector="tax_gst_"+service;
	let tax_sgst_selector="tax_sgst_"+service;
	let tax_igst_selector="tax_igst_"+service;
	let tax_perc_gst="";
	try{tax_perc_gst=parseFloat(document.getElementById(gst_selector).value|| 0);}catch(err){tax_perc_gst=0;}
	let tax_perc_sgst="";
	try{tax_perc_sgst=parseFloat(document.getElementById(sgst_selector).value|| 0);}catch(err){tax_perc_sgst=0;}
	let tax_perc_igst="";
	try{tax_perc_igst=parseFloat(document.getElementById(igst_selector).value|| 0);}catch(err){tax_perc_igst=0;}
	if (Number.isNaN(tax_perc_gst)) {
 tax_perc_gst=0;
}
	if (Number.isNaN(tax_perc_sgst)) {
 tax_perc_sgst=0;
}
	if (Number.isNaN(tax_perc_igst)) {
 tax_perc_igst=0;
}
	
	document.getElementById(price_selector).value=total/(1+(tax_perc_gst+tax_perc_sgst+tax_perc_igst)/100.00);
	document.getElementById(cost_selector).value=parseFloat(document.getElementById(price_selector).value|| 0)/parseFloat((document.getElementById(qty_selector).value|| 0));
	
	document.getElementById(tax_gst_selector).value=((parseFloat(document.getElementById(price_selector).value|| 0)+(parseFloat(discount|| 0))+(parseFloat(roundoff|| 0)))*tax_perc_gst/100.0).toFixed(2);
document.getElementById(tax_sgst_selector).value=((parseFloat(document.getElementById(price_selector).value|| 0)+(parseFloat(discount|| 0))+(parseFloat(roundoff|| 0)))*tax_perc_sgst/100.0).toFixed(2);
document.getElementById(tax_igst_selector).value=((parseFloat(document.getElementById(price_selector).value|| 0)+(parseFloat(discount|| 0))+(parseFloat(roundoff|| 0)))*tax_perc_igst/100.0).toFixed(2);
/////CHECKING FOR CHILDREN

let account_selector="account_"+service;
let account=document.getElementById(account_selector).value;
console.log(account);
let item_selector="item_id_"+service;
let item=document.getElementById(item_selector).value;
if(account=="N"){
var rows = document.querySelectorAll(".purchase_rows");

rows.forEach(function(row) {

    // Example id: rows_5
    var rowId = row.id;

    if (rowId && rowId.startsWith("row_")) {

        // Extract slno (i)
        var slno = rowId.split("_")[1];

        // Build parent_slno id
        var parentElement = document.getElementById("parent_" + slno);

        if (parentElement) {
            console.log("Row:", rowId);
            console.log("Parent SL No value:", parentElement.value); 
			if(parentElement.value==item){
				setTimeout(function () {



        let qtyVal = parseFloat(document.getElementById(qty_selector).value);
        let totalVal = parseFloat(document.getElementById(total_selector).value);

        let child_qty = document.getElementById("qty_" + slno);
        let child_total = document.getElementById("total_" + slno);
        let child_perc = document.getElementById("perc_" + slno);

        child_qty.value = qtyVal;
        child_total.value = totalVal * parseFloat(child_perc.value) / 100;

        calculate(slno);
		add_service_det(slno);

    }, 700);
			}
            // or parentElement.innerText depending on element type
        }
    }
});
	
}
	}
function del_service_det(service){
let id_selector="subtrans_id_"+service;	
let subtrans_id=document.getElementById(id_selector).value;	
let item = document.getElementById("item_id_"+service).value;	
	var r = confirm("Are you sure that you want to delete the transaction");
	if(r){
		 del_mode=1;
		$.post("delete_trans_det_sales.php",
  {
    trans_id: $("#trans_id").val(),
	subtrans_id:subtrans_id,
	item_id:item
  },
  function(data, status){
  let qty_selector = "qty_"+service;
	let discount_selector="discount_"+service;
			let cost_selector = "cost_"+service;
	let total_selector="total_"+service;
			let tax_gst_selector = "tax_gst_"+service;
	let tax_sgst_selector="tax_sgst_"+service;
	document.getElementById(discount_selector).value="0";
	document.getElementById(qty_selector).value="0";
			document.getElementById(cost_selector).value="0";
	document.getElementById(total_selector).value="0";
			document.getElementById(tax_gst_selector).value="0";
	document.getElementById(tax_sgst_selector).value="0";
	let row_selector = "row_"+service;
	document.getElementById(row_selector).style.display="none";
	let amount_paid = parseFloat($.trim(data).split("~")[1]|| 0);
	/*	console.log(amount_paid);	
	$("#trans_amount").val(amount_paid);
	$("#cgst_amount").val(parseFloat($.trim(data).split("~")[2]));
	$("#sgst_amount").val(parseFloat($.trim(data).split("~")[3]));
	$("#igst_amount").val(parseFloat($.trim(data).split("~")[4]));
	$("#price_amount").val(parseFloat($.trim(data).split("~")[5]));*/
	
	
	$("#trans_amount").val(amount_paid);
	$("#cgst_amount").val(parseFloat($.trim(data).split("~")[2])|| 0);
	$("#sgst_amount").val(parseFloat($.trim(data).split("~")[3])|| 0);
	$("#igst_amount").val(parseFloat($.trim(data).split("~")[4]) || 0);
	$("#price_amount").val(parseFloat($.trim(data).split("~")[5])|| 0);
	
	let amt = amount_paid+(discount) || 0;

// Get decimal part
let decimalPart = amt - Math.floor(amt);

// If decimal < 0.5 → round down
// If decimal >= 0.5 → round up
let roundedAmount = (decimalPart < 0.5) 
    ? Math.floor(amt) 
    : Math.floor(amt) + 1;

// Roundoff difference
let roundoffAmount = (roundedAmount - amt).toFixed(2);

// Set values
document.getElementById("final_amount").value = roundedAmount.toFixed(2);
document.getElementById("roundoff_amount").value = roundoffAmount;
	
	//document.getElementById("final_amount").value=amount_paid;
	
	//$("#trans_amount").val($.trim(data).split("~")[1]);
	setTimeout(() => {
 del_mode=0;
  console.log("Variable set:", myVar);
}, 700);
	//window.location.href="service_trans_new.php?trans_id="+$("#trans_id").val();
	
  });
}
}
	function dummy_save(){
	window.location.href="sales_trans_new.php?trans_id="+$("#trans_id").val();
	}
function save_pay(){
	let paid_amount=$("#paid_amount").val();
	let pay_type=$("#pay_type").val();
	let ref_no=$("#ref_no").val();
	let trans_amount=$("#trans_amount").val();
	let account="";
	try{account=$("#account").val().split("~")[1];}catch(err){account="";}
	$.post("update_pay_sales.php",
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
	$.post("update_pay_sales.php",
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
	window.location.href="sales_trans_new.php?trans_id="+$("#trans_id").val();

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
		 $.post("save_draft_sale.php",
  {
    value: $("#trans_id").val(),
	discount: $("#discount_amount").val(),
	roundoff: $("#roundoff_amount").val(),
	trans_amount:$("#trans_amount").val(),
	ver:$("#ver").val()
	
  },
  function(data, status){
	  console.log(data);
	alert("Saved Transaction Successfully");
	 location.href="sales_trans_new.php?trans_id="+$("#trans_id").val();
	$("#add_opener").prop("disabled", false);
  });
	}
	
	function unlock(){
		$.post("unlock_sales.php",
  {
    trans_id: $("#trans_id").val(),
	ver:$("#ver").val()
	
	
  },
  function(data, status){
	  console.log(data);
   alert("Transaction unlocked with ID: "+$("#trans_id").val());
	//$('#invoice').prop('disabled', false);
	window.location.href="sales_trans_new.php?trans_id="+$("#trans_id").val();

  });
	}
	function calculate_final(){
		
		let discount_amount= 0.0,roundoff_amount=0.0,trans_amount=0.0,final_amount=0.0;
		try{discount_amount=parseFloat(document.getElementById("discount_amount").value|| 0);}
		catch(err){discount_amount=0.0;}
		try{roundoff_amount=parseFloat(document.getElementById("roundoff_amount").value|| 0);}
		catch(err){roundoff_amount=0.0;}
		try{trans_amount=parseFloat(document.getElementById("trans_amount").value|| 0);}
		catch(err){trans_amount=0.0;}
		//final_amount=trans_amount+(discount_amount)+(roundoff_amount);
		//document.getElementById("final_amount").value=final_amount;
		let amt=trans_amount+(discount_amount);
		let decimalPart = amt - Math.floor(trans_amount);

// If decimal < 0.5 → round down
// If decimal >= 0.5 → round up
let roundedAmount = (decimalPart < 0.5) 
    ? Math.floor(amt) 
    : Math.floor(amt) + 1;

// Roundoff difference
let roundoffAmount = (roundedAmount - amt).toFixed(2);

// Set values
document.getElementById("final_amount").value = roundedAmount.toFixed(2);
document.getElementById("roundoff_amount").value = roundoffAmount;

	}
</script>




  

</body>

</html>