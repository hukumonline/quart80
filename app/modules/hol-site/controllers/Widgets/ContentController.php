<?php
class HolSite_Widgets_ContentController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
	}
	function utamaAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('lt4aaa29322bdbb',0,3);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			//$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
			//$rowSumComment = $tblRelatedItem->getSumComment($row->getId(), "RELATED_COMMENT");
			
			$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
			$rowSumComment = $modelComment->getParentCommentCount($row->getId());
			
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$data[$content][1] = strftime("%H:%M",strtotime($row->getCreatedDate()));
			$data[$content][2] = $row->getId();
			$data[$content][3] = $row->getShortTitle();
			$data[$content][4] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedAuthor');
			$data[$content][5] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			$data[$content][6] = $rowSumComment;
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
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
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
	}
	function klinikAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('lt4a0a533e31979',0,3);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$rowCatalogTitle = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedCommentTitle');
			$rowCatalogQuestion = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedCommentQuestion');
			$rowCatalogSelectCat = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedKategoriKlinik');
			$author = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedSelectNama');
			$source = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedSelectMitra');
			
			/* Get Category from profile clinic_category */
			$findCategory = $decorator->getCatalogByGuidAsEntity($rowCatalogSelectCat);
			$category = $modelCatalogAttribute->getCatalogAttributeValue($findCategory->getId(),'fixedTitle');
			
			/* Get Author from profile author */
			$findAuthor = $decorator->getCatalogByGuidAsEntity($author);
			$author = $modelCatalogAttribute->getCatalogAttributeValue($findAuthor->getId(),'fixedTitle');
			
			/* Get Source from profile partner */
			$findSource = $decorator->getCatalogByGuidAsEntity($source);
			
			if ($findSource) {
				$source = $modelCatalogAttribute->getCatalogAttributeValue($findSource->getId(),'fixedTitle');
			}
			
			$data[$content][0] = $rowCatalogTitle;
			$data[$content][1] = $rowCatalogQuestion;
			$data[$content][2] = $category;
			$data[$content][3] = $row->getId();
			$data[$content][4] = $row->getCreatedBy();
			$data[$content][5] = (isset($author))? $author : '';
			$data[$content][6] = (isset($source))? $source : '';
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	function fokusAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('fb4',0,1);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$title = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$source = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedAuthor');
			$description = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			
			//$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
			//$rowSumComment = $tblRelatedItem->getSumComment($row->getId(), "RELATED_COMMENT");
			
			$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
			$rowSumComment = $modelComment->getParentCommentCount($row->getId());
			
			$data[$content][0] = $title;
			$data[$content][1] = $source;
			$data[$content][2] = $description;
			$data[$content][3] = $rowSumComment;
			$data[$content][4] = $row->getId();
			$data[$content][5] = $row->getShortTitle();
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
	}
	function isuhangatAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('lt4a6f7d5377193',0,3);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$data[$content][1] = $row->getShortTitle();
			$data[$content][2] = date("d/m/y",strtotime($row->getCreatedDate()));
			$data[$content][3] = $row->getId();
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	/*
	function peraturanAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $modelCatalog->getLatestDocs();
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedTitle');
			$data[$content][1] = $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedSubTitle');
			$data[$content][2] = $row->guid;
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	*/
	function peraturanAction()
	{
		$query = "profile:(kutu_peraturan OR kutu_peraturan_kolonial OR kutu_rancangan_peraturan OR kutu_putusan);year desc, regulationOrder desc";
		
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query,0,5);
		
		$solrNumFound = count($hits->response->docs);
		
		$content = 0;
		$data = array();
		
		for($ii=0;$ii<$solrNumFound;$ii++) {
			$row = $hits->response->docs[$ii];
			$data[$content][0] = $row->title;
			$data[$content][1] = $row->subTitle;
			$data[$content][2] = $row->id;
			$content++;
		}
		
		$num_rows = $solrNumFound;
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	function peraturanpilihanAction()
	{
		$tblAssetSetting = new Pandamp_Modules_Dms_Catalog_Model_AssetSetting();
		$rowset = $tblAssetSetting->fetchAll("valueText='pusatdata'","valueInt DESC",5);

		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			$rowCatalog = $tblCatalog->find($row->guid)->current();
			if ($rowCatalog) 
			{
				$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
				
				$title = $modelCatalogAttribute->getCatalogAttributeValue($rowCatalog->guid,'fixedTitle');
				$subTitle = $modelCatalogAttribute->getCatalogAttributeValue($rowCatalog->guid,'fixedSubTitle');
				
				$data[$content][0] = $title;
				$data[$content][1] = (isset($subTitle))? $subTitle : '';
				$data[$content][2] = $row->guid;
				$data[$content][3] = $row->detail;
			}
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	function kolomAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('fb7',0,2);
		
		$columns = 2;
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$title = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$source = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedAuthor');
			$description = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			
			//$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
			//$rowSumComment = $tblRelatedItem->getSumComment($row->getId(), "RELATED_COMMENT");
			
			$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
			$rowSumComment = $modelComment->getParentCommentCount($row->getId());
			
			$data[$content][0] = $title;
			$data[$content][1] = $source;
			$data[$content][2] = $description;
			$data[$content][3] = $rowSumComment;
			$data[$content][4] = $row->getId();
			$data[$content][5] = $row->getShortTitle();
			$content++;
		}
		
		$num_rows = count($rowset);
		$rows = ceil($num_rows/$columns);
		
		if ($num_rows < 2) {
			$columns = $num_rows;
		}
		if ($num_rows == 0) {}
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
		$this->view->columns = $columns;
		$this->view->rows = $rows;
	}
	function tokohAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('fb12',0,1);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$title = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$source = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedAuthor');
			$description = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			
			//$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
			//$rowSumComment = $tblRelatedItem->getSumComment($row->getId(), "RELATED_COMMENT");
			
			$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
			$rowSumComment = $modelComment->getParentCommentCount($row->getId());
			
			$data[$content][0] = $title;
			$data[$content][1] = $source;
			$data[$content][2] = $description;
			$data[$content][3] = $rowSumComment;
			$data[$content][4] = $row->getId();
			$data[$content][5] = $row->getShortTitle();
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
	}
	function resensiAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('fb17',0,1);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$title = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$source = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedAuthor');
			$description = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			
			//$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
			//$rowSumComment = $tblRelatedItem->getSumComment($row->getId(), "RELATED_COMMENT");
			
			$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
			$rowSumComment = $modelComment->getParentCommentCount($row->getId());
			
			$data[$content][0] = $title;
			$data[$content][1] = $source;
			$data[$content][2] = $description;
			$data[$content][3] = $rowSumComment;
			$data[$content][4] = $row->getId();
			$data[$content][5] = $row->getShortTitle();
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
	}
	function ijtAction()
	{
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		$ta = ($this->_getParam('title'))? $this->_getParam('title') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity($folderGuid,0,1);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$title = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$source = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedAuthor');
			$description = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			
			$data[$content][0] = $title;
			$data[$content][1] = $source;
			$data[$content][2] = $description;
			$data[$content][3] = $row->getId();
			$data[$content][4] = $row->getShortTitle();
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
		$this->view->title = $ta;
	}
	function talkAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('lt4a607b5e3c2f2',0,4);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$data[$content][1] = $row->getId();
			$data[$content][2] = $row->getShortTitle();
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
	}
	function komunitasAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('fb19',0,2);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedDescription');
			$data[$content][1] = $row->getId();
			$data[$content][2] = $row->getShortTitle();
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
	}
	function suarapembacaAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->fetchFromFolderAsEntity('fb8',0,4);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$data[$content][1] = $row->getId();
			$data[$content][2] = $row->getShortTitle();
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;		
	}
}
?>