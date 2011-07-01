<?php
class Pandamp_Controller_Action_Helper_GetCaptcha extends Zend_Controller_Action_Helper_Abstract
{
	public function getCaptcha($dest)
	{
		$registry = Zend_Registry::getInstance();
		$config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
		$cdn = $config->getOption('recaptcha');
		return $cdn[$dest]['key'];
	}
}