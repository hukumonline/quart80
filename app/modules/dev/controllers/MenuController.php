<?php
class Dev_MenuController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->_helper->layout->setLayout('layout-development');
	}
	function indexAction()
	{
		
	}
	function browseAction()
	{
		$r = $this->getRequest();
		
		$node = ($r->getParam('node')?$r->getParam('node'):'root');
		
		$modDir = $this->getFrontController()->getModuleDirectory();
		require_once($modDir.'/components/Menu/ViewFolder.php');
		$w = new Dev_Menu_ViewFolder($node);
		$this->view->widget1 = $w;
		
		$modDir = $this->getFrontController()->getModuleDirectory();
		require_once($modDir.'/components/Menu/FolderBreadcrumbs.php');
		$w = new Dev_Menu_FolderBreadcrumbs($node);
		$this->view->widget2 = $w;
		
		$this->view->currentNode = $node;
	}
	function newAction()
	{
		$f = $this->getRequest();
		
		$guid = $f->getParam('guid');
		$node = $f->getParam('node');
		
		$modelFolder = new Pandamp_Modules_Misc_Menu_Model_Menu();
		$newRow = $modelFolder->createRow();
		
		if (isset($node) && ($node != 'root'))
		{
			$rowNode = $modelFolder->find($node)->current();
			if ($rowNode)
				$this->view->nodeTitle = $rowNode->title;
		}
		else
			$this->view->nodeTitle = 'Root';
			
		$message = '';
		
		if($f->isPost())
		{
			//die('post');
			
			$newRow->parentGuid = $node;
			$newRow->title = $f->getParam('title');
			$newRow->description = $f->getParam('description');
			$newRow->viewOrder = $f->getParam('viewOrder');
			$newRow->cmsParams = $f->getParam('cmsParams');
			$newRow->save();
			
			$message = 'Data was successfully saved.';
			
		}
		
		$this->view->node = $node;
		$this->view->row = $newRow;
	}
	function generateAction()
	{
		$r = $this->getRequest();
		$node = ($r->getParam('node'))? $r->getParam('node') : 'root';
		
		$modelFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowset = $modelFolder->getMenu($node);
		$this->view->rowset = $rowset;
	}
	function getmenuchildAction()
	{
		$r = $this->getRequest();
		$node = $r->getParam('node');
		
		$modelFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowset = $modelFolder->getMenu($node);
		$this->view->rowset = $rowset;
	}
}
?>