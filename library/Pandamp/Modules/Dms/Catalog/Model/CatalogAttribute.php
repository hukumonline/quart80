<?php
class Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
	
	protected $_name = 'KutuCatalogAttribute';
    protected $_rowsetClass = 'Pandamp_Modules_Dms_Catalog_Model_Rowset_CatalogAttribute';
	protected $_referenceMap    = array(
        'Catalog' => array(
            'columns'           => 'catalogGuid',
            'refTableClass'     => 'Pandamp_Modules_Dms_Catalog_Model_Catalog',
            'refColumns'        => 'guid'
        ),
        'Attribute' => array(
            'columns'           => 'attributeGuid',
            'refTableClass'     => 'Pandamp_Modules_Dms_Catalog_Model_Attribute',
            'refColumns'        => 'guid'
        )
    );

    public function insert (array $data)
    {
    	if(empty($data['guid']))
    	{
    		$guidMan = new Pandamp_Core_Guid();
    		$data['guid'] = $guidMan->generateGuid();
    	}
    	
    	return parent::insert($data);
    }
    
	public function getCatalogAttributeValue($catalogGuid, $attributeGuid)
	{
		// try to fetch from the cache first
		//$cache = Zend_Registry::get('cache');
		//$cacheKey = "gcav_cg_".$catalogGuid."_ag_".$attributeGuid;
		//$row = $cache->load($cacheKey);
		//if (!$row) {
			$select = $this->select()
					  ->from($this,array(
					  	'value'
					  ))
					  ->where('catalogGuid=?',$catalogGuid)
					  ->where('attributeGuid=?',$attributeGuid);
					  
			$row = $this->fetchRow($select);
			
			//$cache->save($row);
		//}
		return ($row !== null) ? $row->value : '';
	}
	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Dms_CatalogAttribute';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>