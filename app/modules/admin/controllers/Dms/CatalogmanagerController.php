<?php
class Admin_Dms_CatalogManagerController extends Zend_Controller_Action 
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
				if (($aReturn[1] !== "Master") && ($aReturn[1] !== "Super Admin") && ($aReturn[1] !== "Dc Admin") && ($aReturn[1] !== "Dc Editor") && ($aReturn[1] !== "Dc Coordinator") && 
					($aReturn[1] !== "News Admin") && ($aReturn[1] !== "News Editor") && ($aReturn[1] !== "HolProject") && ($aReturn[1] !== "Clinic Admin") && ($aReturn[1] !== "Marketing"))
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
	function addAction()
	{
		$profileGuid = ($this->_getParam('profileGuid'))? $this->_getParam('profileGuid') : '';
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$profileGuid = str_replace(' ','_',$profileGuid);
		
		$generatorForm = new Pandamp_Form_Helper_CatalogInputGenerator();
		$aRender = $generatorForm->generateFormAdd(strtolower($profileGuid), $folderGuid);
		
		$this->view->aRenderedAttributes = $aRender;
		
		if(empty($folderGuid))
			$this->view->itemGuid = 'system';
		else 
			$this->view->itemGuid = $folderGuid;
	}
    function editAction()
	{
		$catalogGuid = ($this->_getParam('catalogGuid'))? $this->_getParam('catalogGuid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		if ($rowset->getProfile() == 'klinik') {
			$this->_forward('answer.clinic','clinic_manager','admin',array('catalogGuid'=>$catalogGuid));
		}
		else 
		{
			$gen = new Pandamp_Form_Helper_CatalogInputGenerator();
			$aRender = $gen->generateFormEdit($catalogGuid);
			$this->view->aRenderedAttributes = $aRender;
		}
	}
	function saveAction()
	{
		$response = array();
		$request = $this->getRequest();
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$userName = $auth->getIdentity()->username;
		}
		
		$gman = new Pandamp_Core_Guid();
		$catalogGuid = ($request->getParam('guid'))? $request->getParam('guid') : $gman->generateGuid();
		$folderGuid = ($request->getParam('folderGuid'))? $request->getParam('folderGuid') : '';
		
		//if not empty, there are 2 possibilities
		$where = $tblCatalog->getAdapter()->quoteInto('guid=?', $catalogGuid);
		if($tblCatalog->fetchRow($where))
		{
			$rowCatalog = $tblCatalog->find($catalogGuid)->current();
			
			$rowCatalog->shortTitle = $request->getParam('shortTitle');
			$rowCatalog->publishedDate = $request->getParam('publishedDate');
			$rowCatalog->expiredDate = $request->getParam('expiredDate');
			$rowCatalog->modifiedBy = $userName;
			$rowCatalog->modifiedDate = date("Y-m-d h:i:s");
			$rowCatalog->status = $request->getParam('status');
		}
		else 
		{
			$rowCatalog = $tblCatalog->fetchNew();
			
			$rowCatalog->shortTitle = $request->getParam('shortTitle');
			$rowCatalog->profileGuid = $request->getParam('profileGuid');
			$rowCatalog->publishedDate = $request->getParam('publishedDate');
			$rowCatalog->expiredDate = $request->getParam('expiredDate');
			$rowCatalog->createdBy = $userName;
			$rowCatalog->modifiedBy = $userName;
			$rowCatalog->createdDate = date("Y-m-d h:i:s");
			$rowCatalog->modifiedDate = date("Y-m-d h:i:s");
			$rowCatalog->deletedDate = '0000-00-00 00:00:00';
			$rowCatalog->status = $request->getParam('status');
		}
		$catalogGuid = $rowCatalog->save();
		
		$tableProfileAttribute = new Pandamp_Modules_Dms_Profile_Model_ProfileAttribute();
		$profileGuid = $rowCatalog->profileGuid;
		$where = $tableProfileAttribute->getAdapter()->quoteInto('profileGuid=?', $profileGuid);
		$rowsetProfileAttribute = $tableProfileAttribute->fetchAll($where,'viewOrder ASC');
		
		$rowsetCatalogAttribute = $rowCatalog->findDependentRowsetCatalogAttribute();
		foreach ($rowsetProfileAttribute as $rowProfileAttribute)
		{
			if($rowsetCatalogAttribute->findByAttributeGuid($rowProfileAttribute->attributeGuid))
			{
				$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid($rowProfileAttribute->attributeGuid);
			}
			else 
			{
				$tblCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
				$rowCatalogAttribute = $tblCatalogAttribute->fetchNew();
				$rowCatalogAttribute->catalogGuid = $catalogGuid;
				$rowCatalogAttribute->attributeGuid = $rowProfileAttribute->attributeGuid;
			}
			
			$rowCatalogAttribute->value = $request->getParam($rowProfileAttribute->attributeGuid);
			$rowCatalogAttribute->save();
		}
		
		//save to table CatalogFolder only if folderGuid is not empty
		if (!empty($folderGuid)) 
		{
			$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
			
			$rowsetCatalogFolder = $tblCatalogFolder->find($catalogGuid, $folderGuid);
			if(count($rowsetCatalogFolder)<=0)
			{
				$rowCatalogFolder = $tblCatalogFolder->createRow(array('catalogGuid'=>'', 'folderGuid'=>''));
				$rowCatalogFolder->catalogGuid = $catalogGuid;
				$rowCatalogFolder->folderGuid = $folderGuid;
				$rowCatalogFolder->save();
			}
		}
		
		$response['success'] = true;
		echo Zend_Json::encode($response);
	}
	function manageFolderAction()
	{
		
	}
	function manageFolderMoveAction()
	{
		
	}
	function reportAction()
	{
		$wr = ($this->_getParam('wr'))? $this->_getParam('wr') : '';
		
		switch ($wr)
		{
			case 'msk':
				$this->view->report = 'msk';
			break;
			case 'tbt':
				$this->view->report = 'tbt';
			break;
		}
	}
	function reportKategoriAction()
	{
		
	}
	function reportAuthorAction()
	{
		
	}
}
?>