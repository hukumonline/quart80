<?php
class Admin_Dms_FolderManagerController extends Zend_Controller_Action 
{
	function addAction()
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
			$aReturn = $acl->getUserGroupIds($username);
			
			if (isset($aReturn[1]))
			{
				//if (($aReturn[1] !== "admin") && ($aReturn[1] !== "dc_admin") && ($aReturn[1] !== "news_admin"))
				if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin") && ($aReturn[1] !== "dcAdmin") && ($aReturn[1] !== "newsAdmin"))
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
//				else 
//				{
//					return;
//				}
			}
			
			$parentGuid = ($this->_getParam('parentGuid'))? $this->_getParam('parentGuid') : '';
			$this->view->parentGuid = $parentGuid;
		}
	}
	function editAction()
	{
		$guid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowset = $tblFolder->find($guid);
		
		if(count($rowset))
		{
			$row = $rowset->current();
			$this->view->guid = $row->guid;
			$this->view->title = $row->title;
			$this->view->type = $row->type;
			$this->view->viewOrder = $row->viewOrder;
			$this->view->cmsParams = $row->cmsParams;
			$this->view->description = $row->description;
		}
		
	}
	function deleteAction()
	{
		$auth =  Zend_Auth::getInstance();
			// [TODO] else: check if user has access to admin page
			$username = $auth->getIdentity()->username;
			
			// get group information
			$acl = Pandamp_Acl::manager();
			$aReturn = $acl->getUserGroupIds($username);
			
			if (isset($aReturn[1]))
			{
				//if (($aReturn[1] !== "admin"))
				if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin"))
					{
						echo "{success:false, error:'Dont have enough permission. Please contact Administrator.'}";
						die();
					}
			}
		
		$folderGuid = ($this->_getParam('node'))? $this->_getParam('node') : '';
		
		$hol = new Pandamp_Core_Hol_Folder();
		
		try 
		{
			$hol->delete($folderGuid);
			$response['success'] = true;
			$response['message'] = "Folder has been deleted!";
		}
		catch (Exception $e)
		{
			$response['failure'] = true;
			$response['error'] = $e->getMessage();
		}
		
		echo Zend_Json::encode($response);
		
	}
	function forcedeleteAction()
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
			$aReturn = $acl->getUserGroupIds($username);
			
			if (isset($aReturn[1]))
			{
				//if (($aReturn[1] !== "admin"))
				if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin"))
					{
						echo "{success:false, error:'Dont have enough permission. Please contact Administrator.'}";
						die();
					}
			}
		}
		
		$r = $this->getRequest();
		$folderGuid = $r->getParam('node');
		
		$hol = new Pandamp_Core_Hol_Folder();
		
		try {
			$hol->forceDelete($folderGuid);
			$response['success'] = true;
			$response['message'] = "Folder and Sub Folders, with catalog and Sub Catalog has been deleted!";
		}
		catch (Exception $e)
		{
			$response['failure'] = true;
			$response['error'] = $e->getMessage();
		}
		
		echo Zend_Json::encode($response);
	}
	function saveAction()
	{
		$parentGuid = ($this->_getParam('parentGuid'))? $this->_getParam('parentGuid') : '';
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$title = ($this->_getParam('title'))? $this->_getParam('title') : '';
		$description = ($this->_getParam('description'))? $this->_getParam('description') : '';
		$type = ($this->_getParam('type'))? $this->_getParam('type') : '';
		$viewOrder = ($this->_getParam('viewOrder'))? $this->_getParam('viewOrder') : 0;
		$cmsParams = ($this->_getParam('cmsParams'))? $this->_getParam('cmsParams') : '';
		
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		
		if(empty($guid))
		{
			if(empty($parentGuid))
				throw new Zend_Exception('parentGuid must be supplied!');
			
			$newRow = $tblFolder->createRow();
			$newRow->parentGuid = $parentGuid;
			$newRow->title = $title;
			$newRow->description = $description;
			$newRow->type = $type;
			$newRow->viewOrder = $viewOrder;
			$newRow->cmsParams = $cmsParams;
			$newRow->save();
		}
		else 
		{
			$rowset = $tblFolder->find($guid);
			if(count($rowset))
			{
				$row = $rowset->current();
				$row->title = $title;
				$row->description = $description;
				$row->type = $type;
				$row->viewOrder = $viewOrder;
				$row->cmsParams = $cmsParams;
				$row->save();
			}
		}
		
	}
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
	}
}