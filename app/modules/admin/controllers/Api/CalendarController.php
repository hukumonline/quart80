<?php

class Admin_Api_CalendarController extends Zend_Controller_Action 
{
	function preDispatch()
	{
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
            if (!$acl->checkAcl("site",'all','user', $username, false,false))
            {
            	$this->_helper->redirector('restricted', "error", 'admin');
            }
            
            /*
			$aReturn = $acl->getUserGroupIds($username);
			
			if (isset($aReturn[1]))
			{
				if (($aReturn[1] !== "admin") && ($aReturn[1] !== "holproject"))
					{
					$this->_helper->redirector('restricted', "error", 'admin');
				}
			}
			*/
			
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
						if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin"))
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
	function deleteAction()
	{
		$cGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$hol = new Pandamp_Core_Hol_Calendar();
		try {
			$hol->delete($cGuid);
			$response['success'] = true;
			$response['message'] = 'Event Deletion Success';
		}
		catch (Exception $e)
		{
			$response['success'] = false;
			$response['error'] = $e->getMessage();
		}
		echo Zend_Json::encode($response);
	}
	function saveAction()
	{
		$request = $this->getRequest();
		$aData = $request->getParams();
		
		$auth = Zend_Auth::getInstance();
		
		if (!$auth->hasIdentity()) {
			echo "{success:false, error:'You are not login or your session is expired. Please login.'}";
			die();
		}
		else 
		{
			$aData['guid'] = $auth->getIdentity()->guid;
		}
		
		try {
			$hol = new Pandamp_Core_Hol_Calendar();
			$hol->save($aData);
			$response['success'] = true;
			$response['message'] = "EventCalendar is successfully saved";
		}
		catch (Exception $e)
		{
			$response['success'] = true;
			$response['message'] = $e->getMessage();
		}
		
		echo Zend_Json::encode($response);
	}
}

?>