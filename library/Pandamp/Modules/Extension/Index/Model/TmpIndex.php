<?php
class Pandamp_Modules_Extension_Index_Model_TmpIndex
	extends Zend_Db_Table_Abstract {
		
	protected $_name = 'KutuIndexTmp';
	
	public function insert(array $data)
	{
		if(empty($data['guid']))
		{
	    	$guidMan = new Pandamp_Core_Guid();
	    	$data['guid'] = $guidMan->generateGuid();
		}
		if (empty($data['createdDate']))
		{
			$data['createdDate'] = date('Y-m-d H:i:s');
		}
		return parent::insert($data);
	}
	public function delete($where)
	{
		return parent::delete($where);
	}
    function countCatalogsTempIndex()
    {
    	$db = $this->_db->query
    	("SELECT count(*) count From KutuIndexTmp");
    	
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['count']);
    }
}
