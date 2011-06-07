<?php
class Pandamp_Modules_Dms_Profile_Model_ProfileAttribute
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {

	protected $_name = 'KutuProfileAttribute';
    protected $_referenceMap    = array(
        'Profile' => array(
            'columns'           => 'profileGuid',
            'refTableClass'     => 'Pandamp_Modules_Dms_Profile_Model_Profile',
            'refColumns'        => 'guid'
        ),
        'Attribute' => array(
            'columns'           => 'attributeGuid',
            'refTableClass'     => 'Pandamp_Modules_Dms_Catalog_Model_Attribute',
            'refColumns'        => 'guid'
        )
     );
	
	
	public function getPAByPG($profileGuid)
	{
		$row = $this->fetchAll($this->select()->where('profileGuid=?',$profileGuid),'viewOrder ASC');
		
		return $row;
	}
		
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Dms_ProfileAttribute';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>