<?php
class Pandamp_Modules_Dms_Catalog_Model_Attribute
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
	
	protected $_name = 'KutuAttribute';

	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return '';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>