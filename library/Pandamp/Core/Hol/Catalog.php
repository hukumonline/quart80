<?php
class Pandamp_Core_Hol_Catalog
{
	public function save($aData)
	{
		if(empty($aData['profileGuid']))
			throw new Zend_Exception('Catalog Profile can not be EMPTY!');

		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		$gman = new Pandamp_Core_Guid();
		$catalogGuid = (isset($aData['guid']) && !empty($aData['guid']))? $aData['guid'] : $gman->generateGuid();
		$folderGuid = (isset($aData['folderGuid']) && !empty($aData['folderGuid']))? $aData['folderGuid'] : '';
		
		//if not empty, there are 2 possibilities
		$where = $tblCatalog->getAdapter()->quoteInto('guid=?', $catalogGuid);
		if($tblCatalog->fetchRow($where))
		{
			$rowCatalog = $tblCatalog->find($catalogGuid)->current();
			
			$rowCatalog->shortTitle = (isset($aData['shortTitle']))?$aData['shortTitle']:$rowCatalog->shortTitle;
			$rowCatalog->publishedDate = (isset($aData['publishedDate']))?$aData['publishedDate']:$rowCatalog->publishedDate;
			$rowCatalog->expiredDate = (isset($aData['expiredDate']))?$aData['expiredDate']:$rowCatalog->expiredDate;
			$rowCatalog->status = (isset($aData['status']))?$aData['status']:$rowCatalog->status;
			$rowCatalog->price = (isset($aData['price']))?$aData['price']:$rowCatalog->price;
		}
		else 
		{
			$rowCatalog = $tblCatalog->fetchNew();
			
			$rowCatalog->guid = $catalogGuid;
			$rowCatalog->shortTitle = (isset($aData['shortTitle']))?$aData['shortTitle']:'';
			$rowCatalog->profileGuid = $aData['profileGuid'];
			$rowCatalog->publishedDate = (isset($aData['publishedDate']))?$aData['publishedDate']:'0000-00-00 00:00:00';
			$rowCatalog->expiredDate = (isset($aData['expiredDate']))?$aData['expiredDate']:'0000-00-00 00:00:00';
			$rowCatalog->createdBy = (isset($aData['username']))?$aData['username']:'';
			$rowCatalog->modifiedBy = $rowCatalog->createdBy;
			$rowCatalog->createdDate = date("Y-m-d h:i:s");
			$rowCatalog->modifiedDate = $rowCatalog->createdDate;
			$rowCatalog->deletedDate = '0000-00-00 00:00:00';
			$rowCatalog->status = (isset($aData['status']))?$aData['status']:0;
			$rowCatalog->price = (isset($aData['price']))?$aData['price']:0;
		}
		try 
		{
			$catalogGuid = $rowCatalog->save();
		}
		catch (Exception $e)
		{
			die($e->getMessage());
		}
		
		//$cache = Zend_Registry::get('cache');
		
		$tableProfileAttribute = new Pandamp_Modules_Dms_Profile_Model_ProfileAttribute();
		$profileGuid = $rowCatalog->profileGuid;
		$where = $tableProfileAttribute->getAdapter()->quoteInto('profileGuid=?', $profileGuid);
		$rowsetProfileAttribute = $tableProfileAttribute->fetchAll($where,'viewOrder ASC');
		
		$rowsetCatalogAttribute = $rowCatalog->findDependentRowsetCatalogAttribute();
		foreach ($rowsetProfileAttribute as $rowProfileAttribute)
		{
			if($rowsetCatalogAttribute->findByAttributeGuid($rowProfileAttribute->attributeGuid))
			{
				$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid($rowProfileAttribute->attributeGuid);
			}
			else 
			{
				$tblCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
				$rowCatalogAttribute = $tblCatalogAttribute->fetchNew();
				$rowCatalogAttribute->catalogGuid = $catalogGuid;
				$rowCatalogAttribute->attributeGuid = $rowProfileAttribute->attributeGuid;
			}
			
			$rowCatalogAttribute->value = (isset($aData[$rowProfileAttribute->attributeGuid]))?$aData[$rowProfileAttribute->attributeGuid]:'';
			
			//$cacheKey = "gcav_cg_".$catalogGuid."_ag_".$rowProfileAttribute->attributeGuid;
			//if ($cacheKey) $cache->remove($cacheKey);
			
			$rowCatalogAttribute->save();
		}
		
		//save to table CatalogFolder only if folderGuid is not empty
		if (!empty($folderGuid)) 
		{
			$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
			
			$rowsetCatalogFolder = $tblCatalogFolder->find($catalogGuid, $folderGuid);
			if(count($rowsetCatalogFolder)<=0)
			{
				$rowCatalogFolder = $tblCatalogFolder->createRow(array('catalogGuid'=>'', 'folderGuid'=>''));
				$rowCatalogFolder->catalogGuid = $catalogGuid;
				$rowCatalogFolder->folderGuid = $folderGuid;
				$rowCatalogFolder->save();
			}
		}
		
		
		//do indexing
		$indexingEngine = Pandamp_Search::manager();
		$indexingEngine->indexCatalog($catalogGuid);
		
		//$cache->remove("catalog");
		
		return $catalogGuid;
	}
	public function delete($catalogGuid)
	{
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog;
		$rowset = $tblCatalog->find($catalogGuid);
		if(count($rowset))
		{
			$row = $rowset->current();
			$profileGuid = $row->profileGuid;
			
			if($row->profileGuid == 'kutu_doc')
			{
				$rowRelatedItem = $tblRelatedItem->fetchRow("itemGuid='$row->guid' AND relateAs IN ('RELATED_FILE','RELATED_IMAGE','RELATED_VIDEO')");
			}
			
			$row->delete();
			
			//if deleted catalog is kutu_doc then re-index its parentGuid
			if($profileGuid == 'kutu_doc')
			{
				$indexingEngine = Pandamp_Search::manager();
				$indexingEngine->indexCatalog($rowRelatedItem->relatedGuid);
			}
		}
		
		
	}
	public function changeUploadFile($aDataCatalog, $relatedGuid)
	{
		if($aDataCatalog['profileGuid']!='kutu_doc')
			throw new Zend_Exception('Profile does not match profile for FILE');
		
		if(empty($relatedGuid))
			throw new Zend_Exception('No RELATED GUID specified!');
		
		$id = 1 + ($aDataCatalog['id'] - 1);
		
		for ($x=1;$x <= $id; $x++) {
			$registry = Zend_Registry::getInstance();
			$files = $registry->get('files');
			
			if (isset($files['uploadedFile'.$x]))
			{
				$file = $files['uploadedFile'.$x];
			}
			
			$itemGuid = ($aDataCatalog['itemGuid'.$x])? $aDataCatalog['itemGuid'.$x] : '';
			
			$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			$rowset = $tblCatalog->find($itemGuid)->current();
			
			if (isset($rowset)) {
				$rowsetCatAtt = $rowset->findDependentRowsetCatalogAttribute();
				if (isset($rowsetCatAtt))
				{
					$systemname = $rowsetCatAtt->findByAttributeGuid('docSystemName')->value;
					$oriName = $rowsetCatAtt->findByAttributeGuid('docOriginalName')->value;
					
					$parentGuid = $relatedGuid;
					
					$fileBaru = strtoupper(str_replace(' ','_',$file['name']));
					
					if ($oriName !== $fileBaru)
					{
						echo "File $fileBaru tidak sama dengan file aslinya\n";
					}
					else 
					{
						$sDir 	= ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$parentGuid;
						$sDir1 	= ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$systemname;
						$sDir2 	= ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$parentGuid.DIRECTORY_SEPARATOR.$systemname;
						
						if(file_exists($sDir1)) { unlink($sDir1); }
						if(file_exists($sDir2)) { unlink($sDir2); }
						
						if(is_dir($sDir))
				    	{
				    		//if enters here, you may save the files
				    		move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . $fileBaru);
				    		//echo 'dir';
				    	}
				    	else 
				    	{
				    		if(mkdir($sDir))
				    		{
				    			//if enters here, let's continue saving the file.
				    			move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . $fileBaru);
				    		}
				    		else 
				    		{
				    			//if enters here, then it means, you CAN'T create the folder, maybe because the safe mode is ON.
				    			//save the file in the upload/files folder 
				    			move_uploaded_file($file['tmp_name'], ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR . $fileBaru);
				    		}
				    	}
						
					}
				}
			}
		}
	}
    public function uploadFile($aDataCatalog, $relatedGuid)
    {
        if($aDataCatalog['profileGuid']!='kutu_doc')
            throw new Zend_Exception('Profile does not match profile for FILE');

        if(empty($relatedGuid))
            throw new Zend_Exception('No RELATED GUID specified!');

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
		
	    $id = 1 + ($aDataCatalog['id'] - 1);

        for ($x=1;$x < $id; $x++) {
            $title = ($aDataCatalog['fixedTitle'.$x])? $aDataCatalog['fixedTitle'.$x] : 'No-Title';

            $registry = Zend_Registry::getInstance();
            $files = $registry->get('files');

            if(isset($files['uploadedFile'.$x]))
            {
                    $file = $files['uploadedFile'.$x];
                    $this->checkTitle($aDataCatalog['fixedTitle'.$x]);
            }

            $type = ($aDataCatalog['fixedType'.$x])? $aDataCatalog['fixedType'.$x] : '';

            if ($type == 'file')
                    $relatedType = 'RELATED_FILE';
            elseif ($type == 'image')
                    $relatedType = 'RELATED_IMAGE';
            elseif ($type == 'video')
                    $relatedType = 'RELATED_VIDEO';

            $tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();

            $gman = new Pandamp_Core_Guid();
            $catalogGuid = (isset($aDataCatalog['guid']) && !empty($aDataCatalog['guid']))? $aDataCatalog['guid'] : $gman->generateGuid();
            $folderGuid = (isset($aDataCatalog['folderGuid']) && !empty($aDataCatalog['folderGuid']))? $aDataCatalog['folderGuid'] : '';

            $where = $tblCatalog->getAdapter()->quoteInto('guid=?', $catalogGuid);
            if($tblCatalog->fetchRow($where))
            {
                $rowCatalog = $tblCatalog->find($catalogGuid)->current();

                $rowCatalog->shortTitle = (isset($aDataCatalog['shortTitle']))?$aDataCatalog['shortTitle']:$rowCatalog->shortTitle;
                $rowCatalog->publishedDate = (isset($aDataCatalog['publishedDate']))?$aDataCatalog['publishedDate']:$rowCatalog->publishedDate;
                $rowCatalog->expiredDate = (isset($aDataCatalog['expiredDate']))?$aDataCatalog['expiredDate']:$rowCatalog->expiredDate;
                $rowCatalog->status = (isset($aDataCatalog['status']))?$aDataCatalog['status']:$rowCatalog->status;
            }
            else
            {
                $rowCatalog = $tblCatalog->fetchNew();

                $rowCatalog->guid = $catalogGuid;
                $rowCatalog->shortTitle = (isset($aDataCatalog['shortTitle']))?$aDataCatalog['shortTitle']:'';
                $rowCatalog->profileGuid = $aDataCatalog['profileGuid'];
                $rowCatalog->publishedDate = (isset($aDataCatalog['publishedDate']))?$aDataCatalog['publishedDate']:'0000-00-00 00:00:00';
                $rowCatalog->expiredDate = (isset($aDataCatalog['expiredDate']))?$aDataCatalog['expiredDate']:'0000-00-00 00:00:00';
                $rowCatalog->createdBy = (isset($aDataCatalog['username']))?$aDataCatalog['username']:'';
                $rowCatalog->modifiedBy = $rowCatalog->createdBy;
                $rowCatalog->createdDate = date("Y-m-d h:i:s");
                $rowCatalog->modifiedDate = $rowCatalog->createdDate;
                $rowCatalog->deletedDate = '0000-00-00 00:00:00';
                $rowCatalog->status = (isset($aDataCatalog['status']))?$aDataCatalog['status']:0;
            }

            $catalogGuid = $rowCatalog->save();

            $rowsetCatalogAttribute = $rowCatalog->findDependentRowsetCatalogAttribute();

            if(isset($files['uploadedFile'.$x]))
            {
                    if(isset($files['uploadedFile'.$x]['name']) && !empty($files['uploadedFile'.$x]['name']))
                    {
                        $this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'docSystemName', strtoupper(str_replace(' ','_',$file['name'])));
                        $this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'docOriginalName', strtoupper(str_replace(' ','_',$file['name'])));
                        $this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'docSize', $file['size']);
                        $this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'docMimeType', $file['type']);
                        $this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'fixedTitle', $title);
                        $this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'docViewOrder', 0);
                        if ($type == 'file')
                        {
                         	//$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$relatedGuid;
                            $sDir = $cdn['static']['dir']['files']."/".$relatedGuid;
                            $sDir1 = $cdn['remote']['dir']['files']."/".$relatedGuid;
                            
                            // REMOTE UPLOADs
                            /*
                            if(ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)){
                            	$resSFTP = ssh2_sftp($resConnection);
                            	if(is_dir("ssh2.sftp://{$resSFTP}".$sDir))
                            	{
                            		file_put_contents("ssh2.sftp://{$resSFTP}".$sDir."/".strtoupper(str_replace(' ','_',$file['name'])), $file['tmp_name']);	
                            	}
                            	else 
                            	{
                            		if(mkdir("ssh2.sftp://{$resSFTP}".$sDir))
                            		{
                            			file_put_contents("ssh2.sftp://{$resSFTP}".$sDir."/".strtoupper(str_replace(' ','_',$file['name'])), $file['tmp_name']);
                            		}
                            		else
                            		{
                            			file_put_contents("ssh2.sftp://{$resSFTP}".$cdn['static']['dir']['files']."/".strtoupper(str_replace(' ','_',$file['name'])), $file['tmp_name']);
                            		}
                            	}
                            }
                            else
                            {
                            	throw new Zend_Exception("UFile:Unable to authenticate on server");
                            }
                            */
                            // END of REMOTE UPLOADs
                            
                            
                            if(is_dir($sDir))
                        	{
                                move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
                                $this->remotefile('file',$sDir,$sDir1,$file['name']);
	                        }
    	                    else
        	                {
                                if(mkdir($sDir))
                                {
                                        move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
                                        $this->remotefile('file',$sDir,$sDir1,$file['name']);
                                }
                                else
                                {
                                        move_uploaded_file($file['tmp_name'], $cdn['static']['dir']['files'].DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
                                        $this->remotefile('file',$cdn['static']['dir']['files'],$cdn['remote']['dir']['files'],$file['name']);
                                }
            	            }
                        }
                        elseif ($type == 'image')
                        {
                        		$sDir = $cdn['static']['dir']['images'];
                        		
                        		$sDir1 = $cdn['remote']['dir']['images'];
                        		
                                $file = $files['uploadedFile'.$x]['name'];
                                $ext = explode(".",$file);
                                $ext = strtolower(array_pop($ext));

                                if ($ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext == "png")
                                {
                                    $thumb_mode = $sDir."/".$catalogGuid.".".$ext;
                                    $thumb = $sDir."/".$relatedGuid."/".$catalogGuid.".".$ext;
                                    $target_path = $sDir."/".$relatedGuid;
                                    $remote_path = $sDir1."/".$relatedGuid;

		                            // REMOTE UPLOADs
		                            /*
		                            if(ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)){
		                            	$resSFTP = ssh2_sftp($resConnection);
		                            	if(is_dir("ssh2.sftp://{$resSFTP}".$target_path))
		                            	{
		                            		//file_put_contents("ssh2.sftp://{$resSFTP}".$target_path."/".$catalogGuid.".".$ext, $files['uploadedFile'.$x]['tmp_name']);
		                            		
		                            		ssh2_scp_send($resConnection,$files['uploadedFile'.$x]['tmp_name'],"$target_path."/".$catalogGuid.".".$ext",0644) or die("Could not transfer to $strServer - Operation aborted.");
		                            		
		                            		/*
		                            		$stream = @fopen("ssh2.sftp://$resSFTP$target_path."/".$catalogGuid.".".$ext", 'w');
		                            		if (! $stream)
		                            			throw new Zend_Exception("Could not open file: $target_path."/".$catalogGuid.".".$ext");
		                            		
		                            		$data_to_send = @file_get_contents($files['uploadedFile'.$x]['tmp_name']);
		                            		if ($data_to_send === false)
		                            			throw new Zend_Exception("Could not open local file: ".$files['uploadedFile'.$x]['tmp_name']);
		                            			
											if (@fwrite($stream, $data_to_send) === false)
												throw new Exception("Could not send data from file: ".$files['uploadedFile'.$x]['tmp_name']);

											@fclose($stream);												
		                            		
		                            		
		                            		$d=$target_path.'/tn_'.$catalogGuid.".".$ext;
		                            		file_get_contents("http://images.hukumonline.com/generateThumbnail.php?sDir=$d&thumb=$thumb");	
		                            	}
		                            	else 
		                            	{
		                            		if(mkdir("ssh2.sftp://{$resSFTP}".$target_path))
		                            		{
		                            			file_put_contents("ssh2.sftp://{$resSFTP}".$target_path."/".$catalogGuid.".".$ext, $files['uploadedFile'.$x]['tmp_name']);
			                            		$d=$target_path.'/tn_'.$catalogGuid.".".$ext;
			                            		file_get_contents("http://images.hukumonline.com/generateThumbnail.php?sDir=$d&thumb=$thumb");	
		                            		}
		                            		else
		                            		{
		                            			file_put_contents("ssh2.sftp://{$resSFTP}".$cdn['static']['dir']['images']."/".$catalogGuid.".".$ext, $files['uploadedFile'.$x]['tmp_name']);
			                            		$d=$sDir.'/tn_'.$catalogGuid.".".$ext;
			                            		file_get_contents("http://images.hukumonline.com/generateThumbnail.php?sDir=$d&thumb=$thumb_mode");	
		                            		}
		                            	}
		                            }
		                            else
		                            {
		                            	throw new Zend_Exception("UImage:Unable to authenticate on server");
		                            }
		                            */
		                            // END of REMOTE UPLOADs
                            
                                    
                                    if(is_dir($target_path))
                                    {
                                        move_uploaded_file($files['uploadedFile'.$x]['tmp_name'], $target_path. "/" . $catalogGuid . "." .$ext);
                                        Pandamp_Lib_Formater::createthumb($thumb,$target_path.'/tn_'.$catalogGuid.".".$ext,130,130);
                                        $this->remotefile('image',$target_path, $remote_path, $catalogGuid, $ext);
                                    }
                                    else
                                    {
                                        if(mkdir($target_path))
                                        {
                                            move_uploaded_file($files['uploadedFile'.$x]['tmp_name'], $target_path. "/" . $catalogGuid . "." .$ext);
                                            Pandamp_Lib_Formater::createthumb($thumb,$target_path.'/tn_'.$catalogGuid.".".$ext,130,130);
                                            $this->remotefile('image',$target_path, $remote_path, $catalogGuid, $ext);
                                        }
                                        else
                                        {
                                            move_uploaded_file($files['uploadedFile'.$x]['tmp_name'], $cdn['static']['dir']['images']."/".$catalogGuid.".".$ext);
                                            Pandamp_Lib_Formater::createthumb($thumb_mode,$sDir.'/tn_'.$catalogGuid.".".$ext,130,130);
                                            $this->remotefile('image',$cdn['static']['dir']['images'], $cdn['remote']['dir']['images'], $catalogGuid, $ext);
                                        }
                                    }
                                }
                        }
                        elseif ($type == 'video')
                        {
                        	$sDir = $cdn['static']['dir']['video'].DIRECTORY_SEPARATOR.$relatedGuid;
                            //$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'video'.DIRECTORY_SEPARATOR.$relatedGuid;
                            if(is_dir($sDir))
                            {
                                move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
                            }
	                        else
	                        {
	                            if(mkdir($sDir))
	                            {
	                                move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
	                            }
	                            else
	                            {
	                                move_uploaded_file($file['tmp_name'], $cdn['static']['dir']['video'].DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
	                            }
	                        }
                        }

                    }
            }

            $this->relateTo($catalogGuid, $relatedGuid, $relatedType);

            $indexingEngine = Pandamp_Search::manager();
            //$indexingEngine->indexCatalog($relatedGuid);
        }
    }
    private function remotefile($type, $target_path, $remote_path, $catalogGuid='', $ext='')
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
		
    	if(ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)){
    		$resSFTP = ssh2_sftp($resConnection);
    		if ($type=='image')
    		{
	    		if(is_dir("ssh2.sftp://{$resSFTP}".$remote_path))
	    		{
	    			ssh2_scp_send($resConnection,$target_path. "/" . $catalogGuid . "." .$ext,$remote_path. "/" . $catalogGuid . "." .$ext,0644) or die("Could not transfer to $strServer - Operation aborted.");
	    			if (file_exists($target_path.'/tn_'.$catalogGuid.".".$ext))
	    			{
						ssh2_scp_send($resConnection,$target_path.'/tn_'.$catalogGuid.".".$ext,$remote_path. "/tn_" . $catalogGuid . "." .$ext,0644) or die("Could not transfer thumbnail to $strServer - Operation aborted.");    				
	    			}
	    		}
	    		else 
	    		{
	        		if(mkdir("ssh2.sftp://{$resSFTP}".$remote_path)){
	                	ssh2_scp_send($resConnection,$target_path. "/" . $catalogGuid . "." .$ext,$remote_path. "/" . $catalogGuid . "." .$ext,0644) or die("Could not transfer to $strServer - Operation aborted.");
		    			if (file_exists($target_path.'/tn_'.$catalogGuid.".".$ext))
		    			{
							ssh2_scp_send($resConnection,$target_path.'/tn_'.$catalogGuid.".".$ext,$remote_path. "/tn_" . $catalogGuid . "." .$ext,0644) or die("Could not transfer thumbnail after mkdir to $strServer - Operation aborted.");    				
		    			}
	        		}
	        		else 
	        		{
						ssh2_scp_send($resConnection,$target_path. "/" . $catalogGuid . "." .$ext,$remote_path. "/" . $catalogGuid . "." .$ext,0644) or die("Could not transfer to $strServer - Operation aborted.");	        			
		    			if (file_exists($target_path.'/tn_'.$catalogGuid.".".$ext))
		    			{
							ssh2_scp_send($resConnection,$target_path.'/tn_'.$catalogGuid.".".$ext,$remote_path. "/tn_" . $catalogGuid . "." .$ext,0644) or die("Could not transfer thumbnail after mkdir to $strServer - Operation aborted.");    				
		    			}
	        		}
	    		}
    		}
    		elseif ($type=='file')
    		{
	    		if(is_dir("ssh2.sftp://{$resSFTP}".$remote_path))
	    		{ 
	    			ssh2_scp_send($resConnection,$target_path. "/" . strtoupper(str_replace(' ','_',$catalogGuid)),$remote_path. "/" . strtoupper(str_replace(' ','_',$catalogGuid)),0644) or die("Could not transfer to $strServer - Operation aborted.");
	    		}
	    		else 
	    		{
	        		if(mkdir("ssh2.sftp://{$resSFTP}".$remote_path)){
	                	ssh2_scp_send($resConnection,$target_path. "/" . strtoupper(str_replace(' ','_',$catalogGuid)),$remote_path. "/" . strtoupper(str_replace(' ','_',$catalogGuid)),0644) or die("Could not transfer to $strServer - Operation aborted.");
	        		}
	        		else 
	        		{
						ssh2_scp_send($resConnection,$target_path. "/" . strtoupper(str_replace(' ','_',$catalogGuid)),$remote_path. "/" . strtoupper(str_replace(' ','_',$catalogGuid)),0644) or die("Could not transfer to $strServer - Operation aborted.");
	        		}
	    		}
    		}
    	}
    	
    }
	public function uploadFile_old($aDataCatalog, $relatedGuid)
	{
		if($aDataCatalog['profileGuid']!='kutu_doc')
			throw new Zend_Exception('Profile does not match profile for FILE');
		
		if(empty($relatedGuid))
			throw new Zend_Exception('No RELATED GUID specified!');
		
		$id = 1 + ($aDataCatalog['id'] - 1);
		
		for ($x=1;$x < $id; $x++) {
			$title = ($aDataCatalog['fixedTitle'.$x])? $aDataCatalog['fixedTitle'.$x] : 'No-Title';
			
			$registry = Zend_Registry::getInstance();
			$files = $registry->get('files');
			
	    	if(isset($files['uploadedFile'.$x]))
			{
				$file = $files['uploadedFile'.$x];
				$this->checkTitle($aDataCatalog['fixedTitle'.$x]);
			}
			
			$type = ($aDataCatalog['fixedType'.$x])? $aDataCatalog['fixedType'.$x] : '';
			
			if ($type == 'file')
				$relatedType = 'RELATED_FILE';
			elseif ($type == 'image')
				$relatedType = 'RELATED_IMAGE';
			elseif ($type == 'video')
				$relatedType = 'RELATED_VIDEO';
			
			$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			
			$gman = new Pandamp_Core_Guid();
			$catalogGuid = (isset($aDataCatalog['guid']) && !empty($aDataCatalog['guid']))? $aDataCatalog['guid'] : $gman->generateGuid();
			$folderGuid = (isset($aDataCatalog['folderGuid']) && !empty($aDataCatalog['folderGuid']))? $aDataCatalog['folderGuid'] : '';
			
			$where = $tblCatalog->getAdapter()->quoteInto('guid=?', $catalogGuid);
			if($tblCatalog->fetchRow($where))
			{
				$rowCatalog = $tblCatalog->find($catalogGuid)->current();
				
				$rowCatalog->shortTitle = (isset($aDataCatalog['shortTitle']))?$aDataCatalog['shortTitle']:$rowCatalog->shortTitle;
				$rowCatalog->publishedDate = (isset($aDataCatalog['publishedDate']))?$aDataCatalog['publishedDate']:$rowCatalog->publishedDate;
				$rowCatalog->expiredDate = (isset($aDataCatalog['expiredDate']))?$aDataCatalog['expiredDate']:$rowCatalog->expiredDate;
				$rowCatalog->status = (isset($aDataCatalog['status']))?$aDataCatalog['status']:$rowCatalog->status;
			}
			else 
			{
				$rowCatalog = $tblCatalog->fetchNew();
				
				$rowCatalog->guid = $catalogGuid;
				$rowCatalog->shortTitle = (isset($aDataCatalog['shortTitle']))?$aDataCatalog['shortTitle']:'';
				$rowCatalog->profileGuid = $aDataCatalog['profileGuid'];
				$rowCatalog->publishedDate = (isset($aDataCatalog['publishedDate']))?$aDataCatalog['publishedDate']:'0000-00-00 00:00:00';
				$rowCatalog->expiredDate = (isset($aDataCatalog['expiredDate']))?$aDataCatalog['expiredDate']:'0000-00-00 00:00:00';
				$rowCatalog->createdBy = (isset($aDataCatalog['username']))?$aDataCatalog['username']:'';
				$rowCatalog->modifiedBy = $rowCatalog->createdBy;
				$rowCatalog->createdDate = date("Y-m-d h:i:s");
				$rowCatalog->modifiedDate = $rowCatalog->createdDate;
				$rowCatalog->deletedDate = '0000-00-00 00:00:00';
				$rowCatalog->status = (isset($aDataCatalog['status']))?$aDataCatalog['status']:0;
			}
			
			$catalogGuid = $rowCatalog->save();
			
			$rowsetCatalogAttribute = $rowCatalog->findDependentRowsetCatalogAttribute();
			
			if(isset($files['uploadedFile'.$x]))
			{
				if(isset($files['uploadedFile'.$x]['name']) && !empty($files['uploadedFile'.$x]['name']))
				{
					$this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'docSystemName', strtoupper(str_replace(' ','_',$file['name'])));
					$this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'docOriginalName', strtoupper(str_replace(' ','_',$file['name'])));
					$this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'docSize', $file['size']);
					$this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'docMimeType', $file['type']);
					$this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'fixedTitle', $title);
					$this->_updateCatalogAttribute($rowsetCatalogAttribute, $catalogGuid, 'docViewOrder', 0);
					if ($type == 'file')
					{
						$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$relatedGuid;
						if(is_dir($sDir))
				    	{
				    		move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
				    	}
				    	else 
				    	{
				    		if(mkdir($sDir))
				    		{
				    			move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
				    		}
				    		else 
				    		{
				    			move_uploaded_file($file['tmp_name'], ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
				    		}
				    	}
					}
					elseif ($type == 'image') 
					{
						$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'images';
						$file = $files['uploadedFile'.$x]['name'];
						$ext = explode(".",$file);
						$ext = strtolower(array_pop($ext));
						
						if ($ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext == "png")
						{
							$target_path = $sDir.DIRECTORY_SEPARATOR.$catalogGuid.".".$ext;
							
							if(is_dir($target_path))
					    	{
					    		move_uploaded_file($files['uploadedFile'.$x]['tmp_name'], $sDir. DIRECTORY_SEPARATOR . $catalogGuid . "." .$ext);
					    		chmod($target_path,0644);
								Pandamp_Lib_Formater::createthumb($target_path,$sDir.'/tn_'.$catalogGuid.".".$ext,130,130);
					    	}
					    	else 
					    	{
					    		move_uploaded_file($files['uploadedFile'.$x]['tmp_name'], ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$catalogGuid.".".$ext);
					    		chmod($target_path,0644);
								Pandamp_Lib_Formater::createthumb($target_path,$sDir.'/tn_'.$catalogGuid.".".$ext,130,130);
					    	}
							
						}
					}
					elseif ($type == 'video')
					{
						$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'video'.DIRECTORY_SEPARATOR.$relatedGuid;
						if(is_dir($sDir))
				    	{
				    		move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
				    	}
				    	else 
				    	{
				    		if(mkdir($sDir))
				    		{
				    			move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
				    		}
				    		else 
				    		{
				    			move_uploaded_file($file['tmp_name'], ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR . strtoupper(str_replace(' ','_',$file['name'])));
				    		}
				    	}
					}
					
				}
			}
			
			$this->relateTo($catalogGuid, $relatedGuid, $relatedType);
			
			$indexingEngine = Pandamp_Search::manager();
			$indexingEngine->indexCatalog($relatedGuid);
		}
	}
	public function relateTo($itemGuid, $relatedGuid, $as='RELATED_ITEM', $valRelation = 0)
	{
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		
		if(empty($itemGuid))
			throw new Zend_Exception('Can not relate to empty GUID');
		
		$rowsetRelatedItem = $tblRelatedItem->find($itemGuid, $relatedGuid, $as);
		if(count($rowsetRelatedItem) > 0)
		{
			$row = $rowsetRelatedItem->current();
			$row->valueIntRelation = $valRelation;
		}
		else 
		{
			$row = $tblRelatedItem->createNew();
			$row->itemGuid = $itemGuid;
			$row->relatedGuid = $relatedGuid;
			$row->relateAs = $as;
			$row->valueIntRelation = $valRelation;
		}
		$row->save();
	}
	public function removeFromFolder($catalogGuid, $folderGuid)
	{
		$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		$rowset = $tblCatalogFolder->fetchAll("catalogGuid='$catalogGuid'");
		if(count($rowset)>1)
		{
			try
			{
				$tblCatalogFolder->delete("catalogGuid='$catalogGuid' AND folderGuid='$folderGuid'");
			}
			catch (Exception $e)
			{
				throw new Zend_Exception($e->getMessage());
			}
		}
		else
		{
			throw new Zend_Exception("Can not remove from the only FOLDER.");
		}
	}
	public function getPrice($catalogGuid)
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->find($catalogGuid);
		if(count($rowset))
		{
			$row = $rowset->current();
			return $row->price;
			
		}
		else
		{
			return 0;
		}
	}
	
	public function isBoughtByUser($catalogGuid, $userId)
	{
		$db = Pandamp_Application::getResource('db');
		
		$dbResult = $db->query("SELECT KOD.*, KO.datePurchased AS purchasingDate
                                FROM
                                KutuOrderDetail AS KOD,
								KutuOrder AS KO 
                                WHERE 
									KO.orderId = KOD.orderId
								AND
									userId = '$userId'
								AND
									(KO.orderStatus = 3 
									OR
									KO.orderStatus = 5)
								AND 
									itemId LIKE '$catalogGuid'");
                                //LIMIT $offset, $limit");
        
    	$aResult = $dbResult->fetchAll(Zend_Db::FETCH_ASSOC);
		//var_dump($aResult);
		//die();
		if(count($aResult) > 0)
			return true;
		else
			return false;
	}
	public function jCartIsItemSellable($catalogGuid)
	{
		//apakah pernah dibeli
		$auth =  Zend_Auth::getInstance();
		$hasBought = false;
		
		if($auth->hasIdentity())
		{
			$bpm = new Pandamp_Core_Hol_Catalog();
			$hasBought = $bpm->isBoughtByUser($catalogGuid, $auth->getIdentity()->guid);
		}
		if($hasBought)
		{
			$aReturn['isError'] = true;
			$aReturn['message'] = 'You have bought this Item before. Please check your account.';
			$aReturn['code'] = 1;
			return $aReturn;
		}
		
		
		// if status=draft then return false
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowCatalog = $tblCatalog->find($catalogGuid)->current();
		if($rowCatalog)
		{
			if($rowCatalog->status != 99)
			{
				$aReturn['isError'] = true;
				$aReturn['message'] = 'This item is not ready to be bought yet.';
				$aReturn['code'] = 1;
				return $aReturn;
			}
			
			// if price <= 0 then return false
			if($rowCatalog->price <= 0)
			{
				$aReturn['isError'] = true;
				$aReturn['message'] = 'This item is for FREE.';
				$aReturn['code'] = 2;
				return $aReturn;
			}
			
			/*
			$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
			$where = "relatedGuid='$catalogGuid' AND relateAs='RELATED_FILE'";
			$rowsetRelatedItem = $tblRelatedItem->fetchAll($where);
			if(count($rowsetRelatedItem) > 0)
			{
				//check if the physical FILE is available in uploads directory.
				$flagFileFound = true;

				foreach($rowsetRelatedItem as $rowRelatedItem)
				{
					$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			    	$rowsetCatalogFile = $tblCatalog->find($rowRelatedItem->itemGuid);

					$rowCatalogFile = $rowsetCatalogFile->current();
		    		$rowsetCatAtt = $rowCatalogFile->findDependentRowsetCatalogAttribute();

			    	$contentType = $rowsetCatAtt->findByAttributeGuid('docMimeType')->value;
					$systemname = $rowsetCatAtt->findByAttributeGuid('docSystemName')->value;
					$filename = $rowsetCatAtt->findByAttributeGuid('docOriginalName')->value;

					if(true)
					{
						$parentGuid = $rowRelatedItem->relatedGuid;
						$sDir1 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$systemname;
						$sDir2 = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$parentGuid.DIRECTORY_SEPARATOR.$systemname;

						if(file_exists($sDir1))
						{
							//$flagFileFound = true;
						}
						else 
							if(file_exists($sDir2))
							{
								//$flagFileFound = true;
							}
							else 
							{
								$flagFileFound = false;
							}
					}
				}
				
				if($flagFileFound)
				{
					$aReturn['isError'] = false;
					$aReturn['message'] = 'This item is SELLABLE.';
					$aReturn['code'] = 99;
					return $aReturn;
				}
				else
				{
					$aReturn['isError'] = true;
					$aReturn['message'] = 'We are Sorry. The document(s) you are requesting is still under review. Please check back later.';
					$aReturn['code'] = 5;
					return $aReturn;
				}
					
			}
			else
			{
				$aReturn['isError'] = true;
				$aReturn['message'] = 'We are Sorry. The document(s) you are requesting is still being prepared. Please check back later.';
				$aReturn['code'] = 5;
				return $aReturn;
			}
			*/
			
			
 		}
		else
		{
			$aReturn['isError'] = true;
			$aReturn['message'] = 'Can not find your selected item(s).';
			$aReturn['code'] = 10;
			return $aReturn;
		}
		
		
		
		//if ada record related document, but tidak ada dokumen fisik, then return false
		
		// if tidak ada record related document (blm ada dokumen/file diupload), then return false
		
		// if pernah dibeli user sebelumnya, then return false
		
	}
	protected function _updateCatalogAttribute($rowsetCatalogAttribute,$catalogGuid,$attributeGuid, $value)
	{
		if($rowsetCatalogAttribute->findByAttributeGuid($attributeGuid))
		{
			$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid($attributeGuid);
		}
		else 
		{
			$tblCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			$rowCatalogAttribute = $tblCatalogAttribute->fetchNew();
			$rowCatalogAttribute->catalogGuid = $catalogGuid;
			$rowCatalogAttribute->attributeGuid = $attributeGuid;
		}
		
		$rowCatalogAttribute->value = $value;
		$rowCatalogAttribute->save();
	}
	
	/**
	 * revisi june08 2010
	 * @param unknown_type $title
	 */
	
	private function checkTitle($title)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter()->query("SELECT * FROM KutuCatalog, KutuCatalogAttribute 
			WHERE KutuCatalogAttribute.attributeGuid = 'fixedTitle' 
			AND KutuCatalogAttribute.value = '$title'
			AND KutuCatalog.guid = KutuCatalogAttribute.catalogGuid");
		
		$rowset = $db->fetchAll(Zend_Db::FETCH_OBJ);
		
		if ($rowset)
		{
			echo $title.' is not available';
			exit();
		}
		
		/*
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find("title:$title");
		$solrNumFound = count($hits->response->docs);
		
		if ($solrNumFound !== 0)
		{
			echo $title.' is not available';
			exit();
		}
		*/
	}
}
?>