<?php
#########################################################
#	GiveMeEnergy Projects								#
#	Engine by []==ThoR®	for More Interactive			#
#	Contact:											#
#	maurizio@more.it									#
#	piero@more.it										#
#	More Interactive - www.more.it						#
#														#
#	See	More Projects WebSite for updates & patches		#
#	http://projects.moreinteractive.net/				#
#														#
#########################################################

/**
*	Create color Palette. Used by BBCode
*
*	@author		kender
*	@package	classes
*	@version	$Revision: 1.1 $
*	@see		class.bbCode.php
*/

class Pandamp_Lib_ColorConverter	{

	//	Convert from #?????? to RGB
	//	@public
	//	return array
	function HexToRGB($color)	{
		//	Delete the # symbol
		$input	= Trim(Str_Replace("#","",StrToUpper($color)));

		//	Get every single number-letter
		$a		= $this->_convertToDecimal($input[0]);
		$b		= $this->_convertToDecimal($input[1]);
		$c		= $this->_convertToDecimal($input[2]);
		$d		= $this->_convertToDecimal($input[3]);
		$e		= $this->_convertToDecimal($input[4]);
		$f		= $this->_convertToDecimal($input[5]);

		//	Build the RGB code
		$r		= ($a * 16) + $b;
		$g		= ($c * 16) + $d;
		$b		= ($e * 16) + $f;

		//	Put R, G and B in an array for separate use
		$RGB	= Array($r,$g,$b);
		return $RGB;
	}

	//	Convert from RGB to #??????
	//	@public
	//	return array
	function RGBToHex($colorR,$colorG,$colorB)	{
		$colorR	= Trim($colorR);
		$colorG	= Trim($colorG);
		$colorB	= Trim($colorB);

		//	Calculate every single letter-number
		$a	= $this->_convertToHex(floor($colorR / 16));
		$b	= $this->_convertToHex($colorR % 16);
		$c	= $this->_convertToHex(floor($colorG / 16));
		$d	= $this->_convertToHex($colorG % 16);
		$e	= $this->_convertToHex(floor($colorB / 16));
		$f	= $this->_convertToHex($colorB % 16);

		//	Build the Hexa Code
		$color	= $a.$b.$c.$d.$e.$f;

		return $color;
	}

	//	Get the decimal code foreach hexadecimal letter
	//	@private
	//	@return string
	function _convertToDecimal($hex)	{
		switch ($hex)	{
			case "A":
				$value	= 10;
			break;
			case "B":
				$value	= 11;
			break;
			case "C":
				$value	= 12;
			break;
			case "D":
				$value	= 13;
			break;
			case "E":
				$value	= 14;
			break;
			case "F":
				$value	= 15;
			break;
			default:
				$value	= $hex;
			break;
		}
		return $value;
	}

	//	Get the hexadecimal letter foreach decimal code
	//	@private
	//	@return string
	function _convertToHex($dec)	{
		switch ($dec)	{
			case "10":
				$value	= "A";
			break;
			case "11":
				$value	= "B";
			break;
			case "12":
				$value	= "C";
			break;
			case "13":
				$value	= "D";
			break;
			case "14":
				$value	= "E";
			break;
			case "15":
				$value	= "F";
			break;
			default:
				$value	= $dec;
			break;
		}
		return $value;
	}

}	##	End Class
?>