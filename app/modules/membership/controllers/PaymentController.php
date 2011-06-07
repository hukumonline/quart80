<?php
class Membership_PaymentController extends Zend_Controller_Action
{
	protected $_testMode;
	protected $_orderIdNumber;
	protected $_defaultCurrency;
	protected $_currencyValue;
	
	function preDispatch()
	{
        $this->_testMode=true;
		$this->_defaultCurrency='USD';
		$tblPaymentSetting = new Pandamp_Modules_Payment_Setting_Model_PaymentSetting();
		$usdIdrEx = $tblPaymentSetting->fetchAll($tblPaymentSetting->select()->where(" settingKey= 'USDIDR'"));
		$this->_currencyValue = $usdIdrEx[0]->settingValue;
		
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		$this->_helper->layout->setLayout('layout-membership');
		$this->_helper->layout->setLayoutPath(array('layoutPath'=>ROOT_DIR.'/app/modules/membership/views/layouts'));
				
		Zend_Session::start();
	}
	function processAction()
	{
		$formater 	= new Pandamp_Core_Hol_User();
		
		$orderId = $this->_request->getParam('orderId');
		$this->_orderIdNumber = $orderId;
		
		if(empty($orderId))
		{
			echo "kosong";
			die();
		}
		
		$modelAppStore = new App_Model_Store();
		if($modelAppStore->isOrderPaid($orderId))
		{
			//forward to error page
			$this->_helper->redirector->gotoSimple('error', 'store', 'hol-site',array('view'=>'orderalreadypaid'));
			die();
		}
		
		$tblOrder = new Pandamp_Modules_Payment_Order_Model_Order();
		$items = $tblOrder->getOrderDetail($orderId);
		
		$tmpMethod = $this->_request->getParam('method');
		if(!empty($tmpMethod))
		{
			$items[0]['paymentMethod'] = $tmpMethod;
		}
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowUser = $tblUser->find($items[0]['userId'])->current();
		
		$total = $formater->checkPromoValidation('Total',$rowUser->packageId,$rowUser->promotionId,$rowUser->paymentId);
		
		switch($items[0]['paymentMethod'])
		{
			case 'nsiapay' :
				
                require_once('PaymentGateway/Nsiapay.php');  // include the class file
                $paymentObject = new Nsiapay;             // initiate an instance of the class
                
                if($this->_testMode){
                	$paymentObject->enableTestMode();
                }
                
                $paymentObject->addField('TYPE',"IMMEDIATE");
                
                for($iCart=0;$iCart<count($items);$iCart++)
                {
                	$i=$iCart+1;
                	$basket[] = $items[$iCart]['documentName']." ".$rowUser->paymentId." Months,".$items[$iCart]['price'].".00".",".$items[$iCart]['qty'].",".$items[$iCart]['finalPrice'].".00";
                	$subTotal += $items[$iCart]['price'] * $items[$iCart]['qty'];              
                }
                
                $ca = implode(";", $basket);
                
                $merchantId = "000100090000028";
                
                $paymentObject->addField("BASKET",$ca);
                $paymentObject->addField("MERCHANTID",$merchantId);
                $paymentObject->addField("CHAINNUM","NA");
                $paymentObject->addField("TRANSIDMERCHANT",$items[0]['invoiceNumber']);
                $paymentObject->addField("AMOUNT",$subTotal);
                $paymentObject->addField("CURRENCY","360");
                $paymentObject->addField("PurchaseCurrency","360");
                $paymentObject->addField("acquirerBIN","360");
                $paymentObject->addField("password","123456");
                $paymentObject->addField("URL","http://hukumonline.pl");
                $paymentObject->addField("MALLID","199");
                $paymentObject->addField("SESSIONID",Zend_Session::getId());
                $sha1 = sha1($subTotal.".00".$merchantId."08iIWbWvO16w".$items[0]['invoiceNumber']);
//                echo $subTotal.".00".$merchantId."08iIWbWvO16w".$items[0]['invoiceNumber']."<br>";
//                echo $sha1;die;
                $paymentObject->addField("WORDS",$sha1);
                
//                $paymentObject->dumpFields();
                $this->_helper->layout->disableLayout();
                
                $paymentObject->submitPayment();
                
				break;
			case 'manual':
			case 'bank':
                /*
                 1. update order status
                 2. redirect to instruction page 
                */

				//setting payment and status as pending (1), notify = 0, notes = 'paid with...'
				$this->updateInvoiceMethod($orderId, 'bank', 1, 0, 'paid with manual method');
				
				// HAP: i think we should send this notification when user were on page "Complete Order" and after confirmation made by user is approved;
				//$this->Mailer($orderId, 'admin-order', 'admin');
				//$this->Mailer($orderId, 'user-order', 'user');
				
				$this->_helper->redirector('instruction','store_payment','hol-site',array('orderId'=>$orderId));
                break;
				
		}
	}
	function completeAction()
	{
		$formater 	= new Pandamp_Core_Hol_User();
		
		$defaultCurrency = 'Rp';
		
		$guid = $this->_request->getParam('guid');
		$method = $this->_request->getParam('method');
		
		$tblPaymentSetting = new Pandamp_Modules_Payment_Setting_Model_PaymentSetting();
		$usdIdrEx = $tblPaymentSetting->fetchRow(" settingKey= 'USDIDR'");
		$currencyValue = $usdIdrEx->settingValue;      
        $rowTaxRate = $tblPaymentSetting->fetchRow("settingKey='taxRate'");
		$taxRate = $rowTaxRate->settingValue;
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowUser = $tblUser->find($guid)->current();
		
		$this->view->rowUser = $rowUser;
		
		// discount
		$disc = $formater->checkPromoValidation('Disc',$rowUser->packageId,$rowUser->promotionId,$rowUser->paymentId);
		$total = $formater->checkPromoValidation('Total',$rowUser->packageId,$rowUser->promotionId,$rowUser->paymentId);
		
		$tblPackage = new Pandamp_Modules_Identity_Package_Model_Package();
		$rowPackage = $tblPackage->find($rowUser->packageId)->current();
		
		$this->view->rowPackage = $rowPackage;
		
		$tblOrder=new Pandamp_Modules_Payment_Order_Model_Order();
        $row=$tblOrder->fetchNew();
		
		$row->userId=$guid;
		
		if ($this->getRequest()->getPost()) {
			$value = $this->getRequest()->getPost(); 
				
			$row->taxNumber=$value['taxNumber'];
			$row->taxCompany=$value['taxCompany'];
			$row->taxAddress=$value['taxAddress'];
			$row->taxCity=$value['taxCity'];
			$row->taxZip=$value['taxZip'];
			$row->taxProvince=$value['taxProvince'];
			$row->taxCountryId=$value['taxCountry'];
			$row->paymentMethod=$method;
		}
		
        $row->datePurchased=date('YmdHis');
        $row->paymentMethodNote = "membership";
        
        if ($method == "nsiapay") {
        	$row->orderStatus=8;
        }
        else {
        	$row->orderStatus=1; //pending
        }
        
        $row->currency = $defaultCurrency;        
        $row->currencyValue = $currencyValue;    

        $row->orderTotal=$total;
        $row->ipAddress= Pandamp_Lib_Formater::getRealIpAddr();
        
        $orderId = $row->save();
        
        $rowJustInserted = $tblOrder->find($orderId)->current();
		$rowJustInserted->invoiceNumber = date('Ymd') . '.' . $orderId;
		
		$temptime = time();
		$temptime = Pandamp_Lib_Formater::DateAdd('d',5,$temptime);
			
		$rowJustInserted->discount = $disc;
		$rowJustInserted->invoiceExpirationDate = strftime('%Y-%m-%d',$temptime);
		
		$rowJustInserted->save();
		
		$this->view->invoiceNumber = $rowJustInserted->invoiceNumber;
		$this->view->datePurchased = $rowJustInserted->datePurchased;
        
		$tblOrderDetail=new Pandamp_Modules_Payment_OrderDetail_Model_OrderDetail();
		$rowDetail=$tblOrderDetail->fetchNew();
		
		$rowDetail->orderId=$orderId;
		$rowDetail->itemId=$rowPackage->packageId;
		
		if ($rowUser->packageId == 26) {
			$group = "Subsciption for Member Individual ".$rowUser->paymentId." Months";
		} else if ($rowUser->packageId == 27) {
			$group = "Subsciption for Member Corporate".$rowUser->paymentId." Months";
		}
		
		$this->view->itemName = $group;
		
		$rowDetail->documentName=$group;
		
		$rowDetail->price=$total;
		
		$numOfUsers = $tblUser->getUserCount($rowUser->guid);
		
		$this->view->numOfUsers = $numOfUsers;
		$this->view->grandtotal = $grandTotal;
		$this->view->method = $method;
		$this->view->orderId = $orderId;
		$this->view->total = $total;
		
		$rowDetail->qty=$numOfUsers;
		$rowDetail->finalPrice=$total;
		
		$rowDetail->save();
		
		$data = $this->_request->getParams();
		
		$this->view->data = $data;
		
		$modDir = $this->getFrontController()->getModuleDirectory();
		require_once($modDir.'/models/Store/Mailer.php');
		$mod = new Holsite_Model_Store_Mailer();
		
		switch(strtolower($method))
		{
			case 'manual':
			case 'bank':
				//$mod->sendBankInvoiceToUser($orderId);
				break;
			case 'nsiapay':
				$mod->sendInvoiceToUser($orderId);
				break;
		}
	}
	protected function updateInvoiceMethod($orderId, $payMethod, $status, $notify, $note){        
        $tblOrder = new Pandamp_Modules_Payment_Order_Model_Order();
		
		$rows = $tblOrder->find($orderId)->current();
		$row = array();
		
		$ivnum = $rows->invoiceNumber;
		
		/*if(empty($ivnum)){
			if($status==3 || $status==5 || (!empty($_SESSION['_method'])&&($_SESSION['_method'] =='paypal')))
			$ivnum = $this->getInvoiceNumber();
			//$row=array ('invoiceNumber'	=> $ivnum);
		}*/
		//if( )$ivnum = $this->getInvoiceNumber();
		
		
		$row=array ('orderStatus'	=> $status, 'paymentMethod' => $payMethod);
		
		//$_SESSION['_method'] = '';
		/*$this->_paymentMethod=$payMethod;//set payment method on table
		$row->paymentMethod=$this->_paymentMethod;*/
		
		$tblOrder->update($row, 'orderId = '. $orderId);
		
		$tblHistory = new Pandamp_Modules_Payment_OrderHistory_Model_OrderHistory();
		$rowHistory = $tblHistory->fetchNew();
		
		$rowHistory->orderId = $orderId;
		$rowHistory->orderStatusId = $status;
		$rowHistory->dateCreated = date('YmdHis');
		$rowHistory->userNotified = $notify;
		$rowHistory->note = $note;
		$rowHistory->save();
		
		return $ivnum;
	}
	
}