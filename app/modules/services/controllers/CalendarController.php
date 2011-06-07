<?php

class Services_CalendarController extends Zend_Controller_Action 
{
	public function fetchEventCalendarAction()
	{
		$start 		= ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end 		= ($this->_getParam('limit'))? $this->_getParam('limit') : 10;
		
		$lang['days'] = array("Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu");
		$lang['months'] = array("Januari","Februari","Maret","April","May","Juni","Juli","Agustus","September","Oktober","November","Desember");	
		
		
		$modelCalendar = new Pandamp_Modules_Misc_Agenda_Model_Calendar();
		
		$rowset = $modelCalendar->fetchCalendar($start,$end);
		
		$num_rows = $modelCalendar->getCountCalendar();
		
		$a = array();
		$a['totalCount'] = $num_rows;
		$i = 0;
		
		if ($a['totalCount']!=0) 
		{
			foreach ($rowset as $row)
			{
				$a['calendar'][$i]['guid'] 		= $row->id;
				$a['calendar'][$i]['title'] 	= stripslashes($row->title);
				$a['calendar'][$i]['body']	 	= stripslashes(str_replace("\n", "<br />", $row->text));
				$a['calendar'][$i]['author']	= "Posted by : " . $row->username;
				
				if (!($row->start_time == "55:55:55" && $row->end_time == "55:55:55")) {
					if ($row->start_time == "55:55:55")
						$starttime = "- -";
					else
						$starttime = $row->stime;
						
					if ($row->end_time == "55:55:55")
						$endtime = "- -";
					else
						$endtime = $row->etime;
					
					$timestr = "$starttime - $endtime";
				} else {
					$timestr = "";
				}
				
				$a['calendar'][$i]['timestr']	= $timestr;
				
				$d = $row->d;
				$m = $row->m;
				$y = $row->y;
				$dateline = $d." ".$lang['months'][$m-1] ." ".$y;
				$wday = date("w", mktime(0,0,0,$m,$d,$y));
				
				$a['calendar'][$i]['dateline']	= $dateline;
				$a['calendar'][$i]['wday']		= $lang['days'][$wday-1];
				$a['calendar'][$i]['month']		= $m;
				$a['calendar'][$i]['year']		= $y;
				
				$i++;
			}
		}
		if ($a['totalCount']==0)
		{
			$a['calendar'][0]['guid'] = 'XXX';
			$a['calendar'][0]['title'] = "No Data";
			$a['calendar'][0]['body'] = "-";
			$a['calendar'][0]['author'] = '';
			$a['calendar'][0]['timestr'] = '';
			$a['calendar'][0]['dateline'] = '';
			$a['calendar'][0]['wday'] = '';
			$a['calendar'][0]['month'] = '';
			$a['calendar'][0]['year'] = '';
		}
		echo Zend_Json::encode($a);
	}
}

?>