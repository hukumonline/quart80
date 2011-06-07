<?php
class Pandamp_Controller_Action_Helper_GetNumOfComment extends Zend_Controller_Action_Helper_Abstract
{
	public function getNumOfComment($parent)
	{
		$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
		$count = $modelComment->getCommentParentCount($parent);
		
		return ($count != 0)? $count.' Tanggapan' : '';
	}
}
?>