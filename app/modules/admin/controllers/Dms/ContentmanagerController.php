<?php

class Admin_Dms_ContentManagerController extends Zend_Controller_Action 
{
	function emailAction()
	{
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		
		$rowsetCatalogFolder = $tblCatalogFolder->fetchRow("folderGuid='".$folderGuid."'");

		if (isset($rowsetCatalogFolder))
			$catalogGuid = $rowsetCatalogFolder->catalogGuid;
		else 
			$catalogGuid = '';
			
		$gen = new Pandamp_Form_Helper_EmailConfirmGenerator();
		$aRender = $gen->generateFormAdd($catalogGuid, $folderGuid);
		$this->view->aRenderedAttributes = $aRender;
	}
}