<?php
class Pandamp_Modules_Misc_Banner_Zone_Model_PowerbanZones extends Zend_Db_Table_Abstract
	implements Pandamp_BeanContext_Decoratable
{
	protected $_name = 'powerban_zones';

    function countPowerbanZones()
    {
    	$db = $this->_db->query
    	("SELECT count(*) count FROM powerban_zones");
    	
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['count']);
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