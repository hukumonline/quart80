<?php
class Pandamp_Modules_Misc_Banner_Model_Powerban extends Zend_Db_Table_Abstract
	implements Pandamp_BeanContext_Decoratable
{
	protected $_name = 'powerbanner';

    function countPowerban()
    {
    	$db = $this->_db->query
    	("SELECT count(*) count FROM powerbanner");
    	
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['count']);
    }
    function maxPowerban()
    {
    	$db = $this->_db->query
    	("SELECT MAX(added) max FROM powerbanner");
    	
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['max']);
    }
    function sumPowerban()
    {
    	$db = $this->_db->query
    	("SELECT SUM(dised_times) sum FROM powerbanner");
    	
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['sum']);
    }
    public function fetchBanner($zid='')
    {
    	// try to fetch from the cache first
    	//$cache = Zend_Registry::get('cache');
    	//$cacheKey = "fb_zid_".$zid;
    	//$rows = $cache->load($cacheKey);
    	//if (!$rows) {
	    	$now = date('Y-m-d H:i:s');
	    	$select = $this->select()
	    				->from($this)
	    				->where('status=?',99)
	    				->where('zone=?',$zid)
	    				->where("publishedDate = '0000-00-00 00:00:00' OR publishedDate <= '$now'")
	    				->where("expiredDate = '0000-00-00 00:00:00' OR expiredDate >= '$now'")
	    				->order('publishedDate DESC');
	    	
	    	$rows = $this->fetchAll($select);
	    	
	    	//$cache->save($rows);
    	//}
    	return $rows;
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