<?php

class Pandamp_Modules_Misc_Poll_Model_Row_Poll extends Zend_Db_Table_Row_Abstract 
{
	protected function _postDelete()
	{
		$tblPollIp = new Pandamp_Modules_Misc_Poll_Model_PollIp();
		$tblPollIp->delete("pollGuid='".$this->guid."'");
		
		$tblPollOpt = new Pandamp_Modules_Misc_Option_Model_Option();
		$tblPollOpt->delete("pollGuid='".$this->guid."'");
	}
}

?>