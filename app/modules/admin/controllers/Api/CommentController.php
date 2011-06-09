<?php

class Admin_Api_CommentController extends Zend_Controller_Action 
{
	function preDispatch()
	{
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity())
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
			// [TODO] else: check if user has access to admin page
			$username = $auth->getIdentity()->username;
			
			// get group information
			$acl = Pandamp_Acl::manager();
			$aReturn = $acl->getUserGroupIds($username);
			
			if (isset($aReturn[1]))
			{
				if (($aReturn[1] !== "Master") && ($aReturn[1] !== "Super Admin") && ($aReturn[1] !== "News Admin"))
					{
					$this->_helper->redirector('restricted', "error", 'admin');
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
						if (($aReturn[1] !== "Master") && ($aReturn[1] !== "Super Admin"))
						{
							$this->_forward('temporary','error','admin'); 
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
	function statusAction()
	{
		$catalogGuid 	= ($this->_getParam('catalogGuid'))? $this->_getParam('catalogGuid') : '';
		$relatedGuid 	= ($this->_getParam('relatedGuid'))? $this->_getParam('relatedGuid') : '';
		$status			= ($this->_getParam('status'))? $this->_getParam('status') : '';
		
		$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
		$row = $modelComment->find($catalogGuid)->current();
		
		if ($status == 0)
		{
			$row->published = 0;
		}
		else 
		{
			$row->published = 99;
		}
		
		$result = $row->save();
		
		if ($result) {
			
			//$cache = Zend_Registry::get('cache');
			//$cacheKey = "comment";
			//$cache->remove($cacheKey);
			
			$response['success'] = true;
			
		} else {
			 
			$response['success'] = false;
			
		}
			
		echo Zend_Json::encode($response);
	}
	function deleteAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
		$rowset = $modelComment->find($catalogGuid);
		try {
			$row = $rowset->current();
			
			//$cache = Zend_Registry::get('cache');
			//$cacheKey = "comment";
			//$cache->remove($cacheKey);
			
			$row->delete();
			
			$response['success'] = true;
			$response['message'] = "Comment deletion success";
		} 
		catch (Zend_Exception $e)
		{
			$response['success'] = false;
			$response['error'] = $e->getMessage();
		}
		
		echo Zend_Json::encode($response);
	}
}

?>