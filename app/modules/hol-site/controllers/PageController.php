<?php
class HolSite_PageController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->_helper->layout->setLayout('layout-kategori');
		$this->_helper->layout->setLayoutPath(array('layoutPath'=>ROOT_DIR.'/app/modules/hol-site/layouts'));
		$this->view->pageTitle = 'hukumonline.com';
	}
	function indexAction()
	{
		$r = $this->getRequest();
		
		$modelFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$decorator = new Pandamp_BeanContext_Decorator($modelFolder);
		$row = $decorator->getIdByTitleAsEntity($r->getParam('page'));
		$this->view->id = $row->getId();
		$this->view->title = $row->getTitle();
	}
	function browseAction()
	{
		$r = $this->getRequest();
		$page = $r->getParam('page');
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		//$rowset = $decorator->fetchFromFolderAsEntity($page,0,0,'fg_'.$page.'_kategori');
		$rowset = $decorator->fetchFromFolderAsEntity($page,0,0);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$title = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$description = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			
			$data[$content][0] = $title;
			$data[$content][1] = $row->getCreatedDate();
			$data[$content][2] = $description;
			$data[$content][3] = $row->getId();
			$data[$content][4] = $row->getShortTitle();
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
	}
}
?>