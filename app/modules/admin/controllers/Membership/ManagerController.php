<?php

class Admin_Membership_ManagerController extends Zend_Controller_Action 
{
	const CONTEXT_JSON = 'json';
	
    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
    	$contextSwitch = $this->_helper->contextSwitch();
    	
        $contextSwitch->addActionContext('process',  self::CONTEXT_JSON)
        			  ->addActionContext('upload', self::CONTEXT_JSON)
        			  ->addActionContext('delete-user',  self::CONTEXT_JSON)
        			  ->addActionContext('delete-user-detail',  self::CONTEXT_JSON)
        			  ->addActionContext('delete-log-user',  self::CONTEXT_JSON)
        			  ->addActionContext('delete-invoice',  self::CONTEXT_JSON)
        			  ->addActionContext('get-me-username',  self::CONTEXT_JSON)
        			  ->addActionContext('get-me-email',  self::CONTEXT_JSON)
        			  ->addActionContext('update-users',  self::CONTEXT_JSON)
        			  ->addActionContext('set-active',  self::CONTEXT_JSON)
        			  ->addActionContext('confirm',  self::CONTEXT_JSON)
        			  ->addActionContext('renew-invoice',  self::CONTEXT_JSON)
                      ->initContext();
    }
    
	function signupAction(){}
	
	function editAction(){
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		if (!empty($guid))
		{
			$tblUser = new Pandamp_Modules_Identity_User_Model_User();
			$rowset = $tblUser->find($guid)->current();
			if ($rowset)
			{
				$this->view->rowset = $rowset;
			}
		}
	}
	
	public function processAction()
	{
		$uid			= ($this->_getParam('id'))? $this->_getParam('id') : '';
		$ec				= ($this->_getParam('ec'))? $this->_getParam('ec') : '';
		$promotionCode	= ($this->_getParam('promotionCode'))? $this->_getParam('promotionCode') : '';
		$fullName		= ($this->_getParam('fullName'))? $this->_getParam('fullName') : '';
		$gender			= ($this->_getParam('chkGender'))? $this->_getParam('chkGender') : '';
		$month			= ($this->_getParam('month'))? $this->_getParam('month') : '';
		$day			= ($this->_getParam('day'))? $this->_getParam('day') : '';
		$year			= ($this->_getParam('year'))? $this->_getParam('year') : '';
		$education		= ($this->_getParam('education'))? $this->_getParam('education') : '';
		$expense		= ($this->_getParam('expense'))? $this->_getParam('expense') : '';
		$company		= ($this->_getParam('company'))? $this->_getParam('company') : '';
		$businessType	= ($this->_getParam('businessType'))? $this->_getParam('businessType') : '';
		$billing		= ($this->_getParam('billing'))? $this->_getParam('billing') : '';
		$phone			= ($this->_getParam('phone'))? $this->_getParam('phone') : '';
		$fax			= ($this->_getParam('fax'))? $this->_getParam('fax') : '';
		$payment		= ($this->_getParam('payment'))? $this->_getParam('payment') : '';
		$email			= ($this->_getParam('email'))? $this->_getParam('email') : '';		
		$newArtikel		= ($this->_getParam('newArtikel'))? $this->_getParam('newArtikel') : '';
		$newRegulation	= ($this->_getParam('newRegulation'))? $this->_getParam('newRegulation') : '';
		$newWRegulation	= ($this->_getParam('newWeeklyRegulation'))? $this->_getParam('newWeeklyRegulation') : '';
		$iscontact 		= ($this->_getParam('iscontact'))? $this->_getParam('iscontact') : '';
		$aro_groups		= ($this->_getParam('aro_groups'))? $this->_getParam('aro_groups') : '';		
		
		$formater 	= new Pandamp_Core_Hol_User();
		$obj	 	= new Pandamp_Crypt_Password();
		$aclMan 	= Pandamp_Acl::manager();
		
						   					// marketing					// bonus			// corporate		    // complete         // ilb               // ild
		if ($aro_groups == 27 || $aro_groups == 39 || $aro_groups == 37 || $aro_groups == 28 || $aro_groups == 29 || $aro_groups == 30)
		{
			// untuk member seperti corporate, complete, ilb, dan ild
			// field fullName, birthday, gender, education, expense
			// tidak disimpan
			
			$id = 1 + ($uid - 1);
			
			for ($x=1; $x < $id; $x++) {
				
				$guid 		= ($this->_getParam('guid'.$x))? $this->_getParam('guid'.$x) : '';
				$username 	= ($this->_getParam('username'.$x))? $this->_getParam('username'.$x) : '';
				$password 	= ($this->_getParam('password'.$x))? $this->_getParam('password'.$x) : '';
				
				$tblUser = new Pandamp_Modules_Identity_User_Model_User();
				$rowUser = $tblUser->find($guid)->current();
				
				if (!empty($rowUser))
				{
					// $rowUser->username			= $username;
					$rowUser->password			= $obj->encryptPassword($password);
					// $rowUser->fullName			= $fullName;
					// $rowUser->birthday			= $year.'-'.$month.'-'.$day;
					$rowUser->indexCol			= $x;
					$rowUser->billingAddress	= $billing;
					$rowUser->phone				= $phone;
					$rowUser->fax				= $fax;
					// $rowUser->gender			= ($gender == 1)? 'L' : 'P';
					$rowUser->email				= $email;
					$rowUser->company			= $company;
					$rowUser->newArticle		= ($newArtikel == 1)? 'Y' : 'N';
					$rowUser->weeklyList		= ($newWRegulation == "1")? 'Y' : 'N';
					$rowUser->monthlyList		= ($newRegulation == 1)? 'Y' : 'N';
					$rowUser->isContact			= ($iscontact == $x)? 'Y' : 'N';
					$rowUser->packageId			= $aro_groups;
					$rowUser->promotionId		= $promotionCode;
					// $rowUser->educationId		= $education;
					// $rowUser->expenseId			= $expense;
					$rowUser->paymentId			= $payment;
					$rowUser->businessTypeId	= $businessType;
					
				}
				else 
				{
					$rowUser = $tblUser->fetchNew();
					
					$rowUser->username			= $username;
					$rowUser->password			= $obj->encryptPassword($password);
					// $rowUser->fullName			= $fullName;
					// $rowUser->birthday			= $year.'-'.$month.'-'.$day;
					$rowUser->indexCol			= $x;
					$rowUser->billingAddress	= $billing;
					$rowUser->phone				= $phone;
					$rowUser->fax				= $fax;
					// $rowUser->gender			= ($gender == 1)? 'L' : 'P';
					$rowUser->email				= $email;
					$rowUser->company			= $company;
					$rowUser->newArticle		= ($newArtikel == 1)? 'Y' : 'N';
					$rowUser->weeklyList		= ($newWRegulation == "1")? 'Y' : 'N';
					$rowUser->monthlyList		= ($newRegulation == 1)? 'Y' : 'N';
					$rowUser->isContact			= ($iscontact == $x)? 'Y' : 'N';
					$rowUser->packageId			= $aro_groups;
					$rowUser->promotionId		= $promotionCode;
					// $rowUser->educationId		= $education;
					// $rowUser->expenseId			= $expense;
					$rowUser->paymentId			= $payment;
					$rowUser->businessTypeId	= $businessType;
				}
				
				$rowUser->save();
			}
			
			// check!!if u want to send email confirmation?
			if ($ec == 1) {
				// check disc promo
				$disc = $formater->checkPromoValidation('Disc',$aro_groups,$promotionCode,$payment);
				// check total promo
				$total = $formater->checkPromoValidation('Total',$aro_groups,$promotionCode,$payment);
				// get mail content
				$mailcontent = $formater->getMailContent('konfirmasi-email-korporasi');
				// write confirm corporate email
				$formater->_writeConfirmCorporateEmail($mailcontent,$company,$payment,$disc,$total,$this->_getParam('username1'),base64_encode(Pandamp_Lib_Formater::get_user_id($this->_getParam('username1'))),$email);
			}
			else 
			{
				// activate user account
				$aReturn = $aclMan->getGroupData($aro_groups);
				$aclMan->addUser($this->_getParam('username1'),$aReturn[2]);
			}
		}
		else 
		{
			$guid 			= ($this->_getParam('guid1'))? $this->_getParam('guid1') : '';
			$username		= ($this->_getParam('username1'))? $this->_getParam('username1') : '';
			$password		= ($this->_getParam('password1'))? $this->_getParam('password1') : '';
			
			$tblUser = new Pandamp_Modules_Identity_User_Model_User();
			$rowUser = $tblUser->find($guid)->current();
			
			if (!empty($rowUser))
			{
				// $rowUser->username			= $username;
				$rowUser->password			= $obj->encryptPassword($password);
				$rowUser->fullName			= $fullName;
				$rowUser->birthday			= $year.'-'.$month.'-'.$day;
				$rowUser->phone				= $phone;
				$rowUser->fax				= $fax;
				$rowUser->gender			= ($gender == 1)? 'L' : 'P';
				$rowUser->email				= $email;
				$rowUser->company			= $company;
				$rowUser->newArticle		= ($newArtikel == 1)? 'Y' : 'N';
				$rowUser->weeklyList		= ($newWRegulation == "1")? 'Y' : 'N';
				$rowUser->monthlyList		= ($newRegulation == 1)? 'Y' : 'N';
				$rowUser->packageId			= $aro_groups;
				$rowUser->promotionId		= $promotionCode;
				$rowUser->educationId		= $education;
				$rowUser->expenseId			= $expense;
				$rowUser->paymentId			= $payment;
				$rowUser->businessTypeId	= $businessType;
			}
			else 
			{
				$rowUser = $tblUser->fetchNew();
				
				$rowUser->username			= $username;
				$rowUser->password			= $obj->encryptPassword($password);
				$rowUser->fullName			= $fullName;
				$rowUser->birthday			= $year.'-'.$month.'-'.$day;
				$rowUser->phone				= $phone;
				$rowUser->fax				= $fax;
				$rowUser->gender			= ($gender == 1)? 'L' : 'P';
				$rowUser->email				= $email;
				$rowUser->company			= $company;
				$rowUser->newArticle		= ($newArtikel == 1)? 'Y' : 'N';
				$rowUser->weeklyList		= ($newWRegulation == "1")? 'Y' : 'N';
				$rowUser->monthlyList		= ($newRegulation == 1)? 'Y' : 'N';
				$rowUser->packageId			= $aro_groups;
				$rowUser->promotionId		= $promotionCode;
				$rowUser->educationId		= $education;
				$rowUser->expenseId			= $expense;
				$rowUser->paymentId			= $payment;
				$rowUser->businessTypeId	= $businessType;
			}
			
			$guid = $rowUser->save();
			
			if ($ec == 1) // email confirm=yes
			{						// individual
				if ($aro_groups == 26)
				{
					// check disc promo
					$disc = $formater->checkPromoValidation('Disc',$aro_groups,$promotionCode,$payment);
					// check total promo
					$total = $formater->checkPromoValidation('Total',$aro_groups,$promotionCode,$payment);
					// get mail content
					$mailcontent = $formater->getMailContent('konfirmasi-email-individual');
					// write confirm individual email
					$formater->_writeConfirmIndividualEmail($mailcontent,$fullName,$username,$password,$payment,$disc,$total,base64_encode(Pandamp_Lib_Formater::get_user_id($username)),$email);
				}
				else 
				{
					// get mail content
					$mailcontent = $formater->getMailContent('konfirmasi email gratis');
					// write confirm free email
					$aReturn = $aclMan->getGroupData($aro_groups);
					$formater->_writeConfirmFreeEmail($mailcontent,$fullName,$username,$password,base64_encode(Pandamp_Lib_Formater::get_user_id($username)),$email,$aReturn[2]);
				}
			}
			else 
			{
				// activate user account
				$aReturn = $aclMan->getGroupData($aro_groups);
				$aclMan->addUser($username,$aReturn[2]);
			}
			
		}
		
		$this->view->success = true;
		
	}
	function addUploadAction()
	{
		
	}
	function uploadAction()
	{
		
	}
	function getMeUsernameAction()
	{
		$uname = ($this->_getParam('username'))? $this->_getParam('username') : '';
		
		if ($uname == "undefined") {
			
			$this->view->error = '2';
			$this->view->err2 = 'Username is Empty';
			
		} elseif (strlen($uname) < 6) {
			
			$this->view->error = '1';
			$this->view->err1 = 'Sorry, your username must be between 6 and 30<br>characters long.';
			
		} else {	
			
			$tableUser = new Pandamp_Modules_Identity_User_Model_User();
			$rowUser = $tableUser->fetchRow("username='".$uname."'");
	
			if (!empty($rowUser->username)) {
				
				$this->view->error = '3';
				$this->view->err3 = '<i><b>'.$uname.'</b></i> is not available';
				
			} else {
				
				$this->view->success = true;
				$this->view->data = '<i><b>'.$uname.'</b></i> is available';
				
			}		
		}
		
		
	}
	
    // function userAction(){}
    
    function freeAction() {}
    
    function corporateAction() {}
    
    function individualAction() {}
    
    function othersAction() {}
    
    function bonusAction() {}
    
    function reportAction() {}
    
    function invoiceAction() 
    {
    	$this->view->uid = ($this->_getParam('uid'))? $this->_getParam('uid') : '';
    }
    
	function editUsersAction()
	{
		$uid = ($this->_getParam('uid'))? $this->_getParam('uid') : '';
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$this->view->uid = $uid;
		$this->view->guid = $guid;
	}
	
	function viewUserAction()
	{
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowset = $tblUser->find($guid)->current();
		
		if ($rowset) {
			// $current_group = $tblUser->fetchUserGroupHistory($rowset->guid);
			$this->view->rowset = $rowset;
			// $this->view->currgroup = $current_group;
		}
	}
	
	function historyAction()
	{
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$this->view->guid = $guid;
	}
	
	/**	
	 * updateUsers is for administrator direct from GridPanel
	 * @param id, field, value
	 */
	function updateUsersAction()
	{
		$guid 	= ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$field 	= ($this->_getParam('field'))? $this->_getParam('field') : '';
		$value 	= ($this->_getParam('value'))? $this->_getParam('value') : '';
		
		$aclMan		= Pandamp_Acl::manager();
		
		$obj 		= new Pandamp_Crypt_Password();
		$guidMan 	= new Pandamp_Core_Guid();
		
		$tblUserDetail = new Pandamp_Modules_Identity_User_Model_UserDetail();
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowUser = $tblUser->find($guid)->current();
		
		$rowUserDetailNew = $tblUserDetail->fetchNew();
		$rowUserDetailNew->uid = $guid;
		$rowUserDetailNew->packageId = $rowUser->packageId;
		$rowUserDetailNew->promotionId = $rowUser->promotionId;
		$rowUserDetailNew->educationId = $rowUser->educationId;
		$rowUserDetailNew->expenseId = $rowUser->expenseId;
		$rowUserDetailNew->paymentId = $rowUser->paymentId;
		$rowUserDetailNew->businessTypeId = $rowUser->businessTypeId;
		$rowUserDetailNew->periodeId = $rowUser->periodeId;
		$rowUserDetailNew->activationDate = $rowUser->activationDate;
		$rowUserDetailNew->isEmailSent = $rowUser->isEmailSent;
		$rowUserDetailNew->isActive = $rowUser->isActive;
				
		switch ($field)
		{
			case 'packageId':
				
				// delete user group = trial from gacl
				$aclMan->deleteUser($rowUser->username);
				// add user to gacl
				$aReturn = $aclMan->getGroupData($value);
				$aclMan->addUser($rowUser->username,$aReturn[2]);
				
				$rowUser->packageId = $value;
				
				if ($value == 26 || $value == 27)
					$rowUser->periodeId = 2; // set to trial
					
					
				$result = $rowUser->save();
				
				if ($result)
				{
					$rowUserDetailNew->save();
					
					$this->view->success = true;
					$this->view->message = "[user:$rowUser->username] Added Packed Successfully";
				}
				
			break;
			
			case 'periodeId':
				
				if ($value == 1) // waiting
				{
					$rowUser->activationDate = '0000-00-00 00:00:00';
				}
				elseif ($value == 2) // trial
				{
					if ($rowUser->packageId == 26 || $rowUser->packageId == 27)
					{
						$rowUser->activationDate = date("Y-m-d H:i:s");
						// delete user group = trial from gacl
						$aclMan->deleteUser($rowUser->username);
						// add user to gacl
						$aclMan->addUser($rowUser->username,'member_gratis');
					}
					else 
					{
						$this->view->success = false;
						$this->view->message = "Wrong packaged!!";
					}
				}
				elseif ($value == 3) // active
				{
					$rowUser->activationDate = date("Y-m-d H:i:s");
					// delete user group = trial from gacl
					$aclMan->deleteUser($rowUser->username);
					// add user to gacl
					$aReturn = $aclMan->getGroupData($rowUser->packageId);
					$aclMan->addUser($rowUser->username,$aReturn[2]);
				}
				elseif ($value == 4) // downgrade
				{
					if ($rowUser->packageId == 26 || $rowUser->packageId == 27)
					{
						$rowUser->activationDate = date("Y-m-d H:i:s");
						// delete user group = trial from gacl
						$aclMan->deleteUser($rowUser->username);
						// add user to gacl
						$aclMan->addUser($rowUser->username,'member_gratis');
					}
					else 
					{
						$this->view->success = false;
						$this->view->message = "Wrong packaged!!";
					}
				}
				
				// TODO :: should followed by changing the package after user do upgrade
				
				// elseif ($value == 5) // upgrade
				// {
				//	$rowUserDetail->activationDate = date("Y-m-d H:i:s");
				// }
				
				$rowUser->periodeId = $value;
				$result = $rowUser->save();
				
				if ($result)
				{
					$rowUserDetailNew->save();
					
					$this->view->success = true;
					$this->view->message = "[user:$rowUser->username] Update Successfully";
				}
				
			break;
			
			case 'paymentId':
				
				$rowUser->paymentId = $value;
				$result = $rowUser->save();
				
				if ($result)
				{
					$rowUserDetailNew->save();
					
					$this->view->success = true;
					$this->view->message = "[user:$rowUser->username] Update Successfully";
				}
				
			break;
			
		}
		
	}

	/**	
	 * deleteUser
	 * @return JSON 
	 */
	function deleteUserAction()
	{
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowUser = $tblUser->find($guid)->current();
		
		$result = $rowUser->delete();				
		
		if ($result)
		{
			$this->view->success = true;
			$this->view->message = "[username:$rowUser->username] Delete successfully";
		} else {
			$this->view->success = false;
			$this->view->message = "[username:$rowUser->username] Delete failed";
		}
		
	}
	
	/**	
	 * deleteUser
	 * @return JSON 
	 */
	function deleteUserDetailAction()
	{
		$uid = ($this->_getParam('uid'))? $this->_getParam('uid') : '';
		$username = ($this->_getParam('username'))? $this->_getParam('username') : '';
		
		$tblUserDetail = new Pandamp_Modules_Identity_User_Model_UserDetail();
		$rowUserDetail = $tblUserDetail->find($uid)->current();
		
		$result = $rowUserDetail->delete();				
		
		if ($result)
		{
			$this->view->success = true;
			$this->view->message = "[username:$username] Delete successfully";
		} else {
			$this->view->success = false;
			$this->view->message = "[username:$username] Delete failed";
		}
		
	}
	
	/**	
	 * deleteLogUser
	 * @return JSON 
	 */
	function deleteLogUserAction()
	{
		$uid = ($this->_getParam('uid'))? $this->_getParam('uid') : '';
		
		$tblUserLog = new Pandamp_Modules_Identity_Log_Model_Log();
		$rowUser = $tblUserLog->find($uid)->current();
		
		$result = $rowUser->delete();				
		
		if ($result)
		{
			$this->view->success = true;
			$this->view->message = "Delete successfully";
		} else {
			$this->view->success = false;
			$this->view->message = "Delete failed"; 
		}
		
	}
	
	/**	
	 * deleteInvoice
	 * @return JSON 
	 */
	function deleteInvoiceAction()
	{
		$iid = ($this->_getParam('iid'))? $this->_getParam('iid') : '';
		
		$tblInvoice = new Pandamp_Modules_Payment_Invoice_Model_Invoice();
		$rowInvoice = $tblInvoice->find($iid)->current();
		
		$result = $rowInvoice->delete();				
		
		if ($result)
		{
			$this->view->success = true;
			$this->view->message = "Delete successfully";
		} else {
			$this->view->success = false;
			$this->view->message = "Delete failed";
		}
		
	}
	
	function renewInvoiceAction()
	{
		$uid = ($this->_getParam('uid'))? $this->_getParam('uid') : '';
		$iid = ($this->_getParam('iid'))? $this->_getParam('iid') : '';
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowUser = $tblUser->fetchRow("kopel=".$uid);
		
		$tblInvoice = new Pandamp_Modules_Payment_Invoice_Model_Invoice();
		$rowset = $tblInvoice->fetchRow("invoiceId=".$iid." AND isPaid='Y'");
		
		if ($rowset)
		{
			try {
				$rowInvoice = $tblInvoice->fetchNew();
				$rowInvoice->uid 				= $uid;
				$rowInvoice->price				= $rowset->price;
				$rowInvoice->discount			= $rowset->discount;
				$rowInvoice->invoiceOutDate 	= $rowset->expirationDate;
				$rowInvoice->invoiceConfirmDate	= date("Y-m-d");
				$rowInvoice->clientBankAccount	= $rowset->clientBankAccount;
				$rowInvoice->isPaid				= 'Y';
				// get expiration date
				$temptime = time();
				$temptime = Pandamp_Lib_Formater::DateAdd('m',$rowUser->paymentId,$temptime);
				$rowInvoice->expirationDate = strftime('%Y-%m-%d',$temptime);
				$rowInvoice->save();
				
				$this->view->success = true;
				$this->view->message = "Renewable Invoice:".$uid." successfully";
			} 
			catch (Exception $e)			
			{
				$this->view->success = false;
				$this->view->message = $e->getMessage();
			}
		}
		else 
		{
			$this->view->success = false;
			$this->view->message = "Invalid invoice";
		}
	}
	
	/**	
	 * TODO
	 * admin sentEmail
	 * @param id, uid
	 */
	function sentEmailAction()
	{
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$formater 	= new Pandamp_Core_Hol_User();
		$obj 		= new Pandamp_Crypt_Password();
		
		$aclMan 	= Pandamp_Acl::manager();
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowUser = $tblUser->find($guid)->current();
		
		if ($rowUser->packageId == 26)
		{
			// Get disc promo
			$disc = $formater->checkPromoValidation('Disc',$aclMan->getGroupIds('member_individual'),$rowUser->promotionId,$rowUser->paymentId);
			// Get total promo
			$total = $formater->checkPromoValidation('Total',$aclMan->getGroupIds('member_individual'),$rowUser->promotionId,$rowUser->paymentId);
			
			// get mail content
			$mailcontent = $formater->getMailContent('konfirmasi-email-individual');
			
			// write confirm individual email
			$formater->_writeConfirmIndividualEmail($mailcontent,$rowUser->fullName,$rowUser->username,$obj->decryptPassword($rowUser->password),$rowUser->paymentId,$disc,$total,base64_encode(Pandamp_Lib_Formater::get_user_id($rowUser->username)),$rowUser->email);
		}
		elseif ($rowUser->packageId == 27)
		{
			// Get disc promo
			$disc = $formater->checkPromoValidation('Disc',$aclMan->getGroupIds('member_corporate'),$rowUser->promotionId,$rowUser->paymentId);
			// Get total promo
			$total = $formater->checkPromoValidation('Total',$aclMan->getGroupIds('member_corporate'),$rowUser->promotionId,$rowUser->paymentId);
			
			// get mail content
			$mailcontent = $formater->getMailContent('konfirmasi-email-korporasi');
			
			// write confirm korporasi email
			$formater->_writeConfirmCorporateEmail($mailcontent,$rowUser->fullName,$rowUser->company,$rowUser->paymentId,$disc,$total,$rowUser->username,base64_encode(Pandamp_Lib_Formater::get_user_id($rowUser->username)),$rowUser->email);
		}
		else 
		{
			// get mail content
			$mailcontent = $formater->getMailContent('konfirmasi email gratis');
			
			// write confirm free email
			$aReturn = $aclMan->getGroupData($rowUser->packageId);
			$formater->_writeConfirmFreeEmail($mailcontent,$rowUser->fullName,$rowUser->username,$obj->decryptPassword($rowUser->password),base64_encode(Pandamp_Lib_Formater::get_user_id($rowUser->username)),$rowUser->email,$aReturn[2]);
		}
	}
	
	/**	
	 * TODO
	 * admin sentEmailOver
	 * @param guid
	 */
	function sentEmailOverAction()
	{
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$formater = new Pandamp_Core_Hol_User();
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowset = $tblUser->find($guid)->current();
		
		if ($rowset->packageId == 26 || $rowset->packageId == 27)
		{
			// write confirm akun habis
			$formater->_writeConfirmAkunHabis($rowset->guid);
		}
	}
	
	/**	
	 * Set Invoice
	 * @param guid
	 */
	function setInvoiceAction()
	{
		$kopel = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowset = $tblUser->fetchRow("kopel='".$kopel."'");
		
		if (($rowset->packageId == 26 || $rowset->packageId == 27) && ($rowset->paymentId <> 0))
		{
			$formater = new Pandamp_Core_Hol_User();
			// GET disc promo
			$disc = $formater->checkPromoValidation('Disc',$rowset->packageId,$rowset->promotionId,$rowset->paymentId);
			// GET total promo
			$total = $formater->checkPromoValidation('Total',$rowset->packageId,$rowset->promotionId,$rowset->paymentId);
			// WRITE invoice
			$formater->_writeInvoice($rowset->kopel, $total, $disc, $rowset->paymentId,'admin');
		}
		else
		{
			$response = array();
			$response['success'] = false;
			$response['message'] = "check your payment, make sure not 0";
			echo Zend_Json::encode($response);
		}
	}
	
	/**	
	 * TODO
	 * admin setActive
	 * @param id, uid, action
	 */
	function setActiveAction()
	{
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$act = ($this->_getParam('act'))? $this->_getParam('act') : '';
		
		//$formater = new Kutu_Lib_Formater();
		 
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowUser = $tblUser->find($guid)->current();
		
		if ($act == 'down')
		{
			if ($rowUser->packageId == 26 or $rowUser->packageId == 27)
			{
				// set period = trial
				$rowUser->periodeId = 2;
				// -- write invoice
				// Get disc promo
				// $disc = $formater->checkPromoValidation('Disc',$rowset->packageId,$rowset->promotionId,$rowset->paymentId);
				// Get total promo
				// $total = $formater->checkPromoValidation('Total',$rowset->packageId,$rowset->promotionId,$rowset->paymentId);
				// $formater->_writeInvoice($rowset->guid,$total,$disc,$rowset->paymentId,'admin');
			}
			else 
			{
				$rowUser->periodeId = 3;
			}
			
			$rowUser->isActive = 1;
		}
		elseif ($act == 'up')		
		{
			$rowUser->periodeId = 1;
			$rowUser->isActive = 0;
		}
		
		$rowUser->activationDate = date("Y-m-d H:i:s");
		
		$result = $rowUser->save();
		
		if ($result)
		{
			$this->view->success = true;
		} else {
			$this->view->success = false;
		}
		
	}
	
	/**
	 * confirm is for administrator directly from GridPanel	
	 * confirm is for user packed Individual or Corporate
	 * If this function is execute then 
	 * User status changed isActive = 1 and period = active and
	 * Also check, if invoice exist then update it
	 * @param uid
	 */
	function confirmAction()
	{
		$uid = ($this->_getParam('uid'))? $this->_getParam('uid') : '';
		
		//$formater = new Kutu_Lib_Formater();
		$aclMan	= Pandamp_Acl::manager();
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		
    	$sql = $tblUser->select()->setIntegrityCheck(false);
		$sql->from(array('ku' => 'KutuUser'))->join(array('gag' => 'gacl_aro_groups'),'ku.packageId = gag.id')
			->where('ku.kopel=?',$uid);

		$rowUser = $tblUser->fetchRow($sql);
		
		if ($rowUser->packageId == 26 || $rowUser->packageId == 27)
		{
			$tblInvoice = new Pandamp_Modules_Payment_Invoice_Model_Invoice();
			$where = $tblInvoice->getAdapter()->quoteInto("uid=?",$uid);
			$rowInvoice = $tblInvoice->fetchRow($where);
			
			if ($rowInvoice)
			{
				$rowInvoice->invoiceConfirmDate = date("Y-m-d");
				$rowInvoice->isPaid = 'Y';
				// get expiration date
				$temptime = time();
				$temptime = Pandamp_Lib_Formater::DateAdd('m',$rowUser->paymentId,$temptime);
				$rowInvoice->expirationDate = strftime('%Y-%m-%d',$temptime);
				$rowInvoice->save();
				
				// delete user group = trial from gacl
				$aclMan->deleteUser($rowUser->username);
				
				// add user to gacl
				$aReturn = $aclMan->getGroupData($rowUser->packageId);
				$aclMan->addUser($rowUser->username,$aReturn[2]);
				
				$rowUser->periodeId = 3;
				$rowUser->isActive = 1;
				$rowUser->updatedDate = date("Y-m-d h:i:s");
				$rowUser->updatedBy = "$rowUser->username";
				$result = $rowUser->save();
				
				if ($result)
				{
					$this->view->success = true;
					$this->view->message = $rowUser->username.", confirm saved";
				} else {
					$this->view->success = false;
					$this->view->message = "Error user confirmation!";
				}
			}
			else
			{
				$this->view->success = false;
				$this->view->message = "Please, create invoice first!";
			}
			
		}
		else 
		{
			$this->view->success = false;
			$this->view->message = $rowUser->username.", wrong packed";
		}
		
	}
	
	public function getMeEmailAction()
	{
		$email = ($this->_getParam('email'))? $this->_getParam('email') : '';
		
		if ($email == "undefined") {
			
			$this->view->success = false;
			$this->view->message = 'Email is Empty';
			
		} else {	
			
			$tableUser = new Pandamp_Modules_Identity_User_Model_User();
			$rowUser = $tableUser->fetchRow("email='".$email."'");
			
			if (!empty($rowUser->email)) {
				$this->view->success = false;
				$this->view->message = '<i><b>'.$email.'</b></i> is not available';
			} else {
				$this->view->success = true;
				$this->view->message = '<i><b>'.$email.'</b></i> is available';
			}		
			
		}
		
	}
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');	
			
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity())
		{
			$sReturn = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$sReturn = base64_encode($sReturn);
			
			$identity = Pandamp_Application::getResource('identity');
			$loginUrl = $identity->loginUrl;
			
			$this->_redirect($loginUrl.'?returnTo='.$sReturn);     
			
			//$this->_redirect(ROOT_URL.'/helper/synclogin/generate/?returnTo='.$sReturn);
		}
		else 
		{
			// [TODO] else: check if user has access to admin page
			$username = $auth->getIdentity()->username;
			
			// get group information
			$acl = Pandamp_Acl::manager();
			$aReturn = $acl->getUserGroupIds($username);
			
			if (isset($aReturn[1]))
			{
				//if (($aReturn[1] !== "admin") && ($aReturn[1] !== "member_admin") && ($aReturn[1] !== "marketing"))
				if (($aReturn[1] !== "Master") && ($aReturn[1] !== "Super Admin") && ($aReturn[1] !== "Member") && ($aReturn[1] !== "Marketing"))
					{
					$this->_helper->redirector('restricted', "error", 'admin');
				}
			}
			
			// [TODO] else: check if user has access to admin page and status website is online
			$tblSetting = new Pandamp_Modules_Misc_Setting_Model_Setting();
			$rowset = $tblSetting->find(1)->current();
			
			if ($rowset)
			{
				if ($rowset->status == 1)
				{
					// it means that user offline other than admin
					if (isset($aReturn[1]))
					{
						//if (($aReturn[1] !== "admin"))
						if (($aReturn[1] !== "Master") && ($aReturn[1] !== "Super Admin"))
						{
							$this->_forward('temporary','error','admin'); 
						}
					}
				}
				else 
				{
					return;
				}
			}
		}
	}
}

?>