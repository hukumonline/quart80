<?php
class Admin_BrowserController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
	}
	function viewInNewTabAction()
	{
		$catalogGuid = ($this->_getParam('catalogGuid'))? $this->_getParam('catalogGuid') : '';
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$this->view->catalogGuid = $catalogGuid;
		$this->view->folderGuid = $folderGuid;
	}
	function searchAction()
	{
		$query = ($this->_getParam('searchQuery'))? $this->_getParam('searchQuery') : '';
		$this->view->query = $query;
	}
    function downloadFileAction()
    {
    	$catalogGuid = $this->_getParam('guid');
    	$parentGuid = $this->_getParam('parent');
    	
    	$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
    	$rowsetCatalog = $tblCatalog->find($catalogGuid);
    	
    	if(count($rowsetCatalog))
    	{
    		$rowCatalog = $rowsetCatalog->current();
    		$rowsetCatAtt = $rowCatalog->findDependentRowsetCatalogAttribute();
    		
	    	$contentType = $rowsetCatAtt->findByAttributeGuid('docMimeType')->value;
			$filename = $systemname = $rowsetCatAtt->findByAttributeGuid('docSystemName')->value;
			$oriName = $rowsetCatAtt->findByAttributeGuid('docOriginalName')->value;
			
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
					
					if(file_exists($sDir1))
					{
						$flagFileFound = true;
						header("Content-type: $contentType");
						header("Content-Disposition: attachment; filename=$filename");
						@readfile($sDir1);
					}
					else 
						if(file_exists($sDir2))
						{
							$flagFileFound = true;
							header("Content-type: $contentType");
							header("Content-Disposition: attachment; filename=$oriName");
							@readfile($sDir2);
						}
						else 
							$flagFileFound = false;
				}
			}
			
    	}
    	else 
    	{
    		echo 'NO FILE';
    	}
    }
}
?>