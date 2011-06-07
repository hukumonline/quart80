<?php
class Pandamp_Modules_Misc_Setting_Model_Setting
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
	
	protected $_name = 'KutuSetting';
	protected $_schema = 'hid';
    protected function  _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db2');

        parent::_setupDatabaseAdapter();
    }
	
	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Misc_Setting';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>