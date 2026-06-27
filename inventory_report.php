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
									<div class="table-responsive">
										<table class="table table-hover table-bordered" id="inventoryTable">
											<thead class="bg-light">
												<tr>
													<th>Item Name</th>
													<th>Storage Location</th>
													<th>Current Qty</th>
													<th style="width:150px">Actions</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$sql = "SELECT i.ITEM_NAME, s.shop_name, sl.storagelocation_name, inv.qty, inv.item_id, inv.shop_id
														FROM shop_inventory inv
														JOIN item i ON inv.item_id = i.ITEM_ID
														JOIN shop s ON inv.shop_id = s.shop_id
														JOIN storagelocation sl ON inv.storagelocation_id = sl.storagelocation_id
														ORDER BY s.shop_name, i.ITEM_NAME";
												$result = $conn->query($sql);

												$current_shop = "";
												while ($row = $result->fetch_assoc()) {
													if ($current_shop != $row['shop_name']) {
														$current_shop = $row['shop_name'];
														echo '<tr class="bg-gray-100 font-weight-bold"><td colspan="4" class="text-primary" style="font-size:110%"><i class="fas fa-store"></i> ' . $current_shop . '</td></tr>';
													}
													?>
													<tr>
														<td><?php echo $row['ITEM_NAME']; ?></td>
														<td><span class="text-secondary small"><?php echo $row['storagelocation_name']; ?></span></td>
														<td>
															<strong><?php echo $row['qty']; ?></strong>
															<?php if ($row['qty'] < 10) { ?>
																<span class="badge badge-warning">Low Stock</span>
															<?php } ?>
														</td>
														<td class="text-center">
															<?php if ($row['qty'] < 50) { ?>
																<button class="btn btn-danger btn-sm btn-block"
																	onclick="openReorder('<?php echo addslashes($row['ITEM_NAME']); ?>', <?php echo $row['qty']; ?>)">
																	<i class="fas fa-shopping-cart"></i> Reorder
																</button>
															<?php } ?>
														</td>
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

	<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="reorderModalTitle">Reorder Item</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Item Name</label>
						<input type="text" class="form-control" id="reorderItemName" readonly>
					</div>
					<div class="form-group">
						<label>Current Stock</label>
						<input type="text" class="form-control" id="reorderCurrentStock" readonly>
					</div>
					<div class="form-group">
						<label>Reorder Quantity</label>
						<input type="number" class="form-control" id="reorderQty" placeholder="Enter quantity to order">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-success" id="saveReorder">Submit Reorder</button>
				</div>
			</div>
		</div>
	</div>

	<?php include "footer_include.php"; ?>
	<script>
		var addModal = new bootstrap.Modal(document.getElementById('addModal'));

		function openReorder(itemName, currentStock) {
			$('#reorderItemName').val(itemName);
			$('#reorderCurrentStock').val(currentStock);
			$('#reorderQty').val('');
			addModal.show();
		}

		$('#saveReorder').on('click', function () {
			let itemName = $('#reorderItemName').val();
			let qty = $('#reorderQty').val();

			if (!qty || qty <= 0) {
				alert("Please enter a valid quantity.");
				return;
			}

			// In a real scenario, you would send this to a backend script
			alert("Reorder request for " + qty + " units of " + itemName + " has been submitted!");
			addModal.hide();
		});
	</script>

</body>

</html>