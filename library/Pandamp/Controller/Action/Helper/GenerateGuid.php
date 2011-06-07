<?php

/**
 * Generator guid
 * 
 * @author Himawan Anindya Putra <putra@langit.biz>
 * @package Kutu
 * 
 */

class Pandamp_Controller_Action_Helper_GenerateGuid
{
	function generateGuid($prefix=null)
	{
		$o = new Pandamp_Core_Guid();
		return $o->generateGuid($prefix);
	}
}

?>