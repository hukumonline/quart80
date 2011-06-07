<?php
class Pandamp_Modules_Dms_Catalog_Model_Row_Catalog extends Zend_Db_Table_Row_Abstract
{
	protected function _insert()
	{
		if(empty($this->guid))
		{
	    	$guidMan = new Pandamp_Core_Guid();
	    	$this->guid = $guidMan->generateGuid();
		}
		
		if(!empty($this->shortTitle))
		{
			$sTitleLower = strtolower($this->shortTitle);
			$sTitleLower = preg_replace("/[^a-zA-Z0-9 s]/", "", $sTitleLower);
			$sTitleLower = str_replace(' ', '-', $sTitleLower);
			$this->shortTitle = $sTitleLower;
		}
		
		$today = date('Y-m-d H:i:s');
		if(empty($this->createdDate))
			$this->createdDate = $today;
		if(empty($this->modifiedDate) || $this->modifiedDate=='0000-00-00 00:00:00')
			$this->modifiedDate = $today;
			
		$this->deletedDate = '0000-00-00 00:00:00';
		
		if(empty($this->createdBy))
		{
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity())
			{
				$this->createdBy = $auth->getIdentity()->username;
			}
		}
		
		if(empty($this->modifiedBy))
			$this->modifiedBy = $this->createdBy;
		if(empty($this->status))
			$this->status = 0;
	}
	protected function _postDelete()
	{
	    $registry = Zend_Registry::getInstance();
	    $config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
	    $cdn = $config->getOption('cdn');
	    
		//find related docs and delete them
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		$rowsetRelatedDocs = $tblRelatedItem->fetchAll("relatedGuid='$this->guid' AND relateAs IN ('RELATED_FILE','RELATED_IMAGE')");
		if(count($rowsetRelatedDocs))
		{
			foreach ($rowsetRelatedDocs as $rowRelatedDoc) 
			{
				$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
				$rowCatalog = $tblCatalog->find($rowRelatedDoc->itemGuid)->current();	
				$rowCatalog->delete();
			}
		}
		
		if($this->profileGuid == 'kutu_doc')
		{
			//get parentGuid
			$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
			$rowsetRelatedItem = $tblRelatedItem->fetchAll("itemGuid='$this->guid' AND relateAs IN ('RELATED_FILE','RELATED_IMAGE')");
			if(count($rowsetRelatedItem))
			{
				foreach($rowsetRelatedItem as $rowRelatedItem)
				{
					//must delete the physical files
					$rowsetCatAtt = $this->findDependentRowsetCatalogAttribute();
					$systemname = $rowsetCatAtt->findByAttributeGuid('docSystemName')->value;
					$parentGuid = $rowRelatedItem->relatedGuid;
				
					//$sDir1 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$systemname;
					//$sDir2 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$parentGuid.DIRECTORY_SEPARATOR.$systemname;
					$sDir1 = $cdn['static']['dir']['files']."/".$systemname;
					$sDir2 = $cdn['static']['dir']['files']."/".$parentGuid."/".$systemname;
					
					$sDir1_Remote = $cdn['remote']['dir']['files']."/".$systemname;
					$sDir2_Remote = $cdn['remote']['dir']['files']."/".$parentGuid."/".$systemname;
					
					if(file_exists($sDir1))
					{
						// delete file
						unlink($sDir1);
						// check remote file
						if(file_exists($sDir1_Remote))
						{
							$this->remoteDeleteFile($sDir1_Remote);
						}
					}
					else 
					{
						if(file_exists($sDir2))
						{
							//delete file
							unlink($sDir2);
							// check remote file
							if(file_exists($sDir2_Remote))
							{
								$this->remoteDeleteFile($sDir2_Remote);
							}
						}
					}
					
					
					$img1 = $cdn['static']['dir']['images']."/".$systemname;
					$img2 = $cdn['static']['dir']['images']."/".$parentGuid."/".$systemname;
				
					$img1_Remote = $cdn['remote']['dir']['images']."/".$systemname;
					$img2_Remote = $cdn['remote']['dir']['images']."/".$parentGuid."/".$systemname;
				
					if(file_exists($img1))
					{
						// delete file
						unlink($img1);
						// check remote file
						if(file_exists($img1_Remote))
						{
							$this->remoteDeleteFile($img1_Remote);
						}
					}
					else 
					{
						if(file_exists($img2))
						{
							//delete file
							unlink($img2);
							// check remote file
							if(file_exists($img2_Remote))
							{
								$this->remoteDeleteFile($img2_Remote);
							}
						}
					}
					
					
				}
			}
		}
			
		//delete from table CatalogAttribute
		$tblCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$tblCatalogAttribute->delete("catalogGuid='$this->guid'");
		
		//delete catalogGuid from table CatalogFolder
		$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		$tblCatalogFolder->delete("catalogGuid='$this->guid'");
		
		//delete guid from table AssetSetting
		$tblAssetSetting = new Pandamp_Modules_Dms_Catalog_Model_AssetSetting();
		$tblAssetSetting->delete("guid='$this->guid'");
		
		//delete from table relatedItem
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		$tblRelatedItem->delete("itemGuid='$this->guid'");
		$tblRelatedItem->delete("relatedGuid='$this->guid'");
		
		$indexingEngine = Pandamp_Search::manager();		
		
		try {		
			$hits = $indexingEngine->deleteCatalogFromIndex($this->guid);
		}
		catch (Exception $e)
		{
			
		}
		
		
		//delete physical catalog folder from uploads/files/[catalogGuid]
		//$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$this->guid;
		$sDir = $cdn['static']['dir']['files']."/".$this->guid;
		$sDir_Remote = $cdn['remote']['dir']['files']."/".$this->guid;
		try {
			if(is_dir($sDir)) {
				rmdir($sDir);
				// check remote is_dir
				if(is_dir($sDir_Remote))
				{
					$this->remoteRemoveDir($sDir_Remote);
				}
			}
		}
		catch (Exception $e)
		{
			
		}
		
		//$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'images';
		$sDir = $cdn['static']['dir']['images'];
		try {
		   	if (file_exists($sDir."/".$this->guid.".gif")) 		{ unlink($sDir."/".$this->guid.".gif"); 	}
		   	if (file_exists($sDir."/tn_".$this->guid.".gif")) 	{ unlink($sDir."/tn_".$this->guid.".gif"); 	}
		   	if (file_exists($sDir."/".$this->guid.".jpg")) 		{ unlink($sDir."/".$this->guid.".jpg"); 	}
	   	   	if (file_exists($sDir."/tn_".$this->guid.".jpg")) 	{ unlink($sDir."/tn_".$this->guid.".jpg"); 	}
		   	if (file_exists($sDir."/".$this->guid.".jpeg")) 	{ unlink($sDir."/".$this->guid.".jpeg"); 	}
	   	   	if (file_exists($sDir."/tn_".$this->guid.".jpeg")) 	{ unlink($sDir."/tn_".$this->guid.".jpeg"); }
		   	if (file_exists($sDir."/".$this->guid.".png")) 		{ unlink($sDir."/".$this->guid.".png"); 	}
	   	   	if (file_exists($sDir."/tn_".$this->guid.".png")) 	{ unlink($sDir."/tn_".$this->guid.".png"); 	}
		}
		catch (Exception $e)
		{
			
		}
		
		
		$sDir = $cdn['remote']['dir']['images'];
		try {
		   	if (file_exists($sDir."/".$this->guid.".gif")) 		{ $this->remoteDeleteFile($sDir."/".$this->guid.".gif"); 	}
		   	if (file_exists($sDir."/tn_".$this->guid.".gif")) 	{ $this->remoteDeleteFile($sDir."/tn_".$this->guid.".gif"); 	}
		   	if (file_exists($sDir."/".$this->guid.".jpg")) 		{ $this->remoteDeleteFile($sDir."/".$this->guid.".jpg"); 	}
	   	   	if (file_exists($sDir."/tn_".$this->guid.".jpg")) 	{ $this->remoteDeleteFile($sDir."/tn_".$this->guid.".jpg"); 	}
		   	if (file_exists($sDir."/".$this->guid.".jpeg")) 	{ $this->remoteDeleteFile($sDir."/".$this->guid.".jpeg"); 	}
	   	   	if (file_exists($sDir."/tn_".$this->guid.".jpeg")) 	{ $this->remoteDeleteFile($sDir."/tn_".$this->guid.".jpeg"); }
		   	if (file_exists($sDir."/".$this->guid.".png")) 		{ $this->remoteDeleteFile($sDir."/".$this->guid.".png"); 	}
	   	   	if (file_exists($sDir."/tn_".$this->guid.".png")) 	{ $this->remoteDeleteFile($sDir."/tn_".$this->guid.".png"); 	}
		}
		catch (Exception $e)
		{
			
		}
		
	}
	public function findDependentRowsetCatalogAttribute()
	{
		return $this->findDependentRowset('Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute');
	}
	public function relateTo($relatedGuid, $as='RELATED_ITEM', $valRelation = 0)
	{
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		
		if(empty($this->guid))
			throw new Zend_Exception('Can not relate to empty GUID');
		if(empty($relatedGuid))
			throw new Zend_Exception('Can not relate to empty related GUID');
		
		$rowsetRelatedItem = $tblRelatedItem->find($this->guid, $relatedGuid, $as);
		if(count($rowsetRelatedItem) > 0)
		{
//			if ($as == "RELATED_COMMENT") {
				$row = $rowsetRelatedItem->current();
				$row->valueIntRelation = $valRelation;
//			}
		}
		else 
		{
			$row = $tblRelatedItem->createNew();
			$row->itemGuid = $this->guid;
			$row->relatedGuid = $relatedGuid;
			$row->relateAs = $as;
			$row->valueIntRelation = $valRelation;
		}
		$row->save();
	}
	private function remoteDeleteFile($remote_path)
	{
	    $registry = Zend_Registry::getInstance();
	    $config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
	    $ftp = $config->getOption('ftp');
	    
		$strServer = $ftp['remote']['server'];
		$strServerPort = $ftp['remote']['port'];
		$strServerUsername = $ftp['remote']['username'];
		$strServerPassword = $ftp['remote']['passwd'];
		
		//connect to server
		$resConnection = ssh2_connect($strServer, $strServerPort);
		
    	if(ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword))
    	{
    		$resSFTP = ssh2_sftp($resConnection);
    		unlink("ssh2.sftp://{$resSFTP}".$remote_path);
    	}		
	}
	private function remoteRemoveDir($remote_path)
	{
	    $registry = Zend_Registry::getInstance();
	    $config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
	    $ftp = $config->getOption('ftp');
	    
		$strServer = $ftp['remote']['server'];
		$strServerPort = $ftp['remote']['port'];
		$strServerUsername = $ftp['remote']['username'];
		$strServerPassword = $ftp['remote']['passwd'];
		
		//connect to server
		$resConnection = ssh2_connect($strServer, $strServerPort);
		
    	if(ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword))
    	{
    		$resSFTP = ssh2_sftp($resConnection);
    		rmdir("ssh2.sftp://{$resSFTP}".$remote_path);
    	}		
	}
}
?>