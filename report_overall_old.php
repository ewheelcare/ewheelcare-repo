<!DOCTYPE html>
<html lang="en">

<head>
	<link href="multi/searchableOptionList.css" rel="stylesheet">
	<?php include "header_include.php";
	include "db_config.php";
	?>
</head>

<body id="page-top">

	<div id="wrapper">

		<?php include "sidemenu.php"; ?>

		<div id="content-wrapper" class="d-flex flex-column">

			<div id="content">

				<?php include "topmenu.php"; ?>

				<div class="container-fluid">

					<div class="row">
						<div class="col-md-12">
							<form action="#" method="post">
								<table class="table table-striped">
									<tr>
										<td>From date<br><input type="text" name="from_date" id="from_date" class="form-control" autocomplete="off"></td>
										<td>To date<br><input type="text" name="to_date" id="to_date" class="form-control" autocomplete="off"></td>
										<td>GST<br>
											<select name="gst" id="gst" class="form-control" autocomplete="off">
												<option value="">--Select--</option>
												<option value="Y">Yes</option>
												<option value="N">No</option>
											</select>
										</td>
										<td>Customer<br>
											<select class="form-control" name="customer[]" id="customer_search" multiple="multiple" style="max-width:300px!important">
												<?php
												$sql = "SELECT customer_id,company_name,owner_name,owner_mobile FROM customer";
												$result = $conn->query($sql);
												while ($row1 = $result->fetch_assoc()) { ?>
													<option value="<?php echo $row1["customer_id"] ?>"><?php echo $row1["company_name"] ?>,<?php echo $row1["owner_name"] ?></option>
												<?php } ?>
											</select>
										</td>
										<td>Shop<br>
											<select class="form-control" name="shop" autocomplete="off">
												<option value="">--select---</option>
												<?php
												$sql = "SELECT shop_id,shop_name FROM shop ORDER BY shop_id";
												$result = $conn->query($sql);
												$shop_array = [];
												$shop_name_array = [];
												$shop_cnt = 0;
												while ($row = $result->fetch_assoc()) {
													$shop_name_array[$shop_cnt] = $row['shop_name'];
													$shop_array[$shop_cnt++] = $row['shop_id'];
												?>
													<option value="<?php echo $row['shop_id']; ?>"><?php echo $row['shop_name']; ?></option>
												<?php } ?>
											</select>
										</td>
										<td><br><input type="submit" class="btn btn-primary btn-sm" value="Search"></td>
									</tr>
								</table>
							</form>

							<?php
							$from_date = isset($_POST["from_date"]) ? $_POST["from_date"] : "";
							$to_date = isset($_POST["to_date"]) ? $_POST["to_date"] : "";
							$gst_param = isset($_POST["gst"]) ? $_POST["gst"] : "";
							$shop_param = isset($_POST["shop"]) ? $_POST["shop"] : "";
							$customer = "";

							if (isset($_POST['customer']) && is_array($_POST['customer']) && count($_POST['customer']) > 0) {
								foreach ($_POST['customer'] as $customer_id) {
									$customer .= "'" . $customer_id . "',";
								}
								$customer = rtrim($customer, ",");
							}

							$pay_types = [
								" = 'CASH'",
								" = 'CREDIT'",
								" = 'BANK'"
							];

							$pay_types_cd = [
								" IN ('1000003')",
								" IN ('1000005')",
								" NOT IN ('1000003','1000005')"
							];
							?>

							<button class="btn btn-sm btn-primary" type="button" style="float:right;margin:3px;" onclick="print_table()">Excel</button>

							<table class="table table-striped table-bordered" id="report_table" border="1" style="width:100%;font-size:75%;font-weight:bold;text-transform:uppercase">
								<thead>
									<tr style="background-color:#f0f0f0">
										<td rowspan="2"></td>
										<td colspan="3" style="color:black !important;font-weight:bold;">INCOME</td>
										<td colspan="3" style="color:black !important;font-weight:bold;">EXPENDITUTE</td>
										<td colspan="3" style="color:black !important;font-weight:bold;">NET INCOME</td>
										<td rowspan="2" style="color:black !important;font-weight:bold;">TOTAL</td>
									</tr>
									<tr>
										<td>CASH</td>
										<td>CREDIT</td>
										<td>BANK</td>
										<td>CASH</td>
										<td>CREDIT</td>
										<td>BANK</td>
										<td>CASH</td>
										<td>CREDIT</td>
										<td>BANK</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$s = 0;
									$garr = array_fill(0, 3, 0);
									$garr_e = array_fill(0, 3, 0);

									while ($s < $shop_cnt) {
										$arr = array_fill(0, 3, 0);
										$gt = 0;
									?>
										<tr>
											<td><?php echo $shop_name_array[$s]; ?></td>

											<?php
											for ($i = 0; $i < 3; $i++) {
												$sql = "SELECT IFNULL(SUM(t.amount_settled),0) AS total
														FROM payments p
														INNER JOIN pay_track t ON p.payment_id = t.payment_id
														WHERE p.active_status='A'
														AND t.mode='service'
														AND t.trans_id!=''
														AND p.mode " . $pay_types[$i] . "
														AND p.shop_id='" . $shop_array[$s] . "'";

												if ($from_date != "" && $to_date != "") {
													$sql .= " AND p.payment_date BETWEEN STR_TO_DATE('" . $from_date . "', '%d-%m-%Y')
															  AND STR_TO_DATE('" . $to_date . "', '%d-%m-%Y')";
												}

												if ($customer != "") {
													$sql .= " AND p.customer IN (" . $customer . ")";
												}
                                              echo $sql;
												$result2 = $conn->query($sql);
												$total = 0;
												if ($row2 = $result2->fetch_assoc()) {
													$total = $row2["total"];
												}

												$arr[$i] = $total;
												$garr[$i] += $total;
											?>
												<td><?php echo $total; ?></td>
											<?php } ?>

											<?php
											for ($i = 0; $i < 3; $i++) {
												$sql = "SELECT IFNULL(SUM(amount),0) AS total
														FROM expenditure
														WHERE shop_id='" . $shop_array[$s] . "'
														AND paytype_id " . $pay_types_cd[$i];

												if ($from_date != "" && $to_date != "") {
													$sql .= " AND IFNULL(date,'-') BETWEEN STR_TO_DATE('" . $from_date . "', '%d-%m-%Y')
															  AND STR_TO_DATE('" . $to_date . "', '%d-%m-%Y')";
												}

												$result2 = $conn->query($sql);
												$total = 0;
												if ($row2 = $result2->fetch_assoc()) {
													$total = $row2["total"];
												}

												$arr[$i] -= $total;
												$garr_e[$i] += $total;
											?>
												<td><?php echo $total; ?></td>
											<?php } ?>

											<?php
											for ($i = 0; $i < 3; $i++) {
												$gt += $arr[$i];
											?>
												<td><?php echo $arr[$i]; ?></td>
											<?php } ?>

											<td><?php echo $gt; ?></td>
										</tr>
									<?php
										$s++;
									}
									?>

									<tr>
										<td>Total</td>
										<?php for ($i = 0; $i < 3; $i++) { ?>
											<td><?php echo $garr[$i]; ?></td>
										<?php } ?>

										<?php for ($i = 0; $i < 3; $i++) { ?>
											<td><?php echo $garr_e[$i]; ?></td>
										<?php } ?>

										<?php
										$ggt = 0;
										for ($i = 0; $i < 3; $i++) {
											$net = $garr[$i] - $garr_e[$i];
											$ggt += $net;
										?>
											<td><?php echo $net; ?></td>
										<?php } ?>

										<td><?php echo $ggt; ?></td>
									</tr>
								</tbody>
							</table>

						</div>
					</div>

				</div>
			</div>

			<?php include "footer.php"; ?>
		</div>
	</div>

	<?php include "modals.php"; ?>
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
				showSelectAll: true
			});
		});
	</script>

	<script>
		$(function() {
			$("#from_date").datepicker({
				dateFormat: "dd-mm-yy"
			});
			$("#to_date").datepicker({
				dateFormat: "dd-mm-yy"
			});
		});
	</script>

	<script src="js/tableToExcel.js"></script>
	<script type="text/javascript">
		function print_table() {
			TableToExcel.convert(document.getElementById("report_table"));
		}
	</script>

</body>
</html>