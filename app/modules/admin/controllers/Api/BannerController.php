<?php

class Admin_Api_BannerController extends Zend_Controller_Action 
{
	function preDispatch()
	{
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity())
		{
			$sReturn = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$sReturn = base64_encode($sReturn);
			
			$this->_redirect(ROOT_URL.'/helper/synclogin/generate/?returnTo='.$sReturn);
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
				if (($aReturn[1] !== "admin") && ($aReturn[1] !== "marketing"))
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
						if (($aReturn[1] !== "admin"))
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
	function saveAction()
	{
		$request = $this->getRequest();
		$aData = $request->getParams();
		
		$auth = Zend_Auth::getInstance();
		$username = $auth->getIdentity()->username;
		
		if (!$auth->hasIdentity()) {
			echo "You are not login or your session is expired. Please login.";
		}
		else 
		{
			$aData['username'] = $username;
		}
		
		try {
			$hol = new Pandamp_Core_Hol_Banner();
			$hol->save($aData);
			echo "Banner is successfully saved";
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
		
	}
	function deleteAction()
	{
		$guid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$hol = new Pandamp_Core_Hol_Banner();
		try {
			$hol->delete($guid);
			$response['success'] = true;
			$response['message'] = "Banner Deletion Success";
		}
		catch (Exception $e)
		{
			$response['success'] = false;
			$response['message'] = $e->getMessage();
		}
		echo Zend_Json::encode($response);
	}
	function savezoneAction()
	{
		$request = $this->getRequest();
		$aData = $request->getParams();
		
		try {
			$hol = new Pandamp_Core_Hol_Banner();
			$hol->saveZone($aData);
			$response['success'] = true;
			$response['message'] = "Banner Zone is successfully saved";
		}
		catch (Exception $e)
		{
			$response['success'] = false;
			$response['message'] = $e->getMessage();
		}
		
		echo Zend_Json::encode($response);
	}
	function deletezoneAction()
	{
		$zid = $this->_getParam('guid');
		$hol = new Pandamp_Core_Hol_Banner();
		try {
			$hol->deleteZone($zid);
			$response['success'] = true;
			$response['message'] = 'Banner Zone Deletion Success';;
		}
		catch (Exception $e)
		{
			$response['success'] = false;
			$response['error'] = $e->getMessage();
		}
		echo Zend_Json::encode($response);
	}
}

?>