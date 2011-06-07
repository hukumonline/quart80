<?php

/**
 * General Bootstrapping class
 * @author Nihki Prihadi <nihki@madaniyah.com>
 *
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap 
{
	protected function _initAutoload()
	{
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'App',
			'basePath' => APPLICATION_PATH));
		return $moduleLoader;
	}
	
    protected function _initDbRegistry()
    {
        $multidb = $this->getPluginResource('multidb');
        $multidb->init();
        
        Zend_Registry::set('db1', $multidb->getDb('db1'));
        Zend_Registry::set('db2', $multidb->getDb('db2'));
    }

	protected function _initLogger() 
	{
		$writer = new Zend_Log_Writer_Firebug();
		$logger = new Zend_Log( $writer );
		 
		Zend_Registry::set( 'logger', $logger );
	}    
    
	/*
	protected function _initPlugins()
	{
		$bootstrap = $this->getApplication();
	    if ($bootstrap instanceof Zend_Application) {
	         $bootstrap = $this;
	    }
	    $bootstrap->bootstrap('FrontController');
	    $front = $bootstrap->getResource('FrontController');		
	    
		$front->registerPlugin(new Pandamp_Controller_Plugin_MemoryPeakUsageLog());
		
		return $bootstrap;
	}
	*/
}

?>