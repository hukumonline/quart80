<?php
class Pandamp_Controller_Action_Helper_GetRemoteImageUrl extends Zend_Controller_Action_Helper_Abstract
{
	public function getRemoteImageUrl()
	{
		$registry = Zend_Registry::getInstance();
		$config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
		$cdn = $config->getOption('cdn');
		return $cdn['static']['url']['images'];
	}
}