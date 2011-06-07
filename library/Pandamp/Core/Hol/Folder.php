<?php

class Pandamp_Core_Hol_Folder
{
	public function delete($folderGuid)
	{
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowset = $tblFolder->find($folderGuid);
		if (count($rowset))
		{
			$row = $rowset->current();
			$row->delete();
		}
	}
	public function forceDelete($folderGuid)
	{
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowset = $tblFolder->fetchChildren($folderGuid);
		
		$rowFolder = $tblFolder->find($folderGuid)->current();
		
		foreach ($rowset as $row)
		{
			$this->forceDelete($row->guid);
		}
		
		$rowsetCatalogFolder = $rowFolder->findDependentRowsetCatalogFolder();
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$holCatalog = new Pandamp_Core_Hol_Catalog();
		if(count($rowsetCatalogFolder))
		{
			foreach($rowsetCatalogFolder as $rowCatalogFolder)
			{
				$rowCatalog = $tblCatalog->find($rowCatalogFolder->catalogGuid)->current();
				$holCatalog->delete($rowCatalog->guid);
			}
			
			$this->delete($rowFolder->guid);
		}
		else 
		{
			$this->delete($rowFolder->guid);
		}
	}
}

?>