<?php
class Talks_Widgets_CatalogController extends Zend_Controller_Action
{
	function detailAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		//$rowset = $decorator->fetchFromFolderAsEntity('lt4a607b5e3c2f2',0,20,'talks');
		$rowset = $decorator->fetchFromFolderAsEntity('lt4a607b5e3c2f2',0,20);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$data[$content][1] = $row->getId();
			$data[$content][2] = $row->getShortTitle();
			$data[$content][3] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
	}
}