<?php
include "db_config.php";
$payment_id = $_POST["payment_id"];
$account = $_POST["account"];
$account_to = $_POST["account_to"];
$nature = $_POST["nature"];
$amount = $_POST["amount"];
$mode1=$_POST["mode"];

/*$payment_id="4000583";
$nature="CREDIT";
$account="1000010";
$account_to="";
$amount="1200";
$mode="CREDIT";*/
$sql = "update payments set active_status='Z' where payment_id='".$payment_id ."'";
$result = $conn->query($sql);

 
$select_receipt_trans = "SELECT trans_id,amount_settled,mode FROM pay_track where payment_id='".$payment_id."'"	;
$result = $conn->query($select_receipt_trans);
if($nature=="DEBIT" || $nature=="CREDIT"){	
while($row = $result->fetch_assoc()){
	$amount_settled=$row["amount_settled"] ;
	$trans_id=$row["trans_id"] ;
	$mode=$row["mode"] ;
	if($mode =="service"){
			if($mode1!="CREDIT"){
	$update_receipt_trans = "update service_trans set pending=pending+".$amount_settled." where trans_id='".$trans_id."'"	;
	$conn->query($update_receipt_trans);}
	$insert_pay_track = "delete  from  pay_track where payment_id='".$payment_id."' and trans_id='".$trans_id."'";
	$conn->query($insert_pay_track);
	}else{
			if($mode1!="CREDIT"){
	$update_receipt_trans = "update receipt_trans set pending=pending+".$amount_settled." where trans_id='".$trans_id."'"	;
	$conn->query($update_receipt_trans);
			}
	$insert_pay_track = "delete  from  pay_track where payment_id='".$payment_id."' and trans_id='".$trans_id."'";
	$conn->query($insert_pay_track);	
		
	}


	
}
}
if($nature=="DEBIT" || $nature=="BANKTOCASH" ||  $nature=="BANKTOBANK"){
		if($mode1!="CREDIT"){
$update_master = "update account set balance=balance+".$amount ." where account_id='".$account."'"	;
echo 	$update_master;
$conn->query($update_master);
		}
}
if($nature=="CREDIT" || $nature=="CASHTOBANK"){
		if($mode1!="CREDIT"){
$update_master = "update account set balance=balance-".$amount ." where account_id='".$account."'"	;	
$conn->query($update_master);}	
}
if($nature=="BANKTOBANK"){
		if($mode1!="CREDIT"){
$update_master = "update account set balance=balance-".$amount ." where account_id='".$account_to."'"	;	
$conn->query($update_master);	
		}
}
echo "deleted Successfully";
$conn->close();
?>