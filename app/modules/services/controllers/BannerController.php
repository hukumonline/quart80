<?php

class Services_BannerController extends Zend_Controller_Action 
{
	function fetchBannerAction()
	{
		$start 		= ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end 		= ($this->_getParam('limit'))? $this->_getParam('limit') : 10;
		
		$tblPBanner = new Pandamp_Modules_Misc_Banner_Model_Powerban();
		$rowset = $tblPBanner->fetchAll(null,'createdDate DESC',$end,$start);
		
		$a = array();
		$a['totalCount'] = $tblPBanner->countPowerban();
		
		$now = date('Y-m-d H:i:s');
		
		$ii = 0;
		if ($a['totalCount']!=0) {
			foreach ($rowset as $row) {
				$a['banner'][$ii]['guid']= $row->guid;
				$a['banner'][$ii]['title']= $row->name;
				$a['banner'][$ii]['source']= $row->src;
				$a['banner'][$ii]['type']= $row->type;
				$a['banner'][$ii]['alt']= $row->alt;
				$a['banner'][$ii]['url']= $row->url;
				$a['banner'][$ii]['createdby'] = $row->createdBy;
				$a['banner'][$ii]['modifiedby'] = $row->modifiedBy;
				$a['banner'][$ii]['createdDate']= Pandamp_Lib_Formater::get_date($row->createdDate);
				if ($now <= $row->publishedDate && $row->status == 99) {
					$status = "publish_y";
				} 
				else if (($now <= $row->expiredDate || $row->expiredDate == '0000-00-00 00:00:00') && $row->status == 99) {
					$status = "publish_g";
				} 
				else if ($now > $row->expiredDate && $row->status == 99) {
					$status = "publish_r";
				} 
				else if ($row->status == 0) {
					$status = "publish_x";
				} 
				else if ($row->status == -1) {
					$status = "disabled";
				}
				
				$a['banner'][$ii]['status'] = $status;
				$ii++;				
			}
		}
		if ($a['totalCount']==0)
		{
			$a['banner'][0]['guid'] = 'XXX';
			$a['banner'][0]['title'] = "No Data";
			$a['banner'][0]['source'] = "-";
			$a['banner'][0]['createdDate'] = '';
		}
		
		echo Zend_Json::encode($a);
	}
	function fetchZoneAction()
	{
		$start 		= ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end 		= ($this->_getParam('limit'))? $this->_getParam('limit') : 10;
		
		$tblBannerZone = new Pandamp_Modules_Misc_Banner_Zone_Model_PowerbanZones();
		$rowset = $tblBannerZone->fetchAll(null,'zname ASC',$end,$start);
		
		$a = array();
		$a['totalCount'] = $tblBannerZone->countPowerbanZones();
		
		$ii = 0;
		if ($a['totalCount']!=0) {
			foreach ($rowset as $row) {
				$a['zone'][$ii]['guid']= $row->zid;
				$a['zone'][$ii]['title']= $row->zname;
				$ii++;				
			}
		}
		if ($a['totalCount']==0)
		{
			$a['zone'][0]['guid'] = 'XXX';
			$a['zone'][0]['title'] = "No Data";
		}
		
		echo Zend_Json::encode($a);
	}
}

?>