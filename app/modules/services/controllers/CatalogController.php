<?php
class Services_CatalogController extends Zend_Controller_Action
{
	public function fetchCatalogsInFolderAction()
	{
		$prof 		= ($this->_getParam('prof'))? $this->_getParam('prof') : '';
		$profAuth	= ($this->_getParam('profAuth'))? $this->_getParam('profAuth') : '';
		$startdt 	= ($this->_getParam('startdt'))? $this->_getParam('startdt') : '';
		$enddt 		= ($this->_getParam('enddt'))? $this->_getParam('enddt') : '';
		$report 	= ($this->_getParam('report'))? $this->_getParam('report') : '';
		$start 		= ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end 		= ($this->_getParam('limit'))? $this->_getParam('limit') : 10;
		$folderGuid	= ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$modelCatalog 	= new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		$a = array();
		
		if ($startdt && $enddt)
		{
			if ($report == 'msk')
			{
				$rowset = $modelCatalog->fetchAll("createdDate BETWEEN '$startdt' AND '$enddt'",'',$end,$start);
				$rowCount = $modelCatalog->countCatalogsInBetween($startdt,$enddt);
			}
			elseif ($report == 'tbt') 
			{
				$rowset = $modelCatalog->fetchAll("publishedDate BETWEEN '$startdt' AND '$enddt'",'',$end,$start);
				$rowCount = $modelCatalog->countCatalogsPubBetween($startdt,$enddt);
			}
			elseif ($prof !== "")
			{
				$rowset = $modelCatalog->fetchAll("createdDate BETWEEN '$startdt' AND '$enddt' AND profileGuid='$prof'",'',$end,$start);
				$rowCount = $modelCatalog->countCatalogsInBetweenProfile($startdt,$enddt,$prof);
			}
			
			$a['totalCount'] = $rowCount;
		
		}
		elseif ($prof)
		{
			$rowset = $modelCatalog->fetchAll("profileGuid = '$prof'",'',$end,$start);
			$rowCount = $modelCatalog->countCatalogsProfile($prof);
			$a['totalCount'] = $rowCount;
		}
		elseif ($profAuth)
		{
			$rowset = $modelCatalog->fetchAll("createdBy = '$profAuth'",'',$end,$start);
			$rowCount = $modelCatalog->countCatalogsForAuthor($profAuth);
			$a['totalCount'] = $rowCount;
		}
		else 
		{
			$rowset = $modelCatalog->fetchCatalogInFolder($folderGuid,$start,$end);
			$a['totalCount'] = $modelCatalog->getCountCatalogsInFolder($folderGuid);
		}
		
		$now = date('Y-m-d H:i:s');
		
		$ii = 0;
		if ($a['totalCount']!=0) {
			foreach ($rowset as $row)
			{ 
				$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
				
				$a['catalogs'][$ii]['guid']= $row->guid;
				$a['catalogs'][$ii]['title']= $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedTitle');
				$a['catalogs'][$ii]['subtitle']= $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedSubTitle');
				
				$modelProfile = new Pandamp_Modules_Dms_Profile_Model_Profile();
				$p = $modelProfile->getProfileByPG($row->profileGuid);
				$a['catalogs'][$ii]['profile_column']= $p->title; 
				
				$a['catalogs'][$ii]['createdby'] = $row->createdBy;
				$a['catalogs'][$ii]['modifiedby'] = $row->modifiedBy;
				$a['catalogs'][$ii]['createdDate']= Pandamp_Lib_Formater::get_date($row->createdDate);
				
				if ($now <= $row->publishedDate && $row->status == 99) {
					$status = "publish_y";
				} 
				else if (($now <= $row->expiredDate || $row->expiredDate == '0000-00-00 00:00:00') && $row->status == 99) {
					$status = "publish_g";
				} 
				else if ($now > $row->expiredDate && $row->status == 99) {
					$status = "publish_r";
				} 
				else if ($row->status == 0) {
					$status = "publish_x";
				} 
				else if ($row->status == -1) {
					$status = "disabled";
				}
				
				$a['catalogs'][$ii]['status'] = $status;
				$ii++;				
			}
		}
		if ($a['totalCount']==0)
		{
			$a['catalogs'][0]['guid'] = 'XXX';
			$a['catalogs'][0]['title'] = "No Data";
			$a['catalogs'][0]['subtitle'] = "-";
			$a['catalogs'][0]['createdDate'] = '';
		}
		echo Zend_Json::encode($a);
	}
	
	public function fetchProfileInFolderAction()
	{
		$acl = Pandamp_Acl::manager();
		
		$tblProfile = new Pandamp_Modules_Dms_Profile_Model_Profile();
		
		$rowset = $tblProfile->fetchAll();
		$a = array();
		$a['totalCount'] = count($rowset);
		$i = 0;
		if ($a['totalCount']!=0) 
		{
			$auth = Zend_Auth::getInstance();
			
			foreach ($rowset as $row)
			{
				$aReturn = $acl->getUserGroupIds($auth->getIdentity()->username);
				if (($aReturn[1] == "Master") && ($aReturn[1] == "Super Admin"))
					$content = 'all-access';
				else 
					$content = $row->profileType;
					
				if ($acl->getPermissionsOnContent('', $aReturn[1], $content))
				{
					$a['profile'][$i]['guid'] = $row->guid;
					$a['profile'][$i]['title'] = $row->title;
				}
				else 
				{
					continue;
				}
				$i++;
			}
		}
		echo Zend_Json::encode($a);
	}
	public function fetchProfileForReportAction()
	{
		$tblProfile = new Pandamp_Modules_Dms_Profile_Model_Profile();
		
		$rowset = $tblProfile->fetchAll();
		$a = array();
		$a['totalCount'] = count($rowset);
		$i = 0;
		if ($a['totalCount']!=0) 
		{
			foreach ($rowset as $row)
			{
				$a['profile'][$i]['guid'] = $row->guid;
				$a['profile'][$i]['title'] = $row->title;
				$i++;
			}
		}
		echo Zend_Json::encode($a);
	}
	public function fetchAuthorAction()
	{
		$db = Zend_Db_Table::getDefaultAdapter()->query('SELECT * FROM KutuCatalog GROUP BY createdBy');
		
		$rowset = $db->fetchAll(Zend_Db::FETCH_OBJ);
		
		$a = array();
		$a['totalCount'] = count($rowset);
		$i = 0;
		
		if ($a['totalCount']!=0) 
		{
			for ($i=0;$i<count($rowset);$i++)
			{
				$row = $rowset[$i];
				$a['user'][$i]['guid'] 		= $row->guid;
				$a['user'][$i]['username'] 	= $row->createdBy;
			}
		}
		echo Zend_Json::encode($a);
	}
	public function searchAction()
	{
		$query = $this->_getParam('query');
		$category = $this->_getParam('qbox');
		$isrelate = $this->_getParam('isrelate');
		$start = ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end = ($this->_getParam('limit'))? $this->_getParam('limit') : 10;

		try {
			
		switch ($category)
		{
			case 1:
				$query = $query.' profile:article';
			break;
			case 2:
				$query = $query.' profile:klinik';
			break;
			case 3:
				$query = $query.' profile:(kutu_peraturan OR kutu_peraturan_kolonial OR kutu_rancangan_peraturan OR kutu_putusan)';
			break;
			default:
		    	$query = $query;
		}
		
			
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query,$start,$end);
		
		$a = array();
		$a['totalCount'] = $hits->response->numFound;
		$i = 0;
		
		if ($hits->response->numFound > 0)
		{
			foreach ($hits->response->docs as $hit)
			{
				$a['search'][$i]['guid'] = $hit->id;
				if($hit->profile == 'kutu_doc')
					$title = 'File : '.$hit->title;
				else 
					$title = (isset($hit->title))? $hit->title : 'No-Title';
				$a['search'][$i]['title'] = $title;
				if(!isset($hit->subTitle))
					$subTitle = '';
				else 
					$subTitle = $hit->subTitle;
				$a['search'][$i]['subtitle'] = $subTitle;
				
				if ($hit->profile == 'kutu_doc')
				{
					$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
					$rowsetRelatedItem = $tblRelatedItem->fetchRow("itemGuid='$hit->id' AND relateAs='RELATED_FILE'");
					if ($rowsetRelatedItem)
					{
						$parentGuid = $rowsetRelatedItem->relatedGuid;
					}
					else 
					{
						$parentGuid = '';
					}
				}
				else 
				{
					$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
					$rowsetCatalogFolder = $tblCatalogFolder->fetchRow("catalogGuid='$hit->id'");
					if ($rowsetCatalogFolder)
						$parentGuid = $rowsetCatalogFolder->folderGuid;
					else 
						$parentGuid = '';
				}
				
				$a['search'][$i]['folderGuid'] = $parentGuid;
				
				if ($isrelate) $a['search'][$i]['value'] = 'Select Relation';
				
				$i++;
			}
		}
		
		if ($hits->response->numFound==0)
		{
			$a['search'][0]['guid'] = 'XXX';
			$a['search'][0]['title'] = "No Data";
			$a['search'][0]['subtitle'] = "-";
		}
		
		}
		catch (Exception $e) {
			$a['search'][0]['guid'] = 'XXX';
			$a['search'][0]['title'] = "No Data";
			$a['search'][0]['subtitle'] = $e->getMessage();
			
		}
		
		echo Zend_Json::encode($a);
	}
	public function suggestioncollationAction()
	{
		$collation = $this->_getParam('collation');
		
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($collation);
		
		if(isset($hits->spellcheck->suggestions->collation)) {
			$suggest = $hits->spellcheck->suggestions->collation;
			$response['success'] = true;
			$response['collation'] = $suggest;
		} else {
			$response['failure'] = true;
		}
		
		echo Zend_Json::encode($response);
	}	
	public function reportCatalogAction()
	{
		$startdt 	= ($this->_getParam('startdt'))? $this->_getParam('startdt') : '';
		$enddt 		= ($this->_getParam('enddt'))? $this->_getParam('enddt') : '';
		
		$queryAttr = Zend_Db_Table::getDefaultAdapter()->query("SELECT kcf.folderGuid
					FROM KutuCatalog kc INNER JOIN KutuCatalogAttribute kca ON kc.guid = kca.catalogGuid
					INNER JOIN KutuCatalogFolder kcf ON kc.guid = kcf.catalogGuid
					WHERE kc.createdDate BETWEEN '$startdt' AND '$enddt'");
		
		$rowset = $queryAttr->fetchAll(Zend_Db::FETCH_OBJ);
		
		$a = array();
		
		$a['totalCount'] = count($rowset);
		
		$ii = 0;
		if ($a['totalCount']!=0) {
			foreach ($rowset as $row)
			{
				$rowsetCatalogAttribute = $row->findDependentRowsetCatalogAttribute();
				$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle');
				$rowCatalogSubTitle = $rowsetCatalogAttribute->findByAttributeGuid('fixedSubTitle');
//				$rowCatalogisHeadline = $rowsetCatalogAttribute->findByAttributeGuid('fixedMainNews');
				$a['catalogs'][$ii]['guid']= $row->guid;
				$a['catalogs'][$ii]['title']= ((is_object($rowCatalogAttribute)) ? $rowCatalogAttribute->value : '-');
				$a['catalogs'][$ii]['subtitle']= ((is_object($rowCatalogSubTitle)) ? $rowCatalogSubTitle->value : '');
				
//				$a['catalogs'][$ii]['headline']= ((is_object($rowCatalogisHeadline))? $rowCatalogisHeadline->value : "");

				$modelProfile = new Pandamp_Modules_Dms_Profile_Model_Profile();
				$p = $modelProfile->getProfileByPG($row->profileGuid);
				
				$a['catalogs'][$ii]['profile_column']= $p->title;
				
				$a['catalogs'][$ii]['createdby'] = $row->createdBy;
				$a['catalogs'][$ii]['modifiedby'] = $row->modifiedBy;
				$a['catalogs'][$ii]['createdDate']= Pandamp_Lib_Formater::get_date($row->createdDate);
				$a['catalogs'][$ii]['status'] = ($row->status==0)? "" : "Y" ;
				$ii++;				
				
			}
		}
		
		echo Zend_Json::encode($a);
	}
	function indexingTempAction()
	{
		$start 		= ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end 		= ($this->_getParam('limit'))? $this->_getParam('limit') : 10;
		
		$formater = new Kutu_Lib_Formater();
		
		$hTitle = new Pandamp_Controller_Action_Helper_GetCatalogTitle();
		$isFile = new Pandamp_Controller_Action_Helper_GetCatalogIsFile();
		
		$tblTmpIndex = new Pandamp_Modules_Extension_Index_Model_TmpIndex();
		$rowset = $tblTmpIndex->fetchAll(null,null,$end,$start);
		$rowCount = $tblTmpIndex->countCatalogsTempIndex();
		
//		switch ($profileGuid)
//		{
//			case 'peraturan':
//				$rowset = $tblTmpIndex->fetchAll("profileGuid IN ('kutu_doc','kutu_peraturan','kutu_putusan','kutu_peraturan_kolonial','kutu_rancangan_peraturan')",'',$end,$start);
//				$rowCount = $tblTmpIndex->countCatalogsTempIndexPeraturan();
//			break;
//			case 'berita':
//				$rowset = $tblTmpIndex->fetchAll("profileGuid IN ('aktual','suratpembaca','komunitas','news','talks','resensi','isuhangat','fokus','kolom','tokoh','jeda','tajuk','info','utama')",'',$end,$start);
//				$rowCount = $tblTmpIndex->countCatalogsTempIndexArticle();
//			break;
//		}
		
		$a = array();
		$a['totalCount'] = $rowCount;
		$i = 0;
		
		if ($a['totalCount']!=0) 
		{
			foreach ($rowset as $row)
			{
				$a['index'][$i]['guid'] = $row->guid;
				$a['index'][$i]['catalogGuid'] = $row->catalogGuid;
				$a['index'][$i]['title'] = $hTitle->getCatalogTitle($row->catalogGuid).'&nbsp;<font color=green>['.$formater->getCatalogAuthor($row->catalogGuid).']</font>'.'&nbsp;'.$isFile->GetCatalogIsFile($row->catalogGuid).'&nbsp;<font color=blue>['.$row->profileGuid.']</font>';
				$a['index'][$i]['status'] = $row->status;
				$a['index'][$i]['createdDate'] = $formater->get_date($row->createdDate);
				$i++;
			}
		}
		if ($a['totalCount']==0)
		{
			$a['index'][0]['guid'] = '';
			$a['index'][0]['title'] = '';
			$a['index'][0]['status'] = "";
			$a['index'][0]['createdDate'] = '';
		}
		
		echo Zend_Json::encode($a);
		
	}	
}
?>