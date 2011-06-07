<?php
class HolSite_StoreController extends Zend_Controller_Action
{
	protected $_userInfo;
	protected $_configStore;
	protected $_userId;
	
	function preDispatch()
	{
		$this->_helper->layout->setLayout('layout-store');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/hol-site/layouts'));
		
		Zend_Session::start();
		
		$sReturn = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$sReturn = base64_encode($sReturn);
			
		$identity = Pandamp_Application::getResource('identity');
		$loginUrl = $identity->loginUrl;
		//$loginUrl = ROOT_URL.'/helper/synclogin/generate/?returnTo='.$sReturn;
		
		$auth =  Zend_Auth::getInstance();
		if(!$auth->hasIdentity())
		{
			$this->_redirect($loginUrl.'?returnTo='.$sReturn);
			//$this->_redirect($loginUrl);
		}
		else
		{
			// [TODO] else: check if user has access to admin page
			$username = $auth->getIdentity()->username;
			$this->view->username = $username;
		}
		$userId=$auth->getIdentity()->guid;
		$this->_userId = $userId;
		
		$tblUserFinance= new Pandamp_Modules_Identity_UserFinance_Model_UserFinance();
		$this->_userInfo=$tblUserFinance->find($userId)->current();
		
		$storeConfig = Pandamp_Application::getOption('store');
		$this->_configStore = $storeConfig;
	}
	function indexAction()
	{
		
	}
	function checkoutAction()
	{
		$auth = Zend_Auth::getInstance();
		$userId = $auth->getIdentity()->guid;
		
		$modelUser = new Pandamp_Modules_Identity_User_Model_User();
		$userDetailInfo = $modelUser->find($userId)->current();
		
		$modelUserFinance = new Pandamp_Modules_Identity_UserFinance_Model_UserFinance();
		$userFinanceInfo = $modelUserFinance->fetchRow("userId='".$userId."'");
		if (empty($userFinanceInfo)) {
			$finance = $modelUserFinance->fetchNew();
			$finance->userId = $userId;
			$finance->taxNumber = '';
			$finance->taxCompany = $userDetailInfo->company;
			$finance->taxAddress = $userDetailInfo->address;
			$finance->taxCity = $userDetailInfo->city;
			$finance->taxProvince = $userDetailInfo->state;
			$finance->taxCountryId = $userDetailInfo->countryId;
			$finance->taxZip = $userDetailInfo->zip;
			$finance->save();
		}
		
		$userFinanceInfo = $modelUserFinance->fetchRow("userId='".$userId."'");
		
		$cart =& $_SESSION['jCart']; if(!is_object($cart)) $cart = new jCart();
		$this->view->cart = $cart;
		
		$this->view->userInfo = $userFinanceInfo;
		
		if($this->_isStoreClosed())
			$this->_forward('closed','store','site');
	}
	function confirmorderAction()
	{
		if(!is_object($_SESSION['jCart']))
		{
			//forward to somewhere
			echo "FORWARDED";
			$this->_helper->redirector('cartempty','store_payment','hol-site');	
		}
		if(count($_SESSION['jCart']->items)==0)
		{
			//forward to somewhere
			echo "SHOULD BE FORWARDED";
			$this->_helper->redirector('cartempty','store','hol-site');	
		}
		
		$cart =& $_SESSION['jCart']; if(!is_object($cart)) $cart = new jCart();
		$this->view->cart = $cart;
		
		$data = array();
		foreach($this->_request->getParams() as $key=>$value){
			$data[$key] = $value;
		}
		//$this->view->cart = $result;
		
		$this->view->data = $data;
		
		if($data['method']=='postpaid')
		{
			$tblUserFinance= new Pandamp_Modules_Identity_UserFinance_Model_UserFinance();
			$userFinanceInfo = $tblUserFinance->find($this->_userId)->current();
			if(!$userFinanceInfo->isPostPaid){
	            echo 'Not Post Paid Customer';
	            return $this->_helper->redirector('notpostpaid','store_payment','hol-site');
	        }
		}
		
	}
	function completeorderAction()
	{
		$tblPaymentSetting = new Pandamp_Modules_Payment_Setting_Model_PaymentSetting();        
        $rowTaxRate = $tblPaymentSetting->fetchRow("settingKey='taxRate'");
		
		$cart =& $_SESSION['jCart']; if(!is_object($cart)) $cart = new jCart();
		
//		echo "<pre>";
//		print_r($cart);
//		echo "</pre><br>";
		
		if(empty($cart) || count($cart->items)==0)
		{	
			$this->_redirect(ROOT_URL.'/store/cartempty');
		}
		
		$bpm = new Pandamp_Core_Hol_Catalog();
        $result = array('subTotal' => 0, 'taxAmount' => 0, 'grandTotal'=> 0,'items'=>array()); 
        for($iCart=0;$iCart<count($cart->items);$iCart++)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
            $itemId=$cart->items[$iCart];
            $qty= $cart->itemqtys[$itemId];
            $itemPrice=$bpm->getPrice($itemId);
            $result['items'][$iCart]['itemId']= $itemId;
            $result['items'][$iCart]['item_name'] = $modelCatalogAttribute->getCatalogAttributeValue($itemId,'fixedTitle'); 
            $result['items'][$iCart]['itemPrice']= $itemPrice;
            $result['items'][$iCart]['itemTotal']= $qty * $itemPrice;
            $result['items'][$iCart]['qty']= $qty;
            $result['subTotal']+=$itemPrice*$qty;
		}
        $result['taxAmount']= $result['subTotal']*$rowTaxRate->settingValue/100;
        $result['grandTotal'] = $result['subTotal']+$result['taxAmount'];

//		echo "Result : <br><br><pre>";
//		print_r($result);
//		echo "</pre><br>";
//		die;

        $method = $this->_request->getParam('paymentMethod');
		$orderId = $this->saveOrder($result,$method);
		
//		$cart = null;
		
		$data = $this->_request->getParams();
		
		$this->view->cart = $result;
		
		$this->view->data = $data;
		
		$this->view->orderId = $orderId;
		
		$modDir = $this->getFrontController()->getModuleDirectory();
		require_once($modDir.'/models/Store/Mailer.php');
		$mod = new Holsite_Model_Store_Mailer();
		
		switch(strtolower($method))
		{
			case 'manual':
			case 'bank':
				$mod->sendBankInvoiceToUser($orderId);
				break;
			case 'paypal':
				$mod->sendInvoiceToUser($orderId);
				break;
			case 'postpaid':
				$mod->sendInvoiceToUser($orderId);
				break;
		}
	}
	public function viewinvoiceAction()
	{
		$orderId = $this->_request->getParam('orderId');
		
		$tblOrder = new Pandamp_Modules_Payment_Order_Model_Order();
		$items = $tblOrder->getOrderDetail($orderId);
		
		$this->view->orderId = $orderId;
		$this->view->invoiceNumber = $items[0]['invoiceNumber'];
		
		$tblPaymentSetting = new Pandamp_Modules_Payment_Setting_Model_PaymentSetting();        
        $rowTaxRate = $tblPaymentSetting->fetchRow("settingKey='taxRate'");
		
		if($this->_userId != $items[0]['userId'])
		{
			$this->_redirect(ROOT_URL.'/store/cartempty');
		}
		
		$result = array();
		$result['subTotal'] = 0;
		for($iCart=0;$iCart<count($items);$iCart++){
            
			$itemId = $items[$iCart]['itemId'];
            $qty= 1;
            $itemPrice = $items[$iCart]['price'];
            
            $result['items'][$iCart]['itemId']= $itemId;
            $result['items'][$iCart]['item_name'] = $items[$iCart]['documentName']; 
            $result['items'][$iCart]['itemPrice']= $itemPrice;
            $result['items'][$iCart]['qty']= $qty;
            $result['subTotal'] += $itemPrice*$qty;
        }

		$result['taxAmount']= $result['subTotal'] * $rowTaxRate->settingValue/100;
        $result['grandTotal'] = $result['subTotal'] + $result['taxAmount'];

		$this->view->cart = $result;
		
		$data = array();
		$data['taxNumber'] = $items[0]['taxNumber'];
		$data['taxCompany'] = $items[0]['taxCompany'];
		$data['taxAddress'] = $items[0]['taxAddress'];
		$data['taxCity'] = $items[0]['taxCity'];
		$data['taxZip'] = $items[0]['taxZip'];
		$data['taxProvince'] = $items[0]['taxProvince'];
		$data['taxCountry'] = $items[0]['taxCountryId'];
		$data['paymentMethod'] = $items[0]['paymentMethod'];
		$data['currencyValue'] = $items[0]['currencyValue'];
		
		$this->view->data = $data;
	}
	public function cartemptyAction()
	{
		
	}
	private function _isStoreClosed()
	{
		$auth =  Zend_Auth::getInstance();
		if(!$auth->hasIdentity())
		{
		}
		else
		{
			$username = $auth->getIdentity()->username;
			
			$acl = Pandamp_Acl::manager();
			if($acl->checkAcl("site",'all','user', $username, false,false))
			{
				return 0;
			}
		}
		
		return $this->_configStore['isClosed'];
	}
	private function saveOrder($cart,$method)
	{
		$defaultCurrency='Rp';
		
		$tblPaymentSetting = new Pandamp_Modules_Payment_Setting_Model_PaymentSetting();
		$usdIdrEx = $tblPaymentSetting->fetchRow(" settingKey= 'USDIDR'");
		$currencyValue = $usdIdrEx->settingValue;      
        $rowTaxRate = $tblPaymentSetting->fetchRow("settingKey='taxRate'");
		$taxRate = $rowTaxRate->settingValue;
		
		$auth =  Zend_Auth::getInstance();
		$userId=$auth->getIdentity()->guid;
		
		$tblOrder=new Pandamp_Modules_Payment_Order_Model_Order();
        $row=$tblOrder->fetchNew();
		
		$row->userId=$userId;
        
		//get value from post var (store/checkout.phtml)
		if($this->getRequest()->getPost()){
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
        
//        if ($method == "nsiapay") {
//        	$row->orderStatus=8;
//        }
//        else {
//        $row->orderStatus=1; //pending
//        }
        
        $row->orderStatus=1; //pending
        $row->currency = $defaultCurrency;        
        $row->currencyValue = $currencyValue;        
        $row->orderTotal=$cart['grandTotal'];
        $row->orderTax=$cart['taxAmount'];
        $row->ipAddress= Pandamp_Lib_Formater::getRealIpAddr();
        
        $orderId = $row->save();

        $rowJustInserted = $tblOrder->find($orderId)->current();
//		$rowJustInserted->invoiceNumber = date('Ymd') . '.' . $orderId;

		$tblNumber = new Pandamp_Modules_Misc_Number_Model_GenerateNumber();
		$rowset = $tblNumber->fetchRow();
		$num = $rowset->invoice;
		$totdigit = 5;
		$num = strval($num);
		$jumdigit = strlen($num);
		$noinvoice = str_repeat("0",$totdigit-$jumdigit).$num;
		$rowset->invoice = $rowset->invoice += 1;
		$tblNumber->update(array('invoice'=>$rowset->invoice));
        
		$rowJustInserted->invoiceNumber = $noinvoice;
		$rowJustInserted->save();
		
		$this->view->invoiceNumber = $rowJustInserted->invoiceNumber;
		$this->view->datePurchased = $rowJustInserted->datePurchased;
        
        $tblOrderDetail=new Pandamp_Modules_Payment_OrderDetail_Model_OrderDetail();
        
		for($iCart=0;$iCart<count($cart['items']);$iCart++){        
            $rowDetail=$tblOrderDetail->fetchNew();
            
            $itemId=$cart['items'][$iCart]['itemId'];        
            $rowDetail->orderId=$orderId;
            $rowDetail->itemId=$itemId;
            $rowDetail->documentName=$cart['items'][$iCart]['item_name'];
            $rowDetail->price=$cart['items'][$iCart]['itemPrice'];
			$itemPrice = $rowDetail->price;
            @$rowDetail->tax=((($cart['grandTotal']-$cart['subTotal']))/$cart['subTotal'])*100;
            $rowDetail->qty=$cart['items'][$iCart]['qty'];
            $rowDetail->finalPrice=$cart['items'][$iCart]['itemTotal'];                
            $rowDetail->save();
        }

		//[TODO] MUST ALSO INSERT/UPDATE KutuUserFinance

        return $orderId;
    }
	public function errorAction()
	{
		$view = $this->_request->getParam('view');
		
		switch (strtolower($view))
		{
			case 'orderalreadypaid':
				$this->renderScript('store/error-orderalreadypaid.phtml');
				break;
			case 'noorderfound':
				$this->renderScript('store/error-noorderfound.phtml');
				break;
			case 'notowner':
			default:
				$this->renderScript('store/error-notowner.phtml');
				break;
		}
	}
    
}