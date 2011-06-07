<?php
class Hold_Widgets_FolderController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
	}
	function kategoriAction()
	{
		$parentGuid = ($this->_getParam('node'))? $this->_getParam('node') : 'lt49714f3105801';
		
		$columns = 2;
		
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowsetFolder = $tblFolder->fetchAll("parentGuid = '$parentGuid' AND NOT parentGuid=guid","title ASC");
		
		$num_rows = count($rowsetFolder);
		$rows = ceil($num_rows/$columns);
		
		if($num_rows < 3)
			$columns = $num_rows;
		if($num_rows==0)
		{
			
		}
		
		$folder = 0;
		$data = array();
		
		foreach ($rowsetFolder as $rowFolder)
		{
			$data[$folder][0] = $rowFolder->title;
			$data[$folder][1] = $rowFolder->description;
			$data[$folder][2] = $rowFolder->guid;
			$folder++;
		}
		
		$this->view->rows = $rows;
		$this->view->columns = $columns;
		$this->view->data = $data;
		$this->view->numberOfFolders = $num_rows;
		$this->view->node = $parentGuid;
		
	}
	function peraturanAction()
	{
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		
		if ($this->_getParam('node')) {
			$parentGuid = $this->_getParam('node');
			$rowsetFolder = $tblFolder->fetchAll("parentGuid = '$parentGuid' AND NOT parentGuid=guid","title ASC");
		} else {
			$parentGuid = 'lt49c706c641081';	
			$rowsetFolder = $tblFolder->fetchAll("parentGuid = '$parentGuid' AND NOT parentGuid=guid","title ASC");
		}
		
		$columns = 2;
		
		$num_rows = count($rowsetFolder);
		$rows = ceil($num_rows/$columns);
		
		if($num_rows < 3)
			$columns = $num_rows;
		if($num_rows==0)
		{
			
		}
		
		$folder = 0;
		$data = array();
		
		foreach ($rowsetFolder as $rowFolder)
		{
			$data[$folder][0] = $rowFolder->title;
			$data[$folder][1] = $rowFolder->description;
			$data[$folder][2] = $rowFolder->guid;
			$folder++;
		}
		
		$this->view->rows = $rows;
		$this->view->columns = $columns;
		$this->view->data = $data;
		$this->view->numberOfFolders = $num_rows;
		$this->view->node = $parentGuid;
		
	}
	function putusanAction()
	{
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		
		if ($this->_getParam('node')) {
			$parentGuid = $this->_getParam('node');
			$rowsetFolder = $tblFolder->fetchAll("parentGuid = '$parentGuid' AND NOT parentGuid=guid","title ASC");
		} else {
			$parentGuid = 'lt49c7077cce4ce';	
			$rowsetFolder = $tblFolder->fetchAll("parentGuid = '$parentGuid' AND NOT parentGuid=guid","title ASC");
		}
		
		$columns = 1;
		 
		$num_rows = count($rowsetFolder);
		$rows = ceil($num_rows/$columns);
		
		if($num_rows < 3)
			$columns = $num_rows;
		if($num_rows==0)
		{
			
		}
		
		$folder = 0;
		$data = array();
		
		foreach ($rowsetFolder as $rowFolder)
		{
			$data[$folder][0] = $rowFolder->title;
			$data[$folder][1] = $rowFolder->description;
			$data[$folder][2] = $rowFolder->guid;
			$folder++;
		}
		
		$this->view->rows = $rows;
		$this->view->columns = $columns;
		$this->view->data = $data;
		$this->view->numberOfFolders = $num_rows;
		$this->view->node = $parentGuid;
		
	}
	function viewCatalogsAction()
	{
		$time_start = microtime(true);
		
    	$folderGuid = ($this->_getParam('node'))? $this->_getParam('node') : 'root';
		
		if($folderGuid=='root')
		{	
			$a = array();
			$a['totalCount'] = 0;
			$a['totalCountRows'] = 0;
			$a['folderGuid'] = $folderGuid;
			$limit = 20;
			$a['limit'] = $limit;
		}
		else 
		{
			$a = array();
			$a['folderGuid'] = $folderGuid;

    		$db = Zend_Db_Table::getDefaultAdapter()->query
    		("SELECT guid from KutuCatalog, KutuCatalogFolder where guid=catalogGuid AND folderGuid='$folderGuid'");
    		
    		$rowset = $db->fetchAll(Zend_Db::FETCH_OBJ);
    		
			$a['totalCount'] = count($rowset);	
			$a['totalCountRows'] = count($rowset);	
    		$limit = 20;
			$a['limit'] = $limit;
		}
			
		$this->view->aData = $a;
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->view->time = round($time,2) . ' detik';
	}
	function viewCatalogsPeraturanAction()
	{
		$time_start = microtime(true);
		
    	$folderGuid = ($this->_getParam('node'))? $this->_getParam('node') : 'root';
		
		if($folderGuid=='root')
		{	
			$a = array();
			$a['totalCount'] = 0;
			$a['totalCountRows'] = 0;
			$a['folderGuid'] = $folderGuid;
			$limit = 20;
			$a['limit'] = $limit;
		}
		else 
		{
			$a = array();
			$a['folderGuid'] = $folderGuid;

    		$db = Zend_Db_Table::getDefaultAdapter()->query
    		("SELECT guid from KutuCatalog, KutuCatalogFolder where guid=catalogGuid AND folderGuid='$folderGuid'");
    		
    		$rowset = $db->fetchAll(Zend_Db::FETCH_OBJ);
    		
			$a['totalCount'] = count($rowset);	
			$a['totalCountRows'] = count($rowset);	
    		$limit = 20;
			$a['limit'] = $limit;
		}
			
		$this->view->aData = $a;
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->view->time = round($time,2) . ' detik';
	}
	function viewCatalogsPutusanAction()
	{
		$time_start = microtime(true);
		
    	$folderGuid = ($this->_getParam('node'))? $this->_getParam('node') : 'root';
		
		if($folderGuid=='root')
		{	
			$a = array();
			$a['totalCount'] = 0;
			$a['totalCountRows'] = 0;
			$a['folderGuid'] = $folderGuid;
			$limit = 20;
			$a['limit'] = $limit;
		}
		else 
		{
			$a = array();
			$a['folderGuid'] = $folderGuid;

    		$db = Zend_Db_Table::getDefaultAdapter()->query
    		("SELECT guid from KutuCatalog, KutuCatalogFolder where guid=catalogGuid AND folderGuid='$folderGuid'");
    		
    		$rowset = $db->fetchAll(Zend_Db::FETCH_OBJ);
    		
			$a['totalCount'] = count($rowset);	
			$a['totalCountRows'] = count($rowset);	
    		$limit = 20;
			$a['limit'] = $limit;
		}
			
		$this->view->aData = $a;
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->view->time = round($time,2) . ' detik';
	}
	function viewFolderNavigationAction()
	{
		$browserUrl = ROOT_URL . '/pusatdata/view/node';
		
		$folderGuid = ($this->_getParam('node'))? $this->_getParam('node') : 'root';
		
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		
    	$aPath = array();
    	
    	if($folderGuid == 'root')
    	{
    		$aPath[0]['title'] = 'Root';
    		$aPath[0]['url'] = $browserUrl;
    	}
    	else 
    	{
    		$rowFolder = $tblFolder->find($folderGuid)->current();
   			if (!isset($rowFolder)){
    			$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
    			$rowsetCatalogFolder = $tblCatalogFolder->fetchRow("catalogGuid='$folderGuid'");
    			if ($rowsetCatalogFolder) $rowFolder = $tblFolder->find($rowsetCatalogFolder->folderGuid)->current();
   			}
    		
    		if(!empty($rowFolder->path))
    		{
	    		$aFolderGuid = explode("/", $rowFolder->path);
	    		$sPath = 'root >';
	    		$aPath[0]['title'] = 'Root';
    			$aPath[0]['url'] = $browserUrl;
    			$i = 1;
	    		if(count($aFolderGuid))
	    		{
	    			$sPath1 = '';
	    			foreach ($aFolderGuid as $guid)
	    			{
	    				if(!empty($guid))
	    				{
	    					$rowFolder1 = $tblFolder->find($guid)->current();
	    				 	$sPath1 .= $rowFolder1->title . ' > ';
	    				 	$aPath[$i]['title'] = $rowFolder1->title . ' > ';
    						$aPath[$i]['url'] = $browserUrl.'/'.$rowFolder1->guid;
	    				 	$i++;
	    				}
	    			}
	    			
	    			$aPath[$i]['title'] = $rowFolder->title;
					$aPath[$i]['url'] = $browserUrl.'/'.$rowFolder->guid;
	    		}
	    		
    		}
    		else 
    		{
    			$aPath[0]['title'] = 'Root';
    			$aPath[0]['url'] = $browserUrl;
    			$aPath[1]['title'] = $rowFolder->title;
    			$aPath[1]['url'] = $browserUrl.'/'.$rowFolder->guid;
    		}
    	}
    	
    	$this->view->aPath = $aPath;
    	
	}
	function viewFolderNavigationPeraturanAction()
	{
		$browserUrl = ROOT_URL . '/pusatdata/view/nprt';
		
		$folderGuid = ($this->_getParam('nprt'))? $this->_getParam('nprt') : 'root';
		
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		
    	$aPath = array();
    	
    	if($folderGuid == 'root')
    	{
    		$aPath[0]['title'] = 'Root';
    		$aPath[0]['url'] = $browserUrl;
    	}
    	else 
    	{
    		$rowFolder = $tblFolder->find($folderGuid)->current();
    		
    		if(!empty($rowFolder->path))
    		{
	    		$aFolderGuid = explode("/", $rowFolder->path);
	    		$sPath = 'root >';
	    		$aPath[0]['title'] = 'Root';
    			$aPath[0]['url'] = $browserUrl;
    			$i = 1;
	    		if(count($aFolderGuid))
	    		{
	    			$sPath1 = '';
	    			foreach ($aFolderGuid as $guid)
	    			{
	    				if(!empty($guid))
	    				{
	    					$rowFolder1 = $tblFolder->find($guid)->current();
	    				 	$sPath1 .= $rowFolder1->title . ' > ';
	    				 	$aPath[$i]['title'] = $rowFolder1->title . ' > ';
    						$aPath[$i]['url'] = $browserUrl.'/'.$rowFolder1->guid;
	    				 	$i++;
	    				}
	    			}
	    			
	    			$aPath[$i]['title'] = $rowFolder->title;
					$aPath[$i]['url'] = $browserUrl.'/'.$rowFolder->guid;
	    		}
	    		
    		}
    		else 
    		{
    			$aPath[0]['title'] = 'Root';
    			$aPath[0]['url'] = $browserUrl;
    			$aPath[1]['title'] = $rowFolder->title;
    			$aPath[1]['url'] = $browserUrl.'/'.$rowFolder->guid;
    		}
    	}
    	
    	$this->view->aPath = $aPath;
    	
	}
	function viewFolderNavigationPutusanAction()
	{
		$browserUrl = ROOT_URL . '/pusatdata/view/npts';
		
		$folderGuid = ($this->_getParam('npts'))? $this->_getParam('npts') : 'root';
		
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		
    	$aPath = array();
    	
    	if($folderGuid == 'root')
    	{
    		$aPath[0]['title'] = 'Root';
    		$aPath[0]['url'] = $browserUrl;
    	}
    	else 
    	{
    		$rowFolder = $tblFolder->find($folderGuid)->current();
    		
    		if(!empty($rowFolder->path))
    		{
	    		$aFolderGuid = explode("/", $rowFolder->path);
	    		$sPath = 'root >';
	    		$aPath[0]['title'] = 'Root';
    			$aPath[0]['url'] = $browserUrl;
    			$i = 1;
	    		if(count($aFolderGuid))
	    		{
	    			$sPath1 = '';
	    			foreach ($aFolderGuid as $guid)
	    			{
	    				if(!empty($guid))
	    				{
	    					$rowFolder1 = $tblFolder->find($guid)->current();
	    				 	$sPath1 .= $rowFolder1->title . ' > ';
	    				 	$aPath[$i]['title'] = $rowFolder1->title . ' > ';
    						$aPath[$i]['url'] = $browserUrl.'/'.$rowFolder1->guid;
	    				 	$i++;
	    				}
	    			}
	    			
	    			$aPath[$i]['title'] = $rowFolder->title;
					$aPath[$i]['url'] = $browserUrl.'/'.$rowFolder->guid;
	    		}
	    		
    		}
    		else 
    		{
    			$aPath[0]['title'] = 'Root';
    			$aPath[0]['url'] = $browserUrl;
    			$aPath[1]['title'] = $rowFolder->title;
    			$aPath[1]['url'] = $browserUrl.'/'.$rowFolder->guid;
    		}
    	}
    	
    	$this->view->aPath = $aPath;
    	
	}
}
?>