<?php

/**
 * Helper viewer Catalog Title
 * 
 * 
 */

class Pandamp_Controller_Action_Helper_GetClinicTitle
{
	public function getClinicTitle($catalogGuid)
	{ 
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$title = $modelCatalogAttribute->getCatalogAttributeValue($catalogGuid,'fixedCommentTitle');
		
		if(isset($title) && !empty($title))
			return $title;
		else
			return 'No-Title';
	}
}

?>