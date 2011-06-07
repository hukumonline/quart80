<?php
class Hold_Widgets_SearchController extends Zend_Controller_Action
{
	function viewResultAction()
	{
		$time_start = microtime(true);
		
		$query = ($this->_getParam('query'))? $this->_getParam('query') : '';
		
		$a = array();
		
		$querynum = $query.' profile:(kutu_peraturan OR kutu_peraturan_kolonial OR kutu_rancangan_peraturan OR kutu_putusan)';
		
		$a['query']	= $query;

		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query,0,1);
		
		$num = Pandamp_Lib_Formater::findCatalog($querynum);
		//$num = $hits->response->numFound;
		$limit = 20;
		
		$a['totalCount'] = $num;
		$a['limit'] = $limit;
		
		$ii=0;
		
		if($a['totalCount']==0)
		{
			$a['catalogs'][0]['guid']= 'XXX';
			$a['catalogs'][0]['title']= "No Data";
			$a['catalogs'][0]['subTitle']= "";
			$a['catalogs'][0]['createdDate']= '';
			$a['catalogs'][0]['modifiedDate']= '';
		}
					
		
		$this->view->aData = $a;
		$this->view->query = $query;
		$this->view->hits = $hits;
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->view->time = round($time,2);
	}
}
?>