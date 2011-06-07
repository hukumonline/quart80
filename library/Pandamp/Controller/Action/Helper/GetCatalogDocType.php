<?php

class Pandamp_Controller_Action_Helper_GetCatalogDocType
{
	public function GetCatalogDocType($catalogGuid)
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowsetCatalog = $tblCatalog->find($catalogGuid);
		
		if(count($rowsetCatalog))
		{
			$rowCatalog = $rowsetCatalog->current();
			$rowsetCatAtt = $rowCatalog->findDependentRowsetCatalogAttribute();
			
			$docType = $this->imageDocumentType($this->dl_file($rowsetCatAtt->findByAttributeGuid('docOriginalName')->value));
		}
		else 
		{
			$docType = '';
		}
		
		return $docType;
	}
	static function dl_file($file)
	{
	    //Gather relevent info about file
	    $filename = basename($file);
	    $file_extension = strtolower(substr(strrchr($filename,"."),1));		
	    return $file_extension;
	}
	static function imageDocumentType($type)
	{
		switch ($type)
		{
			case 'pdf':
				$type = '<img src="'.ROOT_URL.'/resources/images/file_type/pdf.gif">';
			break;
			case 'doc':
				$type = '<img src="'.ROOT_URL.'/resources/images/file_type/doc.gif">';
			break;
			case 'xls':
				$type = '<img src="'.ROOT_URL.'/resources/images/file_type/xls.gif">';
			break;
			case 'html':
			case 'htm':
				$type = '<img src="'.ROOT_URL.'/resources/images/file_type/html.gif">';
			break;
			case 'avi':
			case 'mpg':
			case 'mpeg':
			case 'flv':
			case 'mp3':
				$type = '<img src="'.ROOT_URL.'/resources/images/file_type/prefs.gif">';
			break;
			case 'gif':
				$type = '<img src="'.ROOT_URL.'/resources/images/file_type/image_new.gif">';
			break;
			case 'jpg':
			case 'jpeg':
				$type = '<img src="'.ROOT_URL.'/resources/images/file_type/jpg.gif">';
			break;
			default:
				$type = '<img src="'.ROOT_URL.'/resources/images/file_type/txt.gif">';
		}
		
		return $type;
	}
}

?>