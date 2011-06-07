<?php
class Pandamp_Modules_Dms_Catalog_Model_RelatedItem 
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
		
	protected $_name = 'KutuRelatedItem';
	
	
	public function getSumComment($relatedGuid, $relateAs)
	{
		$db = $this->_db->query("SELECT count(*) count FROM KutuCatalog, KutuRelatedItem WHERE KutuCatalog.guid=KutuRelatedItem.itemGuid AND KutuRelatedItem.relatedGuid='$relatedGuid' AND KutuRelatedItem.relateAs='$relateAs' AND KutuCatalog.status=99");
		$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
		return ($dataFetch[0]['count'])? $dataFetch[0]['count'] : 0;
	}
	
	public function getDocumentById($catalogGuid, $relateAs)
	{
        $row = $this->fetchRow($this->select()->where('relatedGuid=?', $catalogGuid)->where('relateAs=?', $relateAs));

        return $row;
	}
	
    function createNew()
    {
    	return $this->createRow(array('itemGuid'=>'', 'relatedGuid'=>'','relateAs'=>''));
    }
	

	
	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return '';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		''
    	);
    }
}
?>