<?php

class Admin_Api_FileuploaderController extends Zend_Controller_Action 
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
	function updateAction()
	{
		$catalogGuid	= ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$value			= ($this->_getParam('value'))? $this->_getParam('value') : '';
		$field			= ($this->_getParam('field'))? $this->_getParam('field') : '';
		
		$tblCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$where2 = "catalogGuid='$catalogGuid' AND attributeGuid='$field'";
		$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
		
		if ($rowCatalogAttribute)
		{
			try {
				$rowCatalogAttribute->value = $value;
				$rowCatalogAttribute->save();
				
				$response['success'] = true;
				$response['message'] = "Update successfully";
			}
			catch (Exception $e)
			{
				$response['error'] = true;
				$response['message'] = $e->getMessage();
			}
		}
		
		echo Zend_Json::encode($response);
	}
	function editAction()
	{
		$r = $this->getRequest();
		$relatedGuid = $r->getParam('relatedGuid');
		
		if(empty($relatedGuid))
			throw new Zend_Exception("relatedGuid can not be empty!");
			
		if($r->isPost())
		{
			try {
				$aData = $r->getParams();
				$hol = new Pandamp_Core_Hol_Catalog();
				$hol->changeUploadFile($aData, $relatedGuid);
				echo "\nUpdate successfully";
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}
	}
	function saveAction()
	{
		$r = $this->getRequest();
		$relatedGuid = $r->getParam('relatedGuid');
		if(empty($relatedGuid))
			throw new Zend_Exception("relatedGuid can not be empty!");
		
		if($r->isPost())
		{
			$this->_save();
			echo "File successfully uploaded";
		}
	}
	private function _save()
	{
		$hol = new Pandamp_Core_Hol_Catalog();
		$r = $this->getRequest();
		$aData = $r->getParams();
		
		$hol->uploadFile($aData, $aData['relatedGuid']);
	}
}

?>