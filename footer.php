 <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
			<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Login</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
      <table class="table table-striped">
		<tr>
		<td>User ID</td>
		<td><input class="form-control" name="uid" id="uid" placeholder="Your User ID, please"></td>
		</tr>
		<tr>
		<td>Password</td>
		<td><input type="password" class="form-control" name="pwd" id="pwd" placeholder="Your  password, please"></td>
		</tr>
		<tr>
		<td>Shop</td>
		<td>
		<select class="form-control" name="shop" id="shop" onchange="load_shop_pic()">
		<option value="">--Select--</option>
	<?php
include "db_config.php";
	$sql = "SELECT shop_id, shop_name FROM shop";
//echo $sql;
$result = $conn->query($sql);
$result_str="";
while ($row = $result->fetch_assoc()) {?>
<option value="<?php echo $row["shop_id"];?>~<?php echo $row["shop_name"];?>"><?php echo $row["shop_name"];?> </option>	
<?php }?>	
</td>
		</tr>
		<tr>
		<td colspan=2>
		<center><img class="img img-responsive" id="shop_pic" style="width:70%"></img></center>
		</td>
		</tr>
		</table>
      </div>

     <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="login">Login</button>
      </div>

    </div>
  </div>
</div>
	<!-- ERROR MODAL -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Error from System</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="alert alert-warning" id="error_block"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Message from System</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="alert alert-warning" id="success_block"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
