<?php
class Api_CatalogController extends Zend_Controller_Action
{
	public function getcatalogsinfolderAction()
	{
		$this->_helper->layout()->disableLayout();
		
		$r = $this->getRequest();
		$folderGuid = $r->getParam('folderGuid');
		$start = ($r->getParam('start'))? $r->getParam('start') : 0;
		$limit = ($r->getParam('limit'))? $r->getParam('limit'): 0;
		$sort = ($r->getParam('sort'))? $r->getParam('sort') : 'regulationType desc, year desc';
		
		$a = array();
		$a['folderGuid'] = $folderGuid;

		$db = Zend_Db_Table::getDefaultAdapter()->query
		("SELECT catalogGuid as guid from KutuCatalogFolder where folderGuid='$folderGuid'");
		
		$rowset = $db->fetchAll(Zend_Db::FETCH_OBJ);
		
		$solrAdapter = Pandamp_Search::manager();
		
		$numi = count($rowset);
		$sSolr = "id:(";
		for($i=0;$i<$numi;$i++)
		{
			$row = $rowset[$i];
			$sSolr .= $row->guid .' ';
		}
		$sSolr .= ')';
		
		if(!$numi)
			$sSolr="id:(hfgjhfdfka)";
			
		$solrResult = $solrAdapter->findAndSort($sSolr,$start,$limit, array('sort'=>$sort));
		$solrNumFound = count($solrResult->response->docs);
		
		$ii=0;
		if($solrNumFound==0)
		{
			$a['catalogs'][0]['guid']= 'XXX';
			$a['catalogs'][0]['title']= "No Data";
			$a['catalogs'][0]['subTitle']= "";
			$a['catalogs'][0]['createdDate']= '';
			$a['catalogs'][0]['modifiedDate']= '';
		}
		else 
		{
			if($solrNumFound>$limit)
				$numRowset = $limit ; 
			else 
				$numRowset = $solrNumFound;
				
			for($ii=0;$ii<$numRowset;$ii++)
			{
				$row = $solrResult->response->docs[$ii];
				if(!empty($row))
				{
					$a['catalogs'][$ii]['guid']= $row->id;
					
					if($row->profile == 'kutu_doc')
						$title = 'File : '.$row->title;
					else 
						$title = $row->title;
						
					$a['catalogs'][$ii]['title']= $title;
					
					if(!isset($row->subTitle))
		        		$a['catalogs'][$ii]['subTitle'] = '';
		        	else 
		        		$a['catalogs'][$ii]['subTitle']= $row->subTitle;
		        		
					$a['catalogs'][$ii]['createdDate']= $row->createdDate;
					$a['catalogs'][$ii]['modifiedDate']= $row->modifiedDate;
				}
			}
		}
		
		echo Zend_Json::encode($a);
	}
	public function getsearchAction()
	{
		$r = $this->getRequest();
		
		$query = ($r->getParam('query'))? $r->getParam('query') : '';
		$start = ($r->getParam('start'))? $r->getParam('start') : 0;
		$limit = ($r->getParam('limit'))? $r->getParam('limit'): 20;
		$orderBy = ($r->getParam('orderBy'))? $r->getParam('sortBy') : 'regulationOrder';
		$sortOrder = ($r->getParam('sortOrder'))? $r->getParam('sortOrder') : ' asc';
		
		$a = array();
		
		$query = $query.' profile:(kutu_peraturan OR kutu_peraturan_kolonial OR kutu_rancangan_peraturan OR kutu_putusan)';

		$a['query']	= $query;
		
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query, $start, $limit);

		$num = $hits->response->numFound;
		
		$solrNumFound = count($hits->response->docs);
		
		$ii=0;
		if($solrNumFound==0)
		{
			$a['catalogs'][0]['guid']= 'XXX';
			$a['catalogs'][0]['title']= "No Data";
			$a['catalogs'][0]['subTitle']= "";
			$a['catalogs'][0]['createdDate']= '';
			$a['catalogs'][0]['modifiedDate']= '';
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
					if($row->profile == 'kutu_doc')
					{
						$title = 'File : '.$row->title;
						
						$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
						$rowset = $tblRelatedItem->fetchRow("itemGuid='$row->id'");
						
						if ($rowset)
							$guid = $rowset->relatedGuid;
						else 
							$guid = $row->id;
							
					} else {
						$title = $row->title;
						$guid = $row->id;
					}
					
					$a['catalogs'][$ii]['title']= $title;
					$a['catalogs'][$ii]['guid']= $guid;
					
					if(!isset($row->subTitle))
		        		$a['catalogs'][$ii]['subTitle'] = '';
		        	else 
		        		$a['catalogs'][$ii]['subTitle']= $row->subTitle;
		        	
		        	if($row->profile == 'kutu_doc') {
		        		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		        		$rowsetRelatedItem = $tblRelatedItem->fetchRow("itemGuid='$row->id' AND relateAs='RELATED_FILE'");
		        		if ($rowsetRelatedItem) {
							$parentGuid= $rowsetRelatedItem->relatedGuid;
		        		} else {
		        			$parentGuid = '';
		        		}
		        	}
		        	else 
		        	{
		        		$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		        		$rowsetCatalogFolder = $tblCatalogFolder->fetchRow("catalogGuid='$row->id'");
		        		if ($rowsetCatalogFolder)
		        			$parentGuid= $rowsetCatalogFolder->folderGuid;
		        		else 
		        			$parentGuid='';
		        	}
		        	
					$a['catalogs'][$ii]['folderGuid'] = $parentGuid;
					$a['catalogs'][$ii]['createdDate']= $row->createdDate;
					$a['catalogs'][$ii]['modifiedDate']= $row->modifiedDate;
				}
			}
		}
		
		echo Zend_Json::encode($a);
	}
	public function getclinicsearchAction()
	{
		$r = $this->getRequest();
		
		$query = ($r->getParam('query'))? $r->getParam('query') : '';
		$category = ($r->getParam('ct'))? $r->getParam('ct') : '';
		$start = ($r->getParam('start'))? $r->getParam('start') : 0;
		$limit = ($r->getParam('limit'))? $r->getParam('limit'): 20;
		$orderBy = ($r->getParam('orderBy'))? $r->getParam('sortBy') : 'regulationOrder';
		$sortOrder = ($r->getParam('sortOrder'))? $r->getParam('sortOrder') : ' asc';
		
		$a = array();

		if ($category)
		{
			$query = $query.' profile:klinik status:99 kategoriklinik:'.$category;
		}
		else 
		{
			$query = $query.' profile:klinik status:99';
		}

		$a['query']	= $query;
		
		$indexingEngine = Pandamp_Search::manager();
		
		$hits = $indexingEngine->find($query, $start, $limit);

		$num = $hits->response->numFound;
		
		$solrNumFound = count($hits->response->docs);
		
		$ii=0;
		if($solrNumFound==0)
		{
			$a['catalogs'][0]['guid']= 'XXX';
			$a['catalogs'][0]['title']= "No Data";
			$a['catalogs'][0]['subTitle']= "";
			$a['catalogs'][0]['createdBy']= "";
			$a['catalogs'][0]['kategori']= "";
			$a['catalogs'][0]['kategoriklinik']= '';
			$a['catalogs'][0]['createdDate']= '';
			$a['catalogs'][0]['modifiedDate']= '';
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
					$a['catalogs'][$ii]['title']= $row->title;
					$a['catalogs'][$ii]['guid']= $row->id;
					
					if(!isset($row->commentQuestion))
		        		$a['catalogs'][$ii]['commentQuestion'] = '';
		        	else 
		        		$a['catalogs'][$ii]['commentQuestion']= $row->commentQuestion;
		        	
					$a['catalogs'][$ii]['createdBy'] = 'Pertanyaan oleh:'.$row->createdBy;
					$a['catalogs'][$ii]['kategori'] = "[<b><font color='#FFAD29'>".$row->kategori."</font></b>]&nbsp;";
					$a['catalogs'][$ii]['kategoriklinik'] = $row->kategoriklinik;
					$a['catalogs'][$ii]['createdDate']= $row->createdDate;
					$a['catalogs'][$ii]['modifiedDate']= $row->modifiedDate;
				}
			}
		}
		
		echo Zend_Json::encode($a);
	}
	public function getsearcharticleAction()
	{
		$r = $this->getRequest();
		
		$query = ($r->getParam('query'))? $r->getParam('query') : '';
		$start = ($r->getParam('start'))? $r->getParam('start') : 0;
		$limit = ($r->getParam('limit'))? $r->getParam('limit'): 20;
		$orderBy = ($r->getParam('orderBy'))? $r->getParam('sortBy') : 'regulationOrder';
		$sortOrder = ($r->getParam('sortOrder'))? $r->getParam('sortOrder') : ' asc';
		
		$a = array();
		
		$query = $query.' profile:article';
		
		$a['query']	= $query;
		
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query, $start, $limit);

		$num = $hits->response->numFound;
		
		$solrNumFound = count($hits->response->docs);
		
		$ii=0;
		if($solrNumFound==0)
		{
			$a['catalogs'][0]['guid']= 'XXX';
			$a['catalogs'][0]['title']= "No Data";
			$a['catalogs'][0]['subTitle']= "";
			$a['catalogs'][0]['createdDate']= '';
			$a['catalogs'][0]['modifiedDate']= '';
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
					if($row->profile == 'kutu_doc')
					{
						$title = 'File : '.$row->title;
						
						$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
						$rowset = $tblRelatedItem->fetchRow("itemGuid='$row->id'");
						
						if ($rowset)
							$guid = $rowset->relatedGuid;
						else 
							$guid = $row->id;
							
					} else {
						$title = $row->title;
						$guid = $row->id;
					}
					
					$a['catalogs'][$ii]['title']= $title;
					$a['catalogs'][$ii]['guid']= $guid;
					
					if(!isset($row->shortTitle))
		        		$a['catalogs'][$ii]['subTitle'] = '';
		        	else 
		        		$a['catalogs'][$ii]['subTitle']= $row->shortTitle;
		        	
		        	if($row->profile == 'kutu_doc') {
		        		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		        		$rowsetRelatedItem = $tblRelatedItem->fetchRow("itemGuid='$row->id' AND relateAs='RELATED_FILE'");
		        		if ($rowsetRelatedItem) {
							$parentGuid= $rowsetRelatedItem->relatedGuid;
		        		} else {
		        			$parentGuid = '';
		        		}
		        	}
		        	else 
		        	{
		        		$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		        		$rowsetCatalogFolder = $tblCatalogFolder->fetchRow("catalogGuid='$row->id'");
		        		if ($rowsetCatalogFolder)
		        			$parentGuid= $rowsetCatalogFolder->folderGuid;
		        		else 
		        			$parentGuid='';
		        	}
		        	
					$a['catalogs'][$ii]['folderGuid'] = $parentGuid;
					$a['catalogs'][$ii]['createdDate']= $row->createdDate;
					$a['catalogs'][$ii]['modifiedDate']= $row->modifiedDate;
				}
			}
		}
		
		echo Zend_Json::encode($a);
	}
	function saveAction()
	{
		$aResult = array();
		$request = $this->getRequest();
		$aData = $request->getParams();
		
		$auth = Zend_Auth::getInstance();
		$username = $auth->getIdentity()->username;
		
		if (!$auth->hasIdentity()) {
			$aResult['success'] = false;
			$aResult['msg'] = "You are not login or your session is expired. Please login.";
		}
		else 
		{
			$aData['username'] = $username;
		}
		
		try {
			$hol = new Pandamp_Core_Hol_Catalog();
			$hol->save($aData);
			$aResult['success'] = true;
			$aResult['msg'] = "Catalog is successfully saved";
		}
		catch (Exception $e)
		{
			$aResult['success'] = false;
			$aResult['msg'] = $e->getMessage();
		}
		
		echo Zend_Json::encode($aResult);
	}
}
?>