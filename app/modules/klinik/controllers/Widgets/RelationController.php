<?php
class Klinik_Widgets_RelationController extends Zend_Controller_Action
{
	function beritaterkaitAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		
		$where = "relatedGuid='$catalogGuid' AND relateAs='RELATED_Clinic'";
		$rowsetRelatedItem = $tblRelatedItem->fetchAll($where);
		
		$num_rows = count($rowsetRelatedItem);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->rowsetRelatedItem = $rowsetRelatedItem;
	}
	
}