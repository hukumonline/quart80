<?php

class Pandamp_Modules_Dms_Folder_Model_Row_Folder extends Zend_Db_Table_Row_Abstract 
{
	protected function _insert()
	{
		if(empty($this->guid))
		{
    		$guidMan = new Pandamp_Core_Guid();
    		$this->guid = $guidMan->generateGuid();
		}
		
		if($this->parentGuid == 'root')
		{
			$this->path = '';
			$this->parentGuid = $this->guid;
		}
		else 
		{
			$parentFolder = $this->_getTable()->find($this->parentGuid)->current();
			
			$this->path = $parentFolder->path.$parentFolder->guid.'/';
		}
		
	}
	
	protected function _update()
	{
		//echo $this->guid;
	}
	protected function _delete()
	{
		$rowsetCatalog = $this->findDependentRowsetCatalogFolder();
		$rowsetChildren = $this->fetchChildren();
		if(count($rowsetCatalog) || count($rowsetChildren))
		{
			throw new Exception('Deletion Failed! Folder may contain catalog or sub-folder.');
		}
		else 
		{
			//delete row in lucene index (if folder is indexed)
		}
		
	}
	protected function _updatePath()
	{
		$parentFolder = $this->_getTable()->find($this->parentGuid)->current();
		$this->path = $parentFolder->path.$targetFolder->guid.'/';
		
	}
	
	public function test()
	{
		echo $this->getTableClass();
	}
	
	public function findDependentRowsetCatalogFolder()
	{
		return $this->findDependentRowset('Pandamp_Modules_Dms_Catalog_Model_CatalogFolder');
	}
	
	public function copyFolderContent($currentFolderGuid, $newCatalogGuid)
	{
		$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		$rowset = $tblCatalogFolder->fetchAll("folderGuid = '$currentFolderGuid'");
		
		if (count($rowset) > 0)
		{
			foreach ($rowset as $row)
			{
				$newContent = $tblCatalogFolder->createRow();
				$newContent->folderGuid = $newCatalogGuid;
				$newContent->catalogGuid = $row->catalogGuid;
				$newContent->save();
			}
		}
		
	}
	
	public function copy($targetFolderGuid, $currentFolderGuid)
	{
		$currentFolder = $this->_getTable()->find($currentFolderGuid)->current();
		
		$this->title = $currentFolder->title;
		$this->description = $currentFolder->description;
		
		if($targetFolderGuid == 'root')
		{
			$this->parentGuid = $targetFolderGuid;
			$this->path = '';
			$cGuid = $this->save();
				
			$this->copyFolderContent($currentFolderGuid,$cGuid);
		
			$rowset = $this->_getTable()->fetchAll("path LIKE '%".$currentFolderGuid."/%'");
			foreach ($rowset as $row)
			{
				$rowChild = $this->_getTable()->createRow();
				$rowChild->title = $row->title;
				$rowChild->description = $row->description;
				$rowChild->parentGuid = $this->guid;
				$catalogGuid = $rowChild->save();
				
				$this->copyFolderContent($row->guid,$catalogGuid);
			}
		}
		else 
		{
			$targetFolder = $this->_getTable()->find($targetFolderGuid)->current();
			
			//check if targetFolderGuid is a child of this folder
			$rowset = $this->_getTable()->fetchAll("guid='$targetFolderGuid' AND path LIKE '%".$currentFolderGuid."/%'");
			
			if(count($rowset) > 0)
			{
				throw New Zend_Exception('Can\'t copy folder to children');
			}
			else 
			{
				$this->parentGuid = $targetFolderGuid;
				$this->path = $targetFolder->path.$targetFolder->guid.'/';
				$cGuid = $this->save();
				
				$this->copyFolderContent($currentFolderGuid,$cGuid);
				
				//update all children
				$rowset = $this->_getTable()->fetchAll("guid !='$targetFolderGuid' AND path LIKE '%".$currentFolderGuid."/%'");
				foreach ($rowset as $row)
				{
					$rowChild = $this->_getTable()->createRow();
					$rowChild->title = $row->title;
					$rowChild->description = $row->description;
					$rowChild->parentGuid = $this->guid;
					$catalogGuid = $rowChild->save();
					
					$this->copyFolderContent($row->guid,$catalogGuid);
				}
			}
		}
	}
	
	public function move($targetFolderGuid)
	{
		if($targetFolderGuid == 'root')
		{
			if($this->guid == $this->parentGuid)
			{
				
			}
			else 
			{
				$this->parentGuid = $this->guid;
				$originalPath = $this->path;
				$this->path = '';
				$this->save();
				
				$rowset = $this->_getTable()->fetchAll("path LIKE '%".$this->guid."/%'");
				foreach ($rowset as $row)
				{
					$row->path = str_replace($originalPath, $this->path, $row->path);
					$row->save();
				}
			}
		}
		else 
		{
			$targetFolder = $this->_getTable()->find($targetFolderGuid)->current();
			
			//check if targetFolderGuid is a child of this folder
			$rowset = $this->_getTable()->fetchAll("guid='$targetFolderGuid' AND path LIKE '%".$this->guid."/%'");
			
			$originalPath = $this->path;
			
			if(count($rowset) > 0)
			{
				throw New Zend_Exception('Can\'t move folder to children');
			}
			else 
			{
				$this->parentGuid = $targetFolderGuid;
				$this->path = $targetFolder->path.$targetFolder->guid.'/';
				$this->save();
				
				//update all children
				$rowset = $this->_getTable()->fetchAll("guid !='$targetFolderGuid' AND path LIKE '%".$this->guid."/%'");
				foreach ($rowset as $row)
				{
					$row->path = str_replace($originalPath, $this->path, $row->path);
					$row->save();
				}
			}
		}
		
	}
	
	public function fetchChildren()
	{
		return $this->_getTable()->fetchChildren($this->guid);
	}
	
	public function findRowsetCatalog($startFrom=0, $limit=0)
	{
		$tblCatalog = new Kutu_Core_Orm_Table_Catalog();
		return $tblCatalog->fetchFromFolder($this->guid, $startFrom, $limit);
	}
}