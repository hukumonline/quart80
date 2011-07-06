<?php
class Berita_Widgets_ContentController extends Zend_Controller_Action
{
	function beritaBawahAction()				{}	
	function terbaruBeritaBawahAction()			{}
	function fokusBeritaBawahAction()			{}
	function isuhangatBeritaBawahAction()		{}
	function tajukBeritaBawahAction()			{}
	function kolomBeritaBawahAction()			{}
	function jedaBeritaBawahAction()			{}
	function resensiBeritaBawahAction()			{}
	function tokohBeritaBawahAction()			{}
	function infoBeritaBawahAction()			{}
	function aktualBeritaBawahAction()			{}
	function utamaByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('lt4aaa29322bdbb',$givenDate);
		}
		else {
			$rowset = $decorator->fetchFromFolderAsEntity('lt4aaa29322bdbb',0,5,'warta');
		}
		
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
			$data[$content][1] = date("d/m/y",strtotime($row->getCreatedDate()));
			$data[$content][2] = $row->getId();
			$data[$content][3] = $row->getShortTitle();
			$data[$content][4] = ($rowSumComment <> 0)? '('.$rowSumComment.'&nbsp;tanggapan)' : '';
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	function terbaruByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('fb16',$givenDate);
		}
		else {
			//$rowset = $decorator->fetchFromFolderAsEntity('fb16',0,5,'warta');
			$rowset = $decorator->fetchFromFolderAsEntity('fb16',0,5);
		}
		
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
	function fokusByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('fb4',$givenDate);
		}
		else {
			//$rowset = $decorator->fetchFromFolderAsEntity('fb4',0,5,'warta');
			$rowset = $decorator->fetchFromFolderAsEntity('fb4',0,5);
		}
		
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
			$data[$content][1] = $row->getShortTitle();
			$data[$content][2] = date("d/m/y",strtotime($row->getCreatedDate()));
			$data[$content][3] = $row->getId();
			$data[$content][4] = ($rowSumComment <> 0)? '('.$rowSumComment.'&nbsp;tanggapan)' : '';
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	function isuhangatByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('lt4a6f7d5377193',$givenDate);
		}
		else {
			//$rowset = $decorator->fetchFromFolderAsEntity('lt4a6f7d5377193',0,5,'warta');
			$rowset = $decorator->fetchFromFolderAsEntity('lt4a6f7d5377193',0,13);
		}
		
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
	function tajukByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('fb18',$givenDate);
		}
		else {
			//$rowset = $decorator->fetchFromFolderAsEntity('fb18',0,5,'warta');
			$rowset = $decorator->fetchFromFolderAsEntity('fb18',0,10);
		}
		
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
	function kolomByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('fb7',$givenDate);
		}
		else {
			//$rowset = $decorator->fetchFromFolderAsEntity('fb7',0,5,'warta');
			$rowset = $decorator->fetchFromFolderAsEntity('fb7',0,5);
		}
		
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
			$data[$content][1] = $row->getShortTitle();
			$data[$content][2] = date("d/m/y",strtotime($row->getCreatedDate()));
			$data[$content][3] = $row->getId();
			$data[$content][4] = ($rowSumComment <> 0)? '('.$rowSumComment.'&nbsp;tanggapan)' : '';
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	function jedaByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('fb14',$givenDate);
		}
		else {
			//$rowset = $decorator->fetchFromFolderAsEntity('fb14',0,5,'warta');
			$rowset = $decorator->fetchFromFolderAsEntity('fb14',0,5);
		}
		
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
	function resensiByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('fb17',$givenDate);
		}
		else {
			//$rowset = $decorator->fetchFromFolderAsEntity('fb17',0,5,'warta');
			$rowset = $decorator->fetchFromFolderAsEntity('fb17',0,5);
		}
		
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
			$data[$content][1] = $row->getShortTitle();
			$data[$content][2] = date("d/m/y",strtotime($row->getCreatedDate()));
			$data[$content][3] = $row->getId();
			$data[$content][4] = ($rowSumComment <> 0)? '('.$rowSumComment.'&nbsp;tanggapan)' : '';
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	function tokohByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('fb12',$givenDate);
		}
		else {
			//$rowset = $decorator->fetchFromFolderAsEntity('fb12',0,5,'warta');
			$rowset = $decorator->fetchFromFolderAsEntity('fb12',0,5);
		}
		
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
			$data[$content][1] = $row->getShortTitle();
			$data[$content][2] = date("d/m/y",strtotime($row->getCreatedDate()));
			$data[$content][3] = $row->getId();
			$data[$content][4] = ($rowSumComment <> 0)? '('.$rowSumComment.'&nbsp;tanggapan)' : '';
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	function infoByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('fb9',$givenDate);
		}
		else {
			//$rowset = $decorator->fetchFromFolderAsEntity('fb9',0,5,'warta');
			$rowset = $decorator->fetchFromFolderAsEntity('fb9',0,5);
		}
		
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
	function aktualByDateAction()
	{
		$givenDate = ($this->_getParam('givenDate'))? $this->_getParam('givenDate') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		
		if ($givenDate) {
			$rowset = $decorator->fetchFromFolderByDateAsEntity('fb29',$givenDate);
		}
		else {
			//$rowset = $decorator->fetchFromFolderAsEntity('fb29',0,5,'warta');
			$rowset = $decorator->fetchFromFolderAsEntity('fb29',0,5);
		}
		
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
	
	/**
	 * @todo testing indexwarta dengan jumlah komentar untuk Resensi, Tokoh
	 */
	function indexwartaAction()
	{
		$time_start = microtime(true);
		
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		$data = array();
		
		$num_rows = $modelCatalog->getWartaCount($folderGuid);
		$limit = 50;
		
		$data['folderGuid'] = $folderGuid;
		$data['totalCount'] = $num_rows;
		$data['totalCountRows'] = $num_rows;
		$data['limit'] = $limit;
		
		$this->view->aData = $data;
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->view->time = round($time,2) . ' detik';
	}
	function detailIndexWartaAction()
	{
		$this->_helper->layout->disableLayout();
		
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		$start		= ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$limit		= ($this->_getParam('limit'))? $this->_getParam('limit') : 0;
		
		$a = array();
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		//$rowset = $decorator->fetchFromFolderAsEntity($folderGuid,$start,$limit,'fg_'.$folderGuid.'_detail_warta_s_'.$start.'_e_'.$limit);
		$rowset = $decorator->fetchFromFolderAsEntity($folderGuid,$start,$limit);
		
		$a['folderGuid'] = $folderGuid;
		$a['totalCount'] = $modelCatalog->getWartaCount($folderGuid);
		
		$ii = 0;
		
		if ($a['totalCount']!=0)
		{
			foreach ($rowset as $row)
			{
				$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
				
				//$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
				//$rowSumComment = $tblRelatedItem->getSumComment($row->getId(), "RELATED_COMMENT");
			
				$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
				$rowSumComment = $modelComment->getParentCommentCount($row->getId());
			
				$a['index'][$ii]['title'] 		= $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
				$a['index'][$ii]['shortTitle']	= $row->getShortTitle();
				$a['index'][$ii]['createdDate']	= date("d/m/y",strtotime($row->getCreatedDate()));
				$a['index'][$ii]['guid']		= $row->getId();
				$a['index'][$ii]['comment']		= ($rowSumComment <> 0)? '('.$rowSumComment.'&nbsp;tanggapan)' : '';
				$ii++;
			}
		}
		if ($a['totalCount'] == 0)
		{
			$a['index'][0]['title'] = "-";
			$a['index'][0]['shortTitle'] = "-";
			$a['index'][0]['createdDate'] = "-";
			$a['index'][0]['guid'] = "-";
			$a['index'][0]['comment'] = "-";
		}
		echo Zend_Json::encode($a);		
	}
	
	/**
	 * @todo testing indexwarta untuk Info, Aktual
	 */
	function indexwartawcomAction()
	{
		$time_start = microtime(true);
		
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		$data = array();
		
		$num_rows = $modelCatalog->getWartaCount($folderGuid);
		$limit = 50;
		
		$data['folderGuid'] = $folderGuid;
		$data['totalCount'] = $num_rows;
		$data['totalCountRows'] = $num_rows;
		$data['limit'] = $limit;
		
		$this->view->aData = $data;
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->view->time = round($time,2) . ' detik';
	}
	function detailIndexWartaWComAction()
	{
		$this->_helper->layout->disableLayout();
		
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		$start		= ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$limit		= ($this->_getParam('limit'))? $this->_getParam('limit') : 0;
		
		$a = array();
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		//$rowset = $decorator->fetchFromFolderAsEntity($folderGuid,$start,$limit,'fg_'.$folderGuid.'_detail_iwarta_s_'.$start.'_e_'.$limit);
		$rowset = $decorator->fetchFromFolderAsEntity($folderGuid,$start,$limit);
		
		$a['folderGuid'] = $folderGuid;
		$a['totalCount'] = $modelCatalog->getWartaCount($folderGuid);
		
		$ii = 0;
		
		if ($a['totalCount']!=0)
		{
			foreach ($rowset as $row)
			{
				$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
				
				$a['indexcom'][$ii]['title'] 		= $modelCatalogAttribute->getCatalogAttributeValue($row->getId(),'fixedTitle');
				$a['indexcom'][$ii]['shortTitle']	= $row->getShortTitle();
				$a['indexcom'][$ii]['createdDate']	= date("d/m/y",strtotime($row->getCreatedDate()));
				$a['indexcom'][$ii]['guid']			= $row->getId();
				$ii++;
			}
		}
		if ($a['totalCount'] == 0)
		{
			$a['indexcom'][0]['title'] = "-";
			$a['indexcom'][0]['shortTitle'] = "-";
			$a['indexcom'][0]['createdDate'] = "-";
			$a['indexcom'][0]['guid'] = "-";
		}
		echo Zend_Json::encode($a);		
	}
}
?>