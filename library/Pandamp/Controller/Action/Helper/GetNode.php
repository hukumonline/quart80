<?php

class Pandamp_Controller_Action_Helper_GetNode
{
	public function getNode($guid)
	{
		$modelCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
		$rowset = $modelCatalogFolder->fetchRow("catalogGuid='".$guid."'");
		
		if ($rowset)
			return $rowset->folderGuid;
		else 
			return '';
	}
}

?>