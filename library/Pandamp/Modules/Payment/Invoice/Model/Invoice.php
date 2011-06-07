<?php
class Pandamp_Modules_Payment_Invoice_Model_Invoice extends Zend_Db_Table_Abstract
	implements Pandamp_BeanContext_Decoratable
{
	protected $_name = 'KutuUserInvoice';
	protected $_referenceMap = array(
		'User' => array(
			'columns'		=> 'uid',
			'refTableClass'	=> 'Pandamp_Modules_Identity_User_Model_User',
			'refColumns'	=> 'guid'
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