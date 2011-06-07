<?php
class Pandamp_Lib_Formater
{
	/**
	 * he syntax is DateAdd (interval,number,date).
	 * The interval is a string expression that defines the interval you want to add. 
	 * For example minutes or days, 
	 * the number is the number of that interval that you wish to add, and the date is the date.
	 * Interval can be one of:
	 * @params yyyy	year
	 * @params q	Quarter
	 * @params m	Month
	 * @params y	Day of year
	 * @params d	Day
	 * @params w	Weekday
	 * @params ww	Week of year
	 * @params h	Hour
	 * @params n	Minute
	 * @params s	Second
	 * As far as I can tell, w,y and d do the same thing, 
	 * that is add 1 day to the current date, q adds 3 months and ww adds 7 days. 
	 *
	 */
		
	static function DateAdd($interval, $number, $date) {
	
	    $date_time_array = getdate($date);
	    $hours = $date_time_array['hours'];
	    $minutes = $date_time_array['minutes'];
	    $seconds = $date_time_array['seconds'];
	    $month = $date_time_array['mon'];
	    $day = $date_time_array['mday'];
	    $year = $date_time_array['year'];
	
	    switch ($interval) {
	    
	        case 'yyyy':
	            $year+=$number;
	            break;
	        case 'q':
	            $year+=($number*3);
	            break;
	        case 'm':
	            $month+=$number;
	            break;
	        case 'y':
	        case 'd':
	        case 'w':
	            $day+=$number;
	            break;
	        case 'ww':
	            $day+=($number*7);
	            break;
	        case 'h':
	            $hours+=$number;
	            break;
	        case 'n':
	            $minutes+=$number;
	            break;
	        case 's':
	            $seconds+=$number;
	            break;            
	    }
	    $timestamp= mktime($hours,$minutes,$seconds,$month,$day,$year);
	    return $timestamp;
	}	
	static function implode_with_keys($glue, $array, $valwrap='')
    {
    	if ($array) {
	        foreach($array AS $key => $value) {
	            $ret[] = $valwrap.$value.$valwrap;
	        }
	        return implode($glue, $ret);
    	}
    }    
	static function string_limit_words($string, $word_count=100)
	{
	    $trimmed = "";
	    $string = preg_replace("/\040+/"," ", trim($string));
	    $stringc = explode(" ",$string);
	    if($word_count >= sizeof($stringc))
	    {
	        // nothing to do, our string is smaller than the limit.
	      return $string;
	    }
	    elseif($word_count < sizeof($stringc))
	    {
	        // trim the string to the word count
	        for($i=0;$i<$word_count;$i++)
	        {
	            $trimmed .= $stringc[$i]." ";
	        }
	       
	        if(substr($trimmed, strlen(trim($trimmed))-1, 1) == '.')
	          return trim($trimmed).'..';
	        else
	          return trim($trimmed).'...';
	    }
	}   
    static function createthumb($name,$filename,$new_w,$new_h){
		if (file_exists($filename)) { unlink($filename); }
	    $system=explode('.',$name);
		if (preg_match('/jpg|jpeg/',$system[1])){
			$src_img=imagecreatefromjpeg($name);
		}
		if (preg_match('/png/',$system[1])){
			$src_img=imagecreatefrompng($name);
		}
		if (preg_match('/gif/',$system[1])){
			$src_img=imagecreatefromgif($name);
		}
		$old_x=imageSX($src_img);
		$old_y=imageSY($src_img);
		if ($old_x > $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$old_y*($new_h/$old_x);
		}
		if ($old_x < $old_y) {
			$thumb_w=$old_x*($new_w/$old_y);
			$thumb_h=$new_h;
		}
		if ($old_x == $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
		}
			$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
			imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		if (preg_match("/jpg|jpeg/",$system[1])) {
			imagejpeg($dst_img,$filename); 
		}
		if (preg_match("/png/",$system[1])) {
			imagepng($dst_img,$filename); 
		}
		if (preg_match("/gif/",$system[1])) {
			imagegif($dst_img,$filename); 
		}
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
        //chmod($filename,0644); // new file can sometimes have wrong permissions
	}
	// see if url exists (for picture on remote host as well)
	function url_exists($url) {
		$a_url = parse_url($url);
	    if (!isset($a_url['port'])) $a_url['port'] = 80;
	    $errno = 0;
	    $errstr = '';
	    $timeout = 5;
	    if(isset($a_url['host']) && $a_url['host']!=gethostbyname($a_url['host'])){
	    	$fid = @fsockopen($a_url['host'], $a_url['port'], $errno, $errstr, $timeout);
	        if (!$fid) return false;
	        $page = isset($a_url['path'])  ?$a_url['path']:'';
	        $page .= isset($a_url['query'])?'?'.$a_url['query']:'';
	        fputs($fid, 'HEAD '.$page.' HTTP/1.0'."\r\n".'Host: '.$a_url['host']."\r\n\r\n");
	        $head = fread($fid, 4096);
	        fclose($fid);
	        return preg_match('#^HTTP/.*\s+[200|302]+\s#i', $head);
	    } else {
	    	return false;
	    }
	}
	static function thumb_exists($thumbnail)
	{
		$pos = strpos($thumbnail,"://");
		if ($pos === false) {
			return file_exists($thumbnail);
		}
		else 
		{
			return Pandamp_Lib_Formater::url_exists($thumbnail);																																																																																											
		}
	}
	static function get_date_english($tanggal) {
		$id = $tanggal;
		$id = substr($id,0,4)."-".substr($id,5,2)."-".substr($id,8,2);
		return	$id; 
	}
	static function get_date($tanggal) {
		$id = $tanggal;
		$id = substr($id,8,2).".".substr($id,5,2).".".substr($id,2,2)." ".substr($id,11,2).":".substr($id,14,2);
		return $id; 
	}
	static function getRealIpAddr()
	{
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	    {
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	    {
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else
	    {
	      $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
	}	
	static function _cleanMsWordHtml($textHtml)
	{
		$sTmp = $textHtml;
		/// <summary>
		/// Removes all FONT and SPAN tags, and all Class and Style attributes.
		/// Designed to get rid of non-standard Microsoft Word HTML tags.
		/// </summary>
		// start by completely removing all unwanted tags 

		$sTmp = preg_replace("/<(\/)?(font|span|del|ins)[^>]*>/","",$sTmp); 

		// then run another pass over the html (twice), removing unwanted attributes 

		$sTmp = preg_replace("/<([^>]*)(class|lang|style|size|face)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>/","<\\1>",$sTmp); 
		$sTmp = preg_replace("/<([^>]*)(class|lang|style|size|face)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>/","<\\1>",$sTmp);
		
		$sTmp = str_replace("<P ><SPAN ><o:p><FONT >&nbsp;</FONT></o:p></SPAN></P>", '', $sTmp);
		$sTmp = str_replace("<P ><SPAN ><FONT ><FONT ><SPAN >&nbsp;</SPAN><o:p></o:p></FONT></FONT></SPAN></P>", '', $sTmp);
		$sTmp = str_replace("<P ><SPAN ><o:p><FONT >&nbsp;</FONT></o:p></SPAN><SPAN ><o:p><FONT >&nbsp;</FONT></o:p></SPAN></P>", '', $sTmp);
		
		$anehaneh = array("<P ><SPAN ><o:p><FONT >&nbsp;</FONT></o:p></SPAN><SPAN ><o:p><FONT >&nbsp;</FONT></o:p></SPAN></P>",
							"<P ><SPAN ><SPAN >&nbsp;</SPAN><o:p></o:p></SPAN></P>",
							"<P ><B><SPAN ><o:p>&nbsp;</o:p></SPAN></B></P>",
							"<P ><B><SPAN ><o:p>&nbsp;</o:p></SPAN></B><SPAN ><o:p>&nbsp;</o:p></SPAN></P>",
							"<P ><SPAN ><o:p>&nbsp;</o:p></SPAN></P>",
							"<b><span><o:p>&nbsp;</o:p></span></b>",
							"<p>&nbsp;</p>",
							"<p><b><span><o:p>&nbsp;</o:p></span></b></p>",
							"<P ><B ><SPAN ><o:p>&nbsp;</o:p></SPAN></B></P>",
							"<?xml:namespace prefix = o ns = ".'"urn:schemas-microsoft-com:office:office"'." /><o:p></o:p>",
							"<?xml:namespace prefix = st1 ns = ".'"urn:schemas-microsoft-com:office:smarttags"'." />",
							"<P ><SPAN ></SPAN>&nbsp;</P>");
		$sTmp = str_replace($anehaneh, '', $sTmp);
		
		return $sTmp;
	}
	static function getNode($node)
	{
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$rowFolder = $tblFolder->find($node)->current();
		if ($rowFolder) {
			$path = explode("/",$rowFolder->path);
			$rpath = $path[0];
			$rowFolder1 = $tblFolder->find($rpath)->current();
			if ($rowFolder1) {
				$rowFolder2 = $tblFolder->find($rowFolder1->parentGuid)->current();
				if ($rowFolder2) {
					if ($rowFolder2->title == "Peraturan") {
						return "nprt";
					} 
					elseif ($rowFolder2->title == "Putusan") {
						return "npts";
					}
					else 
					{
						return "node";
					}
				}
				else 
				{
					return "node";
				}
			}
			else 
			{
				return "node";
			}
		}
		else 
		{
			return "node";
		}
	}
	static function findCatalog($query)
	{
		$indexingEngine = Pandamp_Search::manager();
		$hits = $indexingEngine->find($query,0,1);
		$num = $hits->response->numFound;
		return $num;
	}
	static function get_user_id($username)
	{ 
		$tblUser = new Pandamp_Modules_Identity_User_Model_User();
		$rowUser = $tblUser->fetchRow("username='".$username."'");
		return $rowUser->guid;
	}	
	static function getCatalogAuthor($guid)
	{
		$tblCatalog = new Kutu_Core_Orm_Table_Catalog();
		$rowset = $tblCatalog->find($guid)->current();
		
		if ($rowset) return $rowset->createdBy;
	}
	static function updateUserLog()
	{
        $userId = Zend_Auth::getInstance()->getIdentity()->guid;
		
        $model = new Pandamp_Modules_Identity_Log_Model_Log();
        $model->updateUserLog($userId,array(
        	'lastlogin' => new Zend_Db_Expr('NOW()')
        ));
	}
	static function writeLog()
	{
        $userId = Zend_Auth::getInstance()->getIdentity()->guid;
		
        $model = new Pandamp_Modules_Identity_Log_Model_Log();
        $model->addUserLog(array(
        	'user_id' => $userId,
        	'user_ip' => self::getRealIpAddr(),
        	'login' => new Zend_Db_Expr('NOW()')
        ));
	}
	
}