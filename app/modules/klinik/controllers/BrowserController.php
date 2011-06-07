<?php
class Klinik_BrowserController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		$this->_helper->layout->setLayout('layout-klinik');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/klinik/layouts'));
	}
	function indexAction()
	{
		
	}
	function detailAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$this->view->catalogGuid = $catalogGuid;
	}
	function categoryAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$this->view->catalogGuid = $catalogGuid;
	}
	function authorAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$partnerGuid = ($this->_getParam('mitra'))? $this->_getParam('mitra') : '';
		
		$this->view->catalogGuid = $catalogGuid;
		$this->view->partnerGuid = $partnerGuid;
	}
	function searchAction()
	{
		$query 		= ($this->_getParam('searchQuery'))? $this->_getParam('searchQuery') : '';
		$category 	= ($this->_getParam('category'))? $this->_getParam('category') : '';
		
		$this->_helper->layout()->searchQuery = $query;
		$this->_helper->layout()->categorySearchQuery = $category;
		$this->view->query = $query;
		$this->view->category = $category;
	}
}
?>