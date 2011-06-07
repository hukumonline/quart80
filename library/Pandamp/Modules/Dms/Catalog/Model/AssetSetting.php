<?php
class Pandamp_Modules_Dms_Catalog_Model_AssetSetting
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {

	protected $_name = 'KutuAssetSetting';	
	
	public function getAssetNumOfClick($catalogGuid)
	{
		$row = $this->fetchRow($this->select()->where('guid=?',$catalogGuid));
		
		return $row;
	}

	public function addCounterAsset($catalogGuid, array $data)
	{
    	if (!isset($catalogGuid)) {
    		return 0;
    	}
    	
    	$whiteList = array(
    		'guid',
    		'application',
    		'part',
    		'valueType',
    		'valueInt',
    		'valueText'
    	);
    	
    	$addData = array();
    	
        foreach ($data as $key => $value) {
            if (in_array($key, $whiteList)) {
                $addData[$key] = $value;
            }
        }

        if (empty($addData)) {
            return 0;
        }
        
        $where = $this->getAdapter()->quoteInto('guid = ?', $catalogGuid);
        $row = $this->fetchRow($where);
        if ($row) {
        	// check ip
        	/*
        	if (($row->guid == $catalogGuid) && ($row->valueType == Pandamp_Lib_Formater::getRealIpAddr())) {
        		return 0;
        	}
        	*/
        	$row->valueInt = $row->valueInt += 1;
        	$id = $this->update(array('valueInt'=>$row->valueInt),$where);
        } else {
        	$id = $this->insert($addData);
        }
        
        if ((int)$id == 0) {
            return 0;
        }

        return $id;
	}
	
	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Dms_AssetSetting';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		'getAssetNumOfClick'
    	);
    }
}
?>