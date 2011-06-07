<?php

class Admin_Api_CatalogController extends Zend_Controller_Action 
{
	const CONTEXT_JSON = 'json';
	public function init()
	{
		$contextSwitch = $this->_helper->contextSwitch();
        $contextSwitch->addActionContext('save', self::CONTEXT_JSON)
        			  ->addActionContext('remove-from-folder', self::CONTEXT_JSON)
        			  ->addActionContext('delete', self::CONTEXT_JSON)
        			  ->addActionContext('clearcache', self::CONTEXT_JSON)
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
	function saveAction()
	{
		$request = $this->getRequest();
		$aData = $request->getParams();
		
		$auth = Zend_Auth::getInstance();
		$username = $auth->getIdentity()->username;
		
		if (!$auth->hasIdentity()) {
			$this->view->success = false;
			$this->view->message = "You are not login or your session is expired. Please login.";
		}
		else 
		{
			$aData['username'] = $username;
		}
		
		try {
			$hol = new Pandamp_Core_Hol_Catalog();
			$hol->save($aData);
			$this->view->success = true;
			$this->view->message = "Catalog is successfully saved";
		}
		catch (Exception $e)
		{
			$this->view->success = false;
			$this->view->message = $e->getMessage();
		}
		
	}
	function deleteAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$hol = new Pandamp_Core_Hol_Catalog();
		try {
			$hol->delete($catalogGuid);
			$this->view->success = true;
			$this->view->message = 'Catalog Deletion Success';
		}
		catch (Exception $e)
		{
			$this->view->success = false;
			$this->view->error = $e->getMessage();
		}
	}
	function clearcacheAction()
	{
		try {
			$cache = Zend_Registry::get('cache');
			$cache->clean(Zend_Cache::CLEANING_MODE_OLD);
			
			$this->view->success = true;
			$this->view->message = 'Cache clear';
		}
		catch (Exception $e)
		{
			$this->view->success = false;
			$this->view->error = $e->getMessage();
		}
	}
	function clearallcacheAction()
	{
		try {
			$cache = Zend_Registry::get('cache');
			$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
			
			$this->view->success = true;
			$this->view->message = 'Cache clear';
		}
		catch (Exception $e)
		{
			$this->view->success = false;
			$this->view->error = $e->getMessage();
		}
	}
	function removeFromFolderAction()
	{
		$req = $this->getRequest();
		$catalogGuid = $req->getParam('guid');
		$folderGuid = $req->getParam('folderGuid');
		
		$hol = new Pandamp_Core_Hol_Catalog();
		
		try
		{
			$hol->removeFromFolder($catalogGuid, $folderGuid);
			$this->view->success = true;
			$this->view->message = "Data was deleted.";
		}
		catch (Exception $e)
		{
			$this->view->success = false;
			$this->view->message = $e->getMessage();
		}
	}
}

?>