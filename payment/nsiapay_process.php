<?php
error_reporting(E_ALL|E_STRICT);

require_once "../baseinit.php";

Pandamp_Application::getResource('session');
Pandamp_Application::getResource('db');


define("_MERCHANTWEB",ROOT_URL);

$transidmerchant = $_GET['TRANSIDMERCHANT'];
$totalamount = $_GET['AMOUNT'];
$status_code = $_GET['STATUSCODE'];

$tblOrder = new Pandamp_Modules_Payment_Order_Model_Order();
$rowOrder = $tblOrder->fetchRow("invoiceNumber='".$transidmerchant."' AND orderStatus=1");

$datenow = date('YmdHis');

//if ($_SERVER['REMOTE_ADDR'] == "203.190.41.220") {
	
	if ($rowOrder) {
		
			
		if ($status_code == 00) 
			$rowOrder->orderStatus = 3;
		else
			$rowOrder->orderStatus = 6;
			
			
		$rowOrder->paymentDate = $datenow;

		$tblNsiapay = new Pandamp_Modules_Payment_Nsiapay_Model_Nsiapay();
		$tblNsiapay->update(array('status'=>'paid','finishtime'=>date('YmdHis')),"transidmerchant='".$transidmerchant."'");
		
		$tblNhis = new Pandamp_Modules_Payment_NsiapayHistory_Model_NsiapayHistory();
		$tblNhis->insert(array('orderId'=>$rowOrder->orderId,'paymentStatus'=>'paid','dateAdded'=>date('YmdHis')));
		
		
		$redirect_url = _MERCHANTWEB."?status_code=".$status_code."&order_number=".$transidmerchant;
			
		$rowOrder->save();
		
		$tblHistory = new Pandamp_Modules_Payment_OrderHistory_Model_OrderHistory();
		$orderHistory = array(
			'orderId'=>$rowOrder->orderId,
			'orderStatusId'=>$rowOrder->orderStatus,
			'dateCreated'=>date('YmdHis'),
			'userNotified'=>0,
			'note'=>'paid with nsiapay method'
		);
		$tblHistory->insert($orderHistory);
		
	}
	else
	{
		$redirect_url = _MERCHANTWEB;
	}
		
//}
//else
//{
//	$rowOrder->orderStatus = 7;
//	$rowOrder->datePurchased = $datenow;
//		
//	$rowOrder->save();
//		
//	$redirect_url = _MERCHANTWEB."?status_code=7&order_number=".$transidmerchant;
//}


$redirect_url = str_replace("&amp;","&",$redirect_url);

header("Location:$redirect_url");
