<?php

class Admin_Widgets_RelationController extends Zend_Controller_Action
{
	public function documentAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		
		$where = "relatedGuid='$catalogGuid' AND relateAs IN ('RELATED_FILE','RELATED_IMAGE','RELATED_VIDEO')";
		$rowsetRelatedItem = $tblRelatedItem->fetchAll($where);
		
		$this->view->rowsRelatedItem = $rowsetRelatedItem;
		$this->view->catalogGuid = $catalogGuid;
	}
	public function viewfolderAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowCatalog = $tblCatalog->find($catalogGuid)->current();
		$rowsetFolder = $rowCatalog->findManyToManyRowset('Pandamp_Modules_Dms_Folder_Model_Folder', 'Pandamp_Modules_Dms_Catalog_Model_CatalogFolder');
		
		$this->view->rowsetFolder = $rowsetFolder;
		$this->view->catalogGuid = $catalogGuid;
	}
	public function peraturanpelaksanaAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		
		$where = "itemGuid='$catalogGuid' AND relateAs='RELATED_BASE'";
		$rowsetRelatedItem = $tblRelatedItem->fetchAll($where);
		
		$this->view->rowsetRelatedItem = $rowsetRelatedItem;
		$this->view->node = $folderGuid;
	}
	public function viewdasarhukumAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		
		$where = "relatedGuid='$catalogGuid' AND relateAs='RELATED_BASE'";
		$rowsetRelatedItem = $tblRelatedItem->fetchAll($where);
		
		$this->view->rowsetRelatedItem = $rowsetRelatedItem;
		$this->view->catalogGuid = $catalogGuid;
		$this->view->node = $folderGuid;
	}
	public function viewotherAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		
		$where = "relatedGuid='$catalogGuid' AND relateAs IN ('RELATED_OTHER','RELATED_ISSUE','RELATED_Clinic')";
		$rowsetRelatedItem = $tblRelatedItem->fetchAll($where);
		
		$this->view->rowsetRelatedItem = $rowsetRelatedItem;
	}
	public function viewsearchAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowCatalog = $tblCatalog->find($catalogGuid)->current();
		$rowsetCatalogAttribute = $rowCatalog->findDependentRowsetCatalogAttribute();
		
		$this->view->catalogTitle = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle')->value;
		$this->view->catalogGuid = $catalogGuid;
	}
	public function searchAction()
	{
		$query = ($this->_getParam('query'))? $this->_getParam('query') : '';
		$relatedGuid = ($this->_getParam('relatedGuid'))? $this->_getParam('relatedGuid') : '';
		
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query);
		
		$this->view->hits = $hits;
		$this->view->relatedGuid = $relatedGuid;
	}
	public function viewSejarahAction()
	{
		$catalogGuid	= ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$folderGuid		= ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$strRoot = $this->rootPeraturan($catalogGuid);
		
		$tblCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		
		$where = "catalogGuid='$strRoot' AND attributeGuid='fixedTitle'";
		$rowCatalogAttributeRoot = $tblCatalogAttribute->fetchRow($where);
		
		if (isset($rowCatalogAttributeRoot->value))
		{
			$strImage="<img src='../../view-resources/img/sejarah/hol_round.gif' align='absmiddle' WIDTH='15' HEIGHT='15'>&nbsp;";
			if ($rowCatalogAttributeRoot->catalogGuid == $catalogGuid)
			{
				$strPeraturan="";
			}
			else 
			{
				$strPeraturan=$strImage . $rowCatalogAttributeRoot->value . "<BR>";
			}
			
			$this->view->strRoot = $strPeraturan;
		}	
		else 
		{
			$this->view->strRoot = "";
		}
		
		$a2 = $a3 = array();
		$aNodesTraversed = array();
		$this->_traverseHistory($aNodesTraversed, $a2,$catalogGuid);
		
		$tblCatalogAttribute  = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$aTmp2['node'] = $catalogGuid;
		$aTmp2['nodeLeft'] = 'tmpLeft';
		$aTmp2['nodeRight'] =  'tmpRight';
		$aTmp2['description'] = '';
		$aTmp2['relationType'] = '';
		
		$where2 = "catalogGuid='$catalogGuid' AND attributeGuid='fixedTitle'";
		$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
		if(isset($rowCatalogAttribute->value))
			$aTmp2['title'] = $rowCatalogAttribute->value;
		else
			return 'No-Title';
			
		$where2 = "catalogGuid='$catalogGuid' AND attributeGuid='fixedSubTitle'";
		$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
		if(isset($rowCatalogAttribute->value))
			$aTmp2['subTitle'] = $rowCatalogAttribute->value;
		else
			return 'No-Title';
		
		$where2 = "catalogGuid='$catalogGuid' AND attributeGuid='fixedDate'";
		$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
		if(isset($rowCatalogAttribute->value))
			$aTmp2['fixedDate'] = $rowCatalogAttribute->value;
		else
			return '';
		
		array_push($a2, $aTmp2);
		
		UtilHistorySort::sort($a2, 'fixedDate', false);
		
		$a3 = array_shift($a2);
		// echo "<pre>";print_r($a2);echo "</pre>";
		
		$this->view->aData = $a2;
		$this->view->catalogGuid = $catalogGuid;
		$this->view->node = $folderGuid;
	}
	protected function rootPeraturan($vPeraturan)
	{
		$aTmp[] = $vPeraturan;
		
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		
		$where = "relatedGuid='$vPeraturan' AND relateAs='RELATED_HISTORY'";
		$rowsetRelatedItem = $tblRelatedItem->fetchAll($where);
		
		$aTmp = end($aTmp);
		
		foreach ($rowsetRelatedItem as $row)
		{
			$aTmp = $this->rootPeraturan($row->itemGuid);
			
		}
		
		return $aTmp;
	}
	public function viewhistoryAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$a2 = array();
		$aNodesTraversed = array();
		$this->_traverseHistory($aNodesTraversed, $a2,$catalogGuid);
		
		$tblCatalogAttribute  = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$aTmp2['node'] = $catalogGuid;
		$aTmp2['nodeLeft'] = 'tmpLeft';
		$aTmp2['nodeRight'] =  'tmpRight';
		$aTmp2['description'] = '';
		$aTmp2['relationType'] = '';
		
		$where2 = "catalogGuid='$catalogGuid' AND attributeGuid='fixedTitle'";
		$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
		if(isset($rowCatalogAttribute->value))
			$aTmp2['title'] = $rowCatalogAttribute->value;
		else
			return 'No-Title';
			
		$where2 = "catalogGuid='$catalogGuid' AND attributeGuid='fixedSubTitle'";
		$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
		if(isset($rowCatalogAttribute->value))
			$aTmp2['subTitle'] = $rowCatalogAttribute->value;
		else
			return 'No-Title';
		
		$where2 = "catalogGuid='$catalogGuid' AND attributeGuid='fixedDate'";
		$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
		if(isset($rowCatalogAttribute->value))
			$aTmp2['fixedDate'] = $rowCatalogAttribute->value;
		else
			return '';
		
		array_push($a2, $aTmp2);
		
		UtilHistorySort::sort($a2, 'fixedDate', false);
		
		$this->view->aData = $a2;
		$this->view->catalogGuid = $catalogGuid;
		$this->view->node = $folderGuid;
	}
	protected function _traverseHistory(&$aNodesTraversed, &$a2, $node)
	{
		array_push($aNodesTraversed, $node);
		$aTmp = $this->_getNodes($node);
		
		foreach ($aTmp as $node2)
		{
			if(!$this->_checkTraverse($aNodesTraversed, $node2['node']))
			{
				array_push($a2, $node2);
				$this->_traverseHistory($aNodesTraversed, $a2, $node2['node']);
			}
		}
		return true;
	}
	function _getNodes($node)
	{
		$a = array();
		
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		$tblCatalogAttribute  = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		
		$where = "relatedGuid='$node' AND relateAs='RELATED_HISTORY'";
		$rowsetRelatedItem = $tblRelatedItem->fetchAll($where);
		
		foreach ($rowsetRelatedItem as $row)
		{
			$aTmp2['node'] = $row->itemGuid;
			$aTmp2['nodeLeft'] = $row->itemGuid;
			$aTmp2['nodeRight'] =  $node;
			$aTmp2['description'] = $row->description;
			$aTmp2['relationType'] = $row->relationType;
			
			$where2 = "catalogGuid='$row->itemGuid' AND attributeGuid='fixedTitle'";
			$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
			if(isset($rowCatalogAttribute->value))
				$aTmp2['title'] = $rowCatalogAttribute->value;
			else
				$aTmp2['title'] = 'No-Title';
				
			$where2 = "catalogGuid='$row->itemGuid' AND attributeGuid='fixedSubTitle'";
			$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
			if(isset($rowCatalogAttribute->value))
				$aTmp2['subTitle'] = $rowCatalogAttribute->value;
			else
				$aTmp2['subTitle'] = 'No-Title';
			
			$where2 = "catalogGuid='$row->itemGuid' AND attributeGuid='fixedDate'";
			$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
			if(isset($rowCatalogAttribute->value))
				$aTmp2['fixedDate'] = $rowCatalogAttribute->value;
			else
				$aTmp2['fixedDate'] = '';
			
			array_push($a, $aTmp2);	
		}
		
		$where = "itemGuid='$node' AND relateAs='RELATED_HISTORY' AND relatedItemType >= 1";
		$rowsetRelatedItem = $tblRelatedItem->fetchAll($where);
		
		foreach ($rowsetRelatedItem as $row)
		{
			$aTmp2['node'] = $row->relatedGuid;
			$aTmp2['nodeLeft'] = $node;
			$aTmp2['nodeRight'] =  $row->relatedGuid;
			$aTmp2['description'] = $row->description;
			$aTmp2['relationType'] = $row->relationType;
			
			$where2 = "catalogGuid='$row->relatedGuid' AND attributeGuid='fixedTitle'";
			$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
			if(isset($rowCatalogAttribute->value))
				$aTmp2['title'] = $rowCatalogAttribute->value;
			else
				$aTmp2['title'] = 'No-Title';
				
			$where2 = "catalogGuid='$row->relatedGuid' AND attributeGuid='fixedSubTitle'";
			$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
			if(isset($rowCatalogAttribute->value))
				$aTmp2['subTitle'] = $rowCatalogAttribute->value;
			else
				$aTmp2['subTitle'] = 'No-Title';
			
			$where2 = "catalogGuid='$row->relatedGuid' AND attributeGuid='fixedDate'";
			$rowCatalogAttribute = $tblCatalogAttribute->fetchRow($where2); 
			if(isset($rowCatalogAttribute->value))
				$aTmp2['fixedDate'] = $rowCatalogAttribute->value;
			else
				$aTmp2['fixedDate'] = '';
			
			array_push($a, $aTmp2);	
		}
		return $a;
	}
	function _checkTraverse($a, $node)
	{
		foreach($a as $row)
		{
			if($row == $node)
			{
				return true;
			}
		}
		return false;
	}
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
	}
}

class UtilHistorySort 
{
    static private $sortfield = null;
    static private $sortorder = 1;
    static private function sort_callback(&$a, &$b) {
        if($a[self::$sortfield] == $b[self::$sortfield]) return 0;
        return ($a[self::$sortfield] < $b[self::$sortfield])? -self::$sortorder : self::$sortorder;
    }
    static function sort(&$v, $field, $asc=true) {
        self::$sortfield = $field;
        self::$sortorder = $asc? 1 : -1;
        usort($v, array('UtilHistorySort', 'sort_callback'));
    }
}
