<?php

class Services_EmailController extends Zend_Controller_Action 
{
	public function fetchEmailsInFolderAction()
	{
		$tblQueue = new Pandamp_Modules_Misc_Email_Model_Queue();
		
		$rowset = $tblQueue->fetchAll();
		
		$a = array();
		$a['totalCount'] = count($rowset);
		
		$i = 0;
		
		if ($a['totalCount']!=0)
		{
			foreach ($rowset as $row)
			{
				$a['email'][$i]['qid'] = $row->newsletterQID;
				$a['email'][$i]['sender'] = $row->sender;
				$a['email'][$i]['recepientMail'] = $row->recepientMail;
				$a['email'][$i]['recepientName'] = $row->recepientName;
				$a['email'][$i]['subject'] = $row->subject;
				$a['email'][$i]['SendDate'] = Pandamp_Lib_Formater::get_date($row->SendDate);
				$i++;
			}
		}
		if ($a['totalCount']==0)
		{
			$a['email'][0]['qid'] = '';
			$a['email'][0]['sender'] = '';
			$a['email'][0]['recepientMail'] = '';
			$a['email'][0]['recepientName'] = '';
			$a['email'][0]['subject'] = '';
			$a['email'][0]['SendDate'] = '';
		}
		
		echo Zend_Json::encode($a);
	}
}