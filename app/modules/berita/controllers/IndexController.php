<?php

class Berita_IndexController extends Zend_Controller_Action 
{
	public function init()
	{
		$this->_helper->cache(array('index'), array('warta'));
	}	
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		$this->_helper->layout->setLayout('layout-berita');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/berita/layouts'));
	}
	function indexAction()
	{
		$year 	= $this->_getParam('year');
		$month 	= $this->_getParam('month');
		$date 	= $this->_getParam('date');
		
		$this->view->year = $year;
		$this->view->month = $month;
		$this->view->date = $date; 
	}
	function searchAction() 	
	{
		$query = ($this->_getParam('cari'))? $this->_getParam('cari') : '';
		$category = ($this->_getParam('a'))? $this->_getParam('a') : '';
		
		switch ($category)
		{
			case 'artikel':
				$this->_forward('search','browser','berita',array('searchQuery'=>$query));
				//$this->_forward('view-result-article','widgets_search','berita',array('query'=>$query.' profile:article'));
			break;
			case 'klinik':
				$this->_forward('search','browser','klinik',array('searchQuery'=>$query));
			break;
			case 'peraturan':
				$this->_forward('search','browser','hold',array('searchQuery'=>$query));
			break;
			default:
		    	$this->_forward('restricted','index','berita',array('type'=>'search','num'=>101));
		}
		
		$this->_helper->layout()->searchQuery = $query;
		$this->_helper->layout()->categorySearchQuery = $category;
		$this->view->query = $query;
	}
	function restrictedAction()
	{
		$req = $this->getRequest();
		$type = ($req->getParam('type'))? $req->getParam('type') : '';
		$num =  ($req->getParam('num'))? $req->getParam('num') : '';
		switch ($type)
		{
			case "search":
				switch ($num) 
				{
					case 101:
						$error_msg = "Data tidak ditemukan";
						break;
				}
			break;
			case "identity":
				switch ($num) 
				{
					case 101:
						$error_msg = "Page restricted!!";
						break;
				}
			break;
		}
		$this->view->error_msg = $error_msg;
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
		
		//$loginUrl = $identity->loginUrl;
		//$logoutUrl = $identity->logoutUrl;
		//$signUp = $identity->signUp;
		
		//$this->view->loginUrl = ROOT_URL.'/helper/synclogin/generate/?returnTo='.$sReturn;
		//$this->view->logoutUrl = $logoutUrl.'/?returnTo='.$sReturn;
		//$this->view->signUp = $signUp;
		
		
		$r = $this->getRequest();
		$node = ($r->getParam('node'))? $r->getParam('node') : 'root';
		
		$modelFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowset = $modelFolder->getMenu($node);
		//$this->view->rowset = $rowset;
		
		/**
		 * fungsi rubrikasi di non aktifkan
		 * July 12, 2011
		 */
		$this->view->rowset = "";
		
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
	function aktualAction()		{}
	function utamaAction() 		{}
	function terbaruAction() 	{}
	function fokusAction() 		{}
	function isuhangatAction()	{}
	function tajukAction()		{}
	function kolomAction()		{}
	function jedaAction()		{}
	function resensiAction()	{}
	function tokohAction()		{}
	function infoAction()		{}
}

?>