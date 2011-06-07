<?php

class Admin_Api_PollingController extends Zend_Controller_Action 
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
			$aReturn = $acl->getUserGroupIds($username);
			
			if (isset($aReturn[1]))
			{
				//if (($aReturn[1] !== "admin") && ($aReturn[1] !== "news_admin"))
				if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin") && ($aReturn[1] !== "newsAdmin"))
					{
					echo "{success:false, error:'Page restricted!!'}";
					die();
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
						if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin"))
						{
							echo "{success:false, error:'The page you are looking for is temporarily unavailable.<br/>Please try again later.'}";
							die();
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
	function saveAction()
	{
		$request = $this->getRequest();
		$aData = $request->getParams();
		try {
			$hol = new Pandamp_Core_Hol_Poll();
			$hol->save($aData);
			$response['success'] = true;
			$response['message'] = "Poll is successfully saved";
		}
		catch (Exception $e)
		{
			$response['success'] = false;
			$response['message'] = $e->getMessage();
		}
		echo Zend_Json::encode($response);
	}
	function deleteAction()
	{
		$pguid = ($this->_getParam('pguid'))? $this->_getParam('pguid') : '';
		$hol = new Pandamp_Core_Hol_Poll();
		try {
			$hol->delete($pguid);
			$response['success'] = true;
			$response['message'] = "Poll Deletion Success";
		}
		catch (Exception $e)
		{
			$response['success'] = false;
			$response['error'] = $e->getMessage();
		}
		echo Zend_Json::encode($response);
	}
}

?>