<?php

class Admin_Setting_ManagerController extends Zend_Controller_Action 
{
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
				//if ($aReturn[1] !== "admin")
				if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin"))
					{
					$this->_helper->redirector('restricted', "error", 'admin');
				}
			}
		}
	}
	function settingAction()
	{
		$tblSetting = new Pandamp_Modules_Misc_Setting_Model_Setting();
		
		$rowset = $tblSetting->find(1)->current();
		
		$this->view->rowset = $rowset;
	}
	function processAction()
	{
		$guid 	= ($this->_getParam('id'))? $this->_getParam('id') : '';
		$status = ($this->_getParam('status'))? $this->_getParam('status') : '';
		
		$tblSetting = new Pandamp_Modules_Misc_Setting_Model_Setting();
		$rowset = $tblSetting->find($guid);
		
		if (count($rowset) > 0)
		{
			$rowSetting = $rowset->current();
			$rowSetting->status = ($status == 1)? 1 : 0;
			$rowSetting->save();
			
			$response['success'] = true;
		}
		else 
		{
			$response['success'] = false;
		}
		echo Zend_Json::encode($response);
	}
}

?>