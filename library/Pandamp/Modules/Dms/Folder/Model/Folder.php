<?php
class Pandamp_Modules_Dms_Folder_Model_Folder
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {

	protected $_name = 'KutuFolder';
    protected $_rowClass = 'Pandamp_Modules_Dms_Folder_Model_Row_Folder';
    protected $_dependentTables = array('Pandamp_Modules_Dms_Catalog_Model_CatalogFolder');
	
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
    		return $this->fetchAll("parentGuid=guid AND (cmsParams like '%".'"menu":true'."%')",'viewOrder ASC');
    	}
    	else 
    	{
			return $this->fetchAll("parentGuid = '$parentGuid' AND NOT parentGuid=guid AND (cmsParams like '%".'"menu":true'."%')",'viewOrder ASC');
    	}
	}
	public function getIdByTitle($st)
	{
        $row = $this->fetchRow($this->select()->where("cmsParams like '%".'"menu":true,"st":"'.$st.'"'."%'"));

        return $row;
	}
    
    
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Dms_Folder';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		'getIdByTitle'
    	);
    }
}
?>