<!DOCTYPE html>
<html lang="en">

<head>
	<?php include "header_include.php"; ?>
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


					<!-- Content Row -->
					<div class="row">
						<div class="col-md-12">
							<div class="card shadow mb-4">
								<div class="card-header py-3 bg-primary">
									<h6 class="m-0 font-weight-bold text-white">Live Shop Inventory</h6>
								</div>
								<div class="card-body">
									<!-- Fake inputs to prevent browser autofill -->
									<input type="text" style="display:none" name="fake_username_autofill_prevent">
									<input type="password" style="display:none" name="fake_password_autofill_prevent">

									<!-- Search Filters -->
									<div class="row mb-4">
										<div class="col-md-12">
											<div class="form-group mb-0">
												<label>Search Item</label>
												<input type="text" id="itemSearchInput" class="form-control"
													placeholder="Type Item Code or Name..." autocomplete="off">
											</div>
										</div>
									</div>

									<div class="table-responsive">
										<table class="table table-hover table-bordered" id="inventoryTable">
											<thead class="bg-light">
												<tr>
													<th>Item Code</th>
													<th>Item Name</th>
													<th class="text-center">Sirasapalli</th>
													<th class="text-center">Gajuwaka</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$sql = "SELECT 
															i.ITEM_ID, 
															i.ITEM_NAME,
															IFNULL(SUM(CASE WHEN UPPER(inv.shop_name) LIKE '%SIRASAPALLI%' THEN inv.qty ELSE 0 END), 0) AS sirasapalli_qty,
															IFNULL(SUM(CASE WHEN UPPER(inv.shop_name) LIKE '%GAJUWAKA%' THEN inv.qty ELSE 0 END), 0) AS gajuwaka_qty
														FROM item i
														LEFT JOIN inventory_shop inv ON i.ITEM_ID = inv.item_id
														GROUP BY i.ITEM_ID, i.ITEM_NAME
														ORDER BY i.ITEM_NAME";
												$result = $conn->query($sql);

												while ($row = $result->fetch_assoc()) {
													?>
													<tr data-sirasapalli="<?php echo $row['sirasapalli_qty']; ?>" data-gajuwaka="<?php echo $row['gajuwaka_qty']; ?>">
														<td><?php echo htmlspecialchars($row['ITEM_ID']); ?></td>
														<td><?php echo htmlspecialchars($row['ITEM_NAME']); ?></td>
														<?php if ($row['sirasapalli_qty'] == 0) { ?>
															<td class="text-center" style="background-color: #f28f8f !important; color: black !important; font-size: 18px !important; font-weight: bold; vertical-align: middle;">-</td>
														<?php } else { ?>
															<td class="text-center" style="background-color: #8ce0a2 !important; color: black !important; font-size: 18px !important; font-weight: bold; vertical-align: middle;"><?php echo $row['sirasapalli_qty']; ?></td>
														<?php } ?>
														<?php if ($row['gajuwaka_qty'] == 0) { ?>
															<td class="text-center" style="background-color: #f28f8f !important; color: black !important; font-size: 18px !important; font-weight: bold; vertical-align: middle;">-</td>
														<?php } else { ?>
															<td class="text-center" style="background-color: #8ce0a2 !important; color: black !important; font-size: 18px !important; font-weight: bold; vertical-align: middle;"><?php echo $row['gajuwaka_qty']; ?></td>
														<?php } ?>
													</tr>
													<?php
												}
												?>
											</tbody>
										</table>
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
	<script>

		$(document).ready(function () {
			// Filter table rows based on search inputs
			function filterTable() {
				let itemQuery = $('#itemSearchInput').val().toLowerCase().trim();

				$('#inventoryTable tbody tr').each(function () {
					let row = $(this);
					let itemCode = row.find('td:nth-child(1)').text().toLowerCase();
					let itemName = row.find('td:nth-child(2)').text().toLowerCase();

					let matchesItem = (itemCode.indexOf(itemQuery) > -1 || itemName.indexOf(itemQuery) > -1);

					if (matchesItem) {
						row.show();
					} else {
						row.hide();
					}
				});
			}

			// Bind events
			$('#itemSearchInput').on('keyup input', filterTable);
		});
	</script>

</body>

</html>