<?php
class Pandamp_Modules_Identity_UserFinance_Model_UserFinance extends Zend_Db_Table_Abstract
	implements Pandamp_BeanContext_Decoratable
{
	protected $_name = 'KutuUserFinance';
	
	public function getUserFinance($where){
	$db = $this->_db->query("SELECT KUF.*, KU.fullName AS FN, KU.username AS UN, KU.createdDate, KU.createdBy, KU.updatedDate, KU.updatedBy FROM KutuUserFinance AS KUF, KutuUser AS KU WHERE userId = '$where' AND KU.guid = KUF.userId ");
	
	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
	
	$data  = array(
		'table'    => $this,
		'data'     => $dataFetch,
		'rowClass' => $this->_rowClass,
		'stored'   => true
	);

	Zend_Loader::loadClass($this->_rowsetClass);
	return new $this->_rowsetClass($data);
	}
	
	
	
	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Identity_UserFinance';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}