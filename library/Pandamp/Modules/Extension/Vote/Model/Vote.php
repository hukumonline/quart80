<?php
class Pandamp_Modules_Extension_Vote_Model_Vote
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
	
	protected $_name = 'vote';

	
	public function getRating($id, $ip)
	{
		$select = $this->select()
					->from($this)
					->where('guid=?',$id)
					->where('ip=?',$ip);
					
		$row = $this->fetchRow($select);
		
		return $row;
	}
	public function addRating($catalogGuid, array $data)
	{
    	if (!isset($catalogGuid)) {
    		return 0;
    	}
    	
    	$whiteList = array(
    		'guid',
    		'userid',
    		'ip',
    		'counter',
    		'value'
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
        	if ($row->ip == Pandamp_Lib_Formater::getRealIpAddr()) {
        		return 0;
        	}
        	$row->counter = $row->counter += 1;
        	$row->value = $row->value += $addData['value'];
        	$id = $this->update(array(
        		'counter'=>$row->counter,
        		'value'=>$row->value
        	)
        	,$where);
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
    	return 'Pandamp_Modules_Extension_Vote';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		'getRating'
    	);
    }
}