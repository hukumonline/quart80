<?php

class Admin_Api_ExportController extends Zend_Controller_Action 
{
    public function init()
    {
    	$excelConfig = array(
    		'excel' => array(
    			'suffix' 	=> 'excel',
    			'headers'	=> array(
    				'Content-Type'	=> 'application/vnd.ms-excel',
    				'Content-Disposition'	=> "attachment; filename=".date('Ymd').".xls",
    				'Pragma'		=> 'no-cache',
    				'Expires'		=> '0'
    			)
    		),
			'json' => array(
            	'suffix'    => 'json',
                'headers'   => array('Content-Type' => 'application/json'),
                'callbacks' => array(
                	'init' => 'initJsonContext',
                	'post' => 'postJsonContext'
          		)
        	)    		
    	);
    	
    	$contextSwitch = $this->_helper->contextSwitch();
    	
    	$contextSwitch->setContexts($excelConfig);
    	
        $contextSwitch->addActionContext('report.by.selection', 'excel')
        			  ->addActionContext('peraturan', 'excel')
                      ->initContext();
    }
    function preDispatch()
    {
		$auth =  Zend_Auth::getInstance();
		if(!$auth->hasIdentity())
		{
			$sReturn = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$sReturn = base64_encode($sReturn);
			
			$identity = Pandamp_Application::getResource('identity');
			$loginUrl = $identity->loginUrl;
			
			$this->_redirect($loginUrl.'?returnTo='.$sReturn);     
			
			//$this->_redirect(ROOT_URL.'/helper/synclogin/generate/?returnTo='.$sReturn);
		}
		else
		{
			// get group information
			$acl = Pandamp_Acl::manager();
					
			$aReturn = $acl->getUserGroupIds(Zend_Auth::getInstance()->getIdentity()->username);
			
			if (isset($aReturn[1]))
			{
				if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin") && ($aReturn[1] !== "dcAdmin"))
				//if (($aReturn[1] !== "admin") && ($aReturn[1] !== "dc_admin"))
					{
						echo "{success:false, error:'Page restricted!!'}";
					}
			}
			
			// [TODO] else: check if user has access to admin page and status website is online
			$tblSetting = new Pandamp_Modules_Misc_Setting_Model_Setting();
			$rowset = $tblSetting->find(1)->current();
			
			if ($rowset)
			{
				if ($rowset->status == 1)
				{
					// it means that user offline other than admin
					if (isset($aReturn[1]))
					{
						//if (($aReturn[1] !== "admin"))
						if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin"))
						{
							echo "{success:false, error:'The page you are looking for is temporarily unavailable.<br/>Please try again later.'}";
						}
					}
				}
				else 
				{
					return;
				}
			}
		}
    }
	public function peraturanAction()
	{
		$folderGuid = ($this->_getParam('folderGuid'))? $this->_getParam('folderGuid') : '';
		
		$this->view->folderGuid = $folderGuid;
	}
	public function reportBySelectionAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$selectedRows = Zend_Json::decode($catalogGuid);
		
		$this->view->selectedRows = $selectedRows;
	}
}

?>