<?php
class Dev_IndexController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->_helper->layout->setLayout('layout-development');
	}
	function indexAction()
	{
		
	}
	function catalogAction()
	{
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$row = $modelCatalog->getCatalogByGuid('hol20748');
		$rowsetCatalogAttribute = $row->findDependentRowsetCatalogAttribute();
		
		$this->view->rca = $rowsetCatalogAttribute;
	}
	function livedocAction()
	{
		$phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();
		$phpLiveDocx->setUsername('myUsername')
					->setPassword('myPassword');
		$phpLiveDocx->setLocalTemplate(ROOT_DIR.'/data/article-template.docx');
		$phpLiveDocx->assign('title', 'Magic Graphical Compression Suite v1.9');
		$phpLiveDocx->createDocument();
		$document = $phpLiveDocx->retrieveDocument('pdf');
		file_put_contents('document.pdf', $document);
	}
	function tcpdfAction()
	{
		/*
		 * Setup external configuration options
		 */
		define('K_TCPDF_EXTERNAL_CONFIG', true);
		
		define ("K_PATH_MAIN", ROOT_DIR."/library/PdfTool/tcpdf/");
	
		/**
		 * url path (http://localhost/tcpdf/)
		 */
		define ("K_PATH_URL", ROOT_URL."/library/PdfTool/tcpdf/");
	
		/**
		 * path for PDF fonts
		 * use K_PATH_MAIN."fonts/old/" for old non-UTF8 fonts
		 */
		define ("K_PATH_FONTS", K_PATH_MAIN."fonts/");
	
		/**
		 * cache directory for temporary files (full path)
		 */
		define ("K_PATH_CACHE", K_PATH_MAIN."cache/");
	
		/**
		 * cache directory for temporary files (url path)
		 */
		define ("K_PATH_URL_CACHE", K_PATH_URL."cache/");
	
		/**
		 *images directory
		 */
		define ("K_PATH_IMAGES", K_PATH_MAIN."images/");
	
		/**
		 * blank image
		 */
		define ("K_BLANK_IMAGE", K_PATH_IMAGES."_blank.png");
	
		/**
		 * page format
		 */
		define ("PDF_PAGE_FORMAT", "A4");
	
		/**
		 * page orientation (P=portrait, L=landscape)
		 */
		define ("PDF_PAGE_ORIENTATION", "P");
	
		/**
		 * document creator
		 */
		define ("PDF_CREATOR", "TCPDF");
	
		/**
		 * document author
		 */
		define ("PDF_AUTHOR", "TCPDF");
	
		/**
		 * header title
		 */
		define ("PDF_HEADER_TITLE", "header title");
	
		/**
		 * header description string
		 */
		define ("PDF_HEADER_STRING", "first row\nsecond row\nthird row");
	
		/**
		 * image logo
		 */
		define ("PDF_HEADER_LOGO", "logo_hukumonline.jpg");
	
		/**
		 * header logo image width [mm]
		 */
		define ("PDF_HEADER_LOGO_WIDTH", 30);
	
		/**
		 *  document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
		 */
		define ("PDF_UNIT", "mm");
	
		/**
		 * header margin
		 */
		define ("PDF_MARGIN_HEADER", 5);
	
		/**
		 * footer margin
		 */
		define ("PDF_MARGIN_FOOTER", 10);
	
		/**
		 * top margin
		 */
		define ("PDF_MARGIN_TOP", 27);
	
		/**
		 * bottom margin
		 */
		define ("PDF_MARGIN_BOTTOM", 25);
	
		/**
		 * left margin
		 */
		define ("PDF_MARGIN_LEFT", 15);
	
		/**
		 * right margin
		 */
		define ("PDF_MARGIN_RIGHT", 15);
	
		/**
		 * main font name
		 */
		define ("PDF_FONT_NAME_MAIN", "vera"); //vera
		define ('PDF_FONT_MONOSPACED', 'courier');
	
		/**
		 * main font size
		 */
		define ("PDF_FONT_SIZE_MAIN", 10);
	
		/**
		 * data font name
		 */
		define ("PDF_FONT_NAME_DATA", "vera"); //vera
	
		/**
		 * data font size
		 */
		define ("PDF_FONT_SIZE_DATA", 8);
	
		/**
		 *  scale factor for images (number of points in user unit)
		 */
		define ("PDF_IMAGE_SCALE_RATIO", 4);
	
		/**
		 * magnification factor for titles
		 */
		define("HEAD_MAGNIFICATION", 1.1);
	
		/**
		 * height of cell repect font height
		 */
		define("K_CELL_HEIGHT_RATIO", 1.25);
	
		/**
		 * title magnification respect main font size
		 */
		define("K_TITLE_MAGNIFICATION", 1.3);
	
		/**
		 * reduction factor for small font
		 */
		define("K_SMALL_RATIO", 2/3);
		
		require_once('PdfTool/tcpdf/tcpdf.php');
		// create new PDF document
		$pdf = new TCPDF();
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('TCPDF Example 001');
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING);
		
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
		
		// Set some content to print
		$html = '
		<h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;color:black;"><span style="background-color:#CC0000;"> TC<span style="color:white;">PDF</span> </span></a>!</h1>
		<i>This is the first example of TCPDF library.</i>
		<p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
		<p>Please check the source code documentation and other examples for further information.</p>
		<p style="color:#CC0000;">TO IMPROVE AND EXPAND TCPDF I NEED YOUR SUPPORT, PLEASE <a href="http://sourceforge.net/donate/index.php?group_id=128076">MAKE A DONATION!</a></p>
		';
		
		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
		
		// ---------------------------------------------------------
		
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('example_001.pdf', 'I');
		
	}
	function searchAction()
	{
		$query = "profile:(kutu_peraturan OR kutu_peraturan_kolonial OR kutu_rancangan_peraturan OR kutu_putusan);year desc, regulationOrder desc";
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query,0,5);
		echo '<pre>';
		print_r($hits);
		echo '</pre>';
		die;
	}
}
?>