<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
<?php
include "db_config.php";
$payment_date = $_POST["payment_date"];
$nature = $_POST["nature"];
$vendor = $_POST["vendor"];
$vendor_name = $_POST["vendor_name"];
$customer = $_POST["customer"];
$customer_name = $_POST["customer_name"];
$mode = $_POST["mode"];
$account = $_POST["account"];
$account_to = $_POST["account_to"];
$amount = $_POST["amount"];
$reference = $_POST["reference"];

if($nature=="DEBIT" || $mode=="CREDIT"){
	if( isset($_COOKIE["SA"])){
		$verified="Y";
	}
	else{$verified="N";}
}
else{
$verified="Y";	
}
///check if amount is permissible

if($nature =="DEBIT"){
		
			$select_receipt_trans = "SELECT sum(pending) pending FROM receipt_trans where vendor='".$vendor."' and pending>0 and active_status='A'"	;
	$result = $conn->query($select_receipt_trans);

 if(($row = $result->fetch_assoc())  && $amount>0) {
	$pending_total=$row["pending"] ;}
 }
if($nature =="CREDIT"){
	$select_receipt_trans = "SELECT sum(pending) pending FROM service_trans where customer='".$customer."' and pending>0 and active_status='A'"	;
	$result = $conn->query($select_receipt_trans);


 if(($row = $result->fetch_assoc())  && $amount>0) {
	$pending_total=$row["pending"] ;
	
}
		$select_receipt_trans = "SELECT sum(pending) pending FROM sales_trans where customer='".$customer."' and pending>0 and active_status='A'"	;
	$result = $conn->query($select_receipt_trans);


 if(($row = $result->fetch_assoc())  && $amount>0) {
	$pending_total+=$row["pending"] ;
	
}
}
if($pending_total<$amount){
echo "Cannot pay more than what is pending";
	die();
}
$sql = "SELECT ifnull(max(payment_id),1000001)+1 payment_id FROM payments";
$result = $conn->query($sql);

  if($row = $result->fetch_assoc()) {
	$payment_id=$row["payment_id"] ;
  }	
$sql = "INSERT INTO payments (
            payment_id, payment_date, nature, vendor, vendor_name,
            customer, customer_name, mode, account,
            amount, reference, active_status, shop_id, created_on, to_account, VERIFIED
        ) VALUES (
            '$payment_id', STR_TO_DATE($payment_date, '%d-%m-%Y'), '$nature', '$vendor', '$vendor_name',
            '$customer', '$customer_name', '$mode', '$account', $amount,
            '$reference', 'A', '" . $_COOKIE["shop"] . "', NOW(),'".$account_to."','".$verified."'
        )";
$result = $conn->query($sql);
//echo $sql;
	if($nature =="DEBIT" ){
		
			$select_receipt_trans = "SELECT trans_id,pending FROM receipt_trans where vendor='".$vendor."' and pending>0 and active_status='A' order by trans_date"	;
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
	 if( $mode!="CREDIT"){
	$update_receipt_trans = "update receipt_trans set pending=".$pending ." where trans_id='".$trans_id."'"	;
	$conn->query($update_receipt_trans);}
	$insert_pay_track = "insert into pay_track(payment_id,amount_settled,trans_id,mode) values('".$payment_id."','".$amount_settled."','".$trans_id."','RECEIPT')";
	$conn->query($insert_pay_track);
	$amount=$amount-$row["pending"];
  }	
		
		
		
	}
	
	if($nature =="CREDIT" ){
		
			$select_receipt_trans = "SELECT trans_id,pending FROM service_trans where customer='".$customer."' and pending>0 and active_status='A' order by trans_date"	;
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
	 if( $mode!="CREDIT"){
	$update_receipt_trans = "update service_trans set pending=".$pending ." where trans_id='".$trans_id."'"	;
	$conn->query($update_receipt_trans);}
	$insert_pay_track = "insert into pay_track(payment_id,amount_settled,trans_id,mode) values('".$payment_id."','".$amount_settled."','".$trans_id."','service')";
	
	$conn->query($insert_pay_track);
 $amount=$amount-$amount_settled;}
	if($amount>0)	{
	$select_receipt_trans = "SELECT trans_id,pending FROM sales_trans where customer='".$customer."' and pending>0 and active_status='A' order by trans_date"	;
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
	  if( $mode!="CREDIT"){
	$update_receipt_trans = "update sales_trans set pending=".$pending ." where trans_id='".$trans_id."'"	;
	$conn->query($update_receipt_trans);
	  }
	$insert_pay_track = "insert into pay_track(payment_id,amount_settled,trans_id,mode) values('".$payment_id."','".$amount_settled."','".$trans_id."','sales')";
	
	$conn->query($insert_pay_track);
 $amount=$amount-$row["pending"];}	
		
		
	}
		
		
	}
if(($nature=="DEBIT" || $nature=="BANKTOCASH" ||  $nature=="BANKTOBANK" ||  $nature=="CASHTOBANK" )  && $mode!="CREDIT"){
$update_master = "update account set balance=balance-".$amount ." where account_id='".$account."'"	;
//echo 	$update_master;
$conn->query($update_master);	
}
if($nature=="CREDIT"  && $mode!="CREDIT"){
$update_master = "update account set balance=balance+".$amount ." where account_id='".$account."'"	;	
$conn->query($update_master);	
}
if(($nature=="BANKTOBANK" || $nature=="CASHTOBANK" || $nature=="BANKTOCASH")  && $mode!="CREDIT"){
$update_master = "update account set balance=balance+".$amount ." where account_id='".$account_to."'"	;	
$conn->query($update_master);	
}
echo "Receipt added Successfully";
$conn->close();
?>