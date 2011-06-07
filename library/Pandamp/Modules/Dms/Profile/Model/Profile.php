<?php
class Pandamp_Modules_Dms_Profile_Model_Profile
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {

	protected $_name = 'KutuProfile';
		
	public function getProfileByPG($profileGuid)
	{
		$row = $this->fetchRow($this->select()->where('guid=?',$profileGuid));
		
		return $row;
	}
		
	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Dms_Profile';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>