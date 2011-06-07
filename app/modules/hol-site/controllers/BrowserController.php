<?php
class HolSite_BrowserController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		$this->_helper->layout->setLayout('layout-widget-1');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/hol-site/layouts'));
	}
	function detailAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$page = ($this->_getParam('page'))? $this->_getParam('page') : '';
		$this->view->catalogGuid = $catalogGuid;
		$this->view->page = $page;
	}
	function detailIssueAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$this->view->catalogGuid = $catalogGuid;
	}
}
?>