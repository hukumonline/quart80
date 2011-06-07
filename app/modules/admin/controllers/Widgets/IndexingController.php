<?php

class Admin_Widgets_IndexingController extends Kutu_Controller_Action 
{
	const CONTEXT_JSON = 'json';
	
    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
    	$contextSwitch = $this->_helper->contextSwitch();
    	
        $contextSwitch->addActionContext('indexing-catalog',  self::CONTEXT_JSON)
        			  ->addActionContext('delete', self::CONTEXT_JSON)
        			  ->addActionContext('empty', self::CONTEXT_JSON)
//        			  ->addActionContext('index-all', self::CONTEXT_JSON)
//        			  ->addActionContext('index-temp-all', self::CONTEXT_JSON)
        			  ->addActionContext('cekstatus',self::CONTEXT_JSON)
        			  ->addActionContext('change-status',self::CONTEXT_JSON)
                      ->initContext();
    }
    function deleteAction()
    {
    	$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
    	
    	//$log = new Kutu_Log();
    	
    	$tblIndexTmp = new Pandamp_Modules_Extension_Index_Model_TmpIndex();
    	try {
    		$tblIndexTmp->delete("guid='$catalogGuid'");
    		
    		//$log->info("Delete ".$catalogGuid." successfully");
    		
    		$this->view->success = true;
    		$this->view->message = "Delete successfully";
    	}
    	catch (Exception $e)
    	{
    		$log->warn($e->getMessage());
    		$this->view->success = false;
    		$this->view->message = $e->getMessage();
    	}
    }
    function emptyAction()
    {
    	//$log = new Kutu_Log();
    	
    	//$registry = Zend_Registry::getInstance();
    	//$conf = $registry->get('config');
    	
		$solr = new Apache_Solr_Service( 'localhost','8983','/solr/core0' );
		
		$solr->deleteByQuery('*:*');
		$solr->commit();
		
		//$log->info("Indexing empty successfully");
		
		$this->view->success = true;
    }
	function indexingCatalogAction()
	{
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$catalogGuid = ($this->_getParam('catalogGuid'))? $this->_getParam('catalogGuid') : '';
		
		$solrAdapter = Pandamp_Search::manager();				
		
		try {
			$solrAdapter->indexCatalog($catalogGuid);
			
			$tblTmpIndex = new Pandamp_Modules_Extension_Index_Model_TmpIndex();
			$tblTmpIndex->delete("guid='$guid'");
			
			$auth = Zend_Auth::getInstance();
			if ($auth->hasIdentity())
				$username = ' by '.$auth->getIdentity()->username;
			else 
				$username = ' by system';
				
			// log to assetSetting
			$tblAssetSetting = new Pandamp_Modules_Dms_Catalog_Model_AssetSetting();
			$rowAsset = $tblAssetSetting->fetchRow("application='INDEX CATALOG'");
			if ($rowAsset)
			{
				$rowAsset->valueText = "Update indexing-catalog at ".date("d-m-Y H:i:s").$username;
				$rowAsset->valueInt = $rowAsset->valueInt + 1;			
			}
			else 
			{
				$gman = new Pandamp_Core_Guid();
				$catalogGuid = $gman->generateGuid();
				$rowAsset = $tblAssetSetting->fetchNew();	
				$rowAsset->guid = $catalogGuid;
				$rowAsset->application = "INDEX CATALOG";
				$rowAsset->part = "KUTU";
				$rowAsset->valueType = "INDEX";
				$rowAsset->valueInt = 0;
				$rowAsset->valueText = "Indexing catalog at ".date("d-m-Y H:i:s").$username;
			}
			$rowAsset->save();
	
			$this->view->success = true;
		}
		catch (Exception $e)
		{
			$this->view->success = false;
		}
	}
	function indexAction() {}
	
	function cekstatusAction()
	{
		$tblSetting = new Kutu_Core_Orm_Table_Setting();
		$rowset = $tblSetting->find(1)->current();
		if ($rowset)
		{
			if ($rowset->status == 0) $this->view->success = true;
		}
		else 
		{
			$this->view->success = false;
		}
	}
	function changeStatusAction()
	{
		$status = ($this->_getParam('status'))? $this->_getParam('status') : '';
		
		switch ($status)
		{
			case 'online':
				$status = 0;
			break;
			case 'offline':
				$status = 1;
			break;
		}
		
		$tblSetting = new Pandamp_Modules_Misc_Setting_Model_Setting();
		$rowset = $tblSetting->find(1)->current();
		if ($rowset)
		{
			$rowset->status = $status;
			$rowset->save();
			$this->view->success = true;
		}
		else 
		{
			$this->view->success = false;
		}
	}
//	function indexTempAllAction()
//	{
//		$profileGuid = ($this->_getParam('profile'))? $this->_getParam('profile') : '';
//		
//		$formater = new Kutu_Lib_Formater();
//		
//		$tblCatalog = new Kutu_Core_Orm_Table_Catalog();
//		
//		$tblTempIndex = new Pandamp_Modules_Extension_Index_Model_TmpIndex();
//		
//		//start indexing
//		//if(file_exists())
//		//TODO should add variable in config.ini that basically state that if the indexing will be done by cron job or not
//		$registry = Zend_Registry::getInstance(); 
//		$conf = $registry->get('config');
//		
//		switch ($profileGuid)
//		{
//			case 'peraturan':
//				
//				$rowTempIndex = $tblTempIndex->fetchAll("profileGuid IN ('kutu_peraturan','kutu_putusan','kutu_peraturan_kolonial','kutu_rancangan_peraturan')");
//				
//				$solrAdapter = Kutu_Search_Engine::factory($conf->indexing->adapter,array(
//					'host'		=> $conf->indexing->host, 
//					'port'		=> $conf->indexing->port,
//					'homedir'	=> $conf->indexing->dir));
//				
//			break;
//			case 'berita':
//				
//				$rowTempIndex = $tblTempIndex->fetchAll("profileGuid IN ('aktual','suratpembaca','komunitas','news','talks','resensi','isuhangat','fokus','kolom','tokoh','jeda','tajuk','info','utama')");
//				
//				$solrAdapter = Kutu_Search_Engine::factory('solr',array('host' => 'localhost', 'port' => '8983', 'homedir' => '/solr/core1'));
//			
//			break;
//		}
//		
//		if (count($rowTempIndex)>0)
//		{
//			foreach ($rowTempIndex as $rowIndex)
//			{
//				$index[] = $rowIndex->catalogGuid;
//				$guidIndex[] = $rowIndex->guid;
//			}
//			
//			try {
//				
//				$solrAdapter->reIndexCatalog(array($index), TRUE);
//				
//				$guidIndex = $formater->implode_with_keys(", ", $guidIndex, "'");
//				$rowCleanIndex = $tblTempIndex->fetchAll("guid IN ($guidIndex)");
//				foreach ($rowCleanIndex as $rowClean)
//				{
//					$rowClean->delete();
//				}
//				
//				$this->view->success = true;
//			}
//			catch (Exception $e)
//			{
//				$this->view->success = false;
//			}
//		}
//		else 
//		{
//			$this->view->success = false;
//			$this->view->error = "No data";
//		}
//	}
}

?>