<?php

class Admin_Calendar_ManagerController extends Zend_Controller_Action 
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');	
			
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
				//if (($aReturn[1] !== "admin") && ($aReturn[1] !== "holproject"))
				if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin") && ($aReturn[1] !== "holProject"))
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
						if (($aReturn[1] !== "master") && ($aReturn[1] !== "superAdmin"))
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
	function eventcalendarAction() 	{}
	function postmessageAction()	{}
	function editpostAction()
	{
		$pid = $this->_getParam('pid');
		
		$tblcalendar = new Pandamp_Modules_Misc_Agenda_Model_Calendar();
		$rowedit = $tblcalendar->find($pid)->current();
		
		$day = $rowedit->d;
		if ($day < 10) $day = 0 . $day;
		$month = $rowedit->m;
		if ($month < 10) $month = 0 . $month;
		$year = $rowedit->y;
		
		$this->view->dateOfEvent = $day.'-'.$month.'-'.$year;
		$this->view->title = $rowedit->title;
		$this->view->text = $rowedit->text;
		$this->view->starttime = $rowedit->start_time;
		$this->view->endtime = $rowedit->end_time;
		$this->view->pid = $pid;
	}
}

?>