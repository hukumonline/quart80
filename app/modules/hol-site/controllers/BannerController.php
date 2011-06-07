<?php

class HolSite_BannerController extends Zend_Controller_Action 
{
	function viewAction()
	{
		$zid = $this->_getParam('zid');
		$tblPowerban = new Pandamp_Modules_Misc_Banner_Model_Powerban();
		$row = $tblPowerban->fetchBanner($zid);
		if (count($row)) {
			foreach ($row as $rowset) {
			if (($rowset->dis_times > $rowset->dised_times) or ($rowset->dis_times == 0)) {
				$rowset->dised_times = $rowset->dised_times += 1;
				$data = array('dised_times'=>$rowset->dised_times);
				$tblPowerban->update($data,"guid='$rowset->guid'");
				if ($rowset->type == 1) {
					if ($rowset->dtype == 1) {
						if (isset($rowset->url) && !empty($rowset->url)) {
							echo "<a href='".ROOT_URL."/banner/visit/".$rowset->guid."' target='".$rowset->target."'><img src='".$rowset->src."' alt='".$rowset->alt."' border=0 style='display:block;margin:0 auto;text-align:center;'></a>";
						}
						else 
						{
							echo "<img src='".$rowset->src."' alt='".$rowset->alt."' border=0 style='display:block;margin:0 auto;text-align:center;'>";
						}
					}
					else if ($rowset->dtype == 2) 
					{
						$fp = fopen (ROOT_DIR."/tmp/banner/bantemp.htm", "w");
		                fputs($fp,"<title>".$rowset->alt."</title>");
		                fputs($fp,"<a href='".ROOT_URL."/banner/visit/".$rowset->guid."' target='".$rowset->target."'><img src='".$rowset->src."' alt='".$rowset->alt."' border=0></a>");
		                fclose($fp);
		                  
		                echo "<script language='JavaScript'>\n";
		                echo "function popup() {\n";
		                echo "var f = document.forms[0];\n";
		                echo "var docServerPath = '".ROOT_DIR."/tmp/banner/bantemp.htm';\n";
		                echo "window1=window.open(docServerPath,'messageWindow1','scrollbars=no,width=490,height=70');\n";
		                echo "}</script>\n";
					}
				}
				else if ($rowset->type == 2)
				{
	            	$swfdims = preg_split('[x]',$rowset->url);
	              	echo "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' width='$swfdims[0]' height='$swfdims[1]'>";
	              	echo "<param name=movie value='".$rowset->src."'>";
	              	echo "<param name=quality value=high>";
	              	echo "<embed src='".$rowset->src."' quality=high pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' width='$swfdims[0]' height='$swfdims[1]'>";
	              	echo "</embed></object>";
				}
			}
			}
		}
		else 
		{
			echo "&nbsp;";
		}
	}
	function visitAction()
	{
		$guid = $this->_getParam('guid');
		
		$tblPowerban = new Pandamp_Modules_Misc_Banner_Model_Powerban();
		$rowset = $tblPowerban->find($guid);
		if (count($rowset)) {
			$row = $rowset->current();
			$row->visits = $row->visits + 1;
			$gid = $row->save();
			if ($gid) {
				$tblPowerbanVisit = new Pandamp_Modules_Misc_Banner_Statistic_Model_PowerbanStatistik();
				$rowPStatistik = $tblPowerbanVisit->fetchNew();
				$rowPStatistik->bid = $guid;
				$rowPStatistik->host = gethostbyname($_SERVER['HTTP_REFERER']);
				$rowPStatistik->address = Pandamp_Lib_Formater::getRealIpAddr();
				$rowPStatistik->agent = $_SERVER['HTTP_USER_AGENT'];
				$rowPStatistik->datetime = date("Y-m-d h:i:s");
				$rowPStatistik->referer = $_SERVER['HTTP_REFERER'];
				$rowPStatistik->save();
			}
			$this->_redirect($row->url);
		}
	}
}

?>