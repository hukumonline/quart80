<?php
class Admin_Dms_FiledownloaderController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity())
		{
			$sReturn = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$sReturn = base64_encode($sReturn);
			
			$identity = Pandamp_Application::getResource('identity');
			$loginUrl = $identity->loginUrl;
			
			$this->_redirect($loginUrl.'?returnTo='.$sReturn);     
			
			//$this->_redirect(ROOT_URL.'/helper/synclogin/generate/?returnTo='.$sReturn);
		}
		else 
		{
			// [TODO] else: check if user has access to admin page
			$username = $auth->getIdentity()->username;
			$this->view->username = $username;
			
			$acl = Pandamp_Acl::manager();
			$aReturn = $acl->getUserGroupIds($username);
			
			if (isset($aReturn[1]))
			{
				if (($aReturn[1] !== "Master") && ($aReturn[1] !== "Super Admin") && ($aReturn[1] !== "Dc Admin") && ($aReturn[1] !== "Dc Editor") && ($aReturn[1] !== "Dc Coordinator") && 
					($aReturn[1] !== "News Admin") && ($aReturn[1] !== "News Editor") && ($aReturn[1] !== "Marketing"))
					{
					$this->_helper->redirector('restricted', "error", 'admin');
				}
			}
			
			// [TODO] else: check if user has access to admin page and status website is online
			$tblSetting = new Pandamp_Modules_Misc_Setting_Model_Setting();
			$rowset = $tblSetting->find(1)->current();
			
			if ($rowset)
			{
				if ($rowset->status == 1)
				{
					// it means that user offline other than admin
					if (isset($aReturn[1]))
					{
						//if (($aReturn[1] !== "admin"))
						if (($aReturn[1] !== "Master") && ($aReturn[1] !== "Super Admin"))
						{
							$this->_forward('temporary','error','admin'); 
						}
					}
				}
				else 
				{
					return;
				}
			}
		}
	}
	function browseAction()
	{
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		$this->traverseFolderDowload($folderGuid,'',0);
	}
	function traverseFolderDowload($folderGuid, $sGuid, $level)
	{
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowSet = $tblFolder->fetchChildren($folderGuid);
		
		if ($level == 0) {
		$row = $tblFolder->find($folderGuid)->current();
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rows = $tblCatalog->downloadCatalog($row->guid);
		if (count($rows)) {
			foreach ($rows as $rowset) 
			{
		    	$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		    	$rowsetCatalog = $tblCatalog->find($rowset->itemGuid);
				if(count($rowsetCatalog))
				{
					$rowCatalog = $rowsetCatalog->current();
					$rowsetCatAtt = $rowCatalog->findDependentRowsetCatalogAttribute();
					
			    	$contentType = $rowsetCatAtt->findByAttributeGuid('docMimeType')->value;
					$filename = $systemname = $rowsetCatAtt->findByAttributeGuid('docSystemName')->value;
					$oriName = $rowsetCatAtt->findByAttributeGuid('docOriginalName')->value;
					
					$parentGuid = $rowset->relatedGuid;
					$sDir1 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$systemname;
					$sDir2 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$parentGuid.DIRECTORY_SEPARATOR.$systemname;
					
					//$c = ROOT_DIR.'/data/download/file';
					//echo $parentGuid.'<br>';
					if(file_exists($sDir1))
					{
						//header("Content-type: $contentType");
						//header("Content-Disposition: attachment; filename=$oriName");
						//@readfile($sDir2);
						//exec('xcopy '.$sDir1.' d:\\www\holmp\data\download\file /e/i'); 
						exec('wget -P D:\www\holmp\data\download\file '.$sDir1);  
					}
					if(file_exists($sDir2))
					{
						//exec('xcopy '.$sDir2.' d:\\www\holmp\data\download\file /e/i'); 
						exec('wget -P D:\www\holmp\data\download\file '.$sDir2);  
					}
					
				}
			}
		}
		}//en level 0
		
		$sGuid = '';
		
		foreach($rowSet as $row)
		{
//			$sTab = '';
			for($i=0;$i<$level;$i++)
//				$sTab .= '-';
			
				
			$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			$rows = $tblCatalog->downloadCatalog($row->guid);

			if (count($rows)) {
				foreach ($rows as $rowset) 
				{
			    	$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			    	$rowsetCatalog = $tblCatalog->find($rowset->itemGuid);
					if(count($rowsetCatalog))
					{
						$rowCatalog = $rowsetCatalog->current();
						$rowsetCatAtt = $rowCatalog->findDependentRowsetCatalogAttribute();
						
				    	$contentType = $rowsetCatAtt->findByAttributeGuid('docMimeType')->value;
						$filename = $systemname = $rowsetCatAtt->findByAttributeGuid('docSystemName')->value;
						$oriName = $rowsetCatAtt->findByAttributeGuid('docOriginalName')->value;
						
						$parentGuid = $rowset->relatedGuid;
						$sDir1 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$systemname;
						$sDir2 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$parentGuid.DIRECTORY_SEPARATOR.$systemname;
						
						//$c = ROOT_DIR.'/data/download/file';
						if(file_exists($sDir1))
						{
							//header("Content-type: $contentType");
							//header("Content-Disposition: attachment; filename=$oriName");
							//@readfile($sDir2);
							//exec('xcopy '.$sDir1.' d:\\www\holmp\data\download\file /e/i'); 
							exec('wget -P D:\www\holmp\data\download\file '.$sDir1);  
						}
						if(file_exists($sDir2))
						{
							//exec('xcopy '.$sDir2.' d:\\www\holmp\data\download\file /e/i'); 
							exec('wget -P D:\www\holmp\data\download\file '.$sDir2);  
						}
						
					}
				}
			}
			
			
			$sGuid .= $option . $this->traverseFolderDowload($row->guid, '', $level+1);
			
		}
//		return $sGuid;
	}
	function _traverseFolderDowload($folderGuid, $sGuid, $level)
	{
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowSet = $tblFolder->fetchChildren($folderGuid);
		$row = $tblFolder->find($folderGuid)->current();
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rows = $tblCatalog->downloadCatalog($row->guid);
		if (count($rows)) {
			foreach ($rows as $rowset) 
			{
		    	$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		    	$rowsetCatalog = $tblCatalog->find($rowset->itemGuid);
				if(count($rowsetCatalog))
				{
					$rowCatalog = $rowsetCatalog->current();
					$rowsetCatAtt = $rowCatalog->findDependentRowsetCatalogAttribute();
					
			    	$contentType = $rowsetCatAtt->findByAttributeGuid('docMimeType')->value;
					$filename = $systemname = $rowsetCatAtt->findByAttributeGuid('docSystemName')->value;
					$oriName = $rowsetCatAtt->findByAttributeGuid('docOriginalName')->value;
					
					$parentGuid = $rowset->relatedGuid;
					$sDir1 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$systemname;
					$sDir2 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$parentGuid.DIRECTORY_SEPARATOR.$systemname;
					
					//$c = ROOT_DIR.'/data/download/file';
					echo $parentGuid.'<br>';
//					if(file_exists($sDir1))
//					{
						//header("Content-type: $contentType");
						//header("Content-Disposition: attachment; filename=$oriName");
						//@readfile($sDir2);
//						exec('xcopy '.$sDir1.' d:\\www\holmp\data\download\file /e/i'); 
//					}
//					if(file_exists($sDir2))
//					{
//						exec('xcopy '.$sDir2.' d:\\www\holmp\data\download\file /e/i'); 
//					}
					
				}
			}
		}
				
	}
}

?>