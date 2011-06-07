<?php
class Pandamp_Controller_Plugin_MemoryPeakUsageLog extends Zend_Controller_Plugin_Abstract
{
	protected $_log = null;
	
	public function __construct()
	{
		$writer = new Zend_Log_Writer_Stream(
		ROOT_DIR . '/data/logs/memory_usage');
		$log = new Zend_Log($writer);
		
		$this->_log = $log;
	}
	public function dispatchLoopShutdown()
	{
		$peakUsage = memory_get_peak_usage(true);
		$url = $this->getRequest()->getRequestUri();
		$this->_log->info($peakUsage . ' bytes ' . $url);
	}		
}