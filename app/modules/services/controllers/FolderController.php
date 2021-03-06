<?php
class Services_FolderController extends Zend_Controller_Action
{
	public function fetchChildrenAction()
	{
		$parentGuid = ($this->_getParam('node'))? $this->_getParam('node') : '';
		$node = $this->_getParam('parentGuid');
		
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		$modelAroGroup = new Pandamp_Modules_Identity_Group_Folder_AroGroup();
		
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            echo "You aren't login";
        }
        
		$packageId = $auth->getIdentity()->packageId;
		
		// get group information
		$acl = Pandamp_Acl::manager();
		//$aReturn = $acl->getUserGroupIds(Zend_Auth::getInstance()->getIdentity()->username);
		$aReturn = $modelAroGroup->getUserGroup($packageId);
		
		if(!empty($parentGuid))
		{
			$aJson = array();
			$rowset = $tblFolder->fetchChildren($parentGuid);
			
			$i=0;
			foreach ($rowset as $row)
			{
				if (($aReturn['name'] == "Master") || ($aReturn['name'] == "Super Admin"))
					$content = 'all-access';
				else 
					$content = $row->type;

				if ($acl->getPermissionsOnContent('', $aReturn['name'], $content))
				{
					if ($row->title == "Kategori" || $row->title == "Peraturan" || $row->title == "Putusan")
					{
						$title = "<font color=red><b>".$row->title."</b></font>";
					}
					else 
					{
						$title = $row->title;
					}
					
					$aJson[$i]['text'] = $title; //. '&nbsp;('.$tblCatalogFolder->countCatalogsInFolderAndChildren($row->guid).')';
					$aJson[$i]['id'] = $row->guid;
					$checkLeaf = $tblFolder->fetchAll("path like '%$row->guid%'");
					if ($checkLeaf->count() > 0)
					{
						$aJson[$i]['leaf'] = 0;
						$aJson[$i]['cls'] = 'folder';					
					} else {
						$aJson[$i]['leaf'] = 1;
						$aJson[$i]['cls'] = 'leaf';					
					}
				}
				else 
				{
					continue;
				}
				$i++;
			}
			
			echo $json = Zend_Json::encode($aJson);
		}
	}
	public function fetchFolderAction()
	{
		echo '{"folder":['.str_replace(',]',']',$this->_traverseFolder('root','', 0).']}');
	}
	private function _traverseFolder($folderGuid, $sGuid, $level)
	{
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowSet = $tblFolder->fetchChildren($folderGuid);
		$sGuid = '';
		
		foreach($rowSet as $row)
		{
			$sTab = '';
			for($i=0;$i<$level;$i++)
				$sTab .= '-';
			
			if ($level == 0) 
			{
				$option = '{"guid":"'.$row->guid.'",'.'"title":"'.$row->title.'"},';
			} 
			else 
			{
				$option = '{"guid":"'.$row->guid.'",'.'"title":"'.$sTab.$row->title.'"},';
			}
			
			$sGuid .= $option . $this->_traverseFolder($row->guid, '', $level+1);
			
		}
		return $sGuid;
	}	
}
?>