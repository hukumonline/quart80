<?php
class Pandamp_Modules_Extension_Comment_Model_Row_Comment extends Zend_Db_Table_Row_Abstract
{
	protected function _delete()
	{
		$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
		$modelComment->delete("parent=".$this->id);
	}
}