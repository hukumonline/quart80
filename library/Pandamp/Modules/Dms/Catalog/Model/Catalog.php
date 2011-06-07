<?php
class Pandamp_Modules_Dms_Catalog_Model_Catalog
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {

	protected $_name = 'KutuCatalog';
	protected $_rowClass = 'Pandamp_Modules_Dms_Catalog_Model_Row_Catalog';
    protected $_rowsetClass = 'Pandamp_Modules_Dms_Catalog_Model_Rowset_CatalogAttribute';
    protected $_dependentTables = array('Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute','Pandamp_Modules_Dms_Catalog_Model_CatalogFolder');
	
    public function getCatalogByGuid($guid)
    {
        $row = $this->fetchRow($this->select()->where('guid=?', $guid));

        return $row;
    }
    
    // public function fetchFromFolder($folderGuid, $start = 0 , $end = 0, $page='depan')
    public function fetchFromFolder($folderGuid, $start = 0 , $end = 0)
    {
    	// try to fetch from the cache first
    	// $cache = Zend_Registry::get('cache');
    	// $cacheKey = "fff_fg_".$folderGuid.'_page_'.$page;
    	// $rows = $cache->load($cacheKey);
    	//if (!$rows) {
	    	$now = date('Y-m-d H:i:s');
	    	$select = $this->select()
	    				->from($this)
	    				->join('KutuCatalogFolder','KutuCatalog.guid=KutuCatalogFolder.catalogGuid',array())
	    				->where('KutuCatalog.status=?',99)
	    				->where('KutuCatalogFolder.folderGuid=?',$folderGuid)
	    				->where("KutuCatalog.publishedDate = '0000-00-00 00:00:00' OR KutuCatalog.publishedDate <= '$now'")
	    				->where("KutuCatalog.expiredDate = '0000-00-00 00:00:00' OR KutuCatalog.expiredDate >= '$now'")
	    				->order('KutuCatalog.publishedDate DESC')
	    				->limit($end,$start);
	    	
	    	$rows = $this->fetchAll($select);
	    	
	    	// $cache->save($rows,"catalog");
    	// }
    	return $rows;
    }
    public function getWartaCount($folderGuid)
    {
    	$now = date('Y-m-d H:i:s');
        $select = $this->select()
                  ->from($this, array(
                    'COUNT(*) as count'
                  ))
                  ->join('KutuCatalogFolder','KutuCatalog.guid=KutuCatalogFolder.catalogGuid',array())
                  ->where('KutuCatalog.status=?',99)
                  ->where('KutuCatalogFolder.folderGuid=?',"$folderGuid")
	    		  ->where("KutuCatalog.publishedDate = '0000-00-00 00:00:00' OR KutuCatalog.publishedDate <= '$now'")
	    		  ->where("KutuCatalog.expiredDate = '0000-00-00 00:00:00' OR KutuCatalog.expiredDate >= '$now'");
                  
        $row = $this->fetchRow($select);

        return ($row !== null) ? $row->count : 0;
    }
    /*
    function getLatestDocs()
    {
    	$cache = Zend_Registry::get('cache');
    	$cacheKey = "gld";
    	$rows = $cache->load($cacheKey);
    	if (!$rows) {
	    	$db = $this->_db->query
	    	("SELECT KutuCatalogAttribute.catalogGuid as guid, value FROM KutuCatalogAttribute WHERE KutuCatalogAttribute.attributeGuid = 'fixedYear' and KutuCatalogAttribute.catalogGuid IN (select KutuCatalog.guid from KutuCatalog,KutuCatalogFolder where KutuCatalog.guid=KutuCatalogFolder.catalogGuid) order by KutuCatalogAttribute.value DESC limit 5");
	    	
	    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
	    	$rows  = array(
	            'table'    => $this,
	            'data'     => $dataFetch,
	            'rowClass' => $this->_rowClass,
	            'stored'   => true
	        );
    			    	
	    	$cache->save($rows);
    	}
        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($rows);
    }
    */
    function fetchFromFolderByDate($folderGuid, $gDate, $start = 0 ,$end = 0)
    {
    	// try to fetch from the cache first
    	//$cache = Zend_Registry::get('cache');
    	//$cacheKey = "fffbd_fg_".$folderGuid."_".str_replace("-","_",$gDate);
    	//$rows = $cache->load($cacheKey);
    	//if (!$rows) {
	    	$select = $this->select()
	    				->from($this)
	    				->join('KutuCatalogFolder','KutuCatalog.guid=KutuCatalogFolder.catalogGuid',array())
	    				->where('KutuCatalog.status=?',99)
	    				->where("DATE_FORMAT(KutuCatalog.createdDate,'%Y-%m-%d') = '$gDate'")
	    				->where('KutuCatalogFolder.folderGuid=?',"$folderGuid")
	    				->order('KutuCatalog.createdDate DESC')
	    				->limit($end, $start);
	    	
	    	$rows = $this->fetchAll($select);
	    	
	    	//$cache->save($rows,"catalog");
    	//}
    	return $rows;
    }
    public function getCountCatalogsInFolder($folderGuid)
    {
    	$select = $this->select()
    				->from($this,array('COUNT(*) as count'))
    				->join('KutuCatalogFolder','KutuCatalog.guid=KutuCatalogFolder.catalogGuid',array())
    				->where('KutuCatalogFolder.folderGuid=?',$folderGuid);
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count : 0;
    }
    public function fetchCatalogInFolder($folderGuid,$start = 0 ,$end = 0)
    {
    	$select = $this->select()
    				->from($this)
    				->join('KutuCatalogFolder','KutuCatalog.guid=KutuCatalogFolder.catalogGuid',array())
    				->where('KutuCatalogFolder.folderGuid=?',$folderGuid)
    				->order('KutuCatalog.publishedDate DESC')
    				->limit($end,$start);
    				
    	$rows = $this->fetchAll($select);

        return $rows;
    }
    function exportToExcel($folderGuid)
    {
    	$select = $this->select()
    				->from($this)
    				->join('KutuCatalogFolder','KutuCatalog.guid=KutuCatalogFolder.catalogGuid',array())
    				->where('KutuCatalogFolder.folderGuid=?',$folderGuid)
    				->order('KutuCatalog.publishedDate DESC');
    				
    	$rows = $this->fetchAll($select);

        return $rows;
    }
    function fetchFromFolderAdminClinic($status, $start = 0 ,$end = 0)
    {
    	if ($status == 0)
    		$row = $this->fetchAll($this->select()->where('profileGuid=?', 'klinik')->where('status=?', 0)->order('createdDate DESC')->limit($end,$start));
    	else 
    		$row = $this->fetchAll($this->select()->where('profileGuid=?', 'klinik')->where('status=?', $status)->order('publishedDate DESC')->limit($end,$start));

    		
    	return $row;
    }
    public function getCatalogByProfile($profileGuid)
    {
        $row = $this->fetchAll($this->select()->where('profileGuid=?', $profileGuid)->order('createdDate ASC'));

        return $row;
    }
    function countCatalogsInFolderClinic($status)
    {
    	$select = $this->select()
    				->from($this,array('COUNT(*) as count'))
    				->where("profileGuid='klinik' AND status=$status");
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count : 0;
    }
    public function countCatalogsInBetween($start,$end)
    {
    	$select = $this->select()
    				->from($this,array('COUNT(*) as count'))
    				->where("createdDate BETWEEN '$start' AND '$end'");
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count : 0;
    }
    public function countCatalogsPubBetween($start,$end)
    {
    	$select = $this->select()
    				->from($this,array('COUNT(*) as count'))
    				->where("publishedDate BETWEEN '$start' AND '$end'");
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count : 0;
    }
    public function countCatalogsInBetweenProfile($start,$end,$profile)
    {
    	$select = $this->select()
    				->from($this,array('COUNT(*) as count'))
    				->where("createdDate BETWEEN '$start' AND '$end' AND profileGuid = '$profile'");
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count : 0;
    }
    function countCatalogsProfile($profile)
    {
    	$select = $this->select()
    				->from($this,array('COUNT(*) as count'))
    				->where("profileGuid = '$profile'");
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count : 0;
    }
    function countCatalogsForAuthor($author)
    {
    	$select = $this->select()
    				->from($this,array('COUNT(*) as count'))
    				->where("createdBy = '$author'");
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count : 0;
    }
    
    /**
     * @todo Development::migration comment
     */
    public function fetchFromFolderAdminComment($profileGuid)
    {
        $row = $this->fetchAll("profileGuid='comment'");

        return $row;
    }
    function countCatalogsInFolderComment()
    {
    	$select = $this->select()
    				->from($this,array('COUNT(*) as count'))
    				->where("profileGuid='comment'");
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count : 0;
    }
    
    
    
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Dms_Catalog';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		'getCatalogByGuid',
    		'fetchFromFolder',
    		'fetchFromFolderByDate',
    		'fetchCatalogInFolder',
    		'getCatalogByProfile'
    	);
    }
}
?>