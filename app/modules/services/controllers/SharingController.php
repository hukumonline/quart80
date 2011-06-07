<?php

/**
 * @package Pandampmp
 * @author Nihki Prihadi <nihki@hukumonline.com>
 *
 * $Id: SharingController.php 2009-01-10 21:04: $
 */

class Services_SharingController extends Zend_Controller_Action 
{
	public function viewAction()
	{
		$itemGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$start = ($this->_getParam('start'))? $this->_getParam('start') : 0;
		$end = ($this->_getParam('limit'))? $this->_getParam('limit') : 10;
		
		$aclAdapter = Pandamp_Acl::manager();
		
		$aGroups = $aclAdapter->getGroups();
		
		$aTmp = array();
		$aTmp['totalCount'] = count($aGroups);
		
		for($i=0;$i<count($aGroups);$i++) {
			$aTmp['privilege'][$i]['guid'] = $aGroups[$i]['id'];
			$aTmp['privilege'][$i]['group'] = $aGroups[$i]['value'];
			
			$aPerms = $aclAdapter->getPermissionsOnContent(null, $aGroups[$i]['value'], $itemGuid);
			
			if (count($aPerms) == 0) {
				$aTmp['privilege'][$i]['perms']['create'] = 0;
				$aTmp['privilege'][$i]['perms']['delete'] = 0;
				$aTmp['privilege'][$i]['perms']['read'] = 0;
				$aTmp['privilege'][$i]['perms']['update'] = 0;
			} else {
				for($ii=0;$ii<count($aPerms);$ii++)
				{
					$aTmp['privilege'][$i]['perms'][$aPerms[$ii]] = 1;
				}
			}
		}
		
		echo Zend_Json::encode($aTmp);
	}
}