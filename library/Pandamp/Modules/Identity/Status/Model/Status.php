<?php
class Pandamp_Modules_Identity_Status_Model_Status extends Zend_Db_Table_Abstract
	implements Pandamp_BeanContext_Decoratable
{
	protected $_name = 'KutuUserStatus';
	protected $_referenceMap = array(
		'User' => array(
			'columns'		=> 'accountStatusId',
			'refTableClass'	=> 'Pandamp_Modules_Identity_User_Model_UserDetail',
			'refColumns'	=> 'periodeId'
		)
	);
	
	
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