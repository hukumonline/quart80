/* ================================================================ 
This copyright notice must be untouched at all times.

The original version of this script and the associated (x)html
is available at http://www.stunicholls.com/various/tabbed_pages.html
Copyright (c) 2005-2007 Stu Nicholls. All rights reserved.
This script and the associated (x)html may be modified in any 
way to fit your requirements.
=================================================================== */

var lilmenu={init:function(){var b,a=0;while(b=document.getElementById("lilmenu").getElementsByTagName("DIV")[a++]){if(b.className=="hidup"||b.className=="mati"){b.onclick=function(){var d=document.getElementsByTagName("DIV");for(var e=0;e<d.length;e++){d[e].className=d[e].className.replace("buka","tutup");d[e].className=d[e].className.replace("hidup","mati")}this.className="hidup";var c=this.getAttribute("title");document.getElementById(c).className="buka"}}}}};