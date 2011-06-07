<?php

class Pandamp_Core_Hol_Banner
{
	function save($aData)
	{
		if (isset($aData['new_url1']))		
			$aData['new_url'] = $aData['new_url1']."x".$aData['new_url2'];
			
		$tblPowerban = new Pandamp_Modules_Misc_Banner_Model_Powerban();
		
		$max = $tblPowerban->maxPowerban();
		$new_added = $max + 1;
		
		if (isset($aData['new_dis_times']) and ($aData['new_dis_times'] == "EV")) {
			$aData['new_dis_times'] = $aData['new_dis_times_ev'];
		}
		
        if ($aData['new_dis_type'] == 3) {
        	$aData['new_dis_type'] = $aData['new_dis_type']."|".$aData['new_dis_type_loc'];
        }else{
        	$aData['new_dis_type'] = $aData['new_dis_type']."|0";
        }
        
        $guid = (isset($aData['guid']) && !empty($aData['guid']))? $aData['guid'] : rand(1,9999);
        
		$registry = Zend_Registry::getInstance();
		$files = $registry->get('files');
		
		/*
		echo "<pre>";
		//print_r($files);
		echo 'jumlah'.count($files['attachfile']['name']);
		echo "</pre>";
		die();
		*/
		
		if(isset($files['new_src'])) {
			$file = $files['new_src'];
			if(isset($files['new_src']['name']) && !empty($files['new_src']['name'])) {
				if (isset($aData['fa']) && !empty($aData['fa'])) {
					$fname = strtolower($aData['fa']);
					$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'iklan'.DIRECTORY_SEPARATOR.$fname;
					$sDir2 = ROOT_URL.'/iklan/'.$fname;
				}
				else 
				{
					$sDir = ROOT_DIR.'/iklan';
					$sDir2 = ROOT_URL.'/iklan';
				}
				
				$target_path = $sDir2 . '/' . strtolower(str_replace(' ','_',$file['name']));
				
				if(is_dir($sDir))
		    	{
		    		move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . strtolower(str_replace(' ','_',$file['name'])));
		    	}
		    	else 
		    	{
		    		if(mkdir($sDir))
		    		{
		    			move_uploaded_file($file['tmp_name'], $sDir . DIRECTORY_SEPARATOR . strtolower(str_replace(' ','_',$file['name'])));
		    		}
		    		else 
		    		{
		    			//if enters here, then it means, you CAN'T create the folder, maybe because the safe mode is ON.
		    			//save the file in the upload/files folder 
		    			move_uploaded_file($file['tmp_name'], ROOT_DIR.DIRECTORY_SEPARATOR.'iklan'.DIRECTORY_SEPARATOR . strtolower(str_replace(' ','_',$file['name'])));
		    		}
		    	}
				
				$attachfile = array();
		    	$num_files = count($files['attachfile']['name']) -1;
		    	if ($num_files !== 0) {
		    	for ($i=0; $i < $num_files; $i++) {
		    		$attachfile[] = strtolower(str_replace(' ','_',$files['attachfile']['name'][$i]));
		    		if(isset($files['attachfile']['name'][$i]) && !empty($files['attachfile']['name'][$i]))
		    		{
						if(is_dir($sDir))
				    	{
				    		move_uploaded_file($files['attachfile']['tmp_name'][$i], $sDir . DIRECTORY_SEPARATOR . strtolower(str_replace(' ','_',$files['attachfile']['name'][$i])));
				    	}
				    	else 
				    	{
				    		if(mkdir($sDir))
				    		{
				    			move_uploaded_file($files['attachfile']['tmp_name'][$i], $sDir . DIRECTORY_SEPARATOR . strtolower(str_replace(' ','_',$files['attachfile']['name'][$i])));
				    		}
				    		else 
				    		{
				    			move_uploaded_file($files['attachfile']['tmp_name'][$i], ROOT_DIR.DIRECTORY_SEPARATOR.'iklan'.DIRECTORY_SEPARATOR . strtolower(str_replace(' ','_',$files['attachfile']['name'][$i])));
				    		}
				    	}
		    		}
		    	}
		    	}
		    	
//				echo "<pre>";
//				$z = serialize($attachfile);
//				$np = unserialize($z);
//				print_r($np);
//				echo "</pre>";
//				die();
		    	
//		    	chmod($sDir,0644);
			}
		}
		
        $rowset = $tblPowerban->find($guid);
        
        if (count($rowset)) {
        	$row = $rowset->current();
        	
			//$cache = Zend_Registry::get('cache');
			//$cacheKey = "fb_zid_".$row->zone;
			//$cache->remove($cacheKey);
			
        	$row->name = (isset($aData['new_name']))?$aData['new_name']:$row->name;
        	$row->alt = (isset($aData['new_alt']))?$aData['new_alt']:$row->alt;
        	$row->url = (isset($aData['new_url']))?$aData['new_url']:$row->url;
        	
            if (($aData['new_target'] == '_self') and ($aData['new_dis_type'] == 2)) {
            	$aData['new_target'] = '_blank';
            }
            
            $row->dis_times = (isset($aData['new_dis_times']))?$aData['new_dis_times']:$row->dis_times;
            $row->modifiedBy = (isset($aData['username']))?$aData['username']:$row->modifiedBy;
            $row->dtype = (isset($aData['new_dis_type']))?$aData['new_dis_type']:$row->dtype;
            $row->target = (isset($aData['new_target']))?$aData['new_target']:$row->target;
            $row->zone = (isset($aData['new_ban_zone']))?$aData['new_ban_zone']:$row->zone;
			$row->publishedDate = (isset($aData['publishedDate']))?$aData['publishedDate']:$row->publishedDate;
			$row->expiredDate = (isset($aData['expiredDate']))?$aData['expiredDate']:$row->expiredDate;
			$row->modifiedDate = date("Y-m-d h:i:s");
			$row->status = (isset($aData['status']))?$aData['status']:$row->status;
			
        }
        else 
        {
        	$row = $tblPowerban->fetchNew();
        	
        	$row->guid = $guid;
        	$row->name = (isset($aData['new_name']))?$aData['new_name']:'';
        	$row->src = $target_path;
        	$row->fa = (isset($aData['fa']))?$aData['fa']:'';
        	$row->af = (isset($attachfile))?serialize($attachfile):'';
        	$row->alt = (isset($aData['new_alt']))?$aData['new_alt']:'';
        	$row->url = (isset($aData['new_url']))?$aData['new_url']:'';
        	$row->type = (isset($aData['new_type']))?$aData['new_type']:'';
        	$row->dis_times = $aData['new_dis_times'];
        	$row->added = $new_added;
        	$row->createdBy = (isset($aData['username']))?$aData['username']:'';
        	$row->modifiedBy = $row->createdBy;
        	$row->dtype = $aData['new_dis_type'];
        	$row->target = $aData['new_target'];
        	$row->zone = $aData['new_ban_zone'];
			$row->publishedDate = (isset($aData['publishedDate']))?$aData['publishedDate']:'0000-00-00 00:00:00';
			$row->expiredDate = (isset($aData['expiredDate']))?$aData['expiredDate']:'0000-00-00 00:00:00';
			$row->createdDate = date("Y-m-d h:i:s");
			$row->modifiedDate = $row->createdDate;
			$row->deletedDate = '0000-00-00 00:00:00';
			$row->status = (isset($aData['status']))?$aData['status']:0;
        }
        
		try 
		{
			$row->save();
			
			//$cache = Zend_Registry::get('cache');
			//$cacheKey = "fb_zid_".$aData['new_ban_zone'];
			//$cache->remove($cacheKey);
		}
		catch (Exception $e)
		{
			throw new Zend_Exception($e->getMessage());
		}
		
	}
	function delete($guid)
	{
		$tblPowerban = new Pandamp_Modules_Misc_Banner_Model_Powerban();
		$rowset = $tblPowerban->find($guid);
		if (count($rowset)) {
			$row = $rowset->current();
			$split = explode("/",$row->src);
			if (isset($row->fa)) {
				$sDir = ROOT_DIR.'/iklan/'.$row->fa;
				if (isset($split) && !empty($split)) {
					if (file_exists($sDir."/".$split[6])) { unlink($sDir."/".$split[6]); }
				}
				if (isset($row->af) && !empty($row->af)) {
//					$split2 = explode("/",$row->af);
//					if (file_exists($sDir."/".$split2[7])) { unlink($sDir."/".$split2[7]); }
					$attachfile = unserialize($row->af);
					foreach ($attachfile as $file)
					{
						if (file_exists($sDir."/".$file)) { unlink($sDir."/".$file); }
//						echo $file.'<br>';
					}
//					die();
				}
			}
			else 
			{
				$sDir = ROOT_DIR.'/iklan';
				if (isset($split) && !empty($split)) {
					if (file_exists($sDir."/".$split[6])) { unlink($sDir."/".$split[6]); }
				}
				if (isset($row->af) && !empty($row->af)) {
//					$split2 = explode("/",$row->af);
//					if (file_exists($sDir."/".$split2[6])) { unlink($sDir."/".$split2[6]); }
					$attachfile = unserialize($row->af);
					foreach ($attachfile as $file)
					{
						if (file_exists($sDir."/".$file)) { unlink($sDir."/".$file); }
					}
				}
			}
			
			$cache = Zend_Registry::get('cache');
			$cacheKey = "fb_zid_".$row->zone;
			$cache->remove($cacheKey);
			
			$row->delete();
		}
	}
	function saveZone($aData)
	{
		$title = $aData['title'];
		$zid = ($aData['zid'])?$aData['zid']:'';
		
		$tblPowerbanZone = new Pandamp_Modules_Misc_Banner_Zone_Model_PowerbanZones();
		$rowset = $tblPowerbanZone->find($zid);
		
		if (count($rowset)) {
			$row = $rowset->current();
			$row->zname = $title;
		}
		else 
		{
			$row = $tblPowerbanZone->fetchNew();
			$zid = rand(1,9999);
			$row->zid = $zid;
			$row->zname = $title;
		}
		$row->save();
	}
	function deleteZone($zid)
	{
		$tblPowerbanZone = new Pandamp_Modules_Misc_Banner_Zone_Model_PowerbanZones();
		$rowset = $tblPowerbanZone->find($zid);
		if (count($rowset)) {
			$row = $rowset->current();
			$row->delete();
			$regcache = Zend_Registry::getInstance();
			$cache = $regcache->get('regcache');
			$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
		}
	}
}

?>