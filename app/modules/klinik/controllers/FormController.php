<?php
class Klinik_FormController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->_helper->layout->setLayout('layout-klinik');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/klinik/layouts'));
	}
	function indexAction()
	{
		
	}
	function addClinicAction()
	{
		$gen = new Pandamp_Form_Helper_KlinikInputGenerator();
		$aRender = $gen->generateFormAdd();
		$this->view->aRenderedAttributes = $aRender;
	}
}
?>