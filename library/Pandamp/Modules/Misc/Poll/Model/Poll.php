<?php
class Pandamp_Modules_Misc_Poll_Model_Poll
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
	
	protected $_name = 'polls';
	protected $_rowClass = 'Pandamp_Modules_Misc_Poll_Model_Row_Poll';
	
	public function insert (array $data)
	{
		if (empty($data['guid']))
		{
			$guidMan = new Pandamp_Core_Guid;
			$data['guid'] = $guidMan->generateGuid();
		}
		
		return parent::insert($data);
		
	}
	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Misc_Poll';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>