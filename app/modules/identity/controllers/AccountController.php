<?php

class Identity_AccountController extends Zend_Controller_Action 
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		$this->_helper->layout->setLayout('layout-hukumonlineid-welastic');
		$this->_helper->layout->setLayoutPath(array('layoutPath'=>ROOT_DIR.'/app/modules/identity/views/layouts'));
	}
	function lupasandiAction() 			
	{
		$this->_helper->layout->setLayout('layout-hukumonlineid-ext');
	}
	function kirimsandiAction()
	{
		$this->_helper->layout->disableLayout();
		
		$request = $this->getRequest();

		$validator = new Zend_Validate_EmailAddress();
		
		if ($request->getParam('email') == '') {
			$error[] = '- Email harus diisi';
		}
		if (!$validator->isValid($request->getParam('email'))) {
			$error[] = '- Penulisan email salah!';
		}
		if ($request->getParam('user_name') == '') {
			$error[] = '- Nama pengguna diisi!';
		}
		 
		if (isset($error)) {
			
			echo '<b>Error</b>: <br />'.implode('<br />', $error);
			
		} else {
		
		$formater = new Pandamp_Core_Hol_User();
				
		$username = $this->_getParam('user_name');
		$email = $this->_getParam('email');
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowUser = $tblUser->fetchRow("username='".$username."' AND email='".$email."'");
		
		if ($rowUser)
		{
			// get mail content
			$mailcontent = $formater->getMailContent("lupa-password");
			// write forgotPassword
			$formater->_writeForgotPassword($mailcontent, $rowUser->username, $rowUser->email);
		}
		else 
		{
			echo "Invalid email/user";
		}
		}
	}
	function penjelasanAction()
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		$rowset = $tblCatalog->fetchRow("shortTitle='signup-indonesia' AND status=99");
		$rowsetCatalogAttribute = $rowset->findDependentRowsetCatalogAttribute();
		
		$this->view->description = $rowsetCatalogAttribute->findByAttributeGuid('fixedDescription')->value;
		$this->view->content = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent')->value;
	}
	function loginAction()
	{
		//$this->preProcessSession();
		
		$returnTo = ($this->_getParam('returnTo'))? $this->_getParam('returnTo') : '';
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchRow("shortTitle='halaman-depan-login' AND status=99");
		
		if(!empty($rowset))
		{
			$rowsetCatalogAttribute = $rowset->findDependentRowsetCatalogAttribute();
			$fixedContent = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent')->value;
		}
		else 
		{
			$fixedContent = '';
		}
		
		$this->view->content = $fixedContent;
		$this->view->returnTo = $returnTo;
	}
	
	/**	
	 * Login authentication
	 * @param username, password 
	 */
	function kloginAction()
	{
		$this->_helper->layout()->disableLayout();
		
		$request = $this->getRequest();
		
		$userName = ($request->getParam('u'))? $request->getParam('u') : '';
		$password = ($request->getParam('p'))? $request->getParam('p') : '';
		$remember = ($request->getParam('s'))? $request->getParam('s') : '';
		
		$response = array();
		
			
		$saveHandler = Zend_Session::getSaveHandler();
		$saveHandler->setLifetime(3600)
		            ->setOverrideLifetime(true);

		Zend_Session::start();
			
		$authMan = new Pandamp_Auth_Manager($userName, $password);
		$authResult = $authMan->authenticate();
		
		$zendAuth = Zend_Auth::getInstance();
		if($zendAuth->hasIdentity())
		{
			if($authResult->isValid())
			{
				Zend_Session::regenerateId();
				$r = $this->getRequest();
				$returnUrl = base64_decode($r->getParam('r'));
				if(!empty($returnUrl))
				{
					if(strpos($returnUrl,'?'))
					{
						$sAddition = '&';
					}
					else 
					{
						$sAddition = '?';
						
						Pandamp_Lib_Formater::writeLog();
						
						if (isset($remember) && $remember == 'yes') {
						Zend_Session::rememberMe(3600);
						$hol = new Pandamp_Core_Hol_Auth();
						$hol->user = $userName;
						$hol->user_pw = $password;
						$hol->save_login = $remember;
						$hol->login_saver();
						}
						
						$this->_helper->getHelper('Cache')->removePagesTagged(array('entries','hold','warta','clinic'));
				
						$response['success'] = true;
						$response['message'] = "$returnUrl".$sAddition."PHPSESSID=".Zend_Session::getId();
					}
				}
				
			}
			else 
			{
				if($authResult->getCode() != -51)
				{
					// failure : clear database row from session
					Zend_Auth::getInstance()->clearIdentity();
				}
				$messages = $authResult->getMessages();
				$response['error'] = $messages[0];
				$response['success'] = false;
			}
		}
		else 
		{
			$response['failure'] = true;
			$messages = $authResult->getMessages();
			$response['error'] = $messages[0] ;
		}
		
		echo Zend_Json::encode($response);
	}
	function logoutAction()
	{
		$this->_helper->getHelper('Cache')->removePagesTagged(array('entries','hold','warta','clinic'));
		
		Pandamp_Lib_Formater::updateUserLog();
		
		Zend_Auth::getInstance()->clearIdentity();
		
		$returnUrl = $this->_getParam('returnTo');
		if(!empty($returnUrl))
		{
			$returnUrl = base64_decode($returnUrl);
		}
		else
		{
			$returnUrl = ROOT_URL;
		}
		
		$this->_redirect($returnUrl); 
	}
	function feedbackAction()		
	{
		$this->_helper->layout->setLayout('layout-hukumonlineid-ext');
		
	}
	function sendFeedbackAction()
	{
		$this->_helper->layout->disableLayout();
		
		$request = $this->getRequest();

		$validator = new Zend_Validate_EmailAddress();
		
		if ($request->getParam('email') == '') {
			$error[] = '- Email harus diisi';
		}
		if (!$validator->isValid($request->getParam('email'))) {
			$error[] = '- Penulisan email salah!';
		}
		if ($request->getParam('feedback') == '') {
			$error[] = '- Masukkan anda?';
		}
		 
		if (isset($error)) {
			
			echo '<b>Error</b>: <br />'.implode('<br />', $error);
			
		} else {
		
		$formater = new Pandamp_Core_Hol_User();
		
		$email = $this->_getParam('email');
		$feedback = $this->_getParam('feedback');
		
		// send Feedback
		$formater->sendFeedback($email,$feedback);
		}
	}
	function aturanPakaiAction()
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchRow("shortTitle='aturan-pakai' AND profileGuid='kutu_signup'");
		$rowsetCatalogAttribute = $rowset->findDependentRowsetCatalogAttribute();
		
		$this->view->title = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle')->value;
		$this->view->content = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent')->value;
	}
	function paketAction()
	{
		$shortTitle = ($this->_getParam('title'))? $this->_getParam('title') : '';
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchRow("shortTitle='".$shortTitle."'");
		$rowsetCatalogAttribute = $rowset->findDependentRowsetCatalogAttribute();
		
		$this->view->title = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle')->value;
		$this->view->content = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent')->value;
	}
	function personalSettingAction()
	{
		$this->_helper->layout->setLayout('layout-hukumonlineid-ps');
		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity())
		{
			$this->_forward('restricted','error','identity',array('type' => 'identity','num' => 101));			
		}
	}
	function editprofileAction()
	{
		$this->_helper->layout->setLayout('layout-hukumonlineid-ps');
		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity())
		{
			$this->_forward('restricted','error','identity',array('type' => 'identity','num' => 101));			
		}
		else
		{
			$guid = $auth->getIdentity()->guid;
			if (isset($guid)) {
				$tblUser = new Pandamp_Modules_Identity_User_Model_User();
				$rowset = $tblUser->find($guid)->current();
				/*
				if ($rowset->packageId == 27)
				{
					$this->_forward('member_corporate_edit','account');
				} 
				elseif ($rowset->packageId == 26)
				{
					$this->_forward('member_individual_edit','account');
				}
				else 
				{
				*/
					$this->_forward('member_edit','account','identity',array('guid'=>$guid));
				//}
			}
		}
	}
	function membereditAction()
	{
		$this->_helper->layout->setLayout('layout-hukumonlineid-ps');
		
		
		$g = $this->getRequest();
		$guid = $g->getParam('guid');
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowUser = $tblUser->find($guid)->current();
		$this->view->row = $rowUser;
		
		if ($g->isPost()) {
			
			$aData = $g->getParams();
			$aData['guid'] = $guid;
			
			try {
				$hol = new Pandamp_Core_Hol_User();
				$rowUser = $hol->editprofile($aData);
				
				$this->view->row = $rowUser;
				$this->view->message = "Data has been successfully saved.";
			}
			catch (Zend_Exception $e)
			{
				$this->view->message = $e->getMessage();
			}
		}
	}
	function changeusernameAction()
	{
		$this->_helper->layout->setLayout('layout-hukumonlineid-ps');
		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity())
		{
			$this->_forward('restricted','error','identity',array('type' => 'identity','num' => 101));			
		}
		else
		{
			$guid = $auth->getIdentity()->guid;
			$tblUser = new Pandamp_Modules_Identity_User_Model_User();
			$rowUser = $tblUser->find($guid)->current();
			$this->view->row = $rowUser;
			
			$g = $this->getRequest();
			
			if ($g->isPost()) {
				
				$aData = $g->getParams();
				$aData['guid'] = $guid;
				
				try {
					$hol = new Pandamp_Core_Hol_User();
					$rowUser = $hol->editprofile($aData);
					
					$this->view->row = $rowUser;
					$this->view->message = "Data has been successfully saved.";
				}
				catch (Zend_Exception $e)
				{
					$this->view->message = $e->getMessage();
				}
			}
			
		}
	}
	function changeemailAction()
	{
		$this->_helper->layout->setLayout('layout-hukumonlineid-ps');
		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity())
		{
			$this->_forward('restricted','error','identity',array('type' => 'identity','num' => 101));			
		}
		else
		{
			$guid = $auth->getIdentity()->guid;
			$tblUser = new Pandamp_Modules_Identity_User_Model_User();
			$rowUser = $tblUser->find($guid)->current();
			$this->view->row = $rowUser;
			
			$g = $this->getRequest();
			
			if ($g->isPost()) {
				
				$aData = $g->getParams();
				$aData['guid'] = $guid;
				
				try {
					$hol = new Pandamp_Core_Hol_User();
					$rowUser = $hol->editprofile($aData);
					
					$this->view->row = $rowUser;
					$this->view->message = "Data has been successfully saved.";
				}
				catch (Zend_Exception $e)
				{
					$this->view->message = $e->getMessage();
				}
			}
		}
	}
	function changePasswordAction()
	{
		$this->_helper->layout->setLayout('layout-hukumonlineid-ps');
		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity())
		{
			$this->_forward('restricted','error','identity',array('type' => 'identity','num' => 101));			
		}
		else
		{
			$guid = $auth->getIdentity()->guid;
			$tblUser = new Pandamp_Modules_Identity_User_Model_User();
			$rowUser = $tblUser->find($guid)->current();
			$this->view->row = $rowUser;
			
			$g = $this->getRequest();
			
			if ($g->isPost()) {
				
				$aData = $g->getParams();
				
				$hol = new Pandamp_Core_Hol_User();
				
				if($hol->changePassword($guid, $g->getParam('opasswd'), $g->getParam('newpasswd')))
				{
					$this->view->message = "Password was sucessfully changed.";
				}
				else
				{
					$this->view->message = "Old password was wrong. Please retry with correct password.";
				}
			}
			
		}
	}
	function signupAction()
	{
		$this->_helper->layout->setLayout('layout-hukumonlineid-daftar');
		
		$r = $this->getRequest();
		
		if ($r->isPost()) {
		
			$id				= ($r->getParam('id'))? $r->getParam('id') : '';
			$promotionCode	= ($r->getParam('promotionCode'))? $r->getParam('promotionCode') : '';
			$package		= ($r->getParam('paket'))? $r->getParam('paket') : '';
			$fullName		= ($r->getParam('fullName'))? $r->getParam('fullName') : '';
			$gender			= ($r->getParam('chkGender'))? $r->getParam('chkGender') : '';
			$month			= ($r->getParam('month'))? $r->getParam('month') : '';
			$day			= ($r->getParam('day'))? $r->getParam('day') : '';
			$year			= ($r->getParam('year'))? $r->getParam('year') : '';
			$education		= ($r->getParam('education'))? $r->getParam('education') : '';
			$expense		= ($r->getParam('expense'))? $r->getParam('expense') : '';
			$company		= ($r->getParam('company'))? $r->getParam('company') : '';
			$businessType	= ($r->getParam('businessType'))? $r->getParam('businessType') : '';
			$phone			= ($r->getParam('phone'))? $r->getParam('phone') : '';
			$fax			= ($r->getParam('fax'))? $r->getParam('fax') : '';
			$payment		= ($r->getParam('payment'))? $r->getParam('payment') : '';
			$email			= ($r->getParam('email'))? $r->getParam('email') : '';
			$newArtikel		= ($r->getParam('newArtikel'))? $r->getParam('newArtikel') : '';
			$newRegulation	= ($r->getParam('newRegulation'))? $r->getParam('newRegulation') : '';
			$newWRegulation	= ($r->getParam('newWeeklyRegulation'))? $r->getParam('newWeeklyRegulation') : '';
			$iscontact 		= ($r->getParam('iscontact'))? $r->getParam('iscontact') : '';
		
			$obj	 	= new Pandamp_Crypt_Password();
			$formater	= new Pandamp_Core_Hol_User();
			$aclMan 	= Pandamp_Acl::manager();
			
			try {
				
				for ($x=1; $x <= $id; $x++) {
					$username = ($r->getParam('username'.$x))? $r->getParam('username'.$x) : '';
					$password = ($r->getParam('password'.$x))? $r->getParam('password'.$x) : '';
					
					$tblUser = new Pandamp_Modules_Identity_User_Model_User();
					Zend_Db_Table::getDefaultAdapter()->beginTransaction();
					$rowUser = $tblUser->fetchNew();
					
					$rowUser->username			= $username;
					$rowUser->password			= $obj->encryptPassword($password);
					$rowUser->fullName			= $fullName;
					$rowUser->gender			= ($gender == 1)? 'L' : 'P';
					$rowUser->birthday			= $year.'-'.$month.'-'.$day;
					$rowUser->indexCol			= $x;
					$rowUser->phone				= $phone;
					$rowUser->fax				= $fax;
					$rowUser->email				= $email;
					$rowUser->company			= $company;
					$rowUser->newArticle		= ($newArtikel == 1)? 'Y' : 'N';
					$rowUser->weeklyList		= ($newWRegulation == "1")? 'Y' : 'N';
					$rowUser->monthlyList		= ($newRegulation == 1)? 'Y' : 'N';
					$rowUser->isContact			= ($iscontact == $x)? 'Y' : 'N';
					$rowUser->packageId			= $package;
					$rowUser->promotionId		= $promotionCode;
					$rowUser->educationId		= $education;
					$rowUser->expenseId			= $expense;
					$rowUser->paymentId			= $payment;
					$rowUser->businessTypeId	= $businessType;
					
					$tblNumber = new Pandamp_Modules_Misc_Number_Model_GenerateNumber();
					$rowset = $tblNumber->fetchRow();
					$num = $rowset->user;
					$totdigit = 5;
					$num = strval($num);
					$jumdigit = strlen($num);
					$noinvoice = str_repeat("0",$totdigit-$jumdigit).$num;
					$rowset->user = $rowset->user += 1;
					$tblNumber->update(array('user'=>$rowset->user));
					
					$rowUser->kopel = $noinvoice;
					
					$rowUser->save();
					Zend_Db_Table::getDefaultAdapter()->commit();
					
					$aclMan->addUser($username,'member_gratis');
				}
				
				switch ($package)
				{
					case 25:
							$mailcontent = $formater->getMailContent('konfirmasi email gratis');
							$m = $formater->_writeConfirmFreeEmail($mailcontent,$fullName,$r->getParam('username1'),$r->getParam('password1'),base64_encode(Pandamp_Lib_Formater::get_user_id($r->getParam('username1'))),$email,'gratis');
						break;
					case 26:
							$disc = $formater->checkPromoValidation('Disc',$aclMan->getGroupIds('member_individual'),$promotionCode,$payment);
							$total = $formater->checkPromoValidation('Total',$aclMan->getGroupIds('member_individual'),$promotionCode,$payment);
							$mailcontent = $formater->getMailContent('konfirmasi-email-individual');
							$m = $formater->_writeConfirmIndividualEmail($mailcontent,$fullName,$r->getParam('username1'),$r->getParam('password1'),$payment,$disc,$total,base64_encode(Pandamp_Lib_Formater::get_user_id($r->getParam('username1'))),$email);
						break;
					case 27:
							$disc = $formater->checkPromoValidation('Disc',$aclMan->getGroupIds('member_corporate'),$promotionCode,$payment);
							$total = $formater->checkPromoValidation('Total',$aclMan->getGroupIds('member_corporate'),$promotionCode,$payment);
							$mailcontent = $formater->getMailContent('konfirmasi-email-korporasi');
							$m = $formater->_writeConfirmCorporateEmail($mailcontent,$fullName,$company,$payment,$disc,$total,$r->getParam('username1'),base64_encode(Pandamp_Lib_Formater::get_user_id($r->getParam('username1'))),$email);
						break;
				}
				
				
				$this->view->message = $m;
			}
			catch (Zend_Exception $e)
			{
				Zend_Db_Table::getDefaultAdapter()->rollBack();
				$this->view->message = $e->getMessage();
			}
		}
	}
	
	/*
	function processAction()
	{
		
		$r = $this->getRequest();
		
			$id				= ($r->getParam('id'))? $r->getParam('id') : '';
			$promotionCode	= ($r->getParam('promotionCode'))? $r->getParam('promotionCode') : '';
			$package		= ($r->getParam('paket'))? $r->getParam('paket') : '';
			$fullName		= ($r->getParam('fullName'))? $r->getParam('fullName') : '';
			$gender			= ($r->getParam('chkGender'))? $r->getParam('chkGender') : '';
			$month			= ($r->getParam('month'))? $r->getParam('month') : '';
			$day			= ($r->getParam('day'))? $r->getParam('day') : '';
			$year			= ($r->getParam('year'))? $r->getParam('year') : '';
			$education		= ($r->getParam('education'))? $r->getParam('education') : '';
			$expense		= ($r->getParam('expense'))? $r->getParam('expense') : '';
			$company		= ($r->getParam('company'))? $r->getParam('company') : '';
			$businessType	= ($r->getParam('businessType'))? $r->getParam('businessType') : '';
			$phone			= ($r->getParam('phone'))? $r->getParam('phone') : '';
			$fax			= ($r->getParam('fax'))? $r->getParam('fax') : '';
			$payment		= ($r->getParam('payment'))? $r->getParam('payment') : '';
			$email			= ($r->getParam('email'))? $r->getParam('email') : '';
			$newArtikel		= ($r->getParam('newArtikel'))? $r->getParam('newArtikel') : '';
			$newRegulation	= ($r->getParam('newRegulation'))? $r->getParam('newRegulation') : '';
			$newWRegulation	= ($r->getParam('newWeeklyRegulation'))? $r->getParam('newWeeklyRegulation') : '';
			$iscontact 		= ($r->getParam('iscontact'))? $r->getParam('iscontact') : '';
		
			$obj	 	= new Pandamp_Crypt_Password();
			$formater	= new Pandamp_Core_Hol_User();
			$aclMan 	= Pandamp_Acl::manager();
			
			try {
				
				for ($x=1; $x <= $id; $x++) {
					$username = ($r->getParam('username'.$x))? $r->getParam('username'.$x) : '';
					$password = ($r->getParam('password'.$x))? $r->getParam('password'.$x) : '';
					
					$tblUser = new Pandamp_Modules_Identity_User_Model_User();
					Zend_Db_Table::getDefaultAdapter()->beginTransaction();
					$rowUser = $tblUser->fetchNew();
					
					$rowUser->username			= $username;
					$rowUser->password			= $obj->encryptPassword($password);
					$rowUser->fullName			= $fullName;
					$rowUser->gender			= ($gender == 1)? 'L' : 'P';
					$rowUser->birthday			= $year.'-'.$month.'-'.$day;
					$rowUser->indexCol			= $x;
					$rowUser->phone				= $phone;
					$rowUser->fax				= $fax;
					$rowUser->email				= $email;
					$rowUser->company			= $company;
					$rowUser->newArticle		= ($newArtikel == 1)? 'Y' : 'N';
					$rowUser->weeklyList		= ($newWRegulation == "1")? 'Y' : 'N';
					$rowUser->monthlyList		= ($newRegulation == 1)? 'Y' : 'N';
					$rowUser->isContact			= ($iscontact == $x)? 'Y' : 'N';
					$rowUser->packageId			= $package;
					$rowUser->promotionId		= $promotionCode;
					$rowUser->educationId		= $education;
					$rowUser->expenseId			= $expense;
					$rowUser->paymentId			= $payment;
					$rowUser->businessTypeId	= $businessType;
					
					$tblNumber = new Pandamp_Modules_Misc_Number_Model_GenerateNumber();
					$rowset = $tblNumber->fetchRow();
					$num = $rowset->num;
					$totdigit = 5;
					$num = strval($num);
					$jumdigit = strlen($num);
					$noinvoice = str_repeat("0",$totdigit-$jumdigit).$num;
					$rowset->num = $rowset->num += 1;
					$tblNumber->update(array('num'=>$rowset->num));
					
					$rowUser->kopel = $noinvoice;
					
					$rowUser->save();
					Zend_Db_Table::getDefaultAdapter()->commit();
					
					$aclMan->addUser($username,'member_gratis');
				}
				
				switch ($package)
				{
					case 25:
							$mailcontent = $formater->getMailContent('konfirmasi email gratis');
							$m = $formater->_writeConfirmFreeEmail($mailcontent,$fullName,$r->getParam('username1'),$r->getParam('password1'),base64_encode(Pandamp_Lib_Formater::get_user_id($r->getParam('username1'))),$email,'gratis');
						break;
					case 26:
							$disc = $formater->checkPromoValidation('Disc',$aclMan->getGroupIds('member_individual'),$promotionCode,$payment);
							$total = $formater->checkPromoValidation('Total',$aclMan->getGroupIds('member_individual'),$promotionCode,$payment);
							$mailcontent = $formater->getMailContent('konfirmasi-email-individual');
							$m = $formater->_writeConfirmIndividualEmail($mailcontent,$fullName,$r->getParam('username1'),$r->getParam('password1'),$payment,$disc,$total,base64_encode(Pandamp_Lib_Formater::get_user_id($r->getParam('username1'))),$email);
						break;
					case 27:
							$disc = $formater->checkPromoValidation('Disc',$aclMan->getGroupIds('member_corporate'),$promotionCode,$payment);
							$total = $formater->checkPromoValidation('Total',$aclMan->getGroupIds('member_corporate'),$promotionCode,$payment);
							$mailcontent = $formater->getMailContent('konfirmasi-email-korporasi');
							$m = $formater->_writeConfirmCorporateEmail($mailcontent,$fullName,$company,$payment,$disc,$total,$r->getParam('username1'),base64_encode(Pandamp_Lib_Formater::get_user_id($r->getParam('username1'))),$email);
						break;
				}
				
				
				$this->view->rowUser = $rowUser;
			}
			catch (Zend_Exception $e)
			{
				Zend_Db_Table::getDefaultAdapter()->rollBack();
				print_r($e->getMessage());
			}
	}
	*/
	
	function pictureAction()
	{
		$this->_helper->layout->setLayout('layout-hukumonlineid-ps');
		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity())
		{
			$this->_forward('restricted','error','identity',array('type' => 'identity','num' => 101));			
		}
		else
		{
			$guid = $auth->getIdentity()->guid;
			$tblUser = new Pandamp_Modules_Identity_User_Model_User();
			$rowUser = $tblUser->find($guid)->current();
			$this->view->row = $rowUser;
			
			$g = $this->getRequest();
			
			if ($g->isPost()) {
				
				$aData = $g->getParams();
				
					$arraypictureformat = array("jpg", "jpeg", "gif");
					$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'photo';
						
					if ($g->getParam('txt_erase') == 'on') {
						foreach ($arraypictureformat as $key => $val) {
							if (is_file($sDir."/".$guid.".".$val)) {
								unlink($sDir."/".$guid.".".$val);
								break;
							}
						}
					}
					
					$registry = Zend_Registry::getInstance();
					$files = $registry->get('files');
						
					if (isset($files['file_picture']))
					{
						$file = $files['file_picture'];
					}
					
					if ($files['file_picture']['error'] == 0 && $files['file_picture']['size'] > 0) {
						$file = $files['file_picture']['name'];
						$ext = explode(".",$file);
						$ext = strtolower(array_pop($ext));
						if (in_array($ext,$arraypictureformat)) {
							$image_size = getimagesize($files['file_picture']['tmp_name']);
							
							if ($image_size[0] > 200 || $image_size[1] > 250)
							{
								$this->view->message = 'Ukuran gambar melebihi batas maksimal. Proses pengunggahan batal!';
								
							}
							else 
							{
								foreach ($arraypictureformat as $key => $val)
								{
									if (is_file($sDir."/".$guid.".".$val)) {
										unlink($sDir."/".$guid.".".$val);
										break;
									}
								}
								
								if (is_uploaded_file($files['file_picture']['tmp_name'])) {
									@move_uploaded_file($files['file_picture']['tmp_name'], $sDir."/".$guid.".".$ext);
									@chmod($files['file_picture']['tmp_name'], $sDir."/".$guid.".".$ext, 0755);
								}
								
								$this->view->message = "Data has been successfully saved.";
							}
						}
						
						
					}
			}
			
		}
	}
	
	function redirectUrlAction()
	{
		$this->_helper->layout()->disableLayout();
	}
	
	function checkusernameAction()
	{
		$id = $this->_getParam('id');
		$username = $this->_getParam('username'.$id);
		$tbluser = new Pandamp_Modules_Identity_User_Model_User();
		$where = $tbluser->getAdapter()->quoteInto("username=?",$username);
		$rowset = $tbluser->fetchRow($where);
		if(count($rowset)>0)
			echo "0";
		else
			echo "1";
		die();
	}
	function checkemailAction()
	{
		$email = $this->_getParam('email');
		$tbluser = new Pandamp_Modules_Identity_User_Model_User();
		$where = $tbluser->getAdapter()->quoteInto("email=?",$email);
		$rowset = $tbluser->fetchRow($where);
		if ($rowset) 
			echo 'false';
		else 
			echo 'true';
		
		die();	
	}
	function getMeUsernameAction()
	{
		$this->_helper->layout()->disableLayout();
		$request = $this->getRequest();
		$uname = ($request->getParam('username'))? $request->getParam('username') : '';
		
		$response = array();
		
		if ($uname == "undefined") {
			
			$response['error'] = '2';
			$response['err2'] = 'Username is Empty';
			
		} elseif (strlen($uname) < 6) {
			
			$response['error'] = '1';
			$response['err1'] = 'Sorry, your username must be between 6 and 30<br>characters long.';
			
		} else {	
			
			$tableUser = new Pandamp_Modules_Identity_User_Model_User();
			$rowUser = $tableUser->fetchRow("username='".$uname."'");
	
			if (!empty($rowUser->username)) {
				
				$response['error'] = '3';
				$response['err3'] = '<i><b>'.$uname.'</b></i> is not available';
				
			} else {
				
				$response['success'] = 'true';
				$response['data'] = '<i><b>'.$uname.'</b></i> is available';
				
			}		
		}
		
		echo Zend_Json::encode($response);
		
	}
	function getMeEmailAction()
	{
		$this->_helper->layout()->disableLayout();
		$request = $this->getRequest();
		$email = ($request->getParam('email'))? $request->getParam('email') : '';
		$response = array();
		if ($email == "undefined") {
			$response['failure'] = true;
			$response['message'] = 'Email is Empty';
		} else {	
			$tableUser = new Pandamp_Modules_Identity_User_Model_User();
			$rowUser = $tableUser->fetchRow("email='".$email."'");
			if (!empty($rowUser->email)) {
				$response['failure'] = true;
				$response['message'] = '<i><b>'.$email.'</b></i> is not available';
			} else {
				$response['success'] = true;
				$response['message'] = '<i><b>'.$email.'</b></i> is available';
			}		
		}
		echo Zend_Json::encode($response);
	}
	function preProcessSession()
	{
		$zendAuth = Zend_Auth::getInstance();
		if($zendAuth->hasIdentity())
		{
			$r = $this->getRequest();
			$returnUrl = base64_decode($r->getParam('returnTo'));
			
			if(!empty($returnUrl))
			{
				if(strpos($returnUrl,'?'))
					$sAddition = '&';
				else 
					$sAddition = '?';
					header("location: $returnUrl".$sAddition."PHPSESSID=".Zend_Session::getId());
			}
			else 
			{
				echo "AccountController:PreProcessSession => Anda sudah login kok";
			}
		}
		else 
		{
			Zend_Session::rememberMe(86000);
			Zend_Session::regenerateId();
		}
	}
}

?>