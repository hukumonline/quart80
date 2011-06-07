<?php
error_reporting(E_ALL|E_STRICT);

require_once "../baseinit.php";

Pandamp_Application::getResource('session');
Pandamp_Application::getResource('db');

$transidmerchant = $_GET['OrderNumber'];
$responseCode = $_GET['RESPONSECODE'];
$cardNumber = $_GET['CARDNUMBER'];
$bank = $_GET['BANK'];
$approvalCode = $_GET['APPROVALCODE'];
$result = strtoupper($_GET['RESULT']);

$tblOrder = new Pandamp_Modules_Payment_Order_Model_Order();
$rowOrder = $tblOrder->fetchRow("invoiceNumber='".$transidmerchant."' AND orderStatus=1");

$datenow = date('YmdHis');

if ($_SERVER['REMOTE_ADDR'] == "203.190.41.220") {
	
	if ($rowOrder > 0) {
		
		if ($result == "SUCCESS") {
			//$rowOrder->orderStatus = 14;
			$rowOrder->paymentDate = $datenow;
			
			$data = array(
				'status'=>'notify',
				'responseCode'=>$responseCode,
				'creditcard'=>$cardNumber,
				'bank'=>$bank,
				'approvalCode'=>$approvalCode
			);
			
			$tblNsiapay = new Pandamp_Modules_Payment_Nsiapay_Model_Nsiapay();
			$tblNsiapay->update($data,"transidmerchant='".$transidmerchant."'");
			
			$tblNhis = new Pandamp_Modules_Payment_NsiapayHistory_Model_NsiapayHistory();
			$tblNhis->insert(array('orderId'=>$rowOrder->orderId,'paymentStatus'=>'notify','dateAdded'=>date('YmdHis')));
		
			$response = "Continue";
		}
		else
		{
			//$rowOrder->orderStatus = 15;
			$rowOrder->paymentDate = $datenow;
			
			$response = "Stop";
		}
			
		$rowOrder->save();
		
	}
	else
	{
		$response = "Stop";
	}
		
	echo $response;
		
}
else
{
	$rowOrder->orderStatus = 7;
	$rowOrder->datePurchased = $datenow;
		
	$rowOrder->save();
		
	echo "Continue";
}
