<?php
class Pandamp_Controller_Action_Helper_GetFotoAttributeName extends Zend_Controller_Action_Helper_Abstract
{
	public function getFotoAttributeName($catalogGuid)
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		if (isset($rowset))
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
			
			return $title;
	}
}
?>