<?php

class Admin_Api_RelationController extends Zend_Controller_Action 
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
	function createAction()
	{
		$req = $this->getRequest();
		$item = $req->getParam('itemGuid');
		$relatedItem = $req->getParam('relatedGuid');
		$as = $req->getParam('relateAs');
		
		$aResult = array();
		try {
			$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			$rowCatalog = $tblCatalog->find($item)->current();
			$rowCatalog->relateTo($relatedItem, $as);
			
			$aResult['isError'] = false;
			$aResult['msg'] = 'Adding Relation Success';
		}
		catch (Exception $e)
		{
			$aResult['isError'] = true;
			$aResult['msg'] = $e->getMessage();
		}
		
		echo Zend_Json::encode($aResult);
	}
	function deleteAction()
	{
		$req = $this->getRequest();
		$itemGuid = ($req->getParam('itemGuid'))? $req->getParam('itemGuid') : 'XXX';
		$relatedGuid = ($req->getParam('relatedGuid')) ? $req->getParam('relatedGuid') : 'XXX';
		$relateAs = ($req->getParam('relateAs')) ? $req->getParam('relateAs') : 'XXX';

		$hol = new Pandamp_Core_Hol_Relation();
		if ($hol->delete($itemGuid,$relatedGuid,$relateAs))
		{
			$aResult['isError'] = false;
			$aResult['msg'] = 'Relation Removed';
		}
		else 
		{
			$aResult['isError'] = true;
			$aResult['msg'] = 'No Relation Removed';
		}
		
		echo Zend_Json::encode($aResult);
	}
}

?>