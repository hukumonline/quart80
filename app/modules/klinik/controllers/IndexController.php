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
	function indexAction()
	{
		$tw = Zend_Registry::get('twurfl');
		$tw->GetDeviceCapabilitiesFromAgent($_SERVER['HTTP_USER_AGENT'],true);
		$cap = $tw->capabilities;
		
		// check if this device is mobile
		if($cap['product_info']['is_wireless_device']){
			
		    $registry = Zend_Registry::getInstance();
		    $config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
		    $cdn = $config->getOption('mobile');
		    
		    $uri = $_SERVER['REQUEST_URI'];
		    
			header("Location:".$cdn['url'].$uri);
			
		}		
	}
	function disclaimerAction(){}
	function browseAction() 
	{
		
	}
	function rubrikAction() 
	{
		
	}
}
?>