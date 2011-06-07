<?php
#################################
#	DEFINE PATHS				#
#################################
define("_BBCODEPATH",ROOT_URL."/resources/images/bbCode");
define("_EMOTICONSPATH",""._BBCODEPATH."/emoticons");
define("_BBCODEBUTTONSPATH",""._BBCODEPATH."/buttons");
define("_BBCODEICONSPATH",""._BBCODEPATH."/icons");
define("_BBCODEICONSDOCUMENTS",""._BBCODEPATH."/repository");
define("TBL_EMOTICONS","bbcode_emoticons");
//	TABLE MAIN
define("MAIN_TABLE_OPENING","<table width=\"100%\"
								cellpadding=\"1\" cellspacing=\"1\"");
//	STYLE BUTTONS
define("BUTTON_SPAN_STYLE_OPENING","<span style=\"float:left;padding-right:3px;padding-bottom:5px;\">");
define("BUTTON_SPAN_STYLE_CLOSING","</span>");
class Pandamp_Lib_Bbcode
{
	static function writeBbCode($NameOfForm,$NameOfField="theField",$notShown="")
	{
		// initialize counter
		$bbI = 0;
		//	initialize e random
		$randomSuffix	= rand();
		//	set a few commons vars
		$fixedSeparator	= " <img src=\""._BBCODEBUTTONSPATH."/bbCode_separator.png\"
								alt=\"\" title=\"\" border=\"0\"
								width=\"2\" height=\"19\"
								style=\"padding:3px;\"> ";
		
		$putTheBBCode	= "";
		//	start DIV
		$putTheBBCode .= "<div id=\"theBBCodeDIV".$randomSuffix."\" style=\"z-index:10000;\">\n";

		$putTheBBCode .= "".MAIN_TABLE_OPENING."\n";
		$putTheBBCode .= "<tr><td valign=\"top\" align=\"left\">";
		
	/**	START WRITE BBCODE CONTROL PANEL			**/
#################################
#	GROUP 1						#
#################################
		$group	= 0;
		#################################
		#	BOLD						#
		#################################
		$bbI++;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "<a href=\"javascript:void(0);\"
						onClick=\"bbstyle(0,'".$NameOfForm."',".$NameOfField.")\"
						onMouseOver=\"bbCodeRollOver('bbCodeBold".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_bold_on.png')\"
						onMouseOut=\"bbCodeRollOver('bbCodeBold".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_bold.png')\">
						<img src=\""._BBCODEBUTTONSPATH."/bbCode_bold.png\"
						alt=\"bold\" title=\"bold\" border=\"0\" name=\"bbCodeBold".$randomSuffix."\"></a>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		#################################
		#	ITALIC						#
		#################################
		$bbI++;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "	<a href=\"javascript:void(0);\"
						onClick=\"bbstyle(2,'".$NameOfForm."',".$NameOfField.")\"
						onMouseOver=\"bbCodeRollOver('bbCodeItalic".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_italic_on.png')\"
						onMouseOut=\"bbCodeRollOver('bbCodeItalic".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_italic.png')\">
						<img src=\""._BBCODEBUTTONSPATH."/bbCode_italic.png\"
						alt=\"italic\" title=\"italic\" border=\"0\" name=\"bbCodeItalic".$randomSuffix."\"></a>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		#################################
		#	UNDERLINE					#
		#################################
		$bbI++;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "	<a href=\"javascript:void(0);\"
						onClick=\"bbstyle(4,'".$NameOfForm."',".$NameOfField.")\"
						onMouseOver=\"bbCodeRollOver('bbCodeUnderline".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_underline_on.png')\"
						onMouseOut=\"bbCodeRollOver('bbCodeUnderline".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_underline.png')\">
						<img src=\""._BBCODEBUTTONSPATH."/bbCode_underline.png\"
						alt=\"underline\" title=\"underline\" border=\"0\" name=\"bbCodeUnderline".$randomSuffix."\"></a>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		#################################
		#	STRIKE						#
		#################################
		$bbI++;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "	<a href=\"javascript:void(0);\"
						onClick=\"bbstyle(6,'".$NameOfForm."',".$NameOfField.")\"
						onMouseOver=\"bbCodeRollOver('bbCodeStrike".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_strike_on.png')\"
						onMouseOut=\"bbCodeRollOver('bbCodeStrike".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_strike.png')\">
						<img src=\""._BBCODEBUTTONSPATH."/bbCode_strike.png\"
						alt=\"strike\" title=\"strike\" border=\"0\" name=\"bbCodeStrike".$randomSuffix."\"></a>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		if ($group > 0)	{
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= $fixedSeparator;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
#################################
#	GROUP 2						#
#################################
		$group	= 0;
		#################################
		#	LIST/INDENT					#
		#################################
		$bbI++;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "	<a href=\"javascript:void(0);\"
						onClick=\"bbstyle(10,'".$NameOfForm."',".$NameOfField.")\"
						onMouseOver=\"bbCodeRollOver('bbCodeList".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_list_on.png')\"
						onMouseOut=\"bbCodeRollOver('bbCodeList".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_list.png')\">
						<img src=\""._BBCODEBUTTONSPATH."/bbCode_list.png\"
						alt=\"indent\" title=\"indent\" border=\"0\" name=\"bbCodeList".$randomSuffix."\"></a>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		#################################
		#	LIST/POINTS					#
		#################################
		$bbI++;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "	<a href=\"javascript:void(0);\"
						onClick=\"bbstyle(12,'".$NameOfForm."',".$NameOfField.")\"
						onMouseOver=\"bbCodeRollOver('bbCodeListPoint".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_listPoints_on.png')\"
						onMouseOut=\"bbCodeRollOver('bbCodeListPoint".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_listPoints.png')\">
						<img src=\""._BBCODEBUTTONSPATH."/bbCode_listPoints.png\"
						alt=\"list\" title=\"list\" border=\"0\" name=\"bbCodeListPoint".$randomSuffix."\"></a>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		#################################
		#	HORIZONTAL RULES			#
		#################################
		$bbI++;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "	<a href=\"javascript:void(0);\"
						onClick=\"bbstyle(14,'".$NameOfForm."',".$NameOfField.")\"
						onMouseOver=\"bbCodeRollOver('bbCodeHR".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_horizontalRule_on.png')\"
						onMouseOut=\"bbCodeRollOver('bbCodeHR".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_horizontalRule.png')\">
						<img src=\""._BBCODEBUTTONSPATH."/bbCode_horizontalRule.png\"
						alt=\"horizontal rule\" title=\"horizontal rule\" border=\"0\" name=\"bbCodeHR".$randomSuffix."\"></a>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		if ($group > 0)	{
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= $fixedSeparator;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		$bbI++;
		$bbI++;
		$bbI++;
		$bbI++;
#################################
#	GROUP 5						#
#################################
		$group	= 0;
		#################################
		#	URL							#
		#################################
		$bbI++;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "	<a href=\"javascript:void(0);\"
						onClick=\"PromptUrl('".$NameOfForm."',".$NameOfField.",'','')\"
						onMouseOver=\"bbCodeRollOver('bbCodeUrl".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_URL_on.png')\"
						onMouseOut=\"bbCodeRollOver('bbCodeUrl".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_URL.png')\">
						<img src=\""._BBCODEBUTTONSPATH."/bbCode_URL.png\"
						alt=\"url\" title=\"url\" border=\"0\" name=\"bbCodeUrl".$randomSuffix."\"></a>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		#################################
		#	MAIL						#
		#################################
		$bbI++;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "	<a href=\"javascript:void(0);\"
						onClick=\"PromptMail('".$NameOfForm."',".$NameOfField.")\"
						onMouseOver=\"bbCodeRollOver('bbCodeMail".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_MAIL_on.png')\"
						onMouseOut=\"bbCodeRollOver('bbCodeMail".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_MAIL.png')\">
						<img src=\""._BBCODEBUTTONSPATH."/bbCode_MAIL.png\"
						alt=\"mail\" title=\"mail\" border=\"0\" name=\"bbCodeMail".$randomSuffix."\"></a>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		$bbI++;
		if ($group > 0)	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= $fixedSeparator;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		$bbI++;
		$bbI++;
#################################
#	GROUP 8						#
#################################
		$group	= 0;
		#################################
		#	FONT-COLORS					#
		#################################
		$bbI++;
		$thisPaletteID	= $bbI;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "	<a href=\"javascript:void(0);\"
							onClick=\"showHideBBCode('bbCodePalettes".$randomSuffix."');\"
							onMouseOver=\"bbCodeRollOver('bbCodePaletteColors".$randomSuffix."','"._BBCODEBUTTONSPATH."/button_colors_on.png')\"
							onMouseOut=\"bbCodeRollOver('bbCodePaletteColors".$randomSuffix."','"._BBCODEBUTTONSPATH."/button_colors.png')\">";
			$putTheBBCode .= "<img src=\""._BBCODEBUTTONSPATH."/button_colors.png\" border=\"0\" alt=\"font color\" title=\"font color\" name=\"bbCodePaletteColors".$randomSuffix."\">";
			$putTheBBCode .= "</a>\n";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		if ($group > 0)	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= $fixedSeparator;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
#################################
#	GROUP 9						#
#################################
		$group	= 0;
		$bbI++;
		$thisEmoticonsID	= $bbI;
		if (!in_array($bbI,$notShown))	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "<a href=\"javascript:void(0);\"
							onClick=\"showHideBBCode('bbCodeEmoticons".$randomSuffix."');\"
							onMouseOver=\"bbCodeRollOver('bbCodeEmoticonsDiv".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_emoticons_on.png')\"
							onMouseOut=\"bbCodeRollOver('bbCodeEmoticonsDiv".$randomSuffix."','"._BBCODEBUTTONSPATH."/bbCode_emoticons.png')\">";
			$putTheBBCode .= "<img src=\""._BBCODEBUTTONSPATH."/bbCode_emoticons.png\" border=\"0\" alt=\"emoticons\" title=\"emoticons\" name=\"bbCodeEmoticonsDiv".$randomSuffix."\">";
			$putTheBBCode .= "</a>\n";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		if ($group > 0)	{
			$group++;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			//$putTheBBCode .= $fixedSeparator;
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}

		//	END ROW - START NEW ROW FOR SPECIAL CHARS & COLORS & SMILES TABLES
		$putTheBBCode .= "</td></tr>\n";
		$putTheBBCode .= "<tr><td valign=\"top\" align=\"left\">";

		#################################
		#	FONT-COLORS-TABLE			#
		#################################
		if (!in_array($thisPaletteID,$notShown))	{
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "<div id=\"bbCodePalettes".$randomSuffix."\"
								style=\"display:none;position:relative;width:400px;\">";
			$putTheBBCode .= "<table border=0 cellpadding=1 cellspacing=1
								style=\"border:1px;border-color:#333333;\">\n";
			$putTheBBCode .= "<tr>\n";

			//include_once("".dirname(__FILE__)."/class.colorConverter.php");
			$color	= new Pandamp_Lib_ColorConverter();

			$maxPerRow		= 36;
			$maxWidthTD		= 10;		//	Pixels
			$maxHeightTD	= 10;		//	Pixels

			$colorStep		= 51;		//	Color Style (by step 51 is WebBased)

			$cc	= 0;
			for($a=0;$a<=255;$a=$a+$colorStep)	{
				$num1	= $a;
				for($b=0;$b<=255;$b=$b+$colorStep)	{
					$num2	= $b;
					for($c=0;$c<=255;$c=$c+$colorStep)	{
						$num3	= $c;
						$RGB	= $color->RGBToHex($num1,$num2,$num3);
						$numTxt1	= 255 - $num1;
						$numTxt2	= 255 - $num2;
						$numTxt3	= 255 - $num3;
						$RGB2	= $color->RGBToHex($numTxt1,$numTxt2,$numTxt3);

						$putTheBBCode	.= ($cc == 0)	? "<tr>" : "";

						$putTheBBCode .= "<td bgcolor=\"#".$RGB."\" width=\"".$maxWidthTD."\"
											height=\"".$maxHeightTD."\">
								<a href=\"javascript:void(0);\"
									onClick=\"bbfontstyle('[color=#".$RGB."]', '[/color]','".$NameOfForm."',".$NameOfField.");\"><img
									src=\""._BBCODEPATH."/pix.gif\"
									width=\"".$maxWidthTD."\" height=\"".$maxHeightTD."\"
									border=0 alt=\"#".$RGB."\"></a></td>\n";
						$cc++;
						$putTheBBCode	.= ($cc >= $maxPerRow)	? "</tr>\n" : "";
						$cc	= ($cc >= $maxPerRow) ?  0 : $cc;

					}
				}
			}
			$putTheBBCode .= "		</tr>\n";
			$putTheBBCode .= "	</table>\n";
			$putTheBBCode .= "	</div>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}
		#################################
		#	EMOTICONS-TABLE				#
		#################################
		if (!in_array($bbI,$notShown))	{
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_OPENING."";
			$putTheBBCode .= "<div id=\"bbCodeEmoticons".$randomSuffix."\"
								style=\"display:none;position:relative;width:400px;\">";
			$perRow	= 15;
			//	USE YOUR OWN DATABASE CONNECTION CLASS
			//	AND EDIT HERE
			//	OR USE THE SIMPLE PHP-MYSQL FUNCTIONS
			//	mysql_query(), mysql_num_rows(), mysql_fetch_array().
			$model = new Pandamp_Modules_Extension_Bbcode_Model_Bbcode();
			$res 	= $model->fetchAll();
			$num	= count($res);					//	==> your own mysql_num_rows
			if ($num > 0)	{
				$counterPerRow	= 0;
				$putTheBBCode .= "<table border=0 cellpadding=2 cellspacing=2>\n";
				//while($info = $model->fetchAll())	{	//	==> your own mysql_fetch_array
				foreach($res as $info)	{	//	==> your own mysql_fetch_array
					$putTheBBCode	.= ($counterPerRow == 0)	? "<tr>" : "";
//					$putTheBBCode	.= "<td class=\"paddingSmiles\" align=\"center\">
//										<a href=\"javascript:void(0);\" onClick=\"putSmile('[".$info[$counterPerRow]['bbcode']."]','".$NameOfForm."',".$NameOfField.")\">
//										<img src=\""._EMOTICONSPATH."/".$info[$counterPerRow]['image']."\" alt=\"\" border=0></a>
//										</td>";
					$putTheBBCode	.= "<td class=\"paddingSmiles\" align=\"center\">
										<a href=\"javascript:void(0);\" onClick=\"putSmile('[".$info->bbcode."]','".$NameOfForm."',".$NameOfField.")\">
										<img src=\""._EMOTICONSPATH."/".$info->image."\" alt=\"\" border=0></a>
										</td>";
					$counterPerRow++;
					$putTheBBCode	.= ($counterPerRow == $perRow)	? "</tr><tr>" : "";
					$counterPerRow	= ($counterPerRow == $perRow)	? 0 : $counterPerRow;
				}
				$putTheBBCode .= "</table>\n";
			}
			$putTheBBCode .= "</div>";
			$putTheBBCode .= "".BUTTON_SPAN_STYLE_CLOSING."\n";
		}


		$putTheBBCode .= "</td></tr>\n";
		//	end TABLE MAIN
		$putTheBBCode .= "</table>\n";

		//	end DIV
		$putTheBBCode .= "</div>\n";

		return $putTheBBCode;		
	}
	static function parseBBCode($text,$parsing=true,$emoticons=true)	
	{
		$text	= preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $text);
		$text	= " " . $text;

		if (! (strpos($text, "[") && strpos($text, "]")) )	{
			// Remove padding, return.
			$text = substr($text, 1);
			return $text;
		}

		$matches	= array();
		//	PARSING ALL
		if ($parsing)	{
			$randomSuffix	= rand();
			$matches		= array(
				"#\[class=(.*?)\](.*?)\[/class\]#si" => "<span class=\"$1\">$2</span>",
				"#\[b\](.*?)\[/b\]#si" => "<b>$1</b>",
				"#\[u\](.*?)\[/u\]#si" => "<u>$1</u>",
				"#\[i\](.*?)\[/i\]#si" => "<i>$1</i>",
				"#\[strike\](.*?)\[/strike\]#si" => "<span class=\"lineThrough\">$1</span>",
				"#\[list\](.*?)\[/list\]#si" => "<ul>$1</ul>",
				"#\[list=(.*?)\](.*?)\[/list\]#si" => "<ul class=\"$1\">$2</ul>",
				"#\[\*\]#si" => "<li>",
				"#\[hr\]#si" => "<hr>",
				"#\[bgcolor=(\#[0-9A-F]{6}|[a-z]+)\](.*?)\[/bgcolor\]#si" => "<font style=\"background-color:$1\">$2</font>",
				"#\[t_left\](.*?)\[/t_left\]#si" => "<div align=\"left\">$1</div>",
				"#\[t_center\](.*?)\[/t_center\]#si" => "<div align=\"center\">$1</div>",
				"#\[t_right\](.*?)\[/t_right\]#si" => "<div align=\"right\">$1</div>",
				"#\[t_justify\](.*?)\[/t_justify\]#si" => "<div align=\"justify\">$1</div>",
				"#\[img\](.*?)\[/img\]#si" => "<img src=\"$1\" alt=\"\" title=\"\" border=\"0\">",
				"#\[img=(.*?)\](.*?)\[/img\]#si" => "<img src=\"$2\" align=\"$1\" alt=\"\" title=\"\" border=\"0\">",
				"#\[flash=(.*?)x(.*?)\](.*?)\[/flash\]#si" => "<div id=\"flash".$randomSuffix."\" align=\"center\">&nbsp;</div><script language=\"Javascript\" type=\"text/javascript\">loaderSWF('$3','flash".$randomSuffix."',$1,$2);</script>\n",
				"#\[doc=(.*?)\](.*?)\[/doc\]#si" => "<a href=\"$1\" target=\"_blank\" class=\"bbCodeLink\">$2</a>",
				"#\[doc=(.*?)~(.*?)\](.*?)\[/doc\]#si" => "<img src=\""._BBCODEICONSPATH."/$1\" alt=\"\" border=0 align=\"absmiddle\"> <a href=\"$2\" target=\"_blank\" class=\"bbCodeLink\">$3</a>",
				"#\[url=([a-z]+?://){1}([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+\(\)]+) t=(_blank|_parent)\](.*?)\[/url\]#si" => "<a href=\"$1$2\" target=\"_blank\" class=\"bbCodeLink\">$4</a>",
				"#\[url=([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+\(\)]+) t=(_blank|_parent)\](.*?)\[/url\]#si" => "<a href=\"$1\" target=\"_blank\" class=\"bbCodeLink\">$3</a>",
				"#\[email=(.*?)\](.*?)\[/email\]#si" => "<a href=\"mailto:$1\" class=\"bbCodeLink\">$2</a>",
				"#\[gmaps=([0-9]{1,3})x([0-9]{1,3})\]([a-z]+?://){1}([a-z0-9\-\.,'\?!%\*_\#:;~\\&$@\/=\+\(\)maps.google]+)\[/gmaps\]#si" =>
				"<iframe width=\"$1\" height=\"$2\"
							frameborder=\"0\" scrolling=\"no\"
							marginheight=\"0\" marginwidth=\"0\"
							src=\"$3$4\">
						</iframe>\n",
				"#\[font=(.*?)\](.*?)\[/font\]#si" => "<font face=\"$1\">$2</font>",
				"#\[size=(.*?)x(.*?)\](.*?)\[/size\]#si" => "<font size=\"$1\"><span class=\"bbCode$1\">$3</span></font>",
				"#\[color=(\#[0-9A-F]{6}|[a-z]+)\](.*?)\[/color\]#si" => "<font color=\"$1\">$2</font>"
			);

		//	EMOTICONS
			if ($emoticons)	{
				$model = new Pandamp_Modules_Extension_Bbcode_Model_Bbcode();
				$res 	= $model->fetchAll();
				$num 	= count($res);
				$info	= smilesArray($res);

				$zz		= 0;
				if ($num > 0)	{
					for ($zz = 0; $zz < $num; $zz++)	{
						$theSmile	= $info[$zz]['code'];
						$theImage	= $info[$zz]['image'];
						/*	Modified by Fabrizio Parrella	*/
						$theSmile	= str_replace('"', '\"', $theSmile);
						$theSmile	= preg_quote($theSmile);
						$theSmile	= str_replace("/", "\\/", $theSmile);
						$orig[]		= '/\['.$theSmile.'\]/si';
						$repl[]		= ' <img src="'._EMOTICONSPATH.'/'.$theImage.'" alt="" border="0" align="absmiddle" /> ';
					}
					$text = @preg_replace($orig, $repl, $text);
				}
			}	##	End emoticons
		} else {

			$matches		= array(
				"#\[class=(.*?)\](.*?)\[/class\]#si" => "",
				"#\[b\](.*?)\[/b\]#si" => "",
				"#\[u\](.*?)\[/u\]#si" => "",
				"#\[i\](.*?)\[/i\]#si" => "",
				"#\[strike\](.*?)\[/strike\]#si" => "",
				"#\[list\](.*?)\[/list\]#si" => "",
				"#\[list=(.*?)\](.*?)\[/list\]#si" => "",
				"#\[\*\]#si" => "",
				"#\[hr\]#si" => "",
				"#\[bgcolor=(\#[0-9A-F]{6}|[a-z]+)\](.*?)\[/bgcolor\]#si" => "",
				"#\[t_left\](.*?)\[/t_left\]#si" => "",
				"#\[t_center\](.*?)\[/t_center\]#si" => "",
				"#\[t_right\](.*?)\[/t_right\]#si" => "",
				"#\[t_justify\](.*?)\[/t_justify\]#si" => "",
				"#\[img\](.*?)\[/img\]#si" => "",
				"#\[img=(.*?)\](.*?)\[/img\]#si" => "",
				"#\[flash=(.*?)x(.*?)\](.*?)\[/flash\]#si" => "",
				"#\[doc=(.*?)\](.*?)\[/doc\]#si" => "",
				"#\[doc=(.*?)~(.*?)\](.*?)\[/doc\]#si" => "",
				"#\[url=([a-z]+?://){1}([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+\(\)]+) t=(_blank|_parent)\](.*?)\[/url\]#si" => "",
				"#\[url=([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+\(\)]+) t=(_blank|_parent)\](.*?)\[/url\]#si" => "",
				"#\[email=(.*?)\](.*?)\[/email\]#si" => "",
				"#\[gmaps=([0-9]{1,3})x([0-9]{1,3})\]([a-z]+?://){1}([a-z0-9\-\.,'\?!%\*_\#:;~\\&$@\/=\+\(\)maps.google]+)\[/gmaps\]#si" => "",
				"#\[font=(.*?)\](.*?)\[/font\]#si" => "",
				"#\[size=(.*?)x(.*?)\](.*?)\[/size\]#si" => "",
				"#\[color=(\#[0-9A-F]{6}|[a-z]+)\](.*?)\[/color\]#si" => ""
			);
			if ($emoticons)	{
//				$qry	= "	SELECT * FROM ".TBL_EMOTICONS." ";
//				$res	= $this->DBObject->SQL_Query($qry,$this->DBConnection);	//	==> your own mysql_query
//				$num	= $this->DBObject->SQL_NumRows($res);					//	==> your own mysql_num_rows
				$model = new Pandamp_Modules_Extension_Bbcode_Model_Bbcode();
				$res 	= $model->fetchAll();
				$num 	= count($res);
				$info	= smilesArray($res);

				$zz		= 0;
				if ($num > 0)	{
					for ($zz = 0; $zz < $num; $zz++)	{
						$theSmile	= $info[$zz]['code'];
						$theImage	= $info[$zz]['image'];
						/*	Modified by Fabrizio Parrella	*/
						$theSmile	= str_replace('"', '\"', $theSmile);
						$theSmile	= preg_quote($theSmile);
						$theSmile	= str_replace("/", "\\/", $theSmile);
						$orig[]		= '/\['.$theSmile.'\]/si';
						$repl[]		= $theSmile;
					}
					$text = @preg_replace($orig, $repl, $text);
				}
			}	##	End emoticons
		}		##	End normal parsing
		//	REPLACE ALL
		$text 	= @preg_replace(array_keys($matches), array_values($matches), $text);
		$text	= str_Replace("\n","<br>",$text);
		return $text;
	}
}

/**
*	arrayForSmile
*
*	prepare array to use for emoticons, in BBCode
*
*	@author		kender
*	@param		resource
*	@return		array
*	@version	1.0
*/
function smilesArray($resFS,$assoc=0,$num=0)	{
	$array		= "";
	$counter	= 0;
	foreach($resFS as $row) {
		$array[$counter]['code']	= $row->bbcode;
		$array[$counter]['image']	= $row->image;
		$counter++;
	}
//	while($row	= DB::SQL_FetchArray($resFS))	{
//		$array[$counter]['code']	= $row['bbcode'];
//		$array[$counter]['image']	= $row['image'];
//		$counter++;
//	}
	return $array;
}
?>