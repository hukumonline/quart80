<?php
class Pandamp_Modules_Extension_Bbcode_Model_Bbcode
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
	
	protected $_name = 'bbcode_emoticons';
	
	
	
	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Extension_Bbcode';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>