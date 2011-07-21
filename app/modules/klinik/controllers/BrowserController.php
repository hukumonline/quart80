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
		$tw = Zend_Registry::get('twurfl');
		$tw->GetDeviceCapabilitiesFromAgent($_SERVER['HTTP_USER_AGENT'],true);
		$cap = $tw->capabilities;
		
		// check if this device is mobile
		if($cap['product_info']['is_wireless_device']){
			
		    $registry = Zend_Registry::getInstance();
		    $config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
		    $cdn = $config->getOption('mobile');
		    
		    $uri = $_SERVER['REQUEST_URI'];
		    
			//$this->_redirect($cdn['url'].$uri);	        
			header("Location:".$cdn['url'].$uri);
			
		}else{
			
			$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
			$this->view->catalogGuid = $catalogGuid;
		
		}
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