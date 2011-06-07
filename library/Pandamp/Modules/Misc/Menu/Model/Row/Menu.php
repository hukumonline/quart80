<?php
class Pandamp_Modules_Misc_Menu_Model_Row_Menu extends Zend_Db_Table_Row_Abstract
{
	protected function _insert()
	{
		//add your custom logic here
		//must set the new PATH here
		if(empty($this->guid))
		{
    		$guidMan = new Pandamp_Core_Guid();
    		$this->guid = $guidMan->generateGuid();
		}
		if(empty($this->parentGuid))
		{
			throw new Zend_Exception('parentGuid can not be empty!');
		}
		if(empty($this->title))
		{
			throw new Zend_Exception('Title can not be empty!');
		}
		
		if($this->parentGuid == 'root')
		{
			$this->path = '';
			$this->parentGuid = $this->guid;
		}
		else 
		{
			$parentFolder = $this->_getTable()->find($this->parentGuid)->current();
			
			$this->path = $parentFolder->path.$parentFolder->guid.'/';
		}
		
	}
}
?>