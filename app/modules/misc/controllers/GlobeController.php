<?php
class Misc_GlobeController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->addHelperPath(ROOT_DIR.'/library/Pandamp/Controller/Action/Helper','Pandamp_Controller_Action_Helper');
	}
	function pdfAction()
	{
		$this->_helper->layout->setLayout('layout-misc-printpdf');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		define('K_TCPDF_EXTERNAL_CONFIG', true);
		define ("K_PATH_MAIN", ROOT_DIR."/library/PdfTool/tcpdf/");
		define ("K_PATH_URL", ROOT_URL."/library/PdfTool/tcpdf/");
		define ("K_PATH_FONTS", K_PATH_MAIN."fonts/");
		define ("K_PATH_CACHE", K_PATH_MAIN."cache/");
		define ("K_PATH_URL_CACHE", K_PATH_URL."cache/");
		define ("K_PATH_IMAGES", K_PATH_MAIN."images/");
		define ("K_BLANK_IMAGE", K_PATH_IMAGES."_blank.png");
		define ("PDF_PAGE_FORMAT", "A4");
		define ("PDF_PAGE_ORIENTATION", "P");
		define ("PDF_CREATOR", "HUKUMONLINE");
		define ("PDF_AUTHOR", "HUKUMONLINE");
		define ("PDF_HEADER_LOGO", "logo_hukumonline.jpg");
		define ("PDF_HEADER_LOGO_WIDTH", 30);
		define ("PDF_UNIT", "mm");
		define ("PDF_MARGIN_HEADER", 5);
		define ("PDF_MARGIN_FOOTER", 10);
		define ("PDF_MARGIN_TOP", 27);
		define ("PDF_MARGIN_BOTTOM", 25);
		define ("PDF_MARGIN_LEFT", 15);
		define ("PDF_MARGIN_RIGHT", 15);
		define ("PDF_FONT_NAME_MAIN", "vera"); //vera
		define ('PDF_FONT_MONOSPACED', 'courier');
		define ("PDF_FONT_SIZE_MAIN", 10);
		define ("PDF_FONT_NAME_DATA", "vera"); //vera
		define ("PDF_FONT_SIZE_DATA", 8);
		define ("PDF_IMAGE_SCALE_RATIO", 4);
		define("HEAD_MAGNIFICATION", 1.1);
		define("K_CELL_HEIGHT_RATIO", 1.25);
		define("K_TITLE_MAGNIFICATION", 1.3);
		define("K_SMALL_RATIO", 2/3);
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
		$content = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedContent');
		$description = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedDescription');
		$author = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedAuthor');
		$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu");
		$hari = $array_hari[date("N",strtotime($rowset->getCreatedDate()))];
			
		$date = $hari . ', '. date("d F Y",strtotime($rowset->getCreatedDate()));
		
		require_once('PdfTool/tcpdf/tcpdf.php');
		// create new PDF document
		$pdf = new TCPDF();
		
		define ("PDF_HEADER_TITLE", "PT. Justika Siar Publika");
		define ("PDF_HEADER_STRING", "Puri Imperium Office Plaza, Jl. Kuningan Madya Kav 5-6 Kuningan Jakarta 12980,\nTelepon: (62-21) 83701827 / Faksimili: (62-21) 83701826\nE-mail: redaksi@hukumonline.com");
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('TCPDF Example 001');
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		//$pdf->setLanguageArray($l);
		
		// ---------------------------------------------------------
		
		// set default font subsetting mode
		$pdf->setFontSubsetting(true);
		
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 14, '', true);
		
		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();
		
		$sDir = 'uploads/images';
		$thumb = "";
		$modelRelatedItem = new Pandamp_Modules_Dms_Catalog_Model_RelatedItem();
		$rowsetRelatedItem = $modelRelatedItem->getDocumentById($catalogGuid,'RELATED_IMAGE');
		$itemGuid = (isset($rowsetRelatedItem->itemGuid))? $rowsetRelatedItem->itemGuid : '';
		if (Pandamp_Lib_Formater::thumb_exists($sDir ."/". $itemGuid . ".jpg")) 	{ $thumb = $sDir ."/". $itemGuid . ".jpg"; 	}
		if (Pandamp_Lib_Formater::thumb_exists($sDir ."/". $itemGuid . ".gif")) 	{ $thumb = $sDir ."/". $itemGuid . ".gif"; 	}
		if (Pandamp_Lib_Formater::thumb_exists($sDir ."/". $itemGuid . ".png")) 	{ $thumb = $sDir ."/". $itemGuid . ".png"; 	}
		
		if ($thumb == "") 
			$screenshot = ""; 
		else 
			$screenshot = "<img src=\"".ROOT_URL.'/'.$thumb."\" />";
		
		// Set some content to print
		$html = '<div class="kotakisi">';
		$html .= '<h2>'.$title.'</h2>';
		$html .= '<div class="tanggalterbit">'.$date.'</div>';
		if ($description) $html .= '<div class="description">'.$description.'</div>';
		if ($thumb == "") { $screenshot = ""; } else {
			$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
			$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
			$rowset = $decorator->getCatalogByGuidAsEntity($rowsetRelatedItem->itemGuid);
			if (isset($rowset))
				$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
				$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
			
			$html .= '<div class="image">'.$screenshot.'<br><span class="fname">'.$title.'</span></div>';
		}
		$html .= '<p>'.Pandamp_Lib_Formater::_cleanMsWordHtml($content).'</p>';
		$html .= '</div>';
		
		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
		
		// ---------------------------------------------------------
		
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('article.pdf', 'I');
	}
	function printedocAction()
	{
		$this->_helper->layout->setLayout('layout-misc-print');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		if (isset($rowset))
			$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
			$content = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedContent');
			$description = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedDescription');
			$author = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedAuthor');

			$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu");
			$hari = $array_hari[date("N",strtotime($rowset->getCreatedDate()))];
			
			$this->view->title = $title;
			$this->view->shortTitle = $rowset->getShortTitle();
			$this->view->content = $content;
			$this->view->date = $hari . ', '. date("d F Y",strtotime($rowset->getCreatedDate()));
			$this->view->description = $description;
			$this->view->author = $author;
			$this->view->catalogGuid = $catalogGuid;
	}
	function sendmailAction()
	{
		$this->_helper->layout->setLayout('layout-misc-email');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		if (isset($rowset))
			$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
			$description = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedDescription');
			$author = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedAuthor');
			
			$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu");
			$hari = $array_hari[date("N",strtotime($rowset->getCreatedDate()))];
			
			$this->view->title = $title;
			$this->view->shortTitle = $rowset->getShortTitle();
			$this->view->date = $hari . ', '. date("d F Y",strtotime($rowset->getCreatedDate()));
			$this->view->description = $description;
			$this->view->author = $author;
			$this->view->catalogGuid = $catalogGuid;
	}
	function sendshareAction()
	{
		$this->_helper->layout()->disableLayout();
		
		$response = array();
		
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$name = ($this->_getParam('name'))? $this->_getParam('name') : '';
		$email = ($this->_getParam('email'))? $this->_getParam('email') : '';
		$emails = ($this->_getParam('emails'))? $this->_getParam('emails') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity('lt49841c739c980'); // --> kutu_emailconfirm untuk share article
		
		if (isset($rowset))
			//$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
			$mailcontent = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedContent');
			$shortTitle = $this->getCatalogShortTitle($catalogGuid);
			$href = "\n";
			$href .= ROOT_URL.'/berita/baca/'.$catalogGuid.'/'.$shortTitle;
			$tag = $this->getcatalogshare($catalogGuid);
			$mailcontent = str_replace('$tag',$tag,$mailcontent);
			//$mailcontent = str_replace('$profile',$title,$mailcontent);
			$mailcontent = str_replace('$href',$href,$mailcontent);
			$mail_body = $mailcontent;
			
			$subject = "hukumonline.com: ".$name." (".$email.") mengirimkan berita.!";
			
			$config = new Zend_Config_Ini(CONFIG_PATH.'/mail.ini', 'mail');
			$transport = new Zend_Mail_Transport_Smtp($config->mail->host);
			$mail = new Zend_Mail();
			$mail->setBodyText($mail_body);
			$mail->setFrom($email, $name);
			
			$mails = explode(",",$emails);
			
			$validator = new Zend_Validate_EmailAddress(
					Zend_Validate_Hostname::ALLOW_DNS, true
				);
			
			foreach ($mails as $mymail) {
				if($validator->isValid($mymail)) {
					$mail->addTo($mymail);
				}
			}
			$mail->setSubject($subject);
				
			try 
			{
				$mailTransport = Pandamp_Application::getResource('mail');
				$mail->send($mailTransport);
				$response['success'] = true;
				$response['message'] = "Email terkirim";
			}
			catch (Zend_Exception $e)
			{
				$response['failure'] = false;
				$response['message'] = "Kirim email gagal! ".$e->getMessage();
			}
			
			echo Zend_Json::encode($response);
	}
	function karirAction()
	{
		$this->_helper->layout->setLayout('layout-misc');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$this->view->identity = "Karir";
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchRow("guid='lt4da515ba11c37'",'createdDate DESC');
		if ($rowset) :
		$rowsetCatalogAttribute = $rowset->findDependentRowsetCatalogAttribute(); 
		$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent');
		$rowCatalogAttributeTitle = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle');
		$rowCatalogAttributeSubTitle = $rowsetCatalogAttribute->findByAttributeGuid('fixedSubTitle');
		
		$this->view->content = $rowCatalogAttribute->value;
		$this->view->title = $rowCatalogAttributeTitle->value;
		$this->view->subTitle = $rowCatalogAttributeSubTitle->value;
		endif;
	}
	function tentangkamiAction()
	{
		$this->_helper->layout->setLayout('layout-misc');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$this->view->identity = "Tentang Kami";
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchAll("profileGuid='about_us'",'createdDate DESC',1,0);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$rowsetCatalogAttribute = $row->findDependentRowsetCatalogAttribute(); 
			$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent');
			$title = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle');
			$data[$content][0] = $rowCatalogAttribute->value;
			$data[$content][1] = $title->value;
			$data[$content][2] = $row->guid;
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	function syaratketentuanAction()
	{
		$this->_helper->layout->setLayout('layout-misc');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$this->view->identity = "Syarat dan Ketentuan";
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchAll("guid='lt4d5e17eed8711'",'createdDate DESC');
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$rowsetCatalogAttribute = $row->findDependentRowsetCatalogAttribute(); 
			$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent');
			$data[$content][0] = $rowCatalogAttribute->value;
			$data[$content][1] = $row->guid;
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
		
	}
	function kontakAction()
	{
		$this->_helper->layout->setLayout('layout-misc');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$this->view->identity = "Kontak";
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchAll("profileGuid='kutu_contact'",'createdDate DESC',1,0);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$rowsetCatalogAttribute = $row->findDependentRowsetCatalogAttribute(); 
			$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent');
			$title = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle');
			$data[$content][0] = $rowCatalogAttribute->value;
			$data[$content][1] = $title->value;
			$data[$content][2] = $row->guid;
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	function produkAction()
	{
		$this->_helper->layout->setLayout('layout-misc');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$this->view->identity = "Produk";
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchRow("guid='lt4a1cc3ee7eac5'",'createdDate DESC');
		
		$rowsetCatalogAttribute = $rowset->findDependentRowsetCatalogAttribute(); 
		$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent');
		$rowCatalogAttributeTitle = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle');
		$rowCatalogAttributeSubTitle = $rowsetCatalogAttribute->findByAttributeGuid('fixedSubTitle');
		
		$this->view->content = $rowCatalogAttribute->value;
		$this->view->title = $rowCatalogAttributeTitle->value;
		$this->view->subTitle = $rowCatalogAttributeSubTitle->value;
	}
	function detailProdukAction()
	{
		$this->_helper->layout->setLayout('layout-misc');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$this->view->identity = "Detail Produk";
		
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		$rowset = $tblCatalog->find($catalogGuid)->current();
		
		if ($catalogGuid)
		{
			$rowsetCatalogAttribute = $rowset->findDependentRowsetCatalogAttribute();
			$rowsetCatalogAttributeTitle = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle');
			$rowsetCatalogAttributeDesc = $rowsetCatalogAttribute->findByAttributeGuid('fixedDescription');
			$rowsetCatalogAttributeContent = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent');
			
			$this->view->title = $rowsetCatalogAttributeTitle->value;
			$this->view->desc = $rowsetCatalogAttributeDesc->value;
			$this->view->content = $rowsetCatalogAttributeContent->value;
		}
	}
	function kodeetikAction()
	{
		$this->_helper->layout->setLayout('layout-misc');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$this->view->identity = "Kode Etik";
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchAll("profileGuid='kutu_kotik'",'createdDate DESC',1,0);
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$rowsetCatalogAttribute = $row->findDependentRowsetCatalogAttribute(); 
			$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent');
			$title = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle');
			$data[$content][0] = $rowCatalogAttribute->value;
			$data[$content][1] = $title->value;
			$data[$content][2] = $row->guid;
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}	
	function mitrakamiAction()
	{
		$this->_helper->layout->setLayout('layout-misc');
		$this->_helper->layout->setLayoutPath(array('layoutPath' => ROOT_DIR.'/app/modules/misc/layouts'));
		
		$this->view->identity = "Mitra Kami";
		
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchAll("profileGuid='kutu_mitra'",'createdDate DESC');
		
		$content = 0;
		$data = array();
		
		foreach ($rowset as $row)
		{
			$rowsetCatalogAttribute = $row->findDependentRowsetCatalogAttribute(); 
			$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid('fixedContent');
			$data[$content][0] = $rowCatalogAttribute->value;
			$data[$content][1] = $row->guid;
			$content++;
		}
		
		$num_rows = count($rowset);
		
		$this->view->numberOfRows = $num_rows;
		$this->view->data = $data;
	}
	private function getcatalogshare($catalogGuid)
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid); 
		$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
		$author = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedAuthor');
		$description = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedDescription');
		$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu");
		$hari = $array_hari[date("N",strtotime($rowset->getCreatedDate()))];
		$publish = $hari . ', '. date("d F Y",strtotime($rowset->getCreatedDate()));
		$tag = "Dipublikasikan : ".$publish."\n";
		$tag .= "Penulis : ".$author."\n\n";
		$tag .= $title."\n\n\n";
		if ($description) $tag .= $description."\n\n\n\n";
		
		return $tag;
	}
	private function getCatalogShortTitle($catalogGuid)
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid); 
		$st = $rowset->getShortTitle();
		
		return $st;
	}
}
?>