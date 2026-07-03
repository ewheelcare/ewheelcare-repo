<?php
include "db_config.php";
$nature = $_POST["nature"];
$payment_id = $_POST["payment_id"];
$amount = $_POST["amount"];
if($nature =="DEBIT"){
		
			$select_receipt_trans = "SELECT trans_id,pending FROM receipt_trans where vendor='".$vendor."' and pending>0 order by trans_date"	;
	$result = $conn->query($select_receipt_trans);

 while(($row = $result->fetch_assoc())  && $amount>0) {
	$pending=$row["pending"] ;
	$trans_id=$row["trans_id"] ;
	$amount_settled=0;
	if($amount>$pending){
		$amount_settled=$pending;
		$pending=0;
		
	}else{
		$pending = $pending-$amount;
		$amount_settled=$amount;
	}
	$update_receipt_trans = "update receipt_trans set pending=".$pending ." where trans_id='".$trans_id."'"	;
	$conn->query($update_receipt_trans);
	$insert_pay_track = "insert into pay_track(payment_id,amount_settled,trans_id,mode) values('".$payment_id."','".$amount_settled."','".$trans_id."','RECEIPT')";
	$conn->query($insert_pay_track);
	$amount=$amount-$row["pending"];
  }	
		
		
		
	}
	
	if($nature =="CREDIT"){
		
			$select_receipt_trans = "SELECT trans_id,pending FROM service_trans where customer='".$customer."' and pending>0 order by trans_date"	;
	$result = $conn->query($select_receipt_trans);


 while(($row = $result->fetch_assoc())  && $amount>0) {
	$pending=$row["pending"] ;
	$trans_id=$row["trans_id"] ;
	$amount_settled=0;
	if($amount>$pending){
		$amount_settled=$pending;
		$pending=0;
		
	}else{
		$pending = $pending-$amount;
		$amount_settled=$amount;
	}
	$update_receipt_trans = "update service_trans set pending=".$pending ." where trans_id='".$trans_id."'"	;
	$conn->query($update_receipt_trans);
	$insert_pay_track = "insert into pay_track(payment_id,amount_settled,trans_id,mode) values('".$payment_id."','".$amount_settled."','".$trans_id."','service')";
	
	$conn->query($insert_pay_track);
 $amount=$amount-$row["pending"];}
		
		
		
	}
if($nature=="DEBIT" || $nature=="BANKTOCASH" ||  $nature=="BANKTOBANK" ||  $nature=="CASHTOBANK"){
$update_master = "update account set balance=balance-".$amount ." where account_id='".$account."'"	;
//echo 	$update_master;
$conn->query($update_master);	
}
if($nature=="CREDIT" ){
$update_master = "update account set balance=balance+".$amount ." where account_id='".$account."'"	;	
$conn->query($update_master);	
}
$sql = "update payments ser verified='Y' where payment_id='".$payment_id."'";
$result = $conn->query($sql);
?>