<?php

class IndexController extends Zend_Controller_Action  
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/View/Helper','Pandamp_View_Helper');		
	}
	function indexAction()	
	{
		$tw = Zend_Registry::get('twurfl');
        if(!$tw->getDeviceCapability("is_wireless_device")){
        	$this->_forward('index','index','hol-site');
       	}
        else
        {
		    $registry = Zend_Registry::getInstance();
		    $config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
		    $cdn = $config->getOption('mobile');
			$this->_redirect($cdn['url']);	        
       	}
	}
}

?>