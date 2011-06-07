<?php
class Pandamp_Modules_Dms_Catalog_Model_CatalogFolder
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
	
	protected $_name = 'KutuCatalogFolder';
	protected $_referenceMap    = array(
        'Catalog' => array(
            'columns'           => 'catalogGuid',
            'refTableClass'     => 'Pandamp_Modules_Dms_Catalog_Model_Catalog',
            'refColumns'        => 'guid'
        ),
        'Folder' => array(
            'columns'           => 'folderGuid',
            'refTableClass'     => 'Pandamp_Modules_Dms_Folder_Model_Folder',
            'refColumns'        => 'guid'
        )
    );

	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Dms_CatalogFolder';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>