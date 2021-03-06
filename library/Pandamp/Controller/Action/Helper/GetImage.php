<?php

class Pandamp_Controller_Action_Helper_GetImage extends Zend_Controller_Action_Helper_Abstract 
{
	public function getImage($catalogGuid)
	{
		$registry = Zend_Registry::getInstance();
		$config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
		$cdn = $config->getOption('cdn');
		$imageUrl = $cdn['static']['url']['images'];
		
		$modelRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		$rowsetRelatedItem = $modelRelatedItem->getDocumentById($catalogGuid,'RELATED_IMAGE');
		$itemGuid = (isset($rowsetRelatedItem->itemGuid))? $rowsetRelatedItem->itemGuid : '';
		
		$chkDir = $imageUrl."/".$catalogGuid."/".$itemGuid;
		if (@getimagesize($chkDir))
		{
			$pict = $imageUrl ."/". $catalogGuid ."/". $itemGuid;
		}
		else 
		{
			$pict = $imageUrl ."/". $itemGuid;
		}
		
		if (Pandamp_Lib_Formater::thumb_exists($pict . ".jpg")) 	{ $thumb = $pict . ".jpg"; 	}
		if (Pandamp_Lib_Formater::thumb_exists($pict . ".gif")) 	{ $thumb = $pict . ".gif"; 	}
		if (Pandamp_Lib_Formater::thumb_exists($pict . ".png")) 	{ $thumb = $pict . ".png"; 	}
		
		if (!empty($thumb))
		{
			$size = @getimagesize($thumb);
			$mt = '<link rel="image_src" type="'.$size['mime'].'" href="'.$thumb.'" />';
			$mt .= '<meta property="og:image" content="'.$thumb.'"/>';
			return $mt;
		}
		else 
		{
			$th = ROOT_URL.'/resources/images/logo_hukumonline.jpg';
			$size = @getimagesize($th);
			$mt = '<link rel="image_src" type="'.$size['mime'].'" href="'.$th.'" />';
			$mt .= '<meta property="og:image" content="'.$th.'"/>';
			
			return $mt;
		}
	}
}