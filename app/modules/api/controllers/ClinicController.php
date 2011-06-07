<?php

class Api_ClinicController extends Zend_Controller_Action 
{
	/*
	function fetchClinicByCategoryAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$start = ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$limit = ($this->_getParam('limit'))? $this->_getParam('limit') : 20;
		
		$tblCatalogAttribute = new Kutu_Core_Orm_Table_CatalogAttribute();
		$rowCategory = $tblCatalogAttribute->fetchAll("value='".$catalogGuid."'");
		
		if ($rowCategory)
		{
			$vRowCategory = array();
			foreach ($rowCategory as $c)
			{
				$vRowCategory[] = $c->catalogGuid;
			}
			
			$tblCatalog = new Kutu_Core_Orm_Table_Catalog();
			$vRowCategory = $tblCatalog->implode_with_keys(", ", $vRowCategory, "'");
			
			if (isset($vRowCategory))
			{
				$now = date('Y-m-d H:i:s');
				$rowset = $tblCatalog->fetchAll("guid IN($vRowCategory) AND status=99 AND (publishedDate = '0000-00-00 00:00:00' OR publishedDate <= '$now') AND (expiredDate = '0000-00-00 00:00:00' OR expiredDate >= '$now')","publishedDate DESC",$limit,$start);
				$rc = $tblCatalog->fetchAll("guid IN($vRowCategory) AND status=99 AND (publishedDate = '0000-00-00 00:00:00' OR publishedDate <= '$now') AND (expiredDate = '0000-00-00 00:00:00' OR expiredDate >= '$now')","publishedDate DESC");
				
				$content = 0;
				$data = array();
				
				$data['totalCount'] = count($rc);
				
				if ($data['totalCount']!=0) {
					foreach ($rowset as $row)
					{
						$title = Kutu_Core_Util::getCatalogAttributeValue($row->guid,'fixedCommentTitle');
						$question = Kutu_Core_Util::getCatalogAttributeValue($row->guid,'fixedCommentQuestion');
						$category = Kutu_Core_Util::getCatalogAttributeValue($row->guid,'fixedKategoriKlinik');
						$answer = Kutu_Core_Util::getCatalogAttributeValue($row->guid,'fixedAnswer');
						$author = Kutu_Core_Util::getCatalogAttributeValue($row->guid,'fixedSelectNama');
						$source = Kutu_Core_Util::getCatalogAttributeValue($row->guid,'fixedSelectMitra');
						
						// Get Category from profile clinic_category
						$findCategory = $tblCatalog->find($category)->current();
						$category = Kutu_Core_Util::getCatalogAttributeValue($findCategory->guid,'fixedTitle');
						 
						// Get Author from profile author 
						$findAuthor = $tblCatalog->find($author)->current();
						$author = Kutu_Core_Util::getCatalogAttributeValue($findAuthor->guid,'fixedTitle');
						
						// Get Source from profile partner
						$findSource = $tblCatalog->find($source)->current();
						if ($findSource) 
						{
						$source = Kutu_Core_Util::getCatalogAttributeValue($findSource->guid,'fixedTitle');
						}
						
						$data['klinikkategori'][$content]['guid'] = $row->guid;
						$data['klinikkategori'][$content]['title'] = $title;
						$data['klinikkategori'][$content]['pertanyaan'] = "<span style='font:12px verdana,arial,helvetica,sans-serif;font-weight:bold;line-height: 16px;color: #333333;'>Pertanyaan :</span><br />".$question;
						$data['klinikkategori'][$content]['createdBy'] = '('.$row->createdBy.')';
						$data['klinikkategori'][$content]['kategori'] = ($category)? $category : 'No-Title';
						$data['klinikkategori'][$content]['author'] = 'Jawaban oleh : '.$author;
						$data['klinikkategori'][$content]['sumber'] = 'Sumber : '.$source;
						$content++;
					}
				}
				if ($data['totalCount']==0)
				{
					$data['klinikkategori'][0]['title']= 'Kategori klinik kosong';
					$data['klinikkategori'][0]['pertanyaan']= "";
					$data['klinikkategori'][0]['guid']= "";
					$data['klinikkategori'][0]['profileGuid']= "";
					$data['klinikkategori'][0]['createdBy']= "";
					$data['klinikkategori'][0]['kategori']= '';
					$data['klinikkategori'][0]['author']= '';
					$data['klinikkategori'][0]['sumber']= '';
				}
				echo Zend_Json::encode($data);
			}
		}
		else { 
			$this->view->numberOfRows = count($category);
		}
	}
	*/
	
	function indexAction()
	{
		$r = $this->getRequest();
		
		$start = ($r->getParam('start'))? $r->getParam('start') : 0;
		$limit = ($r->getParam('limit'))? $r->getParam('limit'): 20;
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		//$rowset = $decorator->fetchFromFolderAsEntity('lt4a0a533e31979',$start,$limit,"indexklinik");
		$rowset = $decorator->fetchFromFolderAsEntity('lt4a0a533e31979',$start,$limit);
		
		$a = array();
		
		$solrNumFound = $modelCatalog->getWartaCount("lt4a0a533e31979");
		
		$ii=0;
		if($solrNumFound==0)
		{
			$a['terbaru'][0]['guid']= 'XXX';
			$a['terbaru'][0]['title']= 'Kategori klinik kosong';
			$a['terbaru'][0]['pertanyaan']= "";
			$a['terbaru'][0]['createdBy']= "";
			$a['terbaru'][0]['kategori']= '';
			$a['terbaru'][0]['author']= '';
			$a['terbaru'][0]['sumber']= '';
		}
		else 
		{
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
				
				$a['terbaru'][$ii]['guid'] = $row->getId();
				$a['terbaru'][$ii]['title'] = $rowCatalogTitle;
				$a['terbaru'][$ii]['pertanyaan'] = $rowCatalogQuestion;
				$a['terbaru'][$ii]['createdBy'] = 'Penanya:'.$row->getCreatedBy();
				$a['terbaru'][$ii]['kategori'] = $category;
				$a['terbaru'][$ii]['author'] = 'Jawaban oleh : '.$author;
				$a['terbaru'][$ii]['sumber'] = 'Sumber : '.$source;
				$ii++;
			}
		}
		
		echo Zend_Json::encode($a);
	}
	function fetchClinicByCategoryAction()
	{
		$r = $this->getRequest();
		
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$start = ($r->getParam('start'))? $r->getParam('start') : 0;
		$limit = ($r->getParam('limit'))? $r->getParam('limit'): 20;
		$orderBy = ($r->getParam('orderBy'))? $r->getParam('sortBy') : 'publishedDate';
		$sortOrder = ($r->getParam('sortOrder'))? $r->getParam('sortOrder') : ' DESC';
		
		$query = "profile:klinik status:99 kategoriklinik:$catalogGuid;publishedDate desc";
		
		$a = array();

		$a['query']	= $query;
		
		$vTitle = new Pandamp_Controller_Action_Helper_GetCatalogTitle();
		
		$indexingEngine = Pandamp_Search::manager();
		
		$hits = $indexingEngine->find($query, $start, $limit);

		$num = $hits->response->numFound;
		
		$solrNumFound = count($hits->response->docs);
		
		$ii=0;
		if($solrNumFound==0)
		{
			$a['klinikkategori'][0]['guid']= 'XXX';
			$a['klinikkategori'][0]['title']= 'Kategori klinik kosong';
			$a['klinikkategori'][0]['pertanyaan']= "";
			$a['klinikkategori'][0]['createdBy']= "";
			$a['klinikkategori'][0]['kategori']= '';
			$a['klinikkategori'][0]['author']= '';
			$a['klinikkategori'][0]['sumber']= '';
		}
		else 
		{
			if($solrNumFound>$limit)
				$numRowset = $limit ; 
			else 
				$numRowset = $solrNumFound;
				
			for($ii=0;$ii<$numRowset;$ii++)
			{
				$row = $hits->response->docs[$ii];
				if(!empty($row))
				{
					$a['klinikkategori'][$ii]['guid'] = $row->id;
					$a['klinikkategori'][$ii]['title'] = $row->title;
					$a['klinikkategori'][$ii]['pertanyaan'] = $row->commentQuestion;
					$a['klinikkategori'][$ii]['createdBy'] = 'Penanya:'.$row->createdBy;
					$a['klinikkategori'][$ii]['kategori'] = $row->kategori;
					$a['klinikkategori'][$ii]['author'] = 'Jawaban oleh : '.$vTitle->getCatalogTitle($row->kontributor);
					$a['klinikkategori'][$ii]['sumber'] = 'Sumber : '.$vTitle->getCatalogTitle($row->sumber);
				}
			}
		}
		
		echo Zend_Json::encode($a);
	}
}

?>