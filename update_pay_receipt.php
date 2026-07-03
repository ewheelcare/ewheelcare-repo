<?php
include "db_config.php";
$paid_amount = $_POST["paid_amount"];
$trans_amount = $_POST["trans_amount"];
$pay_type = $_POST["pay_type"];
$ref_no = $_POST["ref_no"];
$pending =$trans_amount-$paid_amount;
$gst = $_POST["gst"];
$tally = $_POST["tally"];
$trans_id = $_POST["trans_id"];
$payment_date = $_POST["trans_date"];
$customer = $_POST["customer"];	
$account = $_POST["account"];	
$customer_name = $_POST["customer_name"];	
if($payment_date==""){
	$payment_date ="now()";
}
$sql = "update receipt_trans set pay_type='".$pay_type."',ref_no='".$ref_no."',pending=pending-".$paid_amount.",paid_amount=".$paid_amount." WHERE trans_id='".$trans_id."'";
echo $sql;
$result = $conn->query($sql);
$sql = "SELECT ifnull(max(payment_id),1000001)+1 payment_id FROM payments";
$result = $conn->query($sql);

  if($row = $result->fetch_assoc()) {
	$payment_id=$row["payment_id"] ;
  }	
$sql = "INSERT INTO payments (
            payment_id, payment_date, nature, 
            customer, customer_name, mode, account,
            amount, reference, active_status, shop_id, created_on
        ) VALUES (
            '$payment_id', STR_TO_DATE('$payment_date', '%d-%m-%Y'), 'DEBIT', 
            '$customer', '$customer_name', '$pay_type', '$account', $paid_amount,
            '$ref_no', 'A', '" . $_COOKIE["shop"] . "', NOW()
        )";
$result = $conn->query($sql);

$update_master = "update account set balance=balance-".$paid_amount ." where account_id='".$account."'"	;	
$conn->query($update_master);	
$insert_pay_track = "insert into pay_track(payment_id,amount_settled,trans_id,mode) values('".$payment_id."','".$paid_amount."','".$trans_id."','receipt')";
	
	$conn->query($insert_pay_track);
//echo $sql;
echo "Sales Trans added Successfully";
$conn->close();
?>