<?php

class Admin_Dms_FileUploaderController extends Zend_Controller_Action 
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
			$this->view->username = $username;
			
			$acl = Pandamp_Acl::manager();
			$aReturn = $acl->getUserGroupIds($username);
			
			if (isset($aReturn[1]))
			{
				if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin") && ($aReturn[1] !== "dcAdmin") && ($aReturn[1] !== "dcEditor") && ($aReturn[1] !== "dcCoordinator") && 
					($aReturn[1] !== "newsAdmin") && ($aReturn[1] !== "newsEditor") && ($aReturn[1] !== "marketing"))
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
	function addAction()
	{
		$itemGuid = ($this->_getParam('catalogGuid'))? $this->_getParam('catalogGuid') : '';
		$gen = new Pandamp_Form_Helper_FileUploadGenerator();
		$aRender = $gen->generateFormAdd($itemGuid);
		$this->view->aRenderedAttributes = $aRender;
		$this->view->relatedGuid = $itemGuid;
	}
	function editAction()
	{
		$catalogGuid = ($this->_getParam('catalogGuid'))? $this->_getParam('catalogGuid') : '';
		$relatedGuid = ($this->_getParam('relatedGuid'))? $this->_getParam('relatedGuid') : '';
		
		$selectedRows = Zend_Json::decode($catalogGuid);
		$num_rows = count($selectedRows);
		
		$this->view->selectedRows = $selectedRows;
		$this->view->numberOfRows = $num_rows;
		$this->view->relatedGuid = $relatedGuid;
	}
}