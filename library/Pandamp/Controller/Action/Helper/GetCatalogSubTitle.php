<?php

/**
 * Helper viewer Catalog Title
 * 
 * @author Himawan Anindya Putra <putra@langit.biz>
 * @package Kutu
 * 
 */

class Pandamp_Controller_Action_Helper_GetCatalogSubTitle
{
	public function getCatalogSubTitle($catalogGuid)
	{ 
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$subTitle = $modelCatalogAttribute->getCatalogAttributeValue($catalogGuid,'fixedSubTitle');
		
		if(isset($subTitle) && !empty($subTitle))
			return $subTitle;
		else
			return 'No-Title';
	}
}

?>