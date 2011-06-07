<?php

class Pandamp_Application_Resource_Identity extends Zend_Application_Resource_ResourceAbstract
{
	public function init()
    {
		$options = array_change_key_case($this->getOptions(), CASE_LOWER);
		
		$identity = new Pandamp_Identity();
		$identity->loginUrl = $options['url']['login'];
		$identity->logoutUrl = $options['url']['logout'];
		$identity->signUp = $options['url']['signup'];
		$identity->profile = $options['url']['profile'];
		$identity->rememberMeDuration = $options['rememberme']['duration'];
		$identity->remembermeEnable = $options['rememberme']['enable'];
		
		$db = Zend_Db::factory($options['auth']['db']['adapter'], $options['auth']['db']['params']);
		
		$sAuthAdapter = $options['auth']['adapter'];
		switch (strtolower($sAuthAdapter))
		{
			case 'remote':
				$authAdapter = new Pandamp_Auth_Adapter_Remote($db,'KutuUser','username','password');
				break;
				
			case 'directdb':
			default:
				$authAdapter = new Pandamp_Auth_Adapter_DbTable($db,'KutuUser','username','password');
				break;
		}
		$identity->authAdapter =  $authAdapter;
		
		return $identity;
    }
}

?>