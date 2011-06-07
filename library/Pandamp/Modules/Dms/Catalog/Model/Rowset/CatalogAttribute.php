<?php
class Pandamp_Modules_Dms_Catalog_Model_Rowset_CatalogAttribute extends Zend_Db_Table_Rowset_Abstract
{
	function findByAttributeGuid($attributeGuid)
	{
        foreach ($this as $row) {
            if ($row->attributeGuid == $attributeGuid) 
            {
                return $row;
            }
        }
        return null;
	}
}
?>