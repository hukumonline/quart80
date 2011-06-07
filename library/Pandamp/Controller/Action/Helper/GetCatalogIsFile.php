<?php

class Pandamp_Controller_Action_Helper_GetCatalogIsFile
{
	public function GetCatalogIsFile($catalogGuid)
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->find($catalogGuid)->current();
		
		if ($rowset)
		{
			if ($rowset->profileGuid == "kutu_doc") return "<font color=red>[file]</font>";
		}
		else 
		{
			return '';
		}
	}
}

?>