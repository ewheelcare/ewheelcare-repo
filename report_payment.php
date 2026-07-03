<!DOCTYPE html>
<html lang="en">

<head>
<?php include "header_include.php";
include "db_config.php";
?>
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
 <table class="table table-striped">  
     <tr>
	 <td>From date<br><input type="text" name="from_date" id="from_date" class="form-control"></td>
	 <td>To date<br><input type="text" name="to_date" id="to_date" class="form-control"></td>
	 <td>Vendor<br><select  class="form-control" name="vendor">
	 <option value="">--select---</option>
<?php  $sql="SELECT vendor_id,company_name FROM vendor order by 1";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {?>
<option value="<?php echo $row['company_name'];?>"><?php echo $row['company_name'];?></option>
<?php }?>
	  </select></td>
	  <td>Customer<br><select  class="form-control" name="customer">
<option value="">--select---</option>
<?php  $sql="SELECT customer_id,company_name FROM customer order by 1";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['company_name'];?>"><?php echo $row1['company_name'];?></option>
<?php }?>
	  </select>
	  </td>
	   <td>Mode Of Payment<br><select  class="form-control" name="mode">
<option value="">--select---</option>
<?php  $sql="SELECT paytype_name  FROM  paytype ";
$result = $conn->query($sql);

while ($row1 = $result->fetch_assoc()) {?>
<option value="<?php echo $row1['paytype_name'];?>"><?php echo $row1['paytype_name'];?></option>
<?php }?>
	  </select>
	  </td>
	  <td>Account<br><select type="text"  class="form-control" name="account">
	 <option value="">--Select--</option>
	 <?php  $sql="SELECT account_id,account_name,paytype_id FROM account order by 1";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
?>
	  <option value="<?php echo $row['account_id'];?>"><?php echo $row['account_name'];?></option>
<?php }?>
	 </select></td>
	 <td>Nature<br><select type="text"  class="form-control" name="nature">
	 <option value="">--Select--</option>
	  <option value="DEBIT">DEBIT</option>
	   <option value="CREDIT">CREDIT</option>
	    <option value="CASHTOBANK">CASH TO BANK</option>
	    <option value="BANKTOCASH">BANK TO CASH</option>
			    <option value="BANKTOBANK">BANK TO BANK</option>
	 </select></td>
	  <td><input type="submit" class="btn btn-primary btn-sm" value="Search"></td>
	 </tr>
	 </table>
		</form>
		
		<?php 
		$from_date=$_POST["from_date"];
		$to_date=$_POST["to_date"];
		$customer=$_POST["customer"];
		$vendor=$_POST["vendor"];
		$mode=$_POST["mode"];
		$nature=$_POST["nature"];
		$account=$_POST["account"];
		//echo $nature;
	//	echo $from_date."====".$to_date."====".$item."=====".$customer;
	
	?>
	
<div class="row">	
<?php
$sql_bal="SELECT account_name, balance FROM account";
$result_bal = $conn->query($sql_bal);
while ($row_bal = $result_bal->fetch_assoc()) {
	$account_name=$row_bal['account_name'];
	$balance=$row_bal['balance'];?>
	<div class="col-md-4 alert-success" style="text-align:center;border:3px solid white"><h4><?php echo $account_name ;?></h4>
	<h2><?php echo $balance ;?></h2>
	</div>
<?php }

 ?>	
</div>	
	
	<button class="btn btn-sm btn-primary" type="button" STYLE="float:right;margin:3px;"  onclick="print_table()">Excel</button>
	<table class="table table-striped table-bordered" id="report_table"><thead>
	<tr>
	  <th>Trans Det</th>
                                            <th>Vendor / Customer  Det</th>
                                            <th>Details</th>
	<tr>
	</thead><tbody>	
		<?php $sql="SELECT p.*,a.account_name FROM payments p, account a where p.account=a.account_id and p.active_status='A' ";
		if(isset($from_date) && $from_date!="")
		$sql.="  AND p.payment_date BETWEEN '$from_date' AND '$to_date' ";
if(isset($customer) && $customer!="")
	$sql.="  AND p.customer_name='".$customer."'";
if(isset($vendor) && $vendor!="")
	$sql.="  AND p.vendor_name='".$vendor."'";
if(isset($mode) && $mode!="")
$sql.="  AND p.mode='".$mode."'";
if(isset($nature) && $nature!="")
$sql.="  AND p.nature='".$nature."'";
if(isset($account) && $account!="")
$sql.="  AND p.account='".$account."'";
	//echo $sql;	
		$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {?>
	
<tr>
	<TD><b><?php echo $row["payment_id"];?></b>
<br> Dtd. <b><?php echo $row["payment_date"];?></b><br>
Nature :<?php echo $row["nature"];?>
</TD>
<TD><b>Vendor : <?php echo $row["vendor"];?>(<?php echo $row["vendor_name"];?>)</b>
<br> <b>Costomer : <?php echo $row["customer"];?>(<?php echo $row["customer_name"];?>)</b><hr>
<?php $sql_pay="SELECT mode,trans_id,amount_settled FROM pay_track where payment_id='".$row["payment_id"]."'";
$result_pay = $conn->query($sql_pay);
while ($row_pay = $result_pay->fetch_assoc()) {
	$url="";
	if($row_pay["mode"]=="service")
	{$url="service_trans_new.php?trans_id=".$row_pay["trans_id"];}
else
{$url="sales_trans.php?trans_id=".$row_pay["trans_id"];}?>
<a href="<?php echo $url;?>" target="_blank" class="btn btn-primary badge">Rs. <?php echo $row_pay["amount_settled"];?>
<br> <?php echo $row_pay["trans_id"];?>(<?php echo $row_pay["mode"];?>)
</a>	
<?php }

?>
</TD>
<TD><?php echo $row["details"];?>
<br>Amount : <?php echo $row["amount"];?>
<br>Mode Of Pay : <?php echo $row["mode"];?>
<br>A/C :  <?php echo $row["account_name"];?>
</TD>
<?php }
		?>
		</tbody></table>
		
		
					
									
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
   
   
   

<?php include "footer_include.php";?>
	<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
	 <script src="js/demo/datatables-demo.js"></script>

<script>
 $(function () {
    $("#from_date").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
      });
	  $("#to_date").datepicker({
        dateFormat: "yy-mm-dd"  // Set format to yyyy-mm-dd
      });
  });

</script>


<script src="js/tableToExcel.js"></script>
 <script type="text/javascript">
		function print_table(){
		TableToExcel.convert(document.getElementById("report_table"));
		}
		</script>

  

</body>

</html>