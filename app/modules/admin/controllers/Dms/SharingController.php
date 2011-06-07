<?php

class Admin_Dms_SharingController extends Zend_Controller_Action  
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
		
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
					
			$aReturn = $acl->getUserGroupIds($auth->getIdentity()->username);
				
			if (isset($aReturn[1]))
			{
				//if (($aReturn[1] !== "admin"))
				if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin"))
				{
					$this->_helper->redirector('restricted', "error", 'admin');
				}
			}
		}
	}
	function viewAction()
	{
		$request = $this->getRequest();
		$itemGuid = $request->getParam('guid');
		
//		$aclAdapter = Kutu_Acl::manager();
		
//		$aUsers = $aclAdapter->getUsers();
//		$aGroups = $aclAdapter->getGroups();

//		$aTmp = array();
//		for ($i=0;$i<count($aUsers);$i++)
//		{
//			$aTmp[$i]['username'] = $aUsers[$i];
//			$aPerms = $aclAdapter->getPermissionsOnContent($aUsers[$i], null, $itemGuid);
//			for($ii=0;$ii<count($aPerms);$ii++)
//			{
//				$aTmp[$i]['perms'][$aPerms[$ii]] = 1;
//			}			
//		}
//		$this->view->aDataUser = $aTmp;
				
//		$aTmp = array();
//		for($i=0;$i<count($aGroups);$i++)
//		{
//			$aTmp[$i]['group'] = $aGroups[$i]['value'];
//			
//			$aPerms = $aclAdapter->getPermissionsOnContent(null, $aGroups[$i]['value'], $itemGuid);
//			for($ii=0;$ii<count($aPerms);$ii++)
//			{
//				$aTmp[$i]['perms'][$aPerms[$ii]] = 1;
//			}
//		}
//		$this->view->aDataGroup = $aTmp;
		$this->view->itemGuid = $itemGuid;
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->find($itemGuid);
		if(count($rowset) > 0)
		{
			$row = $rowset->current();
			$this->view->itemTitle = 'CATALOG : ' . $row->shortTitle;
		}
		else 
		{
			$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
			$rowset = $tblFolder->find($itemGuid);
			if(count($rowset) > 0)
			{
				$row = $rowset->current();
				$this->view->itemTitle = 'FOLDER : ' . $row->title;
			}
		}
	}
	public function allowAction()
	{
		$req = $this->getRequest();
		$group = $req->getParam('group');
		$itemGuid = $req->getParam('guid');
		$action = $req->getParam('perm');
		$user = $req->getParam('user');
		
		$this->view->group = $group;
		$this->view->itemGuid = $itemGuid;
		$this->view->action = $action;
		
		$aclMan = Pandamp_Acl::manager();
		if(!empty($group))
			$result = $aclMan->allow(null,$group, $action,'content', $itemGuid);
		else 
			if(!empty($user))
				$result = $aclMan->allow($user, null, $action,'content', $itemGuid);
				
//		$this->view->result = $result;
		if ($result) {
			$response['success'] = true;
			$response['message'] = "Setting permission succeed. Added Allow $group $action <b>$itemGuid</b>";
 		} else {
 			$response['failure'] = true;
 			$response['message'] = "Setting permission error";
 		}
 		
 		echo Zend_Json::encode($response);
 		
	}
	public function removeallowAction()
	{
		$req = $this->getRequest();
		$group = $req->getParam('group');
		$itemGuid = $req->getParam('guid');
		$action = $req->getParam('perm');
		$user = $req->getParam('user');
		
		$this->view->group = $group;
		$this->view->itemGuid = $itemGuid;
		$this->view->action = $action;
		
		$aclMan = Pandamp_Acl::manager();
		if(!empty($group))
			$result = $aclMan->removeAllow(null,$group, $action,'content', $itemGuid);
		else 
			if(!empty($user))
				$result = $aclMan->removeAllow($user, null, $action,'content', $itemGuid);
				
//		$this->view->result = $result;
		if ($result) {
			$response['success'] = true;
			$response['message'] = "Setting permission succeed. Removed Allow $group $action <b>$itemGuid</b>";
 		} else {
 			$response['failure'] = true;
 			$response['message'] = "Setting permission error";
 		}
 		
 		echo Zend_Json::encode($response);
 		
	}
}