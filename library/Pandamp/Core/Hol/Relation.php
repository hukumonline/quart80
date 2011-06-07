<?php

class Pandamp_Core_Hol_Relation
{
	public function delete($itemGuid, $relatedGuid, $relateAs)
	{
		if(empty($relateAs))
			throw new Zend_Exception('relateAs can not be empty!');
			
		$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		
		if($tblRelatedItem->delete("itemGuid='$itemGuid' AND relatedGuid='$relatedGuid' AND relateAs='$relateAs'"))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
}

?>