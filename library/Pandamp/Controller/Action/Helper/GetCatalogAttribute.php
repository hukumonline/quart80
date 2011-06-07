<?php

class Pandamp_Controller_Action_Helper_GetCatalogAttribute
{
	public function getCatalogAttribute($catalogGuid, $value)
	{ 
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		if ($rowset) {
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			$attr = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),$value);
			
			if(isset($attr) && !empty($attr))
				return $attr;
			else
				return 'No-Title';
			
			
		}
	}
}

?>