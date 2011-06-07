<?php
class Pandamp_Modules_Misc_Option_Model_Option
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
	
	protected $_name = 'options';

	
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
    	return 'Pandamp_Modules_Misc_Option';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>