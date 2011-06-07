<?php
class Talks_ManagerController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->_helper->layout->setLayout('layout-depan-talks');
		$this->_helper->layout->setLayoutPath(array('layoutPath'=>ROOT_DIR.'/app/modules/talks/layouts'));
	}
	function viewAction()
	{
		
	}
	function ueventAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('lt4c93230c9d0a5',0,20);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$data[$content][1] = $row->getId();
			$data[$content][2] = $row->getShortTitle();
			$data[$content][3] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			$data[$content][4] = $row->getPrice();
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
	}
}