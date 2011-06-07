<?php
class Pandamp_Modules_Identity_User_Model_Rowset_UserDetail extends Zend_Db_Table_Rowset_Abstract
{
	function findByUserDetail($guid)
	{
        foreach ($this as $row) {
            if ($row->id == $guid) 
            {
                return $row;
            }
        }
        return null;		
	}
}