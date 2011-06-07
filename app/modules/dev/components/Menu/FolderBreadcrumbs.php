<?php
class Dev_Menu_FolderBreadcrumbs
{
	public $view;
	public $folderGuid;
	public $rootGuid;
	
	public function __construct($folderGuid, $rootGuid='')
	{
		$this->view = new Zend_View();
		$this->view->setScriptPath(dirname(__FILE__));
		
		$this->folderGuid = $folderGuid;
		$this->rootGuid = $rootGuid;
		
		$this->view();
	}
	public function view()
	{
		$browserUrl = ROOT_URL . '/dev/menu/browse/node';
    	
    	$folderGuid = ($this->folderGuid)? $this->folderGuid : 'root';
    	
    	$tblFolder = new Pandamp_Modules_Misc_Menu_Model_Menu();
    	
    	$aPath = array();
    	
    	if($folderGuid == 'root')
    	{
    		$aPath[0]['title'] = 'Root';
    		$aPath[0]['url'] = $browserUrl;
    	}
    	else 
    	{
    		$rowFolder = $tblFolder->find($folderGuid)->current();
    		if(!empty($rowFolder->path))
    		{
	    		$aFolderGuid = explode("/", $rowFolder->path);
	    		$sPath = 'root >';
	    		$aPath[0]['title'] = 'Root';
    			$aPath[0]['url'] = $browserUrl;
    			$i = 1;
	    		if(count($aFolderGuid))
	    		{
	    			$sPath1 = '';
	    			foreach ($aFolderGuid as $guid)
	    			{
	    				if(!empty($guid))
	    				{
	    					$rowFolder1 = $tblFolder->find($guid)->current();
	    				 	$sPath1 .= $rowFolder1->title . ' > ';
	    				 	$aPath[$i]['title'] = $rowFolder1->title;
    						$aPath[$i]['url'] = $browserUrl.'/'.$rowFolder1->guid;
	    				 	$i++;
	    				}
	    			}
	    			
	    			$aPath[$i]['title'] = $rowFolder->title;
					$aPath[$i]['url'] = $browserUrl.'/'.$rowFolder->guid;
	    		}
	    		
    		}
    		else 
    		{
    			$aPath[0]['title'] = 'Root';
    			$aPath[0]['url'] = $browserUrl;
    			$aPath[1]['title'] = $rowFolder->title;
    			$aPath[1]['url'] = $browserUrl.'/'.$rowFolder->guid;
    		}
    	}
    	$this->view->aPath = $aPath;
	}
	public function render()
	{
		return $this->view->render(str_replace('.php','.phtml',strtolower(basename(__FILE__))));
	}
}
?>