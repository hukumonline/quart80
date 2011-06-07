<?php
class Talks_IndexController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_helper->cache(array('index'), array('talks'));
		
		Zend_Session::start();
	}	
	function indexAction()
	{
		$this->_helper->layout->setLayout('layout-depan-talks');
		$this->_helper->layout->setLayoutPath(array('layoutPath'=>ROOT_DIR.'/app/modules/talks/layouts'));
	}
	function headerAction()
	{
		
	}
	function viewcartAction()
	{
		
	}
}