<?php
class Klinik_Widgets_ContentController extends Zend_Controller_Action
{
	function kategoriAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByProfileAsEntity("kategoriklinik");
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			$data[$content][0] = $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
			$data[$content][1] = $row->getId();
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
		//$rowset = $decorator->fetchFromFolderAsEntity('lt4a0a533e31979',0,5,'klinikterbaru');
		$rowset = $decorator->fetchFromFolderAsEntity('lt4a0a533e31979',0,5);
		
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
			
			$data[$content][0] = $rowCatalogTitle;
			$data[$content][1] = $rowCatalogQuestion;
			$data[$content][2] = $rowCatalogSelectCat;
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
	function popularAction()
	{
		$tblAssetSetting = new Pandamp_Modules_Dms_Catalog_Model_AssetSetting();
		$rowset = $tblAssetSetting->fetchAll("valueText='klinik'","valueInt DESC",6);

		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
			$rowCatalog = $decorator->getCatalogByGuidAsEntity($row->guid);
			
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$rowCatalogTitle = $modelCatalogAttribute->getCatalogAttributeValue($rowCatalog->getId(),'fixedCommentTitle');
			$rowCatalogQuestion = $modelCatalogAttribute->getCatalogAttributeValue($rowCatalog->getId(),'fixedCommentQuestion');
			$rowCatalogSelectCat = $modelCatalogAttribute->getCatalogAttributeValue($rowCatalog->getId(),'fixedKategoriKlinik');
			$author = $modelCatalogAttribute->getCatalogAttributeValue($rowCatalog->getId(),'fixedSelectNama');
			$source = $modelCatalogAttribute->getCatalogAttributeValue($rowCatalog->getId(),'fixedSelectMitra');
			
			$data[$content][0] = $rowCatalogTitle;
			$data[$content][1] = $rowCatalogQuestion;
			$data[$content][2] = $rowCatalogSelectCat;
			$data[$content][3] = $row->guid;
			$data[$content][4] = $rowCatalog->getCreatedBy();
			$data[$content][5] = (isset($author))? $author : '';
			$data[$content][6] = (isset($source))? $source : '';
			$data[$content][7] = $row->valueInt;
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	
	/**
	 * @todo SOLR for category clinic
	 */
	function kadetAction()
	{
		$this->_helper->layout->disableLayout();
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		if ($rowset)
		{
			$category = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedKategoriKlinik');
			/* Get Category from profile clinic_category */
			$findCategory = $decorator->getCatalogByGuidAsEntity($category);
			if (isset($findCategory)) {
				//$category = $modelCatalogAttribute->getCatalogAttributeValue($findCategory->getId(),'fixedTitle');
				$category = $findCategory->getId();
			}
			
		}
		
		//$this->view->category = $category;
		
		//$c = str_replace(' ','%',$category);
		
		//$query = "kategori:$c status:99 -id:$catalogGuid;publishedDate desc";
		$query = "profile:klinik kategoriklinik:$category status:99 -id:$catalogGuid;publishedDate desc";
		
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query,0,10);
		
		$solrNumFound = count($hits->response->docs);
		
		$content = 0;
		$data = array();
		
		for($ii=0;$ii<$solrNumFound;$ii++) {
			$row = $hits->response->docs[$ii];
			$data[$content][0] = $row->id;
			$data[$content][1] = $row->title;
			$content++;
		}
		
		$num_rows = $solrNumFound;
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	/*
	function kadetAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		$category = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedKategoriKlinik');
		
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$rowCategory = $modelCatalogAttribute->fetchAll("value='".$category."'");
		
		if ($rowCategory) {
			$vRowCategory = array();
			foreach ($rowCategory as $c)
			{
				if ($c->catalogGuid == $catalogGuid) continue;
				$vRowCategory[] = $c->catalogGuid;
			}
			
			$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			$vRowCategory = Pandamp_Lib_Formater::implode_with_keys(", ", $vRowCategory, "'");
			
			if (isset($vRowCategory))
			{
				$now = date('Y-m-d H:i:s');
				$rowset = $tblCatalog->fetchAll("guid IN($vRowCategory) AND status=99 AND (publishedDate = '0000-00-00 00:00:00' OR publishedDate <= '$now') AND (expiredDate = '0000-00-00 00:00:00' OR expiredDate >= '$now')","publishedDate DESC");
				
				$content = 0;
				$data = array();
				
				foreach ($rowset as $row)
				{
					$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
					$title = $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedCommentTitle');
					$question = $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedCommentQuestion');
					$category = $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedKategoriKlinik');
					$answer = $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedAnswer');
					$author = $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedSelectNama');
					$source = $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedSelectMitra');
						
					// Get Category from profile clinic_category
					$findCategory = $tblCatalog->find($category)->current();
					$category = $modelCatalogAttribute->getCatalogAttributeValue($findCategory->guid,'fixedTitle');
						 
					// Get Author from profile author 
					$findAuthor = $tblCatalog->find($author)->current();
					$author = $modelCatalogAttribute->getCatalogAttributeValue($findAuthor->guid,'fixedTitle');
						
					// Get Source from profile partner 
					$findSource = $tblCatalog->find($source)->current();
					if ($findSource) 
					{
						$source = $modelCatalogAttribute->getCatalogAttributeValue($findSource->guid,'fixedTitle');
					}

					$data[$content][0] = $row->guid;
					$data[$content][1] = $title;
					$data[$content][2] = "<span style='font:12px verdana,arial,helvetica,sans-serif;font-weight:bold;line-height: 16px;color: #333333;'>Pertanyaan :</span><br />".$question;
					$data[$content][3] = '('.$row->createdBy.')';
					$data[$content][4] = ($category)? $category : 'No-Title';
					$data[$content][5] = 'Jawaban oleh : '.$author;
					$data[$content][6] = 'Sumber : '.$source;
					$content++;
				}
				$num_rows = count($rowset);
				
				$this->view->numberOfRows = $num_rows;
				$this->view->data = $data;
				$this->view->catalogGuid = $catalogGuid;
			}
		}
		
	}
	*/
}
?>