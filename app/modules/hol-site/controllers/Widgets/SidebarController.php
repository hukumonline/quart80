<?php
class HolSite_Widgets_SidebarController extends Zend_Controller_Action
{
	function kananAction()
	{
		
	}
	function wtwitterAction()
	{
		
	}
	function terbaruAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('fb16',0,10);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$data[$content][1] = $row->getShortTitle();
			$data[$content][2] = date("d/m/y",strtotime($row->getCreatedDate()));
			$data[$content][3] = $row->getId();
			$data[$content][4] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
}