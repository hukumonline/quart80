<?php
class Membership_ManagerController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		$this->_helper->layout->setLayout('layout-membership');
		$this->_helper->layout->setLayoutPath(array('layoutPath'=>ROOT_DIR.'/app/modules/membership/views/layouts'));
		
		Zend_Session::start();
	}
	function activateAction()
	{
		$this->_helper->layout()->disableLayout();
		
		$guid = ($this->_getParam('uid'))? $this->_getParam('uid') : '';
		
		//$aclMan		= new Kutu_Acl_Adapter_Local();
		$obj 		= new Pandamp_Crypt_Password();
		$formater 	= new Pandamp_Core_Hol_User();
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowset = $tblUser->find(base64_decode($guid))->current();
		
		if ($rowset)
		{
			if ($rowset->periodeId == 2)
			{
				$this->_forward('restricted','manager','membership',array('type' => 'user','num' => 106));
			}
			elseif ($rowset->periodeId == 3)
			{
				$this->_forward('restricted','manager','membership',array('type' => 'user','num' => 102));
			}
			elseif ($rowset->periodeId == 4)
			{
				$this->_forward('restricted','manager','membership',array('type' => 'user','num' => 'downgrade'));
			}
			else 
			{
				// set activation date
				$rowset->activationDate = date("Y-m-d h:i:s");
				$rowset->isActive = 1;
				// check package
				if ($rowset->packageId == 26 or $rowset->packageId == 27)
				{
					// set period = trial
					$rowset->periodeId = 2;
					// add user to gacl
					// $aclMan->addUser($rowset->username,'member_gratis');
					// -- write invoice
					// Get disc promo
					$disc = $formater->checkPromoValidation('Disc',$rowset->packageId,$rowset->promotionId,$rowset->paymentId);
					// Get total promo
					$total = $formater->checkPromoValidation('Total',$rowset->packageId,$rowset->promotionId,$rowset->paymentId);
					$formater->_writeInvoice($rowset->kopel,$total,$disc,$rowset->paymentId);
				}
				else 
				{
					$rowset->periodeId = 3;
				}
				// update
//				$result = $rowset->save();
				
//				if ($result)
//				{
					if ($rowset->packageId == 26 or $rowset->packageId == 27) {
						$this->_forward('redirect-subscription-url','manager','membership',array('guid' => base64_decode($guid)));
					}
					else
					{
						$this->_forward('redirect-url','manager','membership',array('username' => $rowset->username));
					}
//				}
//				else 
//				{
//					$this->_forward('restricted','manager','membership',array('type' => 'user','num' => 101));
//				}
				
			}
			
		}
		else 
		{
			$this->_forward('restricted','manager','membership',array('type' => 'user','num' => 105));	
		}
	}
	function redirectSubscriptionUrlAction()
	{
		$this->_helper->layout()->disableLayout();
		
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowset = $tblUser->find($guid)->current();
		
		$this->view->rowUser = $rowset;
		
		$modelUserFinance = new Pandamp_Modules_Identity_UserFinance_Model_UserFinance();
		$userFinanceInfo = $modelUserFinance->fetchRow("userId='".$guid."'");
		if (!$userFinanceInfo) {
			$finance = $modelUserFinance->fetchNew();
			$finance->userId = $guid;
			$finance->taxNumber = '';
			$finance->taxCompany = $userDetailInfo->company;
			$finance->taxAddress = $userDetailInfo->address;
			$finance->taxCity = $userDetailInfo->city;
			$finance->taxProvince = $userDetailInfo->state;
			$finance->taxCountryId = $userDetailInfo->countryId;
			$finance->taxZip = $userDetailInfo->zip;
			$finance->save();
		}
		
		$userFinanceInfo = $modelUserFinance->fetchRow("userId='".$guid."'");
		
		$this->view->userInfo = $userFinanceInfo;
	}
	function redirectUrlAction()
	{
		$this->_helper->layout()->disableLayout();
		$username = ($this->_getParam('username'))? $this->_getParam('username') : '';
		$this->view->username = $username;
	}
	function restrictedAction()
	{
		$this->_helper->layout()->disableLayout();
		
		$type = ($this->_getParam('type'))? $this->_getParam('type') : '';
		$num = ($this->_getParam('num'))? $this->_getParam('num') : '';
		
		switch ($type)
		{
			case "user":
				
				switch ($num) 
				{
					case "downgrade":
						$error_msg = "Akun anda sudah berubah menjadi gratis";
						break;
					case 101:
						$error_msg = "Perbaharui data gagal";
						break;
					case 102:
						$error_msg = "Akun anda sudah aktif";
						break;
					case 105:
						$error_msg = "Nama pengguna tidak ditemukan";
						break;
					case 106:
						$error_msg = "Akun anda sudah aktif tapi status masa percobaan";
						break;
					default:
						$error_msg = "Kesalahan tidak diketahui di sistem manajemen pengguna";
				}
				
			break;
		}
		
		$this->view->error_msg = $error_msg;
		
	}
	
}