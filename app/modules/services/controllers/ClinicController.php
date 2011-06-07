<?php

class Services_ClinicController extends Zend_Controller_Action 
{
	public function fetchClinicAction()
	{
		$start 		= ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end 		= ($this->_getParam('limit'))? $this->_getParam('limit') : 10;
		$status		= ($this->_getParam('status'))? $this->_getParam('status') : 0;
		
		//$folderGuid	= 'lt4a0a533e31979'; // Klinik
		
		$tblCatalog 	= new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		$rowset = $tblCatalog->fetchFromFolderAdminClinic($status,$start,$end);
			
		$a = array();
		
		$a['totalCount'] = $tblCatalog->countCatalogsInFolderClinic($status);
		
		$now = date('Y-m-d H:i:s');
		
		$ii = 0;
		
		if ($a['totalCount']!=0) {
			foreach ($rowset as $row)
			{
				$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
				
				$a['clinic'][$ii]['guid']= $row->guid;
				$a['clinic'][$ii]['title']= $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedCommentTitle');
				$a['clinic'][$ii]['question']= $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedCommentQuestion');
				$a['clinic'][$ii]['content']= $modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedContent');
				
				$findPartner = $tblCatalog->getCatalogByGuid($modelCatalogAttribute->getCatalogAttributeValue($row->guid,'fixedSelect'));
				if ($findPartner)
					$a['clinic'][$ii]['partner']= $modelCatalogAttribute->getCatalogAttributeValue($findPartner->guid,'fixedTitle');
				
				$a['clinic'][$ii]['createdby'] = $row->createdBy;
				$a['clinic'][$ii]['modifiedby'] = $row->modifiedBy;
				$a['clinic'][$ii]['createdDate']= Pandamp_Lib_Formater::get_date($row->createdDate);
				
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
				else if ($row->status == 1) {
					$status = "ruby";
				}
				else if ($row->status == 2) {
					$status = "pill";
				}
				else if ($row->status == -1) {
					$status = "disabled";
				}
				
				$a['clinic'][$ii]['status'] = $status;
				$ii++;				
			}
		}
		
		if ($a['totalCount']==0)
		{
			$a['clinic'][0]['guid'] = 'XXX';
			$a['clinic'][0]['title'] = "No Data";
			$a['clinic'][0]['question'] = "-";
			$a['clinic'][0]['createdDate'] = '';
		}
		
		echo Zend_Json::encode($a);
	}
}

?>