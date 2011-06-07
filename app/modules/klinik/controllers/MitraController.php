<?php

class Klinik_MitraController extends Zend_Controller_Action 
{
	function detailAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$this->view->catalogGuid = $catalogGuid;
	}
	function mitraKlinikAction()
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchAll("guid!='lt4b1a5aadda0f1' AND profileGuid='partner'",'createdDate DESC');
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedTitle');
			$data[$content][1] = $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedContent');
			$data[$content][2] = $row->guid;
			$content++;
		}
		
		$num_row = count($rowset);
		
		$this->view->numberOfRows = $num_row;
		$this->view->aData = $data;
	}
	function preDispatch()
	{
		$this->_helper->layout->setLayout('layout-klinik');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/klinik/layouts'));
	}
}

?>