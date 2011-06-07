<?php

class Pandamp_Core_Hol_Calendar
{
	function save($aData)
	{
		if(empty($aData['guid']))
			throw new Zend_Exception('Guid can not be EMPTY!');

		$guid 		= $aData['guid'];
		$cid 		= $aData['cid'];
		$doe		= $aData['dateOfEvent'];
		$title		= $aData['title'];
		$text		= $aData['text'];
		$starttime	= $aData['starttime'];
		$endtime	= $aData['endtime'];
		
		$tblcalendar = new Pandamp_Modules_Misc_Agenda_Model_Calendar();
		$rowcalendar = $tblcalendar->find($cid)->current();
		if ($rowcalendar)
		{
			$rowcalendar->uid = $guid;
			$rowcalendar->m = substr($doe,3,2);
			$rowcalendar->d = substr($doe,0,2);
			$rowcalendar->y = substr($doe,6,4);
			$rowcalendar->start_time = $starttime;
			$rowcalendar->end_time = $endtime;
			$rowcalendar->title = $title;
			$rowcalendar->text = $text;
			
		}
		else 
		{
			$rowcalendar = $tblcalendar->fetchNew();
			$rowcalendar->uid = $guid;
			$rowcalendar->m = substr($doe,3,2);
			$rowcalendar->d = substr($doe,0,2);
			$rowcalendar->y = substr($doe,6,4);
			$rowcalendar->start_time = $starttime;
			$rowcalendar->end_time = $endtime;
			$rowcalendar->title = $title;
			$rowcalendar->text = $text;
			
		}
		
		$result = $rowcalendar->save();
	}
	function delete($guid)
	{
		$tblCalendar = new Pandamp_Modules_Misc_Agenda_Model_Calendar();
		$rowset = $tblCalendar->find($guid);
		if(count($rowset))
		{
			$row = $rowset->current();
			try {
				$row->delete();
			}
			catch (Exception $e)
			{
				throw new Zend_Exception($e->getMessage());
			}
		}
	}
}

?>