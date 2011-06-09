<?php

/**
 * @package kutump
 * @author Nihki Prihadi <nihki@hukumonline.com>
 *
 * $Id: AuthController.php 2009-01-10 19:41: $
 */

class Services_AuthController extends Zend_Controller_Action 
{
	function userAction()
	{
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		
		$req 		= $this->getRequest();
		$sortDir 	= ($req->getParam('dir'))? $req->getParam('dir') : 'DESC';
		$sortBy 	= ($req->getParam('sort'))? $req->getParam('sort') : 'username';
		$start 		= ($req->getParam('start'))? $req->getParam('start') : 0;
		$end 		= ($req->getParam('limit'))? $req->getParam('limit') : 10;
		$fields 	= ($req->getParam('fields'))? $req->getParam('fields') : '';
		$query 		= ($req->getParam('query'))? $req->getParam('query') : '';
		$startdt 	= ($req->getParam('startdt'))? $req->getParam('startdt') : '';
		$enddt 		= ($req->getParam('enddt'))? $req->getParam('enddt') : '';
		$group		= ($req->getParam('group'))? $req->getParam('group') : '';

		$selectedRows = Zend_Json::decode($fields);
		$rowval = $query;
		
		$a = array();
		$i = 0;
				
		switch ($group)
		{
			case 'member_gratis':
				
				$rowset 			= $tblUser->fetchUserGroupFree("$sortBy $sortDir", $start, $end, $rowval, $selectedRows,'member_gratis',$startdt, $enddt);
				$a['totalCount'] 	= $tblUser->countUserGroup('member_gratis',$startdt, $enddt);
				
				if ($a['totalCount']!=0) {
					foreach ($rowset as $row)
					{
						//$a['user'][$i]['guid'] 				= $row->guid;
						$a['user'][$i]['kopel'] 			= $row->kopel;
						$a['user'][$i]['fullName'] 			= $row->fullName;
						$a['user'][$i]['username'] 			= $row->username;
						$a['user'][$i]['email'] 			= (!empty($row->email))? $row->email : '-';
						$a['user'][$i]['company'] 			= (!empty($row->company))? $row->company : '-';
						$a['user'][$i]['createdDate'] 		= Pandamp_Lib_Formater::get_date($row->createdDate);
						$a['user'][$i]['value']				= $row->packageId;
						$a['user'][$i]['packageId']			= $row->value;
						$a['user'][$i]['periodeId'] 		= ($row->status)? $row->status : "unknown";
						$a['user'][$i]['isEmailSent'] 		= $row->isEmailSent;
						$a['user'][$i]['isEmailSentOver']	= $row->isEmailSentOver;
						$a['user'][$i]['isActive'] 			= $row->isActive;
						$a['user'][$i]['isContact']			= $row->isContact;
						$i++;
					}
				}
				
				if ($a['totalCount']==0)
				{
					$a['user'][0]['guid'] = 'No Data';
				}
				
			break;
			case 'member_corporate':
				
				if ($startdt && $enddt)
				{
					$rowset 			= $tblUser->fetchUser("$sortBy $sortDir", $start, $end, $rowval, $selectedRows,'member_corporate',$startdt, $enddt);
					$a['totalCount'] 	= $tblUser->countUserGroup('member_corporate',$startdt, $enddt);
				}
				else 
				{
					$rowset 			= $tblUser->fetchUser("$sortBy $sortDir", $start, $end, $rowval, $selectedRows,'member_corporate',$startdt, $enddt);
					$a['totalCount'] 	= $tblUser->countUserGroup('member_corporate',$startdt, $enddt);
				}
				
				if ($a['totalCount']!=0) {
					foreach ($rowset as $row)
					{
						// sent email confirm -7 days
						// $formater->_writeConfirmAkunHabis($row->guid, 'panel');
						// check if days > 0
						/*
						if ($row->DaysLeft > 0)
						{
							$formater->downgrade($row->guid);
						}
						*/
						
						//$a['user'][$i]['guid'] 				= $row->guid;
						$a['user'][$i]['kopel'] 			= $row->kopel;
						$a['user'][$i]['fullName'] 			= $row->fullName;
						$a['user'][$i]['username'] 			= $row->username;
						$a['user'][$i]['email'] 			= (!empty($row->email))? $row->email : '-';
						$a['user'][$i]['company'] 			= (!empty($row->company))? $row->company : '-';
						$a['user'][$i]['createdDate'] 		= Pandamp_Lib_Formater::get_date_english($row->createdDate);
						$a['user'][$i]['expirationDate'] 	= ($row->expirationDate)? Pandamp_Lib_Formater::get_date_english($row->expirationDate) : '-';
						$a['user'][$i]['paymentId'] 		= $row->paymentId;
						$a['user'][$i]['DaysLeft'] 			= ($row->isPaid == "Y")? "-" : $row->DaysLeft;
						$a['user'][$i]['value']				= $row->packageId;
						$a['user'][$i]['packageId']			= $row->value;
						$a['user'][$i]['periodeId'] 		= ($row->status)? $row->status : "unknown";
						$a['user'][$i]['isEmailSent'] 		= $row->isEmailSent;
						$a['user'][$i]['isEmailSentOver']	= $row->isEmailSentOver;
						$a['user'][$i]['isActive'] 			= $row->isActive;
						$a['user'][$i]['isContact']			= $row->isContact;
						$i++;
					}
				}
				
				if ($a['totalCount']==0)
				{
					$a['user'][0]['guid'] = 'No Data';
				}
				
			break;
			case 'member_individual':
				
				$rowset 			= $tblUser->fetchUser("$sortBy $sortDir", $start, $end, $rowval, $selectedRows,'member_individual',$startdt, $enddt);
				$a['totalCount'] 	= $tblUser->countUserGroup('member_individual',$startdt, $enddt);
				
				if ($a['totalCount']!=0) {
					foreach ($rowset as $row)
					{
						// sent email confirm -7 days
						// $formater->_writeConfirmAkunHabis($row->guid, 'panel');
						// check if days > 0
						/*
						if ($row->DaysLeft > 0)
						{
							$formater->downgrade($row->guid);
						}
						*/
						
						//$a['user'][$i]['guid'] 				= $row->guid;
						$a['user'][$i]['kopel'] 			= $row->kopel;
						$a['user'][$i]['fullName'] 			= $row->fullName;
						$a['user'][$i]['username'] 			= $row->username;
						$a['user'][$i]['email'] 			= (!empty($row->email))? $row->email : '-';
						$a['user'][$i]['company'] 			= (!empty($row->company))? $row->company : '-';
						$a['user'][$i]['createdDate'] 		= Pandamp_Lib_Formater::get_date_english($row->createdDate);
						$a['user'][$i]['expirationDate'] 	= ($row->expirationDate)? Pandamp_Lib_Formater::get_date_english($row->expirationDate) : '-';
						$a['user'][$i]['paymentId'] 		= $row->paymentId;
						$a['user'][$i]['DaysLeft'] 			= ($row->isPaid == "Y")? "-" : $row->DaysLeft;
						$a['user'][$i]['value']				= $row->packageId;
						$a['user'][$i]['packageId']			= $row->value;
						$a['user'][$i]['periodeId'] 		= ($row->status)? $row->status : "unknown";
						$a['user'][$i]['isEmailSent'] 		= $row->isEmailSent;
						$a['user'][$i]['isEmailSentOver']	= $row->isEmailSentOver;
						$a['user'][$i]['isActive'] 			= $row->isActive;
						$a['user'][$i]['isContact']			= $row->isContact;
						$i++;
					}
				}
				
				if ($a['totalCount']==0)
				{
					$a['user'][0]['guid'] = 'No Data';
					$a['user'][0]['DaysLeft'] = '-';
					$a['user'][0]['isEmailSent'] = '';
					$a['user'][0]['isEmailSentOver'] = '';
					$a['user'][0]['isActive'] = '';
					$a['user'][0]['isContact'] = 'N';
				}
				
			break;
			case 'member_bonus':
				
				if ($startdt && $enddt)
				{
					$rowset 			= $tblUser->fetchUser("$sortBy $sortDir", $start, $end, $rowval, $selectedRows,'member_bonus',$startdt, $enddt);
					$a['totalCount'] 	= $tblUser->countUserGroup('member_bonus',$startdt, $enddt);
				}
				else 
				{
					$rowset 			= $tblUser->fetchUser("$sortBy $sortDir", $start, $end, $rowval, $selectedRows,'member_bonus',$startdt, $enddt);
					$a['totalCount'] 	= $tblUser->countUserGroup('member_bonus',$startdt, $enddt);
				}
				
				if ($a['totalCount']!=0) {
					foreach ($rowset as $row)
					{
						// sent email confirm -7 days
						// $formater->_writeConfirmAkunHabis($row->guid, 'panel');
						// check if days > 0
						/*
						if ($row->DaysLeft > 0)
						{
							$formater->downgrade($row->guid);
						}
						*/
						
						//$a['user'][$i]['guid'] 				= $row->guid;
						$a['user'][$i]['kopel'] 			= $row->kopel;
						$a['user'][$i]['fullName'] 			= $row->fullName;
						$a['user'][$i]['username'] 			= $row->username;
						$a['user'][$i]['email'] 			= (!empty($row->email))? $row->email : '-';
						$a['user'][$i]['company'] 			= (!empty($row->company))? $row->company : '-';
						$a['user'][$i]['createdDate'] 		= Pandamp_Lib_Formater::get_date_english($row->createdDate);
						$a['user'][$i]['expirationDate'] 	= ($row->expirationDate)? Pandamp_Lib_Formater::get_date_english($row->expirationDate) : '-';
						$a['user'][$i]['paymentId'] 		= $row->paymentId;
						$a['user'][$i]['DaysLeft'] 			= ($row->isPaid == "Y")? "-" : $row->DaysLeft;
						$a['user'][$i]['value']				= $row->packageId;
						$a['user'][$i]['packageId']			= $row->value;
						$a['user'][$i]['periodeId'] 		= ($row->status)? $row->status : "unknown";
						$a['user'][$i]['isEmailSent'] 		= $row->isEmailSent;
						$a['user'][$i]['isEmailSentOver']	= $row->isEmailSentOver;
						$a['user'][$i]['isActive'] 			= $row->isActive;
						$a['user'][$i]['isContact']			= $row->isContact;
						$i++;
					}
				}
				
				if ($a['totalCount']==0)
				{
					$a['user'][0]['guid'] = 'No Data';
				}
				
			break;
			case 'others':
				
				$rowset 			= $tblUser->fetchUserGroupOther("$sortBy $sortDir", $start, $end, $rowval, $selectedRows);
				$a['totalCount'] 	= $tblUser->countUserGroupOther();
				
				if ($a['totalCount']!=0) {
					foreach ($rowset as $row)
					{
						//$a['user'][$i]['guid'] 				= $row->guid;
						$a['user'][$i]['kopel'] 			= $row->kopel;
						$a['user'][$i]['fullName'] 			= $row->fullName;
						$a['user'][$i]['email'] 			= (!empty($row->email))? $row->email : '-';
						$a['user'][$i]['username'] 			= $row->username;
						$a['user'][$i]['company'] 			= (!empty($row->company))? $row->company : '-';
						$a['user'][$i]['createdDate'] 		= Pandamp_Lib_Formater::get_date($row->createdDate);
						$a['user'][$i]['value']				= $row->packageId;
						$a['user'][$i]['packageId']			= $row->value;
						$a['user'][$i]['periodeId'] 		= ($row->status)? $row->status : "unknown";
						$a['user'][$i]['isEmailSent'] 		= $row->isEmailSent;
						$a['user'][$i]['isEmailSentOver']	= $row->isEmailSentOver;
						$a['user'][$i]['isActive'] 			= $row->isActive;
						$a['user'][$i]['isContact']			= $row->isContact;
						$i++;
					}
				}
				
				if ($a['totalCount']==0)
				{
					$a['user'][0]['guid'] = 'No Data';
				}
				
			break;
				
		}

		echo Zend_Json::encode($a);
	}
	function packageAction()
	{
		$aclAdapter = Pandamp_Acl::manager();
		$aGroups = $aclAdapter->getGroups();
		$a = array();
		$a['totalCount'] = count($aGroups);
		for($i=0;$i<count($aGroups);$i++)
		{
			$a['package'][$i]['packageId'] = $aGroups[$i]['id'];
			$a['package'][$i]['packageName'] = $aGroups[$i]['value'];
		}
		echo Zend_Json::encode($a);
	}
	function statusAction()
	{
		$tblStatus = new Pandamp_Modules_Identity_Status_Model_Status();
		$rowset = $tblStatus->fetchAll("accountStatusId IN (1,2,3,4)");
		$a = array();
		$a['totalCount'] = count($rowset);
		$i = 0;
		if ($a['totalCount']!=0) 
		{
			foreach ($rowset as $row)
			{
				$a['status'][$i]['statusId'] = $row->accountStatusId;
				$a['status'][$i]['statusMember'] = $row->status;
				$i++;
			}
		}
		echo Zend_Json::encode($a);
	}
	function userlogAction()
	{
		$sortDir = ($this->_getParam('dir'))? $this->_getParam('dir') : 'DESC';
		$sortBy = ($this->_getParam('sort'))? $this->_getParam('sort') : 'user_access_log_id';
		$start = ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end = ($this->_getParam('limit'))? $this->_getParam('limit') : 10;
		$uid = ($this->_getParam('uid'))? $this->_getParam('uid') : '';
		
		$tblAccessLog = new Pandamp_Modules_Identity_Log_Model_Log();
		$rowLog = $tblAccessLog->fetchAll("user_id='".$uid."'","$sortBy $sortDir",$end,$start);
		$rowset = $tblAccessLog->fetchAll("user_id='".$uid."'","user_access_log_id DESC");
		
		$a = array();
		$a['totalCount'] = count($rowset);
		$i = 0;
		if ($a['totalCount']!=0) 
		{
			foreach ($rowLog as $row)
			{
				$a['log'][$i]['user_access_log_id'] = $row->user_access_log_id;
				$a['log'][$i]['login'] = strftime('%d-%m-%Y %H:%M:%S',strtotime($row->login));
				$a['log'][$i]['lastlogin'] = ($row->lastlogin)? strftime('%d-%m-%Y %H:%M:%S',strtotime($row->lastlogin)) : '-';
				$a['log'][$i]['user_ip'] = $row->user_ip;
				$i++;
			}
		}
		if ($a['totalCount']==0)
		{
			$a['log'][0]['user_access_log_id'] = '';
			$a['log'][0]['login'] = "";
			$a['log'][0]['lastlogin'] = '';
			$a['log'][0]['user_ip'] = '';
		}
		
		echo Zend_Json::encode($a);
		
	}
	function invoiceAction()
	{
		$uid = ($this->_getParam('uid'))? $this->_getParam('uid') : '';
		
		$tblInvoice = new Pandamp_Modules_Payment_Invoice_Model_Invoice();
		$rowset = $tblInvoice->fetchAll("uid='".$uid."'",'invoiceId DESC');
		
		$a = array();
		$a['totalCount'] = count($rowset);
		$i = 0;
		
		if ($a['totalCount']!=0) 
		{
			foreach ($rowset as $row)
			{
				$a['invoice'][$i]['invoiceId'] = $row->invoiceId;
				$a['invoice'][$i]['uid'] = $row->uid;
				$a['invoice'][$i]['price'] = $row->price;
				$a['invoice'][$i]['discount'] = $row->discount;
				$a['invoice'][$i]['invoiceOutDate'] = $row->invoiceOutDate;
				$a['invoice'][$i]['invoiceConfirmDate'] = $row->invoiceConfirmDate;
				$a['invoice'][$i]['clientBankAccount'] = $row->clientBankAccount;
				$a['invoice'][$i]['isPaid'] = $row->isPaid;
				$a['invoice'][$i]['expirationDate'] = $row->expirationDate;
				$i++;
			}
		}
		if ($a['totalCount']==0)
		{
			$a['invoice'][0]['invoiceId'] = '';
			$a['invoice'][0]['uid'] = "";
			$a['invoice'][0]['price'] = '';
			$a['invoice'][0]['discount'] = '';
			$a['invoice'][0]['invoiceOutDate'] = '';
			$a['invoice'][0]['invoiceConfirmDate'] = '';
			$a['invoice'][0]['clientBankAccount'] = '';
			$a['invoice'][0]['isPaid'] = "";
			$a['invoice'][0]['expirationDate'] = '';
		}
		
		echo Zend_Json::encode($a);
		
	}	
	function userHistoryAction()
	{
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		
		$req 		= $this->getRequest();
		$sortDir 	= ($req->getParam('dir'))? $req->getParam('dir') : 'ASC';
		$sortBy 	= ($req->getParam('sort'))? $req->getParam('sort') : 'username';
		$start 		= ($req->getParam('start'))? $req->getParam('start') : 0;
		$end 		= ($req->getParam('limit'))? $req->getParam('limit') : 10;
		$fields 	= ($req->getParam('fields'))? $req->getParam('fields') : '';
		$query 		= ($req->getParam('query'))? $req->getParam('query') : '';

		$selectedRows = Zend_Json::decode($fields);
		$rowval = Zend_Json::decode($query);
		
		$rowset = $tblUser->fetchUserHistory($guid, "$sortBy $sortDir", $start, $end);

		$a = array();
		$a['totalCount'] = count($rowset);
		$i = 0;
		
		if ($a['totalCount']!=0) {
			foreach ($rowset as $row)
			{
				$a['history'][$i]['id'] 				= $row->idUser;
				$a['history'][$i]['uid'] 				= $row->uidUser;
				$a['history'][$i]['fullName'] 			= $row->fullName;
				$a['history'][$i]['username'] 			= $row->username;
				$a['history'][$i]['company'] 			= (!empty($row->company))? $row->company : '-';
				$a['history'][$i]['createdDate'] 		= Pandamp_Lib_Formater::get_date_english($row->createdDate);
				$a['history'][$i]['expirationDate'] 	= ($row->expirationDate)? Pandamp_Lib_Formater::get_date_english($row->expirationDate) : '-';
				$a['history'][$i]['paymentId'] 			= $row->paymentId;
				$a['history'][$i]['DaysLeft'] 			= $row->DaysLeft;
				$a['history'][$i]['value']				= $row->packageId;
				$a['history'][$i]['packageId']			= $row->value;
				$a['history'][$i]['periodeId'] 			= ($row->status)? $row->status : "unknown";
				$a['history'][$i]['isEmailSent'] 		= $row->isEmailSent;
				$a['history'][$i]['isActive'] 			= $row->isActive;
				$i++;
			}
		}
		if ($a['totalCount']==0)
		{
				$a['history'][0]['id'] 					= '';
				$a['history'][0]['uid'] 				= '';
				$a['history'][0]['fullName'] 			= '';
				$a['history'][0]['username'] 			= '';
				$a['history'][0]['company'] 			= '';
				$a['history'][0]['createdDate'] 		= '';
				$a['history'][0]['expirationDate'] 		= '';
				$a['history'][0]['paymentId'] 			= '';
				$a['history'][0]['DaysLeft'] 			= 'x';
				$a['history'][0]['value']				= '';
				$a['history'][0]['packageId']			= '';
				$a['history'][0]['periodeId'] 			= '';
				$a['history'][0]['isEmailSent'] 		= 'x';
				$a['history'][0]['isActive'] 			= 'x';
		}
		
		echo Zend_Json::encode($a);		
	}
}