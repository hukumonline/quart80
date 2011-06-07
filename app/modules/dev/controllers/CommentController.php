<?php
class Dev_CommentController extends Zend_Controller_Action
{
	function commentAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $modelCatalog->fetchAll("profileGuid='comment'");
		
		foreach ($rowset as $row) {
			$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
			
			try {
			$modelComment = new Pandamp_Modules_Extension_Comment_Model_Comment();
			$comment = $modelComment->fetchNew();
			
			$tblRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
			$rowsetRelatedItem = $tblRelatedItem->fetchRow("itemGuid='".$row->guid."' AND relateAs='RELATED_COMMENT'");
			
			$comment->object_id = $rowsetRelatedItem->relatedGuid;
			$comment->userid = 0;
			
			$rowsetCatalogAttribute = $row->findDependentRowsetCatalogAttribute();
			
			$comment->name = $rowsetCatalogAttribute->findByAttributeGuid('fixedName')->value;
			$comment->email = $rowsetCatalogAttribute->findByAttributeGuid('fixedEmail')->value;
			$comment->title = $rowsetCatalogAttribute->findByAttributeGuid('fixedJudul')->value;
			$comment->comment = $rowsetCatalogAttribute->findByAttributeGuid('fixedComment')->value;
			$comment->ip = 0;
			$comment->date = $row->createdDate;
			$comment->published = $row->status;
			$comment->checked_out_time = $row->publishedDate;
			$comment->save();
			
			echo $comment->title.' saved<br>';
			
			}
			catch (Zend_Exception $e)
			{
				echo $e->getMessage().'<br>';
			}
		}
	}
}