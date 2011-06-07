<?php
class Comment_ManagerController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
	}
	function list4Action()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$page		= ($this->_getParam('page'))? $this->_getParam('page') : 1;
		
		//$limit = 25;
		//$start = $limit * ($page - 1);
		
		//sleep(2);
		
		//$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		//$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		//$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		//$st = $rowset->getShortTitle();
		
		$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
		$decorator = new Pandamp_BeanContext_Decorator($modelComment);
		$rows = $decorator->getCommentParentByGuidwAjaxAsEntity($catalogGuid,$page);
		
		$num_rows = $modelComment->getParentCommentCount($catalogGuid);
		//$numPage = ceil($num_rows/$limit);
		//$pagination = '';
		//for ($i=1;$i<=$numPage;$i++)
			//$pagination .= "<li><a href='/berita/baca/$catalogGuid/$st/p/$i' title='Halaman $i' rev='$i'>$i</a></li>";
		
		// check is AJAX request or not
	    //if (!$this->getRequest() -> isXmlHttpRequest()) {
	    	//$this->view->pagination = $pagination;
	    //}
		
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		
		$this->view->paginator = $paginator;
		
	    //$this->view->catalogGuid = $catalogGuid;
		$this->view->rows = $rows;
		$this->view->numrows = $num_rows;
		//$this->view->limit = $limit;
	}
	function list3Action()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$page		= ($this->_getParam('page'))? $this->_getParam('page') : 1;
		
		$limit = 10;
		$start = $limit * ($page - 1);
		
		sleep(2);
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		$st = $rowset->getShortTitle();
		
		$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
		$decorator = new Pandamp_BeanContext_Decorator($modelComment);
		$rows = $decorator->getCommentByGuidwAjaxAsEntity($catalogGuid,$start,$limit);
		$tree = new Pandamp_Lib_TuneTree($rows);
		$items = $tree->get();
		
		$num_rows = $modelComment->getCommentCount($catalogGuid);
		$numPage = ceil($num_rows/$limit);
		$pagination = '';
		for ($i=1;$i<=$numPage;$i++)
			$pagination .= "<li><a href='/berita/baca/$catalogGuid/$st/p/$i' title='Halaman $i' rev='$i'>$i</a></li>";
		
		$this->view->rows = $items;
		// check is AJAX request or not
	    if (!$this->getRequest() -> isXmlHttpRequest()) {
	    	$this->view->pagination = $pagination;
	    }
		
		$this->view->catalogGuid = $catalogGuid;
		$this->view->parent = ($rows)? $rows[0]->getParent() : 0;
	}
	function list2Action()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$page = $this->_getParam('page',1);
		
		$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
		$decorator = new Pandamp_BeanContext_Decorator($modelComment);
		$rows = $decorator->getCommentByGuidAsEntity($catalogGuid);
		$tree = new Pandamp_Lib_TuneTree($rows);
		$items = $tree->get();
		
		$paginator = Zend_Paginator::factory($items);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		
		$this->view->paginator = $paginator;
	}
	function listAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
		$decorator = new Pandamp_BeanContext_Decorator($modelComment);
		$rows = $decorator->getCommentByGuidAsEntity($catalogGuid);
		$tree = new Pandamp_Lib_TuneTree($rows);
		$items = $tree->get();
		$this->view->rows = $items;
	}
	function browseAction()
	{
		$catalogGuid 	= ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$page			= ($this->_getParam('page'))? $this->_getParam('page') : '';
		$content		= ($this->_getParam('content'))? $this->_getParam('content') : '';
		$bbCoded		= "&nbsp;";
		
		if ($content != "")	{
			//	Convert BBCode Tags to HTML Tags
			$bbCoded	= Pandamp_Lib_Bbcode::parseBBCode($content);
			$aResult['success'] = true;
			$aResult['data'] = $bbCoded;
			echo Zend_Json::encode($aResult);
			die;
		}
		
		//	Write ControlPanel
		$notShown	= array(9,10,11);
		$bbCodeCP	= Pandamp_Lib_Bbcode::writeBbCode("tanggapan","theField",$notShown);
		$this->view->bbCodeCP = $bbCodeCP;
		
		$sReturn = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$sReturn = base64_encode($sReturn);
		
		$identity = Pandamp_Application::getResource('identity');
		$loginUrl = $identity->loginUrl;
		$signUp = $identity->signUp;
		
		$this->view->loginUrl = $loginUrl.'?returnTo='.$sReturn;
		$this->view->signUp = $signUp;
		
		//$loginUrl = $identity->loginUrl;
		//$signUp = $identity->signUp;
		
		//$this->view->loginUrl = ROOT_URL.'/helper/synclogin/generate/?returnTo='.$sReturn;
		//$this->view->signUp = $signUp;
		
		$this->view->catalogGuid = $catalogGuid;
		$this->view->page = $page;
	}
	function komentarAction()
	{
		
	}
	function listcommentAction()
	{
    	$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$page		= ($this->_getParam('page'))? $this->_getParam('page') : 1;
		
		$limit = 25;
		$start = $limit * ($page - 1);
		
		sleep(2);
		
    	
    	$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
    	$rowset = $tblRelatedItem->fetchAll("relatedGuid='$catalogGuid' AND relateAs='RELATED_COMMENT'");
    	
    	$relatedGuid = array();
    	foreach ($rowset as $related)
    	{
    		$relatedGuid[] = $related->itemGuid;
    	}
    	
    	if ($relatedGuid)
    	{
    		$data = Pandamp_Lib_Formater::implode_with_keys(", ", $relatedGuid, "'");
    		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
    		$rowset = $tblCatalog->fetchAll("guid in($data) AND status=99",'',$limit,$start);
    		$rowset1 = $tblCatalog->fetchAll("guid in($data) AND status=99");
    		
			$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
			$row = $decorator->getCatalogByGuidAsEntity($catalogGuid);
			$st = $row->getShortTitle();
			
			$num_rows = count($rowset1);
			$numPage = ceil($num_rows/$limit);
			$pagination = '';
			for ($i=1;$i<=$numPage;$i++)
				$pagination .= "<li><a href='/berita/baca/$catalogGuid/$st/p/$i' title='Halaman $i' rev='$i'>$i</a></li>";
			
			// check is AJAX request or not
		    if (!$this->getRequest() -> isXmlHttpRequest()) {
		    	$this->view->pagination = $pagination;
		    }
			
			
		    $this->view->catalogGuid = $catalogGuid;
			$this->view->numberOfRows = $num_rows;
			$this->view->rows = $rowset;
			$this->view->limit = $limit;
    	}
	}
}
?>