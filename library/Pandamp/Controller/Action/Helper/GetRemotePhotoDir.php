<?php
class Pandamp_Controller_Action_Helper_GetRemotePhotoDir extends Zend_Controller_Action_Helper_Abstract
{
	public function getRemotePhotoDir()
	{
		$registry = Zend_Registry::getInstance();
		$config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
		$cdn = $config->getOption('cdn');
		return $cdn['static']['dir']['photo'];
	}
}