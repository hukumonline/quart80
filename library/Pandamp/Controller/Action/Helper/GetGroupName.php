<?php
class Pandamp_Controller_Action_Helper_GetGroupName
{
	public function getGroupName($id)
    {
    	$modelGroup = new Pandamp_Modules_Identity_Acl_Model_Group();
    	$row = $modelGroup->fetchRow("id=$id");
    	return ($row) ? $row->name : '-';
   	}
}
?>