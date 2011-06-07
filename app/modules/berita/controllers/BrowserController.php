<?php
class Berita_BrowserController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		$this->_helper->layout->setLayout('layout-berita');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/berita/layouts'));
	}
	function searchAction()
	{
		$query = ($this->_getParam('searchQuery'))? $this->_getParam('searchQuery') : '';
		
		$this->view->query = $query;
	}
}
?>