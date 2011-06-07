<?php
class Pandamp_Modules_Identity_Education_Model_Education extends Zend_Db_Table_Abstract
	implements Pandamp_BeanContext_Decoratable
{
	protected $_name = 'KutuUserEducation';
	
	
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