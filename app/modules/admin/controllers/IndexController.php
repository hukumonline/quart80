<?php
class Admin_IndexController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$auth =  Zend_Auth::getInstance();
		if(!$auth->hasIdentity())
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
				if (($aReturn[1] !== "admin") && ($aReturn[1] !== "member_admin") && 
					($aReturn[1] !== "dc_admin") && ($aReturn[1] !== "dc_editor") && ($aReturn[1] !== "dc_coordinator") && 
					($aReturn[1] !== "news_admin") && ($aReturn[1] !== "news_editor") && ($aReturn[1] !== "marketing") && ($aReturn[1] !== "klinik_admin") && ($aReturn[1] !== "klinik_editor") && ($aReturn[1] !== "holproject"))
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
						//if (($aReturn[1] !== "admin"))
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
	function indexAction()
	{
		$sReturn = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$sReturn = base64_encode($sReturn);
		
		$identity = Pandamp_Application::getResource('identity');
		$logoutUrl = $identity->logoutUrl;
		//$logoutUrl = $identity->logoutUrl;
		
		//$this->view->logoutUrl = $logoutUrl.'/'.$sReturn;
		$this->view->logoutUrl = $logoutUrl.'/'.$sReturn;
		
		// get group information
		$acl = Pandamp_Acl::manager();
		
		$aReturn = $acl->getUserGroupIds(Zend_Auth::getInstance()->getIdentity()->username);
		
		$this->view->group = (isset($aReturn[1]))? $aReturn[1] : '-';
	}
}
?>