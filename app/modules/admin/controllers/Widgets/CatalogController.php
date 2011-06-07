<?php
class Admin_Widgets_CatalogController extends Zend_Controller_Action
{
	public function viewerAction()
	{
		$catalogGuid = ($this->_getParam('guid'))? $this->_getParam('guid') : '';
		
		$modelCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		
		if(!empty($catalogGuid))
		{
			$rowCatalog = $modelCatalog->find($catalogGuid)->current();
			$rowsetCatalogAttribute = $rowCatalog->findDependentRowsetCatalogAttribute();
			
			$tableProfileAttribute = new Pandamp_Modules_Dms_Profile_Model_ProfileAttribute();
			$profileGuid = $rowCatalog->profileGuid;
			$where = $tableProfileAttribute->getAdapter()->quoteInto('profileGuid=?', $profileGuid);
			$rowsetProfileAttribute = $tableProfileAttribute->fetchAll($where,'viewOrder ASC');
			
			$aAttribute = array();
			$i = 0;
			$tblAttribute = new Pandamp_Modules_Dms_Catalog_Model_Attribute();
			foreach ($rowsetProfileAttribute as $rowProfileAttribute)
			{
				if($rowsetCatalogAttribute->findByAttributeGuid($rowProfileAttribute->attributeGuid))
				{
					$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid($rowProfileAttribute->attributeGuid);
					$rowsetAttribute = $tblAttribute->find($rowCatalogAttribute->attributeGuid);
					if(count($rowsetAttribute))
					{
						$rowAttribute = $rowsetAttribute->current();
						$aAttribute[$i]['name'] =  $rowAttribute->name;
					}
					else
					{
						$aAttribute[$i]['name'] =  '';
					}
					$aAttribute[$i]['value'] = $rowCatalogAttribute->value;
				}
				else 
				{
					
				}
				$i++;
			}
		}
		$this->view->aAttribute = $aAttribute;
		$this->view->rowsetCatalogAttribute = $rowsetCatalogAttribute;
		
		$rowCatalogAttribute = $rowsetCatalogAttribute->findByAttributeGuid('fixedExpired');
		
		if(!empty($rowCatalogAttribute->value))
		{
			$tDate = $rowCatalogAttribute->value;
			$aDate = explode('-', $tDate);
			$year=$aDate[0];
			$month=$aDate[1];
			$day=$aDate[2];
			$hour="00";
			$minute="00";
			$second="00";
			
			//set what is going to happen than
			$event="My birthday";
			
			//don't change anything below unless you know what you are doing
			$time=mktime($hour, $minute, $second, $month, $day, $year);
			
			$timecurrent=date('U');
			$cuntdowntime=$time-$timecurrent;
			$cuntdownminutes=$cuntdowntime/60;
			$cuntdownhours=$cuntdowntime/3600;
			$cuntdowndays=$cuntdownhours/24;
			$cuntdownmonths=$cuntdowndays/30;
			$cuntdownyears=$cuntdowndays/365;
			
			//echo 'sisa hari: ' . $cuntdowndays;
			
			if($cuntdowndays < 0)
			{
				echo "<script>alert('Dokumen perjanjian ini telah berakhir masa berlakunya.');</script>";
				echo "<br><strong>Dokumen perjanjian ini telah berakhir masa berlakunya.</strong>";
			}
			else 
			{
				//echo "<script>alert('Dokumen perjanjian ini akan berakhir masa berlakunya dalam ".round($cuntdowndays)." hari.');</script>";
				echo "<br><strong>Dokumen perjanjian ini akan berakhir masa berlakunya dalam ".round($cuntdowndays)." hari.</strong>";
			}
		}
		
		$this->_helper->viewRenderer('viewer-generic');
		
	}
}
?>