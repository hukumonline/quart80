<?php
class Polling_ManagerController extends Zend_Controller_Action
{
	function viewAction()
	{
		$tblPolling = new Pandamp_Modules_Misc_Poll_Model_Poll();
		$tblOption = new Pandamp_Modules_Misc_Option_Model_Option();
		
		$time = time();
		$date = date("Y-m-d H:i:s", $time);
		
		$rowPoll = $tblPolling->fetchRow("checkedTime < '$date'","checkedTime DESC");
		$this->view->rowPoll = $rowPoll;
		
		$rowOpt = $tblOption->fetchAll("pollGuid='$rowPoll->guid'","text ASC");
		$this->view->rowOpt = $rowOpt;
	}
	function browseAction()
	{
		$this->_helper->layout->setLayout('layout-polling');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/polling/layouts'));
		
		$pollId = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$this->view->pollId = $pollId;
	}
	function detailAction()
	{
		$this->_helper->layout->setLayout('layout-polling');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/polling/layouts'));
		
		$pollId = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblPolling = new Pandamp_Modules_Misc_Poll_Model_Poll();
		
		$time = time();
		$date = date("Y-m-d H:i:s", $time);
		
		$rowPoll = $tblPolling->fetchRow("guid='$pollId' AND checkedTime < '$date'","checkedTime DESC");
		$this->view->rowPoll = $rowPoll;
		
		$this->view->pollId = $pollId;
	}
	function pollAction()
	{
		$this->_helper->layout->setLayout('layout-polling');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/polling/layouts'));
		
		$pollId = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblPolling = new Pandamp_Modules_Misc_Poll_Model_Poll();
		
		$time = time();
		$date = date("Y-m-d H:i:s", $time);
		
		$rowPoll = $tblPolling->fetchAll("guid NOT IN('$pollId') AND checkedTime < '$date'","checkedTime DESC");
		$this->view->rowPoll = $rowPoll;
		
		$this->view->pollId = $pollId;
	}
}
?>