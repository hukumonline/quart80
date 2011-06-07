<?php
class Pandamp_Controller_Action_Helper_IsBannerExist
{
	public function isBannerExist($zid='')
	{
		$tblPowerban = new Pandamp_Modules_Misc_Banner_Model_Powerban();
		$row = $tblPowerban->fetchBanner($zid);
		if (count($row)) {
			return true;
		}
		else 
		{
			return false;
		}
	}
}
?>