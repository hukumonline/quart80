<?php
class HolSite_IndexController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_helper->cache(array('index'), array('entries'));
	}	
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
//		$saveHandlerManager = new Pandamp_Session_SaveHandler_Manager();
//		$saveHandlerManager->setSaveHandler();
//		Zend_Session::start();
	}
	function indexAction()
	{
		$this->_helper->layout->setLayout('layout-depan');
		$this->_helper->layout->setLayoutPath(array('layoutPath'=>ROOT_DIR.'/app/modules/hol-site/layouts'));
		$this->view->pageTitle = 'hukumonline.com';
	}
	function headerAction()
	{
		$sReturn = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$sReturn = base64_encode($sReturn);
		
		$identity = Pandamp_Application::getResource('identity');
		$loginUrl = $identity->loginUrl;
		$logoutUrl = $identity->logoutUrl;
		$signUp = $identity->signUp;
		$profile = $identity->profile;
		
		$this->view->loginUrl = $loginUrl.'?returnTo='.$sReturn;
		$this->view->logoutUrl = $logoutUrl.'/'.$sReturn;
		$this->view->signUp = $signUp;
		$this->view->profile = $profile;
		
		//$logoutUrl = $identity->logoutUrl;
		//$signUp = $identity->signUp;
		
		//$this->view->loginUrl = ROOT_URL.'/helper/synclogin/generate/?returnTo='.$sReturn;
		//$this->view->logoutUrl = $logoutUrl.'/?returnTo='.$sReturn;
		//$this->view->signUp = $signUp;
		
		$query = ($this->_getParam('cari'))? $this->_getParam('cari') : '';
		$category = ($this->_getParam('a'))? $this->_getParam('a') : '';
		
		$this->_helper->layout()->searchQuery = $query;
		$this->_helper->layout()->categorySearchQuery = $category;
	}
	function headerkategoriAction()
	{
		$sReturn = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$sReturn = base64_encode($sReturn);
		
		$identity = Pandamp_Application::getResource('identity');
		$loginUrl = $identity->loginUrl;
		$logoutUrl = $identity->logoutUrl;
		$signUp = $identity->signUp;
		
		$this->view->loginUrl = ROOT_URL.'/helper/synclogin/generate/?returnTo='.$sReturn;
		$this->view->logoutUrl = $logoutUrl.'/?returnTo='.$sReturn;
		$this->view->signUp = $signUp;
		
		
		$r = $this->getRequest();
		$node = ($r->getParam('node'))? $r->getParam('node') : 'root';
		
		$modelFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowset = $modelFolder->getMenu($node);
		$this->view->rowset = $rowset;
		
		$query = ($this->_getParam('cari'))? $this->_getParam('cari') : '';
		$category = ($this->_getParam('a'))? $this->_getParam('a') : '';
		
		$this->_helper->layout()->searchQuery = $query;
		$this->_helper->layout()->categorySearchQuery = $category;
	}
	function getmenuchildAction()
	{
		$r = $this->getRequest();
		$node = $r->getParam('node');
		
		$modelFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowset = $modelFolder->getMenu($node);
		$this->view->rowset = $rowset;
	}
	function footerAction()	{}
	function tickerAction() {}
}
?>