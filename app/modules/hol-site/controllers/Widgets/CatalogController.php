<?php
class HolSite_Widgets_CatalogController extends Zend_Controller_Action
{
	function detailAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		$page = ($this->_getParam('page'))? $this->_getParam('page') : '';
		// get current ip address
		$ip = Pandamp_Lib_Formater::getRealIpAddr();
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		if (isset($rowset))
		
		$modelAsset = new Pandamp_Modules_Dms_Catalog_Model_AssetSetting();
		$decorator = new Pandamp_BeanContext_Decorator($modelAsset);
		$rowAsset = $decorator->getAssetNumOfClickAsEntity($catalogGuid);
		$data = array(
			'guid'			=> $catalogGuid,
			'application'	=> 'ASSET',
			'part'			=> 'MOST_READABLE_TICKER',
			'valueType'		=> $ip,
			'valueInt'		=> 1,
			'valueText'		=> 'TICKER'
		);
		$asset = $modelAsset->addCounterAsset($rowset->getId(),$data);
		
		$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
		$subtitle = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedSubTitle');
		$content = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedContent');
		$description = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedDescription');
		$author = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedAuthor');
		
		$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu");
		$hari = $array_hari[date("N",strtotime($rowset->getCreatedDate()))];
		
		// get votes
		$modelVote = new Pandamp_Modules_Extension_Vote_Model_Vote();
		$decorator = new Pandamp_BeanContext_Decorator($modelVote);
		$rowRate = $decorator->getRatingAsEntity($catalogGuid,$ip);
		$val = ($rowRate)? $rowRate->getValue() : 0;
		$counter = ($rowRate)? $rowRate->getCounter() : 0;
		
		if ($counter < 1) {
			$count = 0;
		} else {
			$count=$counter; //how many votes total
		}
		$current_rating = $val;
		$tense=($count==1) ? "vote" : "votes"; //plural form votes/vote
		$rating = @number_format($current_rating/$count,1);
		
		$drawrating = '('.$count.' '.$tense.', average: '. $rating .' out of 5)';
		
		$this->view->title = $title;
		$this->view->subtitle = $subtitle;
		$this->view->content = $content;
		$this->view->description = $description;
		$this->view->author = $author;
		$this->view->date = $hari . ', '. date("d F Y",strtotime($rowset->getPublishedDate()));
		$this->view->numOfClick	= (isset($rowAsset))? $rowAsset->getValueInt() : 0;
		
		$this->view->drawrating = $drawrating;
		
		$this->view->catalogGuid = $catalogGuid;
		$this->view->page = $page;
	}
    function shareAction()
    {
        $this->_helper->layout->disableLayout();
        
        $catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';

        $this->view->catalogGuid = $catalogGuid;

		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);

		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');

        $this->view->title = $title;

                /*Wawan :: short url :: begin */
        //echo $title;

        $temp = $rowset->toArray();
        //$temp = $rowset;
        
        $this->view->st = $temp["shortTitle"];
        
        $sub = $temp["shortTitle"];

        $sub="";
        if($temp["shortTitle"] != null ) { $sub = '/'.$temp["shortTitle"]; }


        $http = new Zend_Http_Client();
        $longUrl=ROOT_URL."/berita/baca/".$catalogGuid.$sub;

        //echo $longUrl;
         $http->setUri('http://api.bit.ly/shorten?version=2.0.1&longUrl='.$longUrl.'&login=hukumonline&apiKey=R_77ba1fce98783c1734e24bc28dfdb8c7');
        $shortUrl="";

          $response = $http->request();
          if ($response->isSuccessful())
         {
              $result = Zend_Json::decode($response->getBody());
              if (isset($result["results"][$longUrl]["shortUrl"])) {
                  $shortUrl =  $result["results"][$longUrl]["shortUrl"];
              }

			  //print_r($result["results"]);
			  //echo "<pre>";print_r( $result);echo "</pre>";
			  //echo $shortUrl;

          }

          $statsShortUrl="";
         $http->setUri('http://api.bit.ly/stats?version=2.0.1&shortUrl='.$shortUrl.'&login=hukumonline&apiKey=R_77ba1fce98783c1734e24bc28dfdb8c7');
         $response = $http->request();

          if ($response->isSuccessful())
         {
              $result = Zend_Json::decode($response->getBody());
              $statsShortUrl =  $result["results"]["clicks"];

              //print_r($result["results"]);
            //  echo "<pre>";print_r( $result);echo "</pre>";
              //echo $statsShortUrl;

          }

          $this->view->shortUrl = $shortUrl;
          $this->view->statsShortUrl = $statsShortUrl;
        /*Wawan :: short url :: end */
    }
	function detailIssueAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$modelCatalogAttribute = new Pandamp_Modules_Dms_Catalog_Model_CatalogAttribute();
		$decorator = new Pandamp_BeanContext_Decorator($modelCatalog);
		$rowset = $decorator->getCatalogByGuidAsEntity($catalogGuid);
		
		if ($rowset)
		{
			$title = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedTitle');
			$subtitle = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedSubTitle');
			$description = $modelCatalogAttribute->getCatalogAttributeValue($rowset->getId(),'fixedDescription');
			
			$this->view->title = $title;
			$this->view->subtitle = $subtitle;
			$this->view->description = $description;
			
			$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu");
			$hari = $array_hari[date("N",strtotime($rowset->getCreatedDate()))];
			
			$this->view->date = $hari . ', '. date("d F Y",strtotime($rowset->getCreatedDate()));
			$this->view->catalogGuid = $catalogGuid;
		}
	}
}
?>