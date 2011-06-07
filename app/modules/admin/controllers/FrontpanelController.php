<?php

class Admin_FrontpanelController extends Zend_Controller_Action 
{
	function panelAction() {}
	function panelArticleAction()
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchFromFolder('lt498d49d30c8c1'); // article
		
		$num_rows = count($rowset);
		
		$this->view->TotalArticle = $num_rows;
	}
	function panelDmsAction()
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		$rowCatalog = $tblCatalog->fetchAll("profileGuid IN ('kutu_peraturan','kutu_putusan','kutu_peraturan_kolonial','kutu_rancangan_peraturan')");
		$rowDoc		= $tblCatalog->fetchAll("profileGuid='kutu_doc'");
		
		$num_rows_catalog 	= count($rowCatalog);
		$num_rows_doc		= count($rowDoc);
		
		$this->view->totalCatalog 	= $num_rows_catalog;
		$this->view->totalDoc		= $num_rows_doc;
	}
	function panelMembershipAction()
	{
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		
		$rowInActive 	= $tblUser->fetchAll("isActive=0");
		$rowActive		= $tblUser->fetchAll("isActive=1");
		
		$num_rows_active 	= count($rowActive);
		$num_rows_inactive	= count($rowInActive);
		
		$this->view->active		= $num_rows_active;
		$this->view->inactive	= $num_rows_inactive;
	}
	function panelClinicAction()
	{
//		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
//		
//		$rowsetNewQuestion 		= $tblCatalog->fetchAll("profileGuid='klinik' and status=0");
//		$num_rows_newquestion 	= count($rowsetNewQuestion);
//		
//		$this->view->numDocsOfNewQuestion = $num_rows_newquestion;
//		
//		$rowsetPublished	= $tblCatalog->fetchAll("profileGuid='klinik' and status=1");
//		$num_rows_published	= count($rowsetPublished);
//		
//		$this->view->numDocsOfPublished = $num_rows_published;
//		
//   		$db = Zend_Db_Table::getDefaultAdapter()->query
//    		("SELECT KutuCatalogAttribute.catalogGuid as guid, value
//				FROM KutuCatalogAttribute
//				WHERE KutuCatalogAttribute.attributeGuid = 'fixedAnswer' 
//				and KutuCatalogAttribute.catalogGuid IN 
//				(select KutuCatalog.guid from KutuCatalog,KutuCatalogFolder where
//				KutuCatalog.guid=KutuCatalogFolder.catalogGuid) AND KutuCatalogAttribute.value<>''");
//    		
//    		
//   		$rowset = $db->fetchAll(Zend_Db::FETCH_OBJ);
//		
//   		$num_rows_answer = count($rowset);
//   		
//   		$this->view->numberOfRowsAnswer = $num_rows_answer;
	}
	function panelCommentAction()
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchAll("profileGuid='comment' and status=0");
		
		$num_rows = count($rowset);
		
		$this->view->numDocs = $num_rows;
	}
	function panelIndexAction()
	{
		$tblAssetSetting = new Pandamp_Modules_Dms_Catalog_Model_AssetSetting();
		$rowAsset = $tblAssetSetting->fetchRow("application='INDEX CATALOG'");
		if ($rowAsset) {
			$this->view->valueText = $rowAsset->valueText;
		}
		else 
		{
			$this->view->valueText = 'None';
		}
	}
}

?>