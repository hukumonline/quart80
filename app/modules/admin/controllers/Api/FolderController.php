<?php

class Admin_Api_FolderController extends Zend_Controller_Action 
{
	const CONTEXT_JSON = 'json';
	public function init()
	{
		$contextSwitch = $this->_helper->contextSwitch();
        $contextSwitch->addActionContext('move-to-folder', self::CONTEXT_JSON)
        			  ->addActionContext('move-to-catalog', self::CONTEXT_JSON)
        			  ->addActionContext('add-to-folder', self::CONTEXT_JSON)
        			  ->addActionContext('add-to-catalog', self::CONTEXT_JSON)
                      ->initContext();
	}
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
	function copyMoveItemsAction()
	{
		$action = ($this->_getParam('act'))? $this->_getParam('act') : '';
		$option = ($this->_getParam('option'))? $this->_getParam('option') : '';
		switch ($action)
		{
			case 'move':
				
				if ($option == "tree") 
					$this->_forward('move-to-folder','api_folder','admin');
				else 
					$this->_forward('move-to-catalog','api_folder','admin');
					
			break;
			case 'copy':
				
				if ($option == "tree")
					$this->_forward('add-to-folder','api_folder','admin');
				else 
					$this->_forward('add-to-catalog','api_folder','admin');
					
			break;
		}
	}
	function addToCatalogAction()
	{
		$selitem = ($this->_getParam('selitem'))? $this->_getParam('selitem') : '';
		$targetGuid = ($this->_getParam('targetGuid'))? $this->_getParam('targetGuid') : '';
		$currentGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		$rowset = $tblCatalogFolder->find($selitem,$targetGuid)->current();
		if (count($rowset))
		{
			$this->view->success = true;
			$this->view->message = "Folder Already Exist";
		}
		else 
		{
			$row = $tblCatalogFolder->createRow();
			$row->catalogGuid = $selitem;
			$row->folderGuid = $targetGuid;
			try {
				$row->save();
				$this->view->success = true;
				$this->view->message = "Add to Catalog Success";
			}
			catch (Exception $e)
			{
				$this->view->success = false;
				$this->view->error = $e->getMessage();
			}
		}
	}
	function addToFolderAction()
	{
		$targetGuid = ($this->_getParam('targetGuid'))? $this->_getParam('targetGuid') : '';
		$currentGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		
		if (empty($currentGuid) || $currentGuid == 'root')
		{
			$this->view->success = false;
			$this->view->error = "Can't move ROOT";
		}
		else 
		{
			$newRow = $tblFolder->createRow();
			$newContent = $tblCatalogFolder->createRow();
			try 
			{
				$newRow->copy($targetGuid,$currentGuid);
				$this->view->success = true;
				$this->view->message = "Copy to Folder Success";
			}
			catch (Exception $e)
			{
				$this->view->success = false;
				$this->view->error = $e->getMessage();
			}
		}
	}
	function moveToCatalogAction()
	{
		$selitem = ($this->_getParam('selitem'))? $this->_getParam('selitem') : '';
		$targetGuid = ($this->_getParam('targetGuid'))? $this->_getParam('targetGuid') : '';
		$currentGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		$rowset = $tblCatalogFolder->find($selitem,$currentGuid)->current();
		$rowset->folderGuid = $targetGuid;
		try {
			$rowset->save();
			$this->view->success = true;
			$this->view->message = 'Move to Catalog Success';
		}
		catch (Exception $e) {
			$this->view->success = false;
			$this->view->error = $e->getMessage();
		}
	}
	function moveToFolderAction()
	{
		$targetGuid = ($this->_getParam('targetGuid'))? $this->_getParam('targetGuid') : '';
		$currentGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';

		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$row = $tblFolder->find($currentGuid)->current();
		if (empty($currentGuid) || $currentGuid == 'root')
		{
			$this->view->success = false;
			$this->view->error = "Can't move ROOT";
		}
		else 
		{
			try {
				$row->move($targetGuid);
				$this->view->success = true;
				$this->view->message = "Move to Folder Success";
			}
			catch (Exception $e)
			{
				$this->view->success = false;
				$this->view->error = $e->getMessage();
			}
		}
	}
}

?>