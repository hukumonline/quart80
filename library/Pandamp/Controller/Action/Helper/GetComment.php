<?php
class Pandamp_Controller_Action_Helper_GetComment extends Zend_Controller_Action_Helper_Abstract
{
	public function getComment($parent)
	{
		$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
		$decorator = new Pandamp_BeanContext_Decorator($modelComment);
		$rows = $decorator->getParentCommentAsEntity($parent);
		return $rows;
	}
}
?>