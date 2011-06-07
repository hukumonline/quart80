<?php
class Pandamp_Lib_Form
{
	function selectDate (
                        $sel_d = 0			// selected day
                      , $sel_m = 0       	// selected month
                      , $sel_y = 0       	// selected year
                      , $var_d = 'd'     	// name for day variable
                      , $var_m = 'm'    // name for month variable
                      , $var_y = 'y'     // name for year variable
                      , $min_y = 2000       	// minimum year
                      , $max_y = 0       	// maximum year
                      , $enabled = true  	// enable drop-downs?
                    ) {
                    	
	  	// Default day is today
	  	if ($sel_d == 0) 
	    	$sel_d = date('j');
	  	// Default month is this month
	  	if ($sel_m == 0) 
	    	$sel_m = date('n');
	  	// Default year is this year
	  	if ($sel_y == 0) 
	    	$sel_y = date('Y');
	  	// Default minimum year is this year
	  	if ($min_y == 0) 
	    	$min_y = date('Y');
	  	// Default maximum year is two years ahead
	  	if ($max_y == 0) 
			$max_y = ($min_y + 2);
                    	
		// --------------------------------------------------------------------------
	  	// Start off with the drop-down for Days
	  	// Start opening the select element
	  	$dateout = '<select name="'. $var_d. '"';
	  	// Add disabled attribute if necessary
	  	if (!$enabled) 
	    	$dateout .= ' disabled="disabled"';
	  	// Finish opening the select element
	  	$dateout .= '>\n';
	  	// Loop round and create an option element for each day (1 - 31)
	  	for ($i = 1; $i <= 31; $i++) {
	    	// Start the option element
	    	$dateout .= '\t<option value="'. $i. '"';
	    	// If this is the selected day, add the selected attribute
	    	if ($i == $sel_d) 
	      		$dateout .= ' selected="selected"';
	    	// Display the value and close the option element
	    	$dateout .= '>'. $i. '</option>\n';
	  	}
		// Close the select element
	  	$dateout .= '</select>&nbsp;';
	  	
		// --------------------------------------------------------------------------
  		// Now do the drop-down for Months
  		// Start opening the select element
  		$dateout .= '<select name="'. $var_m. '"';

  		// Add disabled attribute if necessary
  		if (!$enabled) 
    		$dateout .= ' disabled="disabled"';

  		// Finish opening the select element
  		$dateout .= '>\n';

  		// Loop round and create an option element for each month (Jan - Dec)
  		for ($i = 1; $i <= 12; $i++) {
    		// Start the option element
    		$dateout .= '\t<option value="'. $i. '"';
    		// If this is the selected month, add the selected attribute
    		if ($i == $sel_m) 
      			$dateout .= ' selected="selected"';
    		// Display the value and close the option element
    		$dateout .= '>'. date('F', mktime(3, 0, 0, $i)). '</option>\n';
  		}

  		// Close the select element
  		$dateout .= '</select>&nbsp;';	  	
  		
  		$max_y = date("Y");
  		// --------------------------------------------------------------------------
  		// Finally, the drop-down for Years
  		// Start opening the select element
  		$dateout .= '<select name="'. $var_y. '"';

  		// Add disabled attribute if necessary
  		if (!$enabled) 
    		$dateout .= ' disabled="disabled"';

  		// Finish opening the select element
  		$dateout .= '>\n';

  		// Loop round and create an option element for each year ($min_y - $max_y)
  		for ($i = $min_y; $i <= $max_y; $i++) {
    		// Start the option element
    		$dateout .= '\t<option value="'. $i. '"';
    		// If this is the selected year, add the selected attribute
    		if ($i == $sel_y) 
      			$dateout .= ' selected="selected"';
    		// Display the value and close the option element
    		$dateout .= '>'. $i. '</option>\n';
  		}
  		// Close the select element
  		$dateout .= '</select>';  	
  		return $dateout;	
	}
	
	/**
	 * categoryClinicPullDown
	 * @return category clinic
	 */
	
	static function categoryClinicPullDown($cat='')
	{
		$tblCatalog = new Pandamp_Modules_Dms_Catalog_Model_Catalog();
		$rowset = $tblCatalog->fetchAll("profileGuid='kategoriklinik'",'createdDate DESC');
		$category = "<select name=\"category\">\n";
		if ($cat) {
			$rowCategory = $tblCatalog->find($cat)->current();
			$rowCategoryAttribute = $rowCategory->findDependentRowsetCatalogAttribute();
			$title = $rowCategoryAttribute->findByAttributeGuid('fixedTitle');
			$category .= "<option value='$rowCategory->guid' selected>$title->value</option>";
			$category .= "<option value=''>---Semua Bidang---</option>";
		}
		else 
		{
			$category .= "<option value='' selected>---Semua Bidang---</option>";
		}
		foreach ($rowset as $row)
		{
			$rowsetCatalogAttribute = $row->findDependentRowsetCatalogAttribute();
			$attributeTitle = $rowsetCatalogAttribute->findByAttributeGuid('fixedTitle');
			if (($cat) && ($row->guid == $rowCategory->guid))
				continue;
			else 
				$category .= "<option value='$row->guid'>$attributeTitle->value</option>";
		}
		
		$category .= "</select>\n\n";
		return $category;
	}
	
	/**
	 * select month
	 * @param $montharray
	 * @return $month
	 */
	function monthPullDown($month, $montharray)
	{
	 	$monthSelect = "\n<select name=\"month\">\n";
		for($j=0; $j < 12; $j++) {
			if ($j != ($month - 1)) 
				$monthSelect .= " <option value=\"" . ($j+1) . "\">$montharray[$j]</option>\n";
			else
				$monthSelect .= " <option value=\"" . ($j+1) . "\" selected>$montharray[$j]</option>\n";
		}
		
		$monthSelect .= "</select>\n\n";
		return $monthSelect;
	}
	/**
	 * dayPullDown
	 * @param $day
	 * @return day
	 */
	 
	function dayPullDown($tday='')
	{
		$day = "<select name=\"day\" id=\"day\">\n";
		if ($tday) {
			$day .= "<option value=\"" . $tday . "\" selected>$tday</option>\n";
			$day .= "<option value=''>Tgl</option>";
		} else {
			$day .= "<option value='' selected>Tgl</option>";
		}
		for($i=1;$i <= 31; $i++) {
			if (($tday) and ($i == $tday)) {
				continue;
			} else {
				$day .= " <option value=\"" . $i ."\">$i</option>\n";
			}
		}
	
		$day .= "</select>\n\n";
		return $day;
	}
	
	/**
	 * educationPullDown
	 * @return education
	 */
	
	function educationPullDown($edu='')
	{
		$tblEducation = new Pandamp_Modules_Identity_Education_Model_Education();
		$row = $tblEducation->fetchAll();
		$education = "<select name=\"education\" id=\"education\">\n";
		if ($edu) {
			$rowEducation = $tblEducation->find($edu)->current();
			$education .= "<option value='$rowEducation->educationid' selected>$rowEducation->description</option>";
			$education .= "<option value =''>----- Pilih -----</option>";
		} else {
			$education .= "<option value ='' selected>----- Pilih -----</option>";
		}
		foreach ($row as $rowset) {
			if (($edu) and ($rowset->educationid == $rowEducation->educationid)) {
				continue;
			} else {
				$education .= "<option value='$rowset->educationid'>$rowset->description</option>";
			}
		}
		$education .= "</select>\n\n";
		return $education;
	}
	
	/**
	 * expensePullDown
	 * @return expense
	 */
	
	function expensePullDown($exp='')
	{
		$tblExpense = new Pandamp_Modules_Identity_Expense_Model_Expense();
		$row = $tblExpense->fetchAll();
		$expense = "<select name=\"expense\" id=\"expense\">\n";
		if ($exp) {
			$rowExpense = $tblExpense->find($exp)->current();	
			$expense .= "<option value='$rowExpense->expenseId' selected>$rowExpense->description</option>";
			$expense .= "<option value=''>----- Pilih -----</option>";
		} else {
			$expense .= "<option value='' selected>----- Pilih -----</option>";
		}
		foreach ($row as $rowset) {
			if (($exp) and ($rowset->expenseId == $rowExpense->expenseId)) {
				continue;
			} else {
				$expense .= "<option value='$rowset->expenseId'>$rowset->description</option>";
			}
		}
		$expense .= "</select>\n\n";
		return $expense;
	}
	
	/**
	 * businessTypePullDown
	 * @return businessType
	 */
	
	function businessTypePullDown($businessTypeId='')
	{
		$tblBusiness = new Pandamp_Modules_Identity_Business_Model_Business();
		$row = $tblBusiness->fetchAll();
		$businessType = "<select name=\"businessType\" id=\"businessType\">\n";
		if ($businessTypeId) {
			$rowBusinessType = $tblBusiness->find($businessTypeId)->current();
			$businessType .= "<option value='$rowBusinessType->businessTypeId' selected>$rowBusinessType->description</option>";
			$businessType .= "<option value=''>----- Pilih -----</option>";			
		} else {
			$businessType .= "<option value='' selected>----- Pilih -----</option>";
		}
		foreach ($row as $rowset) {
			if (($businessTypeId) and ($rowset->businessTypeId == $rowBusinessType->businessTypeId)) {
				continue;
			} else {
				$businessType .= "<option value='$rowset->businessTypeId'>$rowset->description</option>";
			}
		}
		$businessType .= "</select>\n\n";
		return $businessType;		
	}	
	
	function zone($name, $zid="")
	{
		$tblBannerZone = new Pandamp_Modules_Misc_Banner_Zone_Model_PowerbanZones();
		$row = $tblBannerZone->fetchAll(null,'zname ASC');
		$txt_zone = "<select name=\"$name\" id=\"$name\">";
		if ($zid) {
			$rowZoneName = $tblBannerZone->find($zid)->current();
			$txt_zone .= "<option value='$rowZoneName->zid' selected>$rowZoneName->zname</option>";
			$txt_zone .= "<option value=''>--- Pilih ---</option>";			
		} else {
			$txt_zone .= "<option value='' selected>--- Pilih ---</option>";
		}
		foreach ($row as $rowset) {
			if (($zid) and ($rowset->zid == $rowZoneName->zid)) {
				continue;
			} else {
				$txt_zone .= "<option value='$rowset->zid'>$rowset->zname</option>";
			}
		}
		$txt_zone .= "</select>";
		return $txt_zone;
	}
	
	/**
	 * GroupTree
	 * @return 
	 */
	
	function groupTree(array $selected=NULL)
	{
		// get group information
		$acl = Pandamp_Acl::manager();
		$params = $acl->optionsAroGroups();		
		
		$_html_result = '';
		
		foreach ($params as $_key => $_val)
			$_html_result .= $this->html_options_optoutput($_key, $_val, $selected);
			
		return $_html_result;
	}
	function html_options_optoutput($key, $value, $selected) {
	    if(!is_array($value)) {
	        $_html_result = '<option label="' . $this->escape_special_chars($value) . '" value="' .
	            $this->escape_special_chars($key) . '"';
	        if (in_array((string)$key, $selected))
	            $_html_result .= ' selected="selected"';
	        $_html_result .= '>' . $this->escape_special_chars($value) . '</option>' . "\n";
	    } else {
	        $_html_result = $this->html_options_optgroup($key, $value, $selected);
	    }
	    return $_html_result;
	}
	function escape_special_chars($string)
	{
	    if(!is_array($string)) {
	        $string = preg_replace('!&(#?\w+);!', '%%%KUTU_START%%%\\1%%%KUTU_END%%%', $string);
	        $string = htmlspecialchars($string);
	        $string = str_replace(array('%%%KUTU_START%%%','%%%KUTU_END%%%'), array('&',';'), $string);
	    }
	    return $string;
	}
}
?>