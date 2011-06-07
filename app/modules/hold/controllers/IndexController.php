<?php
class Hold_IndexController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_helper->cache(array('index'), array('hold'));
	}	
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		$this->_helper->layout->setLayout('layout-pusatdata');
		$this->_helper->layout->setLayoutPath(array('layoutPath'=>ROOT_DIR.'/app/modules/hold/layouts'));
	}
	function indexAction()
	{
		$this->_forward('view','browser','hold');
	}
}
?>