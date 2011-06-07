<?php

class Services_RelationController extends Zend_Controller_Action 
{
	function fetchDocumentAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$hTitle = new Pandamp_Controller_Action_Helper_GetCatalogTitle();
		$hType = new Pandamp_Controller_Action_Helper_GetCatalogDocType();
		$hSize = new Pandamp_Controller_Action_Helper_GetCatalogDocSize();
		
		$query = "SELECT * FROM `KutuCatalogAttribute`, `KutuRelatedItem` t2 WHERE `KutuCatalogAttribute`.catalogGuid=t2.itemGuid AND t2.relateAs IN ('RELATED_FILE','RELATED_IMAGE','RELATED_VIDEO') AND t2.relatedGuid='$catalogGuid' AND `KutuCatalogAttribute`.attributeGuid = 'docViewOrder' ORDER BY `KutuCatalogAttribute`.value ASC";
		$db = Zend_Db_Table::getDefaultAdapter()->query($query);
		
		$rowsetRelatedItem = $db->fetchAll(Zend_Db::FETCH_OBJ);
		
		$a = array();
		$a['totalCount'] = count($rowsetRelatedItem);
		$i = 0;
		
		if ($a['totalCount'] != 0)
		{
			foreach ($rowsetRelatedItem as $row)
			{
				$a['document'][$i]['guid']			= $row->guid;
				$a['document'][$i]['itemGuid']		= $row->itemGuid;
				$a['document'][$i]['fixedTitle']	= $hTitle->getCatalogTitle($row->itemGuid);
				$a['document'][$i]['docType']		= $hType->GetCatalogDocType($row->itemGuid);
				$a['document'][$i]['docSize']		= $hSize->GetCatalogDocSize($row->itemGuid);
				$a['document'][$i]['docViewOrder']	= $row->value;
				$a['document'][$i]['relatedGuid'] 	= $row->relatedGuid; 
				$i++;
			}
		}
		if ($a['totalCount']==0)
		{
			$a['document'][0]['guid'] = '';
			$a['document'][0]['itemGuid'] = "";
			$a['document'][0]['fixedTitle'] = "";
			$a['document'][0]['docType'] = '';
			$a['document'][0]['docSize'] = '';
			$a['document'][0]['docViewOrder'] = '';
			$a['document'][0]['relatedGuid'] = '';
		}
		
		echo Zend_Json::encode($a);
		
	}
}

?>