<?php

class Admin_Polling_ManagerController extends Zend_Controller_Action 
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
				//if (($aReturn[1] !== "admin") && ($aReturn[1] !== "news_admin"))
				if (($aReturn[1] !== "Master") && ($aReturn[1] !== "Super Admin") && ($aReturn[1] !== "News Admin"))
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
	function pollingAction() {}
	function addAction() {}
}

?>