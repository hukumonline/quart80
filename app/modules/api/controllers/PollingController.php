<?php
class Api_PollingController extends Zend_Controller_Action
{
	function saveAction()
	{
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			
			$aData = $request->getParams();
			try {
				$hol = new Pandamp_Core_Hol_Poll();
				$hol->poll($aData);
			}
			catch (Zend_Exception $e)
			{
				throw new Zend_Exception($e->getMessage());
			}
			
		}
		
		$this->_forward('browse','manager','polling', array('guid'=>$request->getParam('id')));
	}
}
?>