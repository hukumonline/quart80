<?php
class Pandamp_Modules_Misc_Menu_Model_Menu
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
	
	protected $_name = 'folder';
	protected $_rowClass = 'Pandamp_Modules_Misc_Menu_Model_Row_Menu';

	
    public function fetchChildren($parentGuid)
    {
    	if($parentGuid == 'root')
    	{
    		return $this->fetchAll("parentGuid=guid",'title ASC');
    	}
    	else 
    	{
			return $this->fetchAll("parentGuid = '$parentGuid' AND NOT parentGuid=guid",'title ASC');
    	}
    }
	public function getMenu($node='root')
	{
		$parentGuid = $node;
		
		if($parentGuid == 'root')
    	{
    		return $this->fetchAll("parentGuid=guid AND (cmsParams like '%".'"menu":true'."%' OR cmsParams is NULL OR cmsParams = '')",'viewOrder ASC');
    	}
    	else 
    	{
			return $this->fetchAll("parentGuid = '$parentGuid' AND NOT parentGuid=guid AND (cmsParams like '%".'"menu":true'."%' OR cmsParams is NULL OR cmsParams = '')",'viewOrder ASC');
    	}
	}
    
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Misc_Menu';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>