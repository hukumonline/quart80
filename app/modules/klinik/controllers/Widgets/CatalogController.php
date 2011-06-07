<?php
class Klinik_Widgets_CatalogController extends Zend_Controller_Action
{
	function klideAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$ip = Pandamp_Lib_Formater::getRealIpAddr();
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		if ($rowset)
		{
			$modelAsset = new Pandamp_Modules_Dms_Catalog_Model_AssetSetting();
			$decorator = new Pandamp_BeanContext_Decorator($modelAsset);
			$rowAsset = $decorator->getAssetNumOfClickAsEntity($catalogGuid);
			$data = array(
				'guid'			=> $catalogGuid,
				'application'	=> 'ASSET',
				'part'			=> 'MOST_READABLE_CLINIC',
				'valueType'		=> $ip,
				'valueInt'		=> 1,
				'valueText'		=> 'klinik'
			);
			$asset = $modelAsset->addCounterAsset($rowset->getId(),$data);
			
			$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedCommentTitle');
			$question = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedCommentQuestion');
			$category = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedKategoriKlinik');
			$answer = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedAnswer');
			$author = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedSelectNama');
			$source = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedSelectMitra');
			
			$this->view->title 			= (isset($title))? $title : '';
			$this->view->question 		= (isset($question))? $question : '';
			$this->view->category 		= (isset($category))? $category : '';
			$this->view->answer 		= (isset($answer))? $answer : '';
			$this->view->author 		= (isset($author))? $author : '';
			$this->view->source 		= (isset($source))? $source : '';
			$this->view->createdBy 		= $rowset->getCreatedBy();
			$this->view->publishedDate	= Pandamp_Lib_Formater::get_date($rowset->getPublishedDate());
			$this->view->numofclick		= (isset($rowAsset))? $rowAsset->getValueInt() : 0;
			
			// get votes
			$modelVote = new Pandamp_Modules_Extension_Vote_Model_Vote();
			$decorator = new Pandamp_BeanContext_Decorator($modelVote);
			$rowRate = $decorator->getRatingAsEntity($catalogGuid,$ip);
			$val = ($rowRate)? $rowRate->getValue() : 0;
			$counter = ($rowRate)? $rowRate->getCounter() : 0;
			
			if ($counter < 1) {
				$count = 0;
			} else {
				$count=$counter; //how many votes total
			}
			$current_rating = $val;
			$tense=($count==1) ? "vote" : "votes"; //plural form votes/vote
			$rating = @number_format($current_rating/$count,1);
			
			$drawrating = '('.$count.' '.$tense.', average: '. $rating .' out of 5)';
		
			$this->view->drawrating = $drawrating;
			
			$this->view->catalogGuid = $catalogGuid;
		}
	}
	function terbaruAction()
	{
		$time_start = microtime(true);
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		$data = array();
		
		$num_rows = $modelCatalog->getWartaCount("lt4a0a533e31979");
		$limit = 50;
		
		$data['totalCount'] = $num_rows;
		$data['limit'] = $limit;
		
		$this->view->aData = $data;
	}
	function kategoriklinikAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$query = "kategoriklinik:$catalogGuid status:99";
		
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query);
		
		$solrNumFound = count($hits->response->docs);
		
		$num_rows = $solrNumFound;
		
		$limit = 20;
		
		$data['catalogGuid'] = $catalogGuid;
		$data['totalCount'] = $num_rows;
		$data['limit'] = $limit;
		
		$this->view->aData = $data;
	}
	function detailmitraAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		if ($rowset)
		{
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			$this->view->title 		= $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
			//$this->view->subtitle 	= $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedSubTitle');
			$this->view->content 	= $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedContent');
			$this->view->catalogGuid = $catalogGuid;
		}
	}
	function authorAction()
	{
		$mkGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$query = "profile:author sumber:$mkGuid;publishedDate desc";
		
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query);
		
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
		$this->view->catalogGuid = $mkGuid;
	}
	function pengasuhAction()
	{
		$author = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($author);
		
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		
		$this->view->author = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
		$this->view->description = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedSubTitle');
	}
	function viewerClinicAction()
	{
		$author = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$query = "profile:klinik status:99 kontributor:$author;publishedDate desc";
		
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query);
		
		$solrNumFound = count($hits->response->docs);
		
		$content = 0;
		$data = array();
		
		for($ii=0;$ii<$solrNumFound;$ii++) {
			$row = $hits->response->docs[$ii];
			$data[$content][0] = $row->title;
			$data[$content][1] = $row->commentQuestion;
			$data[$content][2] = $row->kategori;
			$data[$content][3] = $row->id;
			$data[$content][4] = $row->createdBy;
			$data[$content][5] = $row->createdBy;
			$data[$content][6] = $row->createdBy;
			$content++;
		}
		
		$num_rows = $solrNumFound;
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
}
?>