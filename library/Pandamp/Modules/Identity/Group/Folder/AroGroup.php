<?php
class Pandamp_Modules_Identity_Group_Folder_AroGroup extends Zend_Db_Table_Abstract
	implements Pandamp_BeanContext_Decoratable
{
	protected $_name = 'gacl_aro_groups';
	
    protected function  _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db2');

        parent::_setupDatabaseAdapter();
    }
    public function getUserGroup($packageId)
    {
        $select = $this->select()->from($this)
                    ->where("id = $packageId");

        $result = $this->fetchRow($select);

        return $result;
    }
	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return '';
    }
    public function getDecoratableMethods()
    {
    	return array(
    	);
    }
}