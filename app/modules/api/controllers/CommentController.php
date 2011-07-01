<?php
require_once('recaptchalib.php');
class Api_CommentController extends Zend_Controller_Action
{
	function saveAction()
	{
		$request = $this->getRequest();
		
		$validator = new Zend_Validate_EmailAddress();
		
		if ($request->getParam('name') == '') {
			$error[] = '- Nama harus diisi';
		}
		if ($request->getParam('email') == '') {
			$error[] = '- Email harus diisi';
		}
		if (!$validator->isValid($request->getParam('email'))) {
			$error[] = '- Penulisan email salah!';
		}
		if ($request->getParam('title') == '') {
			$error[] = '- Judul harus diisi';
		}
		if ($request->getParam('comment') == '') {
			$error[] = '- Komentar harus diisi';
		}
		
		$registry = Zend_Registry::getInstance();
		$config = $registry->get(Pandamp_Keys::REGISTRY_APP_OBJECT);
		$cdn = $config->getOption('recaptcha');
		$privatekey = $cdn['private']['key'];
		
		//$privatekey = "6LcL47wSAAAAAATTV9Xufi-GCHj1GvL9TxyuKm-E"; // http://hukumonline.pl
		//$privatekey = "6Lem4rwSAAAAAFeSUqpBonLBhixm-GLeap1eTWNK"; // http://www.hukumonline.com
		
		$resp = recaptcha_check_answer ($privatekey,
		                                $_SERVER["REMOTE_ADDR"],
		                                $_POST["recaptcha_challenge_field"],
		                                $_POST["recaptcha_response_field"]);

		if (!$resp->is_valid) {
			// What happens when the CAPTCHA was entered incorrectly
			$error[] = "- The reCAPTCHA wasn't entered correctly. Go back and try it again." .
			"(reCAPTCHA said: " . $resp->error . ")";
		}		
		 
		if (isset($error)) {
			
			echo '<b>Error</b>: <br />'.implode('<br />', $error);
			
		} else {
			
			$aResult = array();
			
			$aData = $request->getParams();
			
			try {
				$com = new Pandamp_Core_Hol_Comment();
				$com->save($aData);
				
				echo "Terima kasih atas tanggapan anda";
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
			
		}
	}
	
	/*
	function saveAction()
	{
		$gman = new Pandamp_Core_Guid();
		
		$catalogGuid = $gman->generateGuid();
		
		/*
		 * todo
		 * folderGuid = lt4a028f7416e39
		 * adalah guid foldernya Tanggapan pada admin panel
		
		$folderGuid = 'lt4a028f7416e39';
		$relatedGuid = ($this->_getParam('relatedGuid'))? $this->_getParam('relatedGuid') : '';
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$where = $tblCatalog->getAdapter()->quoteInto('guid=?', $catalogGuid);

		if ($tblCatalog->fetchRow($where))
		{
			
		}
		else 
		{
			$rowCatalog = $tblCatalog->fetchNew();
			
			$rowCatalog->profileGuid = $this->_getParam('profileGuid');
			$rowCatalog->createdBy = $this->_getParam('fixedName');
			$rowCatalog->createdDate = date("Y-m-d H:i:s");
			$rowCatalog->status = $this->_getParam('status');
		}
		
		$catalogGuid = $rowCatalog->save();
		
		$tableProfileAttribute = new Pandamp_Modules_Dms_Profile_Model_ProfileAttribute();
		$profileGuid = $rowCatalog->profileGuid;
		$where = $tableProfileAttribute->getAdapter()->quoteInto('profileGuid=?', $profileGuid);
		$rowsetProfileAttribute = $tableProfileAttribute->fetchAll($where,'viewOrder ASC');
		
		$rowsetCatalogAttribute = $rowCatalog->findDependentRowsetCatalogAttribute();
		foreach ($rowsetProfileAttribute as $rowProfileAttribute)
		{
			if($rowsetCatalogAttribute->findByAttributeGuid($rowProfileAttribute->attributeGuid))
			{
				$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid($rowProfileAttribute->attributeGuid);
			}
			else 
			{
				$tblCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
				$rowCatalogAttribute = $tblCatalogAttribute->fetchNew();
				$rowCatalogAttribute->catalogGuid = $catalogGuid;
				$rowCatalogAttribute->attributeGuid = $rowProfileAttribute->attributeGuid;
			}
			
			$rowCatalogAttribute->value = $this->_getParam($rowProfileAttribute->attributeGuid);
			$rowCatalogAttribute->save();
		}
		
		if (!empty($folderGuid)) 
		{
			$tblCatalogFolder = new Pandamp_Modules_Dms_Catalog_Model_CatalogFolder();
			$rowsetCatalogFolder = $tblCatalogFolder->find($catalogGuid, $folderGuid);
			if(count($rowsetCatalogFolder)<=0)
			{
				$rowCatalogFolder = $tblCatalogFolder->createRow(array('catalogGuid'=>'', 'folderGuid'=>''));
				$rowCatalogFolder->catalogGuid = $catalogGuid;
				$rowCatalogFolder->folderGuid = $folderGuid;
				$rowCatalogFolder->save();
			}
		}
		
		$rowCatalog = $tblCatalog->find($catalogGuid)->current();
		$rowCatalog->relateTo($relatedGuid, "RELATED_COMMENT");
		
		$aResult = array();
		$aResult['success'] = true;
		$aResult['msg'] = "Catalog is successfully saved";
		
		echo Zend_Json::encode($aResult);
	}
	*/
}
?>