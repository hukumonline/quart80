<?php
class Pandamp_Modules_Extension_Comment_Model_Comment
	extends Zend_Db_Table_Abstract implements Pandamp_BeanContext_Decoratable {
	
	protected $_name = 'comments';
	protected $_rowClass = 'Pandamp_Modules_Extension_Comment_Model_Row_Comment';
	
    public function getCommentByGuidwAjax($guid, $start = 0 , $end = 0)
    {
    	// try to fetch from the cache first
    	//$cache = Zend_Registry::get('cache');
    	//$cacheKey = "gcbg_g_".$guid.'_p_'.$start;
    	//$rows = $cache->load($cacheKey);
    	//if (!$rows) {
    		$rows = $this->fetchAll($this->select()->where('object_id=?', $guid)->limit($end,$start));
    		//$cache->save($rows,'comment');
    	//}	
        return $rows;
    }
    public function getCommentParentByGuidwAjax($guid, $start = 0 , $end = 0)
    {
    	// try to fetch from the cache first
    	//$cache = Zend_Registry::get('cache');
    	//$cacheKey = "gcbg_g_".$guid.'_p_'.$start;
    	//$rows = $cache->load($cacheKey);
    	//if (!$rows) {
    		$rows = $this->fetchAll($this->select()
    			->where('object_id=?', $guid)
    			->where('parent=?',0)
    			->where('published=?',99)
    			->order('id DESC'));
    		//$cache->save($rows,'comment');
    	//}	
        return $rows;
    }
    public function getCommentByGuid($guid)
    {
    	// try to fetch from the cache first
    	//$cache = Zend_Registry::get('cache');
    	//$cacheKey = "gcbg_g_".$guid;
    	//$rows = $cache->load($cacheKey);
    	//if (!$rows) {
    		$rows = $this->fetchAll($this->select()->where('object_id=?', $guid));
    		//$cache->save($rows,'comment');
    	//}	
        return $rows;
    }
    public function getParentComment($parent)
    {
    	// try to fetch from the cache first
    	//$cache = Zend_Registry::get('cache');
    	//$cacheKey = "gpc_p_".$parent;
    	//$rows = $cache->load($cacheKey);
    	//if (!$rows) {
    		$rows = $this->fetchAll($this->select()->where('parent=?', $parent)->where('published=?',99));
    		//$cache->save($rows,'comment');
    	//}	
        return $rows;
    }
    public function getCommentParentByGuid($guid)
    {
    	//$cache = Zend_Registry::get('cache');
    	//$cacheKey = "gcpbg_g_".$guid;
    	//$rows = $cache->load($cacheKey);
    	//if (!$rows) {
    		$rows = $this->fetchAll(
    			$this->select()
    				->where('object_id=?',$guid)
    				->where('parent=?',0)
    		);
    		//$cache->save($rows,'comment');
    	//}
    	return $rows;
    }
    
    public function getCommentParentCount($parentId)
    {
    	if (!isset($parentId)) {
    		return 0;
    	}
    	
    	$select = $this->select()
    				->from($this, array(
    					'COUNT(id) as count_id'
    				))
    				->where('parent=?',$parentId)
    				->where('published=?',99);
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count_id : 0;
    }
    public function getCommentCount($objectId)
    {
    	if (!isset($objectId)) {
    		return 0;
    	}
    	
    	$select = $this->select()
    				->from($this, array(
    					'COUNT(id) as count_id'
    				))
    				->where('object_id=?',$objectId);
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count_id : 0;
    }
    public function getParentCommentCount($objectId)
    {
    	if (!isset($objectId)) {
    		return 0;
    	}
    	
    	$select = $this->select()
    				->from($this, array(
    					'COUNT(id) as count_id'
    				))
    				->where('object_id=?',$objectId)
    				->where('parent=?',0)
    				->where('published=?',99);
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count_id : 0;
    }
    
	/**
     * Adss a catalog to the db.
     *
     * @param Array $data An assoc array with the following key/value pairs:
     * @param integer $userId id of the ser for whom the data will be added
     *
     * @return the id of the added data, or 0 if an error occurred
     */
    public function addComment($data)
    {
    	if (!isset($data['object_id'])) {
    		return 0;
    	}
    	if (!isset($data['name'])) {
    		return 0;
    	}
    	if (!isset($data['email'])) {
    		return 0;
    	}
    	if (!isset($data['title'])) {
    		return 0;
    	}
    	if (!isset($data['comment'])) {
    		return 0;
    	}
    	
    	$whiteList = array(
    		'parent',
    		'object_id',
    		'userid',
    		'name',
    		'email',
    		'title',
    		'comment',
    		'ip',
    		'date'
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

        $id = $this->insert($addData);

        if ((int)$id == 0) {
            return 0;
        }

        return $id;
    }
    public function fetchComment($start = 0 , $end = 0)
    {
    	$row = $this->fetchAll($this->select()->order('id DESC')->limit($end,$start));
    	return $row;
    }
    public function getNumOfComment()
    {
    	$select = $this->select()
    				->from($this, array(
    					'COUNT(id) as count_id'
    				));
    				
    	$row = $this->fetchRow($select);
    	
    	return ($row !== null) ? $row->count_id : 0;
    }
    
	
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Extension_Comment';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		'getCommentByGuid',
    		'getCommentByGuidwAjax',
    		'getCommentParentByGuidwAjax',
    		'getCommentParentByGuid',
    		'getParentComment',
    		'fetchComment'
    	);
    }
}
?>