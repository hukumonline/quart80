<?php
class Pandamp_Modules_Misc_Banner_Statistic_Model_PowerbanStatistik extends Zend_Db_Table_Abstract
	implements Pandamp_BeanContext_Decoratable
{
	protected $_name = 'powerban_stats_visits';

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