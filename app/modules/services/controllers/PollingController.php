<?php

/**
 * @package kutump
 * @author Nihki Prihadi <nihki@hukumonline.com>
 *
 * $Id: PollingController.php 2009-02-19 11:20: $
 */

class Services_PollingController extends Zend_Controller_Action 
{
	public function fetchPollingAction()
	{
		$start 		= ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end 		= ($this->_getParam('limit'))? $this->_getParam('limit') : 10;
		
		$tblPoll = new Pandamp_Modules_Misc_Poll_Model_Poll();
		
		$rowset = $tblPoll->fetchAll(null,null,$end,$start);
		
		$a = array();
		$a['totalCount'] = count($rowset);
		
		$i = 0;
		
		if ($a['totalCount']!=0)
		{
			foreach ($rowset as $row)
			{
				$a['poll'][$i]['guid'] = $row->guid;
				$a['poll'][$i]['title'] = $row->title;
				$i++;
			}
		}
		if ($a['totalCount']==0)
		{
			$a['poll'][$i]['guid'] = '';
			$a['poll'][$i]['title'] = '';
		}
		
		echo Zend_Json::encode($a);
	}
}

