<?php
class Hold_BrowserController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		$this->_helper->layout->setLayout('layout-pusatdata');
		$this->_helper->layout->setLayoutPath(array('layoutPath'=>ROOT_DIR.'/app/modules/hold/layouts'));
	}
	function viewAction()
	{
		$node = ($this->_getParam('node'))? $this->_getParam('node') : '';
		$npts = ($this->_getParam('npts'))? $this->_getParam('npts') : '';
		$nprt = ($this->_getParam('nprt'))? $this->_getParam('nprt') : '';
		$this->view->npts = $npts;
		$this->view->nprt = $nprt;
		$this->view->node = $node;
	}
	function pencaharianAction()
	{
		
	}
	function browseAction()
	{
		
	}
	function detailAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$node = ($this->_getParam('node'))? $this->_getParam('node') : '';
		$npts = ($this->_getParam('npts'))? $this->_getParam('npts') : '';
		$nprt = ($this->_getParam('nprt'))? $this->_getParam('nprt') : '';
		
		if ($node) $fd = $node;
		if ($npts) $fd = $npts;
		if ($nprt) $fd = $nprt;
		
		$sReturn = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$sReturn = base64_encode($sReturn);
			
		$identity = Pandamp_Application::getResource('identity');
		$loginUrl = $identity->loginUrl;
		//$loginUrl = ROOT_URL.'/helper/synclogin/generate/?returnTo='.$sReturn;
			
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		if (isset($rowset)) {
			$modelAsset = new Pandamp_Modules_Dms_Catalog_Model_AssetSetting();
			$rowAsset = $modelAsset->find($catalogGuid)->current();
				
			if ($rowAsset) {
				$rowAsset->valueInt = $rowAsset->valueInt + 1;
			}
			else 
			{
				$rowAsset = $modelAsset->fetchNew();	
				$rowAsset->guid = $catalogGuid;
				$rowAsset->detail = $fd;
				$rowAsset->application = $rowset->getProfile();
				$rowAsset->part = "MOST_READABLE_DATACENTER";
				$rowAsset->valueInt = 1;
				$rowAsset->valueText = 'pusatdata';
			}
					
			$rowAsset->save();
			
			$auth = Zend_Auth::getInstance();
			
			if ($rowset->getProfile() == 'kutu_putusan') {
				if (!$auth->hasIdentity()) {
					$this->_redirect($loginUrl);
				}
			}
					
			$rowsetCatalogAttributeJenis = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'prtJenis');
			if (!empty($rowsetCatalogAttributeJenis)) 
			{
				if (($rowsetCatalogAttributeJenis == 'Undang-Undang ') || ($rowsetCatalogAttributeJenis == "uu") || ($rowsetCatalogAttributeJenis == "pp") || ($rowsetCatalogAttributeJenis == "Peraturan Pemerintah") || ($rowsetCatalogAttributeJenis == "konstitusi"))
				{
				}
				else 
				{
					if (!$auth->hasIdentity()) {
						$this->_redirect($loginUrl.'?returnTo='.$sReturn);
						//$this->_redirect($loginUrl);
					}
					else
					{
						$username = $auth->getIdentity()->username;
						$acl = Pandamp_Acl::manager();
						$aReturn = $acl->getUserGroupIds($username);
						//print_r($aReturn[1]);die;
						if (isset($aReturn[0])) {
							
							if ($aReturn[0] == "member_gratis") {
								$this->_helper->redirector('restricted', "browser", 'hold');
							}
							
						}
					}
				}
			}
			
	    	$this->view->catalogGuid = $catalogGuid;
	    	$this->view->node = $node;
	    	$this->view->npts = $npts;
	    	$this->view->nprt = $nprt;
		}
	}
	function restrictedAction()
	{
		
	}
	function searchAction()
	{
		$as_q = ($this->_getParam('as_q'))? $this->_getParam('as_q') : '';
		$as_epq = ($this->_getParam('as_epq'))? $this->_getParam('as_epq') : '';
		$as_oq = ($this->_getParam('as_oq'))? $this->_getParam('as_oq') : '';
		$as_eq = ($this->_getParam('as_eq'))? $this->_getParam('as_eq') : '';
		$nomor = ($this->_getParam('nomor'))? $this->_getParam('nomor') : '';
		$tahun = ($this->_getParam('tahun'))? $this->_getParam('tahun') : '';
		$jenis_peraturan = ($this->_getParam('jenis_peraturan'))? $this->_getParam('jenis_peraturan') : '';
		$lembaga_peradilan = ($this->_getParam('lembaga_peradilan'))? $this->_getParam('lembaga_peradilan') : '';
		$hakim = ($this->_getParam('hakim'))? $this->_getParam('hakim') : '';
		$pihak = ($this->_getParam('pihak'))? $this->_getParam('pihak') : '';
		$pengacara = ($this->_getParam('pengacara'))? $this->_getParam('pengacara') : '';
		
		$query = ($this->_getParam('searchQuery'))? $this->_getParam('searchQuery') : '';
		
		// query 1
		if (isset($as_q))
		{
			$query .= $as_q;
		}
		
		// query 2
		if (($as_q) && ($as_epq) && ($as_oq))
		{
			$query .= ' '.$as_oq;
		}
		if (($as_q) && (!$as_epq) && ($as_oq))
		{
			$query .= ' '.$as_oq;
		}
		if ((!$as_q) && ($as_epq) && ($as_oq))
		{
			$query .= $as_oq.' ';
		}
		if ((!$as_q) && (!$as_epq) && ($as_oq))
		{
			$query .= $as_oq;
		}
		
		// query 3
		if (($as_epq) && ($as_q))
		{
			$query .= ' "'.$as_epq.'"';
		}
		if (($as_epq) && (!$as_q))
		{
			$query .= '"'.$as_epq.'"';
		}
		
		// query 4
		if (($as_epq) && ($as_q) && ($as_eq) && ($as_oq))
		{
			$query .= ' -'.$as_eq;
		}
		if ((!$as_epq) && ($as_q) && ($as_eq) && ($as_oq))
		{
			$query .= ' -'.$as_eq;
		}
		if (($as_epq) && (!$as_q) && ($as_eq) && ($as_oq))
		{
			$query .= ' -'.$as_eq;
		}
		if (($as_epq) && ($as_q) && (!$as_eq) && ($as_oq))
		{
			$query .= ' -'.$as_eq;
		}
		if ((!$as_epq) && (!$as_q) && ($as_eq) && ($as_oq))
		{
			$query .= ' -'.$as_eq;
		}
		if (($as_epq) && (!$as_q) && ($as_eq) && (!$as_oq))
		{
			$query .= ' -'.$as_eq;
		}
		if ((!$as_epq) && ($as_q) && ($as_eq) && (!$as_oq))
		{
			$query .= ' -'.$as_eq;
		}
		if ((!$as_epq) && (!$as_q) && ($as_eq) && (!$as_oq))
		{
			$query .= '-'.$as_eq;
		}
		if (($as_epq) && ($as_q) && ($as_eq) && (!$as_oq))
		{
			$query .= '-'.$as_eq;
		}
		
		if ($jenis_peraturan)
		{
			// query 5
			if (($as_epq) && ($as_q) && ($as_eq) && ($as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if (($as_epq) && (!$as_q) && ($as_eq) && ($as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if ((!$as_epq) && (!$as_q) && ($as_eq) && ($as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if ((!$as_epq) && (!$as_q) && ($as_eq) && (!$as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if ((!$as_epq) && (!$as_q) && (!$as_eq) && (!$as_oq) && ($jenis_peraturan))
			{
				$query .= 'regulationOrder:'.$jenis_peraturan;
			}
			if ((!$as_epq) && ($as_q) && (!$as_eq) && (!$as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if (($as_epq) && (!$as_q) && (!$as_eq) && (!$as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if ((!$as_epq) && (!$as_q) && (!$as_eq) && ($as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if (($as_epq) && ($as_q) && (!$as_eq) && (!$as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if ((!$as_epq) && ($as_q) && (!$as_eq) && ($as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if ((!$as_epq) && ($as_q) && ($as_eq) && (!$as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if (($as_epq) && (!$as_q) && (!$as_eq) && ($as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if (($as_epq) && (!$as_q) && ($as_eq) && (!$as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if (($as_epq) && ($as_q) && ($as_eq) && (!$as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			if ((!$as_epq) && ($as_q) && ($as_eq) && ($as_oq) && ($jenis_peraturan))
			{
				$query .= ' regulationOrder:'.$jenis_peraturan;
			}
			
			// query 7
			if ($nomor)
			{
				$query .= ' number:'.$nomor;
			}
			
			// query 8
			if ($tahun)
			{
				$query .= ' year:'.$tahun;
			}
			
		}
		
		if ($lembaga_peradilan)
		{
			// query 6
			if (($as_epq) && ($as_q) && ($as_eq) && ($as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if (($as_epq) && (!$as_q) && ($as_eq) && ($as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if ((!$as_epq) && (!$as_q) && ($as_eq) && ($as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if ((!$as_epq) && (!$as_q) && ($as_eq) && (!$as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if ((!$as_epq) && (!$as_q) && (!$as_eq) && (!$as_oq) && ($lembaga_peradilan))
			{
				$query .= 'regulationOrder:'.$lembaga_peradilan;
			}
			if ((!$as_epq) && ($as_q) && (!$as_eq) && (!$as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if (($as_epq) && (!$as_q) && (!$as_eq) && (!$as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if ((!$as_epq) && (!$as_q) && (!$as_eq) && ($as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if (($as_epq) && ($as_q) && (!$as_eq) && (!$as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if ((!$as_epq) && ($as_q) && (!$as_eq) && ($as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if ((!$as_epq) && ($as_q) && ($as_eq) && (!$as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if (($as_epq) && (!$as_q) && (!$as_eq) && ($as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if (($as_epq) && (!$as_q) && ($as_eq) && (!$as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if (($as_epq) && ($as_q) && ($as_eq) && (!$as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			if ((!$as_epq) && ($as_q) && ($as_eq) && ($as_oq) && ($lembaga_peradilan))
			{
				$query .= ' regulationOrder:'.$lembaga_peradilan;
			}
			
			// query 7
			if ($nomor)
			{
				$query .= ' number:'.$nomor;
			}
			
			// query 8
			if ($tahun)
			{
				$query .= ' year:'.$tahun;
			}
			
			// query 9
			if ($hakim)
			{
				$query .= ' all:'.$hakim;
			}
			
			// query 10
			if ($pihak)
			{
				$query .= ' all:'.$pihak;
			}
			
			// query 11
			if ($pengacara)
			{
				$query .= ' all:'.$pengacara;
			}
			
		}
		
		
		$this->_helper->layout()->searchQuery = $query;
		$this->view->query = $query;
	}
	function advancedsearchAction()
	{
		$query = ($this->_getParam('searchQuery'))? $this->_getParam('searchQuery') : '';
		
		$this->_helper->layout()->searchQuery = $query;
	}
	function downloadFileAction()
	{
    	$this->_helper->layout()->disableLayout();
    	
    	$catalogGuid = $this->_getParam('guid');
    	$parentGuid = $this->_getParam('parent');
    	
    	$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
    	$rowsetCatalog = $tblCatalog->find($catalogGuid);
    	
    	if(count($rowsetCatalog))
    	{
    		$auth = Zend_Auth::getInstance();
    		if ($auth->hasIdentity())
    		{
    			$guidUser = $auth->getIdentity()->kopel;
    		}
    		
    		$tblAsetSetting = new Pandamp_Modules_Dms_Catalog_Model_AssetSetting();
    		$rowAset = $tblAsetSetting->find($catalogGuid)->current();
    		if ($rowAset)
    		{
    			$rowAset->valueInt = $rowAset->valueInt + 1;
    		}
    		else 
    		{
    			$rowAset = $tblAsetSetting->fetchNew();
				$rowAset->guid = $catalogGuid;
				$rowAset->application = "kutu_doc";
				$rowAset->part = (isset($guidUser))? $guidUser : '';
				$rowAset->valueType = gethostbyaddr($_SERVER['REMOTE_ADDR']);
				$rowAset->valueInt = 1;
    		}
    		
    		$rowAset->save();
    		
    		$rowCatalog = $rowsetCatalog->current();
    		$rowsetCatAtt = $rowCatalog->findDependentRowsetCatalogAttribute();
    		
	    	$contentType = $rowsetCatAtt->findByAttributeGuid('docMimeType')->value;
			$filename = $systemname = $rowsetCatAtt->findByAttributeGuid('docSystemName')->value;
			$oriName = $oname = $rowsetCatAtt->findByAttributeGuid('docOriginalName')->value;
			
			$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
			$rowsetRelatedItem = $tblRelatedItem->fetchAll("itemGuid='$catalogGuid' AND relateAs='RELATED_FILE'");
			
		    $registry = Zend_Registry::getInstance();
		    $config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
		    $cdn = $config->getOption('cdn');
		    $ftp = $config->getOption('ftp');
		    
			$strServer = $ftp['remote']['server'];
			$strServerPort = $ftp['remote']['port'];
			$strServerUsername = $ftp['remote']['username'];
			$strServerPassword = $ftp['remote']['passwd'];
			
			//connect to server
			$resConnection = ssh2_connect($strServer, $strServerPort);
		
			$flagFileFound = false;
			
			foreach($rowsetRelatedItem as $rowRelatedItem)
			{
				if(!$flagFileFound)
				{
					$parentGuid = $rowRelatedItem->relatedGuid;
					$sDir1 = $cdn['remote']['dir']['files']."/".$systemname;
					$sDir2 = $cdn['remote']['dir']['files']."/".$parentGuid."/".$systemname;
					$sDir3 = $cdn['remote']['dir']['files']."/".$oname;
					$sDir4 = $cdn['remote']['dir']['files']."/".$parentGuid."/".$oname;
					
					if(ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword))
					{
						$resSFTP = ssh2_sftp($resConnection);
						
						if(file_exists("ssh2.sftp://{$resSFTP}".$sDir1))
						{
							$flagFileFound = true;
							header("Content-type: $contentType");
							header("Content-Disposition: attachment; filename=$oriName");
							@readfile("ssh2.sftp://{$resSFTP}".$sDir1);
							die();
						}
						else 
						{
							if(file_exists("ssh2.sftp://{$resSFTP}".$sDir2))
							{
								$flagFileFound = true;
								header("Content-type: $contentType");
								header("Content-Disposition: attachment; filename=$oriName");
								@readfile("ssh2.sftp://{$resSFTP}".$sDir2);
								die();
							}
							if(file_exists("ssh2.sftp://{$resSFTP}".$sDir3))
							{
								$flagFileFound = true;
								header("Content-type: $contentType");
								header("Content-Disposition: attachment; filename=$oriName");
								@readfile("ssh2.sftp://{$resSFTP}".$sDir3);
								die();
							}
							if(file_exists("ssh2.sftp://{$resSFTP}".$sDir4))
							{
								$flagFileFound = true;
								header("Content-type: $contentType");
								header("Content-Disposition: attachment; filename=$oriName");
								@readfile("ssh2.sftp://{$resSFTP}".$sDir4);
								die();
							}
							else 
							{
								$flagFileFound = false;
								$this->_forward('forbidden','browser','hold');
							}
						}
					
					
					} // end of ssh2
					
					
				}
			}
			
    	}
    	else 
    	{
    		$flagFileFound = false;
    		$this->_forward('forbidden','browser','hold');
    	}		
	}
    function downloadFile_OldAction()
    {
    	$this->_helper->layout()->disableLayout();
    	
    	$catalogGuid = $this->_getParam('guid');
    	$parentGuid = $this->_getParam('parent');
    	
    	$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
    	$rowsetCatalog = $tblCatalog->find($catalogGuid);
    	
    	if(count($rowsetCatalog))
    	{
    		$auth = Zend_Auth::getInstance();
    		if ($auth->hasIdentity())
    		{
    			$guidUser = $auth->getIdentity()->kopel;
    		}
    		
    		$tblAsetSetting = new Pandamp_Modules_Dms_Catalog_Model_AssetSetting();
    		$rowAset = $tblAsetSetting->find($catalogGuid)->current();
    		if ($rowAset)
    		{
    			$rowAset->valueInt = $rowAset->valueInt + 1;
    		}
    		else 
    		{
    			$rowAset = $tblAsetSetting->fetchNew();
				$rowAset->guid = $catalogGuid;
				$rowAset->application = "kutu_doc";
				$rowAset->part = (isset($guidUser))? $guidUser : '';
				$rowAset->valueType = gethostbyaddr($_SERVER['REMOTE_ADDR']);
				$rowAset->valueInt = 1;
    		}
    		
    		$rowAset->save();
    		
    		$rowCatalog = $rowsetCatalog->current();
    		$rowsetCatAtt = $rowCatalog->findDependentRowsetCatalogAttribute();
    		
	    	$contentType = $rowsetCatAtt->findByAttributeGuid('docMimeType')->value;
			$filename = $systemname = $rowsetCatAtt->findByAttributeGuid('docSystemName')->value;
			$oriName = $oname = $rowsetCatAtt->findByAttributeGuid('docOriginalName')->value;
			
			$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
			$rowsetRelatedItem = $tblRelatedItem->fetchAll("itemGuid='$catalogGuid' AND relateAs='RELATED_FILE'");
			
			$flagFileFound = false;
			
			foreach($rowsetRelatedItem as $rowRelatedItem)
			{
				if(!$flagFileFound)
				{
					$parentGuid = $rowRelatedItem->relatedGuid;
					$sDir1 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$systemname;
					$sDir2 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$parentGuid.DIRECTORY_SEPARATOR.$systemname;
					$sDir3 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$oname;
					$sDir4 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$parentGuid.DIRECTORY_SEPARATOR.$oname;
					
					if(file_exists($sDir1))
					{
						$flagFileFound = true;
						header("Content-type: $contentType");
						header("Content-Disposition: attachment; filename=$oriName");
						@readfile($sDir1);
						die();
					}
					else 
						if(file_exists($sDir2))
						{
							$flagFileFound = true;
							header("Content-type: $contentType");
							header("Content-Disposition: attachment; filename=$oriName");
							@readfile($sDir2);
							die();
						}
						if (file_exists($sDir3))
						{
							$flagFileFound = true;
							header("Content-type: $contentType");
							header("Content-Disposition: attachment; filename=$oriName");
							@readfile($sDir3);
							die();
						}
						if (file_exists($sDir4))
						{
							$flagFileFound = true;
							header("Content-type: $contentType");
							header("Content-Disposition: attachment; filename=$oriName");
							@readfile($sDir4);
							die();
						}
						else 
						{
							$flagFileFound = false;
							$this->_forward('forbidden','browser','hold');
						}
				}
			}
			
    	}
    	else 
    	{
    		$flagFileFound = false;
    		$this->_forward('forbidden','browser','hold');
    	}
    }
    function forbiddenAction() 	
    {
    }
}
?>