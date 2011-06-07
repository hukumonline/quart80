<?php

class Helper_SyncloginController extends Zend_Controller_Action
{
	public function generateAction()
	{
		$this->_helper->layout->disableLayout();
		
		$req = $this->getRequest();
		$returnTo =($req->getParam('returnTo'))? $req->getParam('returnTo') : ROOT_URL;
		
		setcookie('returnMeTo', base64_decode($returnTo), null, '/');

		$flagSessionIdSent = false;
		if(isset($_GET['PHPSESSID']) && !empty($_GET['PHPSESSID']))
		{
			$sessid = $_GET['PHPSESSID'];
			
			Zend_Session::setId($sessid);
			$flagSessionIdSent = true;
		}

		if($flagSessionIdSent)
		{
			$saveHandlerManager = new Pandamp_Session_SaveHandler_Manager();
			$saveHandlerManager->setSaveHandler();
			
			Zend_Session::start();
			
			if(isset($_COOKIE['returnMeTo']) && !empty($_COOKIE['returnMeTo']))
			{
				header("location: ".$_COOKIE['returnMeTo']);
				exit();
			}
		}
		else 
		{	
			$identity = Pandamp_Application::getResource('identity');
			$url = $identity->loginUrl;
			$sReturn = ROOT_URL.'/helper/synclogin/generate';
			$sReturn = base64_encode($sReturn);
			header("location: $url/?returnTo=".$sReturn);
			
			exit();
		}
	}
}

?>