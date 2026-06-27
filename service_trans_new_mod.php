<!DOCTYPE html>
<html lang="en">

<head>
	<?php
session_start();

if (
   (!isset($_SESSION["user_id"]) || $_SESSION["user_id"]=="") ||
   (!isset($_SESSION["shop"]) || $_SESSION["shop"]=="")
) {
   header("Location: login.php?redirect=" . urlencode($_SERVER["REQUEST_URI"]));
   exit();
}

include "header_include.php";
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
	<link href="multi/searchableOptionList.css" rel="stylesheet">
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

						<?php
						// ================= INIT VARIABLES =================
						$trans_id      = "";
						$trans_date    = "";
						$details       = "";
						$customer      = "";
						$trans_amount  = "";
						$pending       = "";
						$gst           = "";
						$tally         = "";
						$created_by    = "";
						$created_on    = "";
						$modified_by   = "";
						$modified_on   = "";
						$active_status = "";
						$VEHICLE_NO    = "";
						$VEHICLE_MODEL = "";
						$NO_OF_WHEELS  = "";
						$COMPANY_NAME  = "";
						$CUSTOMER_NAME = "";
						$CUSTOMER_ADDRESS = "";
						$CUSTOMER_GST  = "";
						$VEHICLE_ODOMETER = "";
						$VEHICLE       = "";
						$CUSTOMER_MOBILE = "";
						$VEHICLE_MAKE  = "";
						$det_price     = "";
						$det_discount  = "";
						$mech          = "";
						$final_disabler = "";
						$grand_total   = 0;
						$total_amount  = 0;

						$trans_id = isset($_GET["trans_id"]) ? $_GET["trans_id"] : "";

						$sql = "SELECT trans_id, trans_date, details, customer, trans_amount, pending, gst, tally,
						        created_by, created_on, modified_by, modified_on, active_status,
						        VEHICLE_NO, VEHICLE_MODEL, NO_OF_WHEELS, COMPANY_NAME, CUSTOMER_NAME,
						        CUSTOMER_ADDRESS, CUSTOMER_GST, VEHICLE_ODOMETER, VEHICLE,
						        CUSTOMER_MOBILE, VEHICLE_MAKE, mech
						        FROM service_trans WHERE trans_id='" . $trans_id . "'";
						$result = $conn->query($sql);

						if ($row = $result->fetch_assoc()) {
							$trans_id         = $row["trans_id"];
							$trans_date       = $row["trans_date"];
							$details          = $row["trans_id"];
							$customer         = $row["customer"];
							$trans_amount     = $row["trans_amount"];
							$pending          = $row["pending"];
							$gst              = $row["gst"];
							$tally            = $row["tally"];
							$created_by       = $row["created_by"];
							$created_on       = $row["created_on"];
							$modified_by      = $row["modified_by"];
							$modified_on      = $row["modified_on"];
							$active_status    = $row["active_status"];
							$VEHICLE_NO       = $row["VEHICLE_NO"];
							$VEHICLE_MODEL    = $row["VEHICLE_MODEL"];
							$VEHICLE_MAKE     = $row["VEHICLE_MAKE"];
							$NO_OF_WHEELS     = $row["NO_OF_WHEELS"];
							$COMPANY_NAME     = $row["COMPANY_NAME"];
							$CUSTOMER_NAME    = $row["CUSTOMER_NAME"];
							$CUSTOMER_ADDRESS = $row["CUSTOMER_ADDRESS"];
							$CUSTOMER_GST     = $row["CUSTOMER_GST"];
							$CUSTOMER_MOBILE  = $row["CUSTOMER_MOBILE"];
							$VEHICLE_ODOMETER = $row["VEHICLE_ODOMETER"];
							$VEHICLE          = $row["VEHICLE"];
							$mech             = $row["mech"];
							if ($active_status == "A") {
								$final_disabler = "disabled";
							}
						}

						// Only fall back to cookie if gst is truly empty
						if (!isset($gst) || $gst == "") {
							$gst = isset($_COOKIE["gst"]) ? $_COOKIE["gst"] : "";
						}

						// Visibility of GST columns
						if ($gst == "Y") {
							$visibility = "";
						} else {
							$visibility = "none";
						}
						?>

						<div class="col-md-12">
							<div class="alert alert-warning" style="text-align:center;font-weight:bold">
								Service : GST:<?php echo $gst; ?>
							</div><br><br>

							<span class="alert alert-info">Vehicle Details</span>
							<table class="table">
								<tr>
									<td>Date<input type="text" id="datepicker" class="form-control"></td>
									<td>Invoice No <input class="form-control" readonly name="trans_id" id="trans_id" value="<?php echo $trans_id; ?>"></td>
									<td colspan="2">Vehicle No <input class="form-control" name="vehicle_no" id="vehicle_no" value="<?php echo $VEHICLE_NO; ?>" onblur="get_details();"></td>
								</tr>
								<tr>
									<td>Vehicle Make <input class="form-control" name="vehicle_make" id="vehicle_make" list="make_list" value="<?php echo $VEHICLE_MAKE; ?>" autocomplete="off">
										<datalist id="make_list">
											<?php $sql = "SELECT make_name FROM make order by 1";
											$result = $conn->query($sql);
											while ($row1 = $result->fetch_assoc()) { ?>
												<option value="<?php echo $row1["make_name"]; ?>"></option>
											<?php } ?>
										</datalist>
									</td>
									<td>Vehicle Model <input class="form-control" name="vehicle_model" id="vehicle_model" list="model_list" value="<?php echo $VEHICLE_MODEL; ?>" autocomplete="off">
										<datalist id="model_list">
											<?php $sql = "SELECT model_name FROM model order by 1";
											$result = $conn->query($sql);
											while ($row1 = $result->fetch_assoc()) { ?>
												<option value="<?php echo $row1["model_name"]; ?>"></option>
											<?php } ?>
										</datalist>
									</td>
									<td>No of Wheels <input class="form-control" name="no_of_wheels" id="no_of_wheels" value="<?php echo $NO_OF_WHEELS; ?>" onblur="setwheels()"></td>
									<td>Odometer <input class="form-control" name="vehicle_odometer" id="vehicle_odometer" value="<?php echo $VEHICLE_ODOMETER; ?>"></td>
								</tr>
								<input class="form-control" name="vehicle" id="vehicle" value="<?php echo $VEHICLE; ?>" type="hidden">
							</table>
						</div>

						<div class="col-md-12">
							<span class="alert alert-success">Customer Details</span>
							<table class="table">
								<tr>
									<td>
										Search Customer<br>
										<select class="form-control" name="customer_search" id="customer_search" multiple="multiple" style="max-width:300px!important">
											<?php $sql = "SELECT customer_id,company_name,owner_name,owner_mobile FROM customer";
											$result = $conn->query($sql);
											while ($row1 = $result->fetch_assoc()) { ?>
												<option value="<?php echo $row1["customer_id"] ?>~<?php echo $row1["company_name"] ?>~<?php echo $row1["owner_name"] ?>~<?php echo $row1["owner_mobile"] ?>"><?php echo $row1["company_name"] ?>,<?php echo $row1["owner_name"] ?></option>
											<?php } ?>
										</select>
									</td>
									<td>Company Name<input class="form-control" name="company_name" id="company_name" value="<?php echo $COMPANY_NAME; ?>" LIST="cust_list"></td>
									<td>Customer Name <input class="form-control" name="customer_name" id="customer_name" value="<?php echo $CUSTOMER_NAME; ?>"></td>
									<td>Customer Mobile
										<input class="form-control" name="customer_mobile" id="customer_mobile"
											value="<?php echo $CUSTOMER_MOBILE; ?>" maxlength="10"
											pattern="[6-9]{1}[0-9]{9}" inputmode="numeric"
											oninput="this.value=this.value.replace(/\D/g,'')" placeholder="">
									</td>
								</tr>
								<tr>
									<td colspan="1" style="display:<?php echo $visibility; ?>">
										GST <input class="form-control" name="customer_gst" id="customer_gst"
											value="<?php echo $CUSTOMER_GST; ?>" list="gst_list"
											onfocus="get_gst()" autocomplete="off" onblur="set_gst()">
										<datalist id="gst_list"></datalist>
									</td>
									<td colspan="2">Address <input class="form-control" name="customer_address" id="customer_address" value="<?php echo $CUSTOMER_ADDRESS; ?>" list="address_list" onfocus="get_address()" autocomplete="off">
										<datalist id="address_list"></datalist>
										<input class="form-control" name="customer" id="customer" value="<?php echo $customer; ?>" type="hidden">
									</td>
									<td>
										<td colspan="1">Mechanic <input class="form-control" name="mech" id="mech" value="<?php echo $mech; ?>" list="mech_list">
											<datalist id="mech_list">
												<?php $sql = "SELECT mech_id,mech_name FROM mech";
												$result = $conn->query($sql);
												while ($row1 = $result->fetch_assoc()) { ?>
													<option value="<?php echo $row1["mech_name"] ?>"></option>
												<?php } ?>
											</datalist>
										</td>
									</td>
								</tr>
							</table>
						</div>

						<div class="col-md-12">
							<?php $sql = "SELECT service_id,service_name,service_description,cost,tax_pc,tax_pc_sgst,price_edit FROM service order by view_order";
							$result = $conn->query($sql);
							while ($row1 = $result->fetch_assoc()) {
								$service_name = str_replace("CAR", "<i class='fa fa-car' aria-hidden='true'></i>", $row1['service_name']);
								$service_name = str_replace("TRUCK", "<i class='fa fa-truck' aria-hidden='true'></i>", $service_name);
							?>
								<button <?php echo $final_disabler; ?> class="btn btn-secondary badge" style="font-size:80%"
									onclick="load_service('<?php echo $row1['service_id']; ?>','<?php echo $row1['cost']; ?>','<?php echo $row1['tax_pc']; ?>','<?php echo $row1['tax_pc_sgst']; ?>','<?php echo $row1['price_edit']; ?>')"><?php echo $service_name; ?></button>
							<?php } ?>

							<?php $sql = "SELECT service_id,service_name,service_description,cost,tax_pc,tax_pc_sgst,price_edit FROM service order by view_order";
							$result = $conn->query($sql); ?>

							<table class="table table-striped">
								<thead>
									<tr>
										<td>Service</td>
										<td>Qty</td>
										<td>Price</td>
										<td style="display:none"><span id="cgst_igst">CGST</span>(%)</td>
										<td style="display:none">SGST(%)</td>
										<td>Cost</td>
										<td style="display:<?php echo $visibility; ?>"><span id="cgst_igst_amount">CGST(9%)</span></td>
										<td style="display:<?php echo $visibility; ?>" class="hide_sgst">SGST(9%)</td>
										<td>Discount</td>
										<td>Total</td>
										<td></td>
									</tr>
								</thead>
								<tbody>
									<?php
									$grand_total = 0;
									while ($row1 = $result->fetch_assoc()) {

										if ($gst == "Y") {
											$tax_pc      = $row1["tax_pc"];
											$tax_pc_sgst = $row1["tax_pc_sgst"];
										} else {
											$tax_pc      = "0";
											$tax_pc_sgst = "0";
										}

										$sql_det = "SELECT subtrans_id, service_id, cost, discount, qty, total,
										            tax, tax_amount, tax_sgst, tax_amount_sgst,
										            total+tax_amount+tax_amount_sgst as grand_total
										            FROM service_trans_det
										            WHERE trans_id='" . $trans_id . "'
										            AND service_id='" . $row1["service_id"] . "'
										            AND active_status='A' AND trans_id!=''";
										$result_det = $conn->query($sql_det);
										$display = "none";

										$det_price          = "";
										$det_qty            = "";
										$det_discount       = "";
										$det_cost           = "";
										$det_tax_amount     = "";
										$det_tax_amount_sgst = "";
										$det_total          = "";
										$det_subtrans_id    = "";

										if ($row_det = $result_det->fetch_assoc()) {
											$det_price           = $row_det["cost"];
											$det_tax             = $row_det["tax"];
											$tax_pc              = $det_tax;
											$det_tax_sgst        = $row_det["tax_sgst"];
											$tax_pc_sgst         = $det_tax_sgst;
											$det_qty             = $row_det["qty"];
											$det_discount        = $row_det["discount"];
											$det_cost            = ($det_price) * $det_qty - $det_discount;
											$det_tax_amount      = $row_det["tax_amount"];
											$det_tax_amount_sgst = $row_det["tax_amount_sgst"];
											$det_total           = $row_det["grand_total"];
											$det_subtrans_id     = $row_det["subtrans_id"];
											$display             = "";
											$grand_total        += $det_total;
										}

										// FIX: determine price_edit readonly state in PHP correctly
										$price_readonly = ($row1["price_edit"] == 'Y') ? '' : 'readonly';
									?>
										<tr id="row_<?php echo $row1["service_id"]; ?>"
											style="display:<?php echo $display; ?>"
											class="row_service">
											<td><?php echo $row1["service_name"]; ?>
												<input class="form-control" id="msp_<?php echo $row1["service_id"]; ?>" type="hidden">
											</td>
											<td>
												<div class="tooltip-wrapper">
													<!-- FIX: onblur triggers save only; no onfocusout on row -->
													<input style="text-align:right" <?php echo $final_disabler; ?>
														class="form-control tooltip-input qty"
														id="qty_<?php echo $row1["service_id"]; ?>"
														onblur="on_qty_blur('<?php echo $row1["service_id"]; ?>')"
														value="<?php echo $det_qty; ?>">
													<div class="tooltip-text">Once saved cannot be edited. If required, delete and reenter.</div>
												</div>
											</td>
											<td>
												<!-- FIX: readonly controlled by PHP price_edit flag -->
<input class="form-control" <?php echo $price_readonly; ?>
    value="<?php echo ($det_price == '') ? $row1["cost"] : $det_price; ?>"
    id="price_<?php echo $row1["service_id"]; ?>"
    onblur="on_price_blur('<?php echo $row1["service_id"]; ?>')">
											</td>
											<td style="display:none">
												<input style="text-align:right" <?php echo $final_disabler; ?>
													class="form-control" readonly
													value="<?php echo $tax_pc; ?>"
													id="gst_<?php echo $row1["service_id"]; ?>">
											</td>
											<td style="display:none">
												<input style="text-align:right" <?php echo $final_disabler; ?>
													class="form-control" readonly
													value="<?php echo $tax_pc_sgst; ?>"
													id="sgst_<?php echo $row1["service_id"]; ?>">
											</td>
											<td>
												<input class="form-control" style="text-align:right"
													<?php echo $final_disabler; ?> readonly
													id="cost_<?php echo $row1["service_id"]; ?>"
													value="<?php echo $det_cost; ?>">
											</td>
											<td style="display:<?php echo $visibility; ?>">
												<input class="form-control" readonly style="text-align:right"
													id="tax_gst_<?php echo $row1["service_id"]; ?>"
													value="<?php echo $det_tax_amount; ?>">
											</td>
											<td style="display:<?php echo $visibility; ?>" class="hide_sgst">
												<input class="form-control" style="text-align:right" readonly
													id="tax_sgst_<?php echo $row1["service_id"]; ?>"
													value="<?php echo $det_tax_amount_sgst; ?>">
											</td>
											<td>
												<div class="tooltip-wrapper">
													<input <?php echo $final_disabler; ?> style="text-align:right"
														class="form-control tooltip-input"
														id="discount_<?php echo $row1["service_id"]; ?>"
														onblur="on_discount_blur('<?php echo $row1["service_id"]; ?>')"
														value="<?php echo $det_discount; ?>">
													<div class="tooltip-text">Once saved cannot be edited. If required, delete and reenter.</div>
												</div>
											</td>
											<td>
												<input class="form-control" readonly style="text-align:right"
													id="total_<?php echo $row1["service_id"]; ?>"
													value="<?php echo $det_total; ?>">
												<input class="form-control" type="hidden"
													id="id_<?php echo $row1["service_id"]; ?>"
													value="<?php echo $det_subtrans_id; ?>">
											</td>
											<td>
												<button class="btn btn-xs btn-danger" <?php echo $final_disabler; ?>
													onclick="del_service_det('<?php echo $row1["service_id"]; ?>')"
													id="del_<?php echo $row1["service_id"]; ?>">X</button>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>

							<span class="alert alert-info" style="text-align:right;font-weight:bold;width:100%">
								Total Amount <input id="trans_amount" class="form-control" readonly
									value="<?php echo $grand_total; ?>" style="width:160px;float:right">
							</span>

							<?php if ($active_status == "A") { ?>
								<hr>
								<center>
									<button class="btn btn-success" disabled>Saved already</button>&nbsp;
									<button class="btn btn-warning" onclick="get_invoice('PRINT')">Print</button>&nbsp;
									<button class="btn btn-danger" id="invoice" onclick="get_invoice('DOWNLOAD')">Get Invoice</button>&nbsp;
									<button class="btn btn-primary" onclick="get_jobcard()">Get JobCard</button>&nbsp;
									<button class="btn btn-primary" onclick="unlock()">Edit</button>
								</center>
								<hr>
							<?php } else { ?>
								<hr>
								<center><button class="btn btn-success" onclick="save_dummy()">Save</button></center>
							<?php } ?>
						</div>

						<div class="col-md-12">
							<table class="table">
								<?php
								$sql_pay = "SELECT p.mode, p.account, p.reference, i.amount_settled amount, a.account_name, p.payment_id
								            FROM payments p, pay_track i, account a
								            WHERE i.trans_id='" . $trans_id . "'
								            AND i.payment_id=p.payment_id
								            AND a.account_id=p.account
								            AND i.mode='service'
								            AND i.trans_id!=''";
								$result_pay = $conn->query($sql_pay);
								$total_amount = 0;
								while ($row_pay = $result_pay->fetch_assoc()) {
									$mode      = $row_pay["mode"];
									$account   = $row_pay["account"];
									$reference = $row_pay["reference"];
									$amount    = $row_pay["amount"];
									$total_amount += $amount;
								?>
									<tr>
										<td></td>
										<td><?php echo $row_pay['mode']; ?></td>
										<td><?php echo $row_pay['account_name']; ?></td>
										<td><?php echo $reference; ?></td>
										<td><?php echo $amount; ?></td>
										<td>
											<button class="btn btn-danger btn-sm"
												onclick="delete_it_pay('<?php echo $row_pay["payment_id"]; ?>','CREDIT','<?php echo $row_pay["account"]; ?>','','<?php echo $row_pay["amount"]; ?>','<?php echo $row_pay['mode']; ?>')">X</button>
										</td>
									</tr>
									<input type="hidden" id="already_paid" value="<?php echo $total_amount; ?>">
								<?php } ?>
							</table>

							<?php
							$disabled = "";
							?>
							<button type="button" class="btn btn-primary"
    data-toggle="modal"
    data-target="#addModal"
    id="add_opener">
    Add Payments
</button>
						</div>

						<div class="card-body"></div>
					</div>
				</div>
				<!-- /.container-fluid -->

			</div>
			<!-- End of Main Content -->

			<?php include "footer.php"; ?>
		</div>
		<!-- End of Content Wrapper -->
	</div>
	<!-- End of Page Wrapper -->

	<?php include "modals.php"; ?>

	<!-- Add Payment Modal -->
	<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add Payment</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
<table class="table" id="payment_table">
						<tr>
							<td>Pay Type
								<select id="pay_type1" class="form-control" onchange="filter_account1()">
									<option value="">--Select--</option>
									<?php $sql = "SELECT paytype_id,paytype_name FROM paytype order by 1";
									$result = $conn->query($sql);
									while ($row = $result->fetch_assoc()) { ?>
										<option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['paytype_name']; ?>"><?php echo $row['paytype_name']; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td>Account
								<select class="form-control" id="account1">
									<option value="">--Select--</option>
									<?php $sql = "SELECT account_id,account_name,paytype_id FROM account order by 1";
									$result = $conn->query($sql);
									while ($row = $result->fetch_assoc()) { ?>
										<option value="<?php echo $row['paytype_id']; ?>~<?php echo $row['account_id']; ?>"><?php echo $row['account_name']; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td>Ref No <input id="ref_no1" class="form-control"></td>
						</tr>
						<tr>
							<td>Paid Amount <input id="paid_amount" class="form-control" type="number"></td>
						</tr>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-success" onclick="save_pay1()">Save</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Edit Modal -->
	<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<table class="table table-striped">
						<tr>
							<td>Vehicle</td>
							<td>
								<select class="form-control" id="edit_vehicle" onchange="populate_edit_vehicle_det()">
									<option value="">--select---</option>
									<?php $sql = "SELECT vehicle_id,vehicle_no,vehicle_model,vehicle_brand FROM vehicle WHERE customer_id='" . $customer . "' order by 1";
									$result = $conn->query($sql);
									while ($row1 = $result->fetch_assoc()) { ?>
										<option value="<?php echo $row1['vehicle_id']; ?>">(<?php echo $row1['vehicle_no']; ?>, Model: <?php echo $row1['vehicle_model']; ?>, Brand: <?php echo $row1['vehicle_brand']; ?>)</option>
									<?php } ?>
								</select>
								<input type="hidden" id="edit_vehicle_id">
							</td>
						</tr>
						<tr>
							<td>Service</td>
							<td>
								<select class="form-control" id="edit_service" onchange="populate_edit_det()">
									<option value="">--select---</option>
									<?php $sql = "SELECT service_id,service_name,service_description,cost,tax_pc,tax_pc_sgst FROM service order by 1";
									$result = $conn->query($sql);
									while ($row1 = $result->fetch_assoc()) { ?>
										<option value="<?php echo $row1['service_id']; ?>~<?php echo $row1['cost']; ?>~<?php echo $row1['tax_pc']; ?>~<?php echo $row1['tax_pc_sgst']; ?>"><?php echo $row1['service_name']; ?> (Cost: <?php echo $row1['cost']; ?>, Tax: <?php echo $row1['tax_pc']; ?>%)</option>
									<?php } ?>
								</select>
								<input type="hidden" id="edit_cost">
								<input type="hidden" id="edit_tax_pc">
								<input type="hidden" id="edit_tax_pc_sgst">
								<input type="hidden" id="edit_service_id">
							</td>
						</tr>
						<tr><td>Qty</td><td><input type="number" id="edit_qty" class="form-control" oninput="show_edit_amount()"></td></tr>
						<tr><td>Amount</td><td><input type="number" class="form-control" id="edit_amount"></td></tr>
						<tr><td>Tax</td><td><input type="number" class="form-control" id="edit_tax" readonly></td></tr>
						<tr><td>Tax (SGST)</td><td><input type="number" class="form-control" id="edit_tax_sgst" readonly></td></tr>
						<tr>
							<td>Total</td>
							<td>
								<input type="number" class="form-control" id="edit_total" readonly>
								<input type="hidden" id="edit_trans_id">
								<input type="hidden" id="subtrans_id">
							</td>
						</tr>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-success" id="edit_btn" onclick="save_edit()">Save</button>
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
	<script src="multi/searchableOptionList.js"></script>

	<script type="text/javascript">
		$(function() {
			$('#customer_search').searchableOptionList({
				maxHeight: '350px',
				showSelectAll: true,
				onChange: function() {
					setTimeout(function() {
						select_customer();
					}, 2000);
				}
			});
		});
	</script>

	<script>
		var del_mode = 0;

		// ================= FIX 1: Guard flag =================
		// Prevents onblur on qty/discount from saving while we are in the middle
		// of programmatically showing a row (focus set by show_service_row).
		// Without this, focus() → immediate blur (before user types anything) → qty=0 → row hidden.
		var row_being_loaded = false;

		var addModal = new bootstrap.Modal(document.getElementById('addModal'));
		var editModal = new bootstrap.Modal(document.getElementById('editModal'));

		// ================= DATEPICKER INIT =================
		$(function() {
			$("#datepicker").datepicker({
				dateFormat: "dd-mm-yy"
			}).datepicker("setDate", new Date());
		});

		// ================= PAGE LOAD: SET CORRECT TAX COLUMNS =================
		$(function() {
			let savedGst  = "<?php echo addslashes($CUSTOMER_GST); ?>";
			let cookieGst = "<?php echo $gst; ?>";

			if (cookieGst === "Y") {
				if (savedGst !== "" && savedGst.startsWith("37")) {
					$("#cgst_igst").html("CGST(9%)");
					$("#cgst_igst_amount").html("CGST(9%)");
					document.querySelectorAll('.hide_sgst').forEach(el => el.style.display = '');
					document.querySelectorAll('[id^="gst_"]').forEach(el => el.value = 9);
					document.querySelectorAll('[id^="sgst_"]').forEach(el => el.value = 9);
				} else if (savedGst !== "") {
					$("#cgst_igst").html("IGST(18%)");
					$("#cgst_igst_amount").html("IGST(18%)");
					document.querySelectorAll('.hide_sgst').forEach(el => el.style.display = 'none');
					document.querySelectorAll('[id^="gst_"]').forEach(el => el.value = 18);
					document.querySelectorAll('[id^="sgst_"]').forEach(el => el.value = 0);
				}
			}
		});

		// ================= SET_GST =================
		function set_gst() {
			let str       = document.getElementById("customer_gst").value;
			let cookieGst = "<?php echo $gst; ?>";
			let trans_id  = $("#trans_id").val();

			// Only wipe rows if no trans_id exists yet (new transaction)
			if (!trans_id) {
				document.querySelectorAll('.row_service').forEach(el => {
					el.style.display = 'none';
				});
				document.querySelectorAll('.qty').forEach(el => {
					el.value = '0';
				});
			}

			if (cookieGst !== "Y") {
				document.querySelectorAll('[id^="gst_"]').forEach(el => el.value = 0);
				document.querySelectorAll('[id^="sgst_"]').forEach(el => el.value = 0);
				document.querySelectorAll('.hide_sgst').forEach(el => el.style.display = 'none');
				$("#cgst_igst").html("No GST");
				$("#cgst_igst_amount").html("No GST");
			} else if (str.startsWith("37")) {
				$("#cgst_igst").html("CGST(9%)");
				$("#cgst_igst_amount").html("CGST(9%)");
				document.querySelectorAll('.hide_sgst').forEach(el => el.style.display = '');
				document.querySelectorAll('[id^="gst_"]').forEach(el => el.value = 9);
				document.querySelectorAll('[id^="sgst_"]').forEach(el => el.value = 9);
			} else {
				$("#cgst_igst").html("IGST(18%)");
				$("#cgst_igst_amount").html("IGST(18%)");
				document.querySelectorAll('.hide_sgst').forEach(el => el.style.display = 'none');
				document.querySelectorAll('[id^="gst_"]').forEach(el => el.value = 18);
				document.querySelectorAll('[id^="sgst_"]').forEach(el => el.value = 0);
			}
		}

		// ================= APPLY TAX TO ONE SERVICE ROW =================
		// Sets the hidden gst_/sgst_ inputs for a single service based on current GST state.
		function applyTaxToService(service) {
			let str       = $("#customer_gst").val();
			let cookieGst = "<?php echo $gst; ?>";

			let gst_el  = document.getElementById("gst_"  + service);
			let sgst_el = document.getElementById("sgst_" + service);
			if (!gst_el || !sgst_el) return;

			if (cookieGst !== "Y") {
				gst_el.value  = 0;
				sgst_el.value = 0;
			} else if (str !== "" && str.startsWith("37")) {
				gst_el.value  = 9;
				sgst_el.value = 9;
			} else if (str !== "") {
				// Non-empty GST but not 37 = inter-state IGST
				gst_el.value  = 18;
				sgst_el.value = 0;
			}
			// If str is empty, leave existing values untouched
		}

		// ================= LOAD_SERVICE =================
		function load_service(service, price, gst, sgst, price_edit) {
			let trans_id = $("#trans_id").val();

			if ($("#vehicle_no").val() == "") {
				alert("Please enter Vehicle Details");
				return false;
			}

			// FIX: Apply correct tax to this row BEFORE showing it
			applyTaxToService(service);

			// FIX: Handle price_edit in JS too (for rows shown after page load)
			let priceInput = document.getElementById("price_" + service);
			if (price_edit === 'Y') {
				priceInput.removeAttribute("readonly");
			} else {
				priceInput.setAttribute("readonly", true);
			}

			if (trans_id == "" || trans_id == null) {

				$.post("add_service.php", {
					vehicle_no:       $("#vehicle_no").val(),
					vehicle_model:    $("#vehicle_model").val(),
					vehicle_make:     $("#vehicle_make").val(),
					no_of_wheels:     $("#no_of_wheels").val(),
					vehicle_odometer: $("#vehicle_odometer").val(),
					vehicle:          $("#vehicle").val(),
					company_name:     $("#company_name").val(),
					customer_name:    $("#customer_name").val(),
					customer_address: $("#customer_address").val(),
					customer_mobile:  $("#customer_mobile").val(),
					customer_gst:     $("#customer_gst").val(),
					customer:         $("#customer").val(),
					trans_date:       $("#datepicker").val(),
					gst:              "<?php echo $gst; ?>",
					mech:             $("#mech").val()

				}, function(data, status) {

					let resp = (typeof data === "string") ? JSON.parse(data) : data;

					if (resp.login_required) {
						alert(resp.message);
						window.location.href = "login.php";
						return;
					}

					if (!resp.success) {
						alert(resp.message);
						return;
					}

					$("#trans_id").val(resp.trans_id);

					show_service_row(service);

				}).fail(function() {

					alert("Server communication failed");

				});

			} else {

				show_service_row(service);
			}
			} // Close load_service()

		function show_service_row(service) {
    row_being_loaded = true;

    document.getElementById("row_" + service).style.display = "";

    let qtyEl = document.getElementById("qty_" + service);
    let priceEl = document.getElementById("price_" + service);

    // wheel alignment special qty rule
    if (service == "1000002") {
        qtyEl.value = 1;
    }

    // for new rows only, try special cost
    if ($("#id_" + service).val() == "") {
        $.post("get_service_cost.php", {
            make: $("#vehicle_make").val(),
            model: $("#vehicle_model").val(),
            service_id: service
        }, function(data1) {
            let sp = $.trim(data1);

            if (sp !== "" && (priceEl.value === "" || priceEl.value === "0")) {
                priceEl.value = sp;
            }

            // always calculate once so non-wheel services do not stay 0
            calculate(service);

            qtyEl.focus();

            setTimeout(function() {
                row_being_loaded = false;
                if (service == "1000002") {
					    row_being_loaded = false;

            //        add_service_det(service);
                }
            }, 300);
        });
    } else {
        // existing saved row
        calculate(service);
        qtyEl.focus();

        setTimeout(function() {
            row_being_loaded = false;
            if (service == "1000002") {
				    row_being_loaded = false;

             //   add_service_det(service);
            }
        }, 300);
    }
}

		// ================= BLUR HANDLERS (replaces onfocusout on <tr>) =================
		// These are called from onblur on qty and discount inputs.
		// The row_being_loaded guard prevents premature saves during show_service_row.

		function on_qty_blur(service) {
			if (row_being_loaded) return;   // FIX: ignore blur during programmatic focus
			calculate(service);
		//	add_service_det(service);
		}

		function on_discount_blur(service) {
			if (row_being_loaded) return;   // FIX: same guard for discount
			calculate(service);
		//	add_service_det(service);
		}
		function on_price_blur(service) {
    if (row_being_loaded) return;

    calculate(service);
    // add_service_det(service);
}

		// ================= ADD_SERVICE_DET =================
		function add_service_det(service_id) {
			let service  = service_id;
			let vehicle  = $("#vehicle").val();
let price     = $("#price_" + service).val();
let qty       = $("#qty_" + service).val();
let discount  = $("#discount_" + service).val();
let taxable   = $("#cost_" + service).val();
let gst       = $("#gst_" + service).val();
let sgst      = $("#sgst_" + service).val();
let tax_gst   = $("#tax_gst_" + service).val();
let tax_sgst  = $("#tax_sgst_" + service).val();
let total_amt = $("#total_" + service).val();

			if (del_mode == 0) {
				if (qty != "" && qty != "0") {
$.post("add_service_det.php", {
    price: price,
    qty: qty,
    discount: discount,
    service_id: service,
    vehicle: vehicle,
    trans_id: $("#trans_id").val(),
    taxable: taxable,
    tax: gst,
    tax_sgst: sgst,
    tax_amount: tax_gst,
    tax_amount_sgst: tax_sgst,
    grand_total: total_amt
}, function(data, status) {
    let parts = $.trim(data).split("~");
    $("#id_" + service).val(parts[0]);
    $("#trans_amount").val(parts[1]);
    const paid_amount = document.getElementById("paid_amount");
    paid_amount.setAttribute("min", "0");
    paid_amount.setAttribute("max", parts[1]);
    $("#del_" + service).prop('disabled', false);
});
				} else {
					del_service_det_quiet(service_id);
				}
			}
		}

		// ================= CALCULATE =================
		function calculate(service) {
			let price    = parseFloat($("#price_"    + service).val()) || 0;
			let qty      = parseFloat($("#qty_"      + service).val()) || 0;
			let discount = parseFloat($("#discount_" + service).val()) || 0;
			let gst      = parseFloat($("#gst_"      + service).val()) || 0;
			let sgst     = parseFloat($("#sgst_"     + service).val()) || 0;

			let cost = price * qty - discount;
			if (cost < 0) cost = 0;

			let tax_gst  = cost * gst  / 100;
			let tax_sgst = cost * sgst / 100;
			let total    = cost + tax_gst + tax_sgst;

			$("#cost_"     + service).val(cost.toFixed(2));
			$("#tax_gst_"  + service).val(tax_gst.toFixed(2));
			$("#tax_sgst_" + service).val(tax_sgst.toFixed(2));
			$("#total_"    + service).val(total.toFixed(2));

			updateGrandTotal();
		}

		function updateGrandTotal() {
			let total = 0;
			$(".row_service").each(function() {
				if ($(this).is(":visible")) {
					let id  = $(this).attr("id").split("_")[1];
					let val = parseFloat($("#total_" + id).val()) || 0;
					total  += val;
				}
			});
			$("#trans_amount").val(total.toFixed(2));
		}

		// ================= LOAD_SPECIAL_COST =================
// 		function load_special_cost(service) {
// 			$.post("get_service_cost.php", {
// 				make:       $("#vehicle_make").val(),
// 				model:      $("#vehicle_model").val(),
// 				service_id: service
// 			}, function(data1, status1) {
// 				if ($.trim(data1) != "") {
// 					if ($("#price_" + service).val() == "" || $("#price_" + service).val() == "0") {
//     if ($("#price_" + service).val() == "" || $("#price_" + service).val() == "0") {
//     $("#price_" + service).val($.trim(data1));
// }
// }
// 					calculate(service);
// 				}
// 			});
// 		}
function load_special_cost(service) {
    $.post("get_service_cost.php", {
        make: $("#vehicle_make").val(),
        model: $("#vehicle_model").val(),
        service_id: service
    }, function(data1) {
        let sp = $.trim(data1);
        if (sp !== "" && ($("#price_" + service).val() == "" || $("#price_" + service).val() == "0")) {
            $("#price_" + service).val(sp);
        }
        calculate(service);
    });
}

		// ================= DELETE SERVICE DET =================
		function del_service_det(service) {
			let subtrans_id = document.getElementById("id_" + service).value;
			if (confirm("Are you sure that you want to delete the transaction")) {
				del_mode = 1;
				$.post("delete_trans_det.php", {
					trans_id:    $("#trans_id").val(),
					subtrans_id: subtrans_id
				}, function(data, status) {
					document.getElementById("discount_" + service).value = "0";
					document.getElementById("qty_"      + service).value = "0";
					document.getElementById("cost_"     + service).value = "0";
					document.getElementById("total_"    + service).value = "0";
					document.getElementById("tax_gst_"  + service).value = "0";
					document.getElementById("tax_sgst_" + service).value = "0";
					document.getElementById("row_"      + service).style.display = "none";
					$("#trans_amount").val($.trim(data).split("~")[1]);
					setTimeout(() => { del_mode = 0; }, 700);
				});
			}
		}

		function del_service_det_quiet(service) {
			let subtrans_id = document.getElementById("id_" + service).value;
			if (!subtrans_id) return; // FIX: nothing saved yet — just hide the row silently
			del_mode = 1;
			$.post("delete_trans_det.php", {
				trans_id:    $("#trans_id").val(),
				subtrans_id: subtrans_id
			}, function(data, status) {
				document.getElementById("discount_" + service).value = "0";
				document.getElementById("qty_"      + service).value = "0";
				document.getElementById("cost_"     + service).value = "0";
				document.getElementById("total_"    + service).value = "0";
				document.getElementById("tax_gst_"  + service).value = "0";
				document.getElementById("tax_sgst_" + service).value = "0";
				document.getElementById("row_"      + service).style.display = "none";
				$("#trans_amount").val($.trim(data).split("~")[1]);
				setTimeout(() => { del_mode = 0; }, 700);
			});
		}

		// ================= SAVE =================
		function save_dummy() {

    let trans_id = $("#trans_id").val();

    if (!trans_id) {
        alert("Please add at least one service before saving");
        return;
    }

    let rows = [];

    $(".row_service:visible").each(function () {

        let service = $(this).attr("id").split("_")[1];

        let qty = parseFloat($("#qty_" + service).val()) || 0;

        if (qty > 0) {

            rows.push({
                service_id: service,
                vehicle: $("#vehicle").val(),
                price: $("#price_" + service).val(),
                qty: $("#qty_" + service).val(),
                discount: $("#discount_" + service).val(),
                taxable: $("#cost_" + service).val(),
                tax: $("#gst_" + service).val(),
                tax_amount: $("#tax_gst_" + service).val(),
                tax_sgst: $("#sgst_" + service).val(),
                tax_amount_sgst: $("#tax_sgst_" + service).val()
            });

        }

    });

    if (rows.length === 0) {
        alert("Please enter at least one service");
        return;
    }

    $.post("save_draft_service.php", {
        trans_id: trans_id,
        rows: JSON.stringify(rows)
    }, function (data) {

        alert(data);
        window.location.href =
            "service_trans_new.php?trans_id=" + trans_id;

    });

}

		// ================= PAYMENTS =================
		function save_pay1() {
			let paid_amount  = parseFloat($("#paid_amount").val()) || 0;
    let pay_type     = $("#pay_type1").val();
    let ref_no       = $("#ref_no1").val();
    let trans_amount = parseFloat($("#trans_amount").val()) || 0;

    let account = "";
    try {
        account = $("#account1").val().split("~")[1];
    } catch (err) {
        account = "";
    }

    // calculate already paid dynamically
let already_paid = parseFloat($("#already_paid").val()) || 0;

    let max_amount = trans_amount - already_paid;

    if (paid_amount > max_amount) {
    alert(`❌ Excess Amount!
    
Entered: ${paid_amount}
Pending: ${max_amount.toFixed(2)}

Please enter valid amount.`);
    return false;
}
    $.post("update_pay.php", {
        trans_id: $("#trans_id").val(),
        paid_amount: paid_amount,
        pay_type: pay_type.split("~")[1],
        account: account,
        ref_no: ref_no,
        trans_amount: trans_amount,
        customer_name: $("#customer_name").val(),
        customer: $("#customer").val(),
        trans_date: $("#datepicker").val(),
        gst: "<?php echo $gst; ?>"
    }, function(data, status) {

    let resp = (typeof data === "string") ? JSON.parse(data) : data;

    if (resp.login_required) {
        alert(resp.message);
        window.location.href = "login.php";
        return;
    }

    if (!resp.success) {
        alert(resp.message);
        return;
    }

    alert(resp.message);
    location.reload();
});
}

		function delete_it_pay(payment_id, nature, account, account_to, amount, mode) {
			if (confirm("Are you sure that you want to delete this payment?")) {
				$.post("delete_payment.php", {
					payment_id: payment_id,
					nature:     nature,
					account:    account,
					account_to: account_to,
					amount:     amount,
					mode:       mode
				}, function(data, status) {
					alert($.trim(data));
					location.reload();
				});
			}
		}

		// ================= VEHICLE / CUSTOMER LOOKUPS =================
		function get_details() {
			let vehicle_no = $("#vehicle_no").val();
			$.post("get_details.php", { vehicle_no: vehicle_no }, function(data, status) {
				let parts = $.trim(data).split("~");
				$("#vehicle").val(parts[0]);
				$("#vehicle_model").val(parts[1]);
				$("#no_of_wheels").val(parts[2]);
				$("#customer").val(parts[3]);
				$("#company_name").val(parts[4]);
				$("#customer_name").val(parts[5]);
				$("#customer_mobile").val(parts[6]);
				$("#vehicle_make").val(parts[7]);
				get_gst();
				get_address();
			});
		}

		function get_gst() {
			let customer = $("#customer").val();
			$.post("get_gst.php", { customer_id: customer }, function(data, status) {
				let parts   = $.trim(data).split("~");
				let datalist = document.getElementById("gst_list");
				datalist.innerHTML = "";
				parts.forEach(part => {
					const option   = document.createElement("option");
					option.value   = part;
					datalist.appendChild(option);
				});
				if (parts.length > 0) {
					document.getElementById("customer_gst").value = parts[0];
					set_gst();
				}
			});
		}

		function get_address() {
			let customer = $("#customer").val();
			$.post("get_address.php", { customer_id: customer }, function(data, status) {
				let parts    = $.trim(data).split("~");
				let datalist = document.getElementById("address_list");
				datalist.innerHTML = "";
				parts.forEach(part => {
					const option   = document.createElement("option");
					option.value   = part;
					datalist.appendChild(option);
				});
				if (parts.length > 0 && parts[0] != "") {
					document.getElementById("customer_address").value = parts[0];
				}
			});
		}

		function select_customer() {
			set_customer();
			get_gst();
			get_address();
		}

		function set_customer() {
			let a = document.getElementsByClassName("sol-selected-display-item");
			let myvar = "";
			for (let i = 0; i < a.length; i++) {
				myvar += a[i].getAttribute("data-sol-item-val");
			}
			let parts = myvar.split("~");
			$("#customer").val(parts[0]);
			$("#company_name").val(parts[1]);
			$("#customer_name").val(parts[2]);
			$("#customer_mobile").val(parts[3]);
		}

		// ================= WHEELS =================
function setwheels() {
    let wheels = $("#no_of_wheels").val();
    if (!wheels || wheels == "0") return;

    // wheel alignment should not multiply by wheel count unless you really want per-wheel billing
    document.getElementById("qty_1000002").value = 1;
    calculate("1000002");
   // add_service_det("1000002");
}

		// ================= INVOICE / JOBCARD =================
		function get_invoice(param) {
			let trans_id = $("#trans_id").val();
			window.open("service_receipt.php?trans_id=" + trans_id + "&action=" + param, '_blank');
		}

		function get_jobcard() {
			window.open("job_card.php?trans_id=" + $("#trans_id").val(), '_blank');
		}

		function unlock() {
			$.post("unlock_service.php", { trans_id: $("#trans_id").val() }, function(data, status) {
				alert("Transaction unlocked: " + $("#trans_id").val());
				window.location.href = "service_trans_new.php?trans_id=" + $("#trans_id").val();
			});
		}

		// ================= EDIT SERVICE DET =================
		function edit_it(trans_id, subtrans_id, service, vehicle, qty) {
			$("#edit_trans_id").val(trans_id);
			$("#subtrans_id").val(subtrans_id);
			$("#edit_service").val(service);
			$("#edit_vehicle").val(vehicle);
			$("#edit_qty").val(qty);
			populate_edit_det();
			populate_edit_vehicle_det();
			show_edit_amount();
			editModal.show();
		}

		function save_edit() {
			let service     = $("#edit_service_id").val();
			let vehicle     = $("#edit_vehicle_id").val();
			let cost        = $("#edit_cost").val();
			let tax_pc      = $("#edit_tax_pc").val();
			let tax_pc_sgst = $("#edit_tax_pc_sgst").val();
			let total       = $("#edit_amount").val();
			let tax         = $("#edit_tax").val();
			let tax_sgst    = $("#edit_tax_sgst").val();
			let qty         = $("#edit_qty").val();
			let subtrans_id = $("#subtrans_id").val();
			$.post("edit_service_det.php", {
				cost:        cost,
				tax_pc:      tax_pc,
				tax_pc_sgst: tax_pc_sgst,
				total:       total,
				tax:         tax,
				tax_sgst:    tax_sgst,
				qty:         qty,
				service_id:  service,
				vehicle:     vehicle,
				trans_id:    "<?php echo $trans_id; ?>",
				subtrans_id: subtrans_id
			}, function(data, status) {
				location.reload();
			});
		}

		function populate_edit_det() {
			let gst   = "<?php echo $gst; ?>";
			let parts = document.getElementById("edit_service").value.split("~");
			$("#edit_service_id").val(parts[0]);
			$("#edit_cost").val(parts[1]);
			if (gst == "Y") {
				$("#edit_tax_pc").val(parts[2]);
				$("#edit_tax_pc_sgst").val(parts[3]);
			} else {
				$("#edit_tax_pc").val("0");
				$("#edit_tax_pc_sgst").val("0");
			}
			show_edit_amount();
		}

		function populate_edit_vehicle_det() {
			let parts = document.getElementById("edit_vehicle").value.split("~");
			$("#edit_vehicle_id").val(parts[0]);
		}

		function show_edit_amount() {
			let cost  = parseFloat($("#edit_cost").val()) || 0;
			let qty   = parseFloat($("#edit_qty").val())  || 0;
			let amount = cost * qty;
			let tax    = amount * (parseFloat($("#edit_tax_pc").val())      || 0) / 100;
			let tax_s  = amount * (parseFloat($("#edit_tax_pc_sgst").val()) || 0) / 100;
			$("#edit_amount").val(amount.toFixed(2));
			$("#edit_tax").val(tax.toFixed(2));
			$("#edit_tax_sgst").val(tax_s.toFixed(2));
			$("#edit_total").val((amount + tax + tax_s).toFixed(2));
		}

		// ================= ACCOUNT FILTER =================
		function filter_account1() {
			let pay_type = document.getElementById("pay_type1").value.split("~")[0];
			$('#account1 option').filter(function() {
				return !$(this).val().toLowerCase().includes(pay_type);
			}).prop('disabled', true);
			if (document.getElementById("pay_type1").value.includes("CASH")) {
				document.getElementById("account1").value = "1000003~1000008";
			}
		}

		// ================= PAID AMOUNT GUARD =================
		const paid_amount_input = document.getElementById('paid_amount');
		paid_amount_input.addEventListener('input', function() {
			const min   = parseInt(this.min);
			const max   = parseInt(this.max);
			const value = parseInt(this.value);
			if (value > max) this.value = max;
			else if (value < min) this.value = min;
		});

		// ================= TOOLTIP =================
		document.querySelectorAll('.tooltip-wrapper').forEach(wrapper => {
			const inp = wrapper.querySelector('input');
			wrapper.querySelector('input').addEventListener('mouseenter', () => { if (inp.readOnly) wrapper.classList.add('show'); });
			wrapper.querySelector('input').addEventListener('focus',      () => { if (inp.readOnly) wrapper.classList.add('show'); });
			wrapper.querySelector('input').addEventListener('mouseleave', () => wrapper.classList.remove('show'));
			wrapper.querySelector('input').addEventListener('blur',       () => wrapper.classList.remove('show'));
		});

	</script>

</body>
</html>