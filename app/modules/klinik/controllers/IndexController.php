<?php
class Klinik_IndexController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_helper->cache(array('index'), array('clinic'));
	}	
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		$this->_helper->layout->setLayout('layout-klinik');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/klinik/layouts'));
	}
	function indexAction(){}
	function disclaimerAction(){}
	function browseAction() 
	{
		
	}
	function rubrikAction() 
	{
		
	}
}
?>