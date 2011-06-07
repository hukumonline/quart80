<?php

class Services_CommentController extends Zend_Controller_Action 
{
	public function fetchCommentAction()
	{
		$start 		= ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end 		= ($this->_getParam('limit'))? $this->_getParam('limit') : 10;
		
		$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
		$decorator = new Pandamp_BeanContext_Decorator($modelComment);
		$rowset = $decorator->fetchCommentAsEntity($start,$end);
		
		$num_rows = $modelComment->getNumOfComment();
		
		$gShort			= new Pandamp_Controller_Action_Helper_GetCatalogShortTitle();
		
		$a = array();
		
		$a['totalCount'] = $num_rows;
		
		$ii = 0;
		
		if ($a['totalCount']!=0) {
			foreach ($rowset as $row)
			{
				$a['comment'][$ii]['guid']= $row->getId();
				$a['comment'][$ii]['title']= $row->getTitle();
				$a['comment'][$ii]['description']= $row->getComment();
				$a['comment'][$ii]['user_email']= $row->getEmail();
				$a['comment'][$ii]['guid_article'] = $row->getobjectId();
				$a['comment'][$ii]['shortarticle'] = $gShort->getCatalogShortTitle($row->getobjectId());
				$a['comment'][$ii]['createdby'] = $row->getName();
				$a['comment'][$ii]['ip'] = ($row->getIp() <> 0)?$row->getIp():'-';
				$a['comment'][$ii]['createdDate']= Pandamp_Lib_Formater::get_date($row->getDate());
				$a['comment'][$ii]['status'] = $row->getPublished();
				$ii++;				
			}
		}
		if ($a['totalCount']==0)
		{
			$a['comment'][0]['guid'] = 'XXX';
			$a['comment'][0]['title'] = "No Data";
			$a['comment'][0]['description'] = "-";
			$a['comment'][0]['createdDate'] = '';
		}
		echo Zend_Json::encode($a);
	}
}

?>