<?php
class Pandamp_Controller_Action_Helper_GetClinicCategory
{
	public function getClinicCategory($catalogGuid)
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		if ($rowset)
		{
			$category = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedKategoriKlinik');
			/* Get Category from profile clinic_category */
			$findCategory = $decorator->getCatalogByGuidAsEntity($category);
			if (isset($findCategory)) {
				$category = $modelCatalogAttribute->getCatalogAttributeValue($findCategory->getId(),'fixedTitle');
			}
			
			return $category;
		}
	}
}
?>