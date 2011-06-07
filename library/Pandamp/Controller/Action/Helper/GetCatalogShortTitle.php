<?php

class Pandamp_Controller_Action_Helper_GetCatalogShortTitle
{
	public function getCatalogShortTitle($guid)
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->find($guid)->current();
		if ($rowset)
			return $rowset->shortTitle;
		else 
			return 'no-title';
	}
}

?>