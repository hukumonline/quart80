// bbCode control by
// subBlue design
// www.subBlue.com


//********	HOW TO USE IT	****************************************//
//																	//
//	Colori		bbfontstyle(bbopen, bbclose, theForm, theField);	//
//	Font		bbfontstyle(bbopen, bbclose, theForm, theField);	//
//	Bottoni		bbstyle(numero,theForm, theField);					//
//	MouseOver	helpline(letter,theForm);							//
//																	//
//******************************************************************//

/*
	_library classes:
	$Revision: 1.30 $
*/
/**	-------------------------------	**/
/**	function to open tickBox style	**/
function tbOpen(page,caption,w,h,params)	{
	thisUrl	= page;
	addPrimalParams		= '';
	addParams			= '';
	addParams			= addParams + '?kdr=1';
	addParams			= addParams + '&width=' + w;
	addParams			= addParams + '&height=' + h;
	addParams			= addParams + '' + params;
	//addParams			= addParams + '&KeepThis=true';
	//addParams			= addParams + '&modal=true';
	//addParams			= addParams + '&TB_iframe=true';
	fullUrl				= thisUrl + addPrimalParams + addParams;

	//alert(thisUrl + '\n' + addPrimalParams + '\n' + addParams);

	tb_show(caption, fullUrl, '');
}
/**	-------------------------------	**/
/*	TRIM SPACES	*/
function bbCodeTrim(str)	{
	return str.replace(/^\s*|\s*$/g,"");
}
/***************************************
	Function Copy To ClipBoard
	http://www.krikkit.net/
***************************************/
function copy_clip(divID,text,theCounter)	{
	/*
	var clipboarddiv	= document.getElementById("clpswf" + ID);
	if(clipboarddiv==null)	{
		clipboarddiv	= document.createElement('div');
		clipboarddiv.setAttribute("name", "clpswf" + ID);
		clipboarddiv.setAttribute("id", "clpswf" + ID);
		document.body.appendChild(clipboarddiv);
	}
	*/
	var flashvars	= {
							txtToCopy	: text,
							js			: "copied",
							jsParams	: theCounter
						};
	var attributes	= {
							menu: "false",
							swliveconnect: "true",
							allowScriptAccess: "always",
							wmode: "transparent"
						}
	var params		= {
							menu: "false",
							swliveconnect: "true",
							allowScriptAccess: "always",
							wmode: "transparent"
						}
	loaderSWF("/img/bbCode/copyClip.swf",divID,15,13,flashvars,attributes,params);
}

function copied(divIDFromFlash,txtCopied)	{
	//	SET ID OF LAYERS
	divIDMain	= "copyClipforResp" + divIDFromFlash;			//	This is on page
	divID		= "respCopyClip";								//	This will be created and filled
	//	CHECK IF THE MAIN DIV ALREADY EXISTS
	var clipBRespMain	= document.getElementById(divIDMain);
	if(clipBRespMain == null)	{
		//	MAIN DIV (relative)
		clipBRespMain	= document.createElement('div');
		clipBRespMain.setAttribute("name", divIDMain);
		clipBRespMain.setAttribute("id", divIDMain);
		clipBRespMain.setAttribute("position", "relative");
		document.body.appendChild(clipBRespMain);
	}
	//	Display
	clipBRespMain.style.display	= 'block';
	//	CHECK IF THE MESSAGE DIV ALREADY EXISTS
	var clipBResp	= document.getElementById(divID);
	if(clipBResp == null)	{} else {
		//	REMOVE FIRST
		clipBResp.parentNode.removeChild(clipBResp);
	}
	//	MESSAGE DIV (absolute)
	clipBResp	= document.createElement('div');
	clipBResp.setAttribute("name", divID);
	clipBResp.setAttribute("id", divID);
	clipBRespMain.appendChild(clipBResp);

	if (txtCopied.length > 80)	{
		var putTxtCopiedDiv1	= txtCopied.substring(0,10);
		var putTxtCopiedDiv2	= txtCopied.substring(txtCopied.length-50);
		var putTxtCopiedDiv		= putTxtCopiedDiv1 + '...' + putTxtCopiedDiv2;
	} else {
		var putTxtCopiedDiv		= txtCopied;
	}
	clipBResp.innerHTML		= "Copiato negli appunti<br>" + putTxtCopiedDiv;

	var	newRand	= setTimeout("fadeOut('" + divID + "','" + clipBRespMain + "')",2000);
}
function fadeOut(divID,objMainDiv)	{
	/**	Add your own effect here	**/
	var el	= document.getElementById(divID);
	el.style.display = 'none';
}
/***************************************/


var DHTML = (document.getElementById || document.all || document.layers);

function bbCodeRollOver(imgName,imgRoll)	{
	document.images['' + imgName + ''].src	= imgRoll;
}
function bbCodeWriteDiv(divID,msg,color)	{
	var el	= document.getElementById(divID);
	el.innerHTML	= msg;
	el.style.color	= color;
}
function getObj(name)	{
	if (document.getElementById)	{
		this.obj		= document.getElementById(name);
		this.style		= document.getElementById(name).style;
	} else if (document.all)	{
		this.obj		= document.all[name];
		this.style		= document.all[name].style;
	} else if (document.layers)	{
		this.obj		= document.layers[name];
		this.style		= document.layers[name];
	}
}

function showHideBBCode(divID)	{
	var el	= document.getElementById(divID);
	if (el.style.display == 'none')	{
		el.style.display = 'block';
	} else {
		el.style.display = 'none';
	}
}

function bbCodePutSpecialChar(formName,theField,sChar)	{
	var MyForm 	= document.forms[formName];
	var txtarea	= MyForm[theField];
	if (txtarea == undefined)	{
		//alert("Seleziona un campo di form dove inserire il carattere speciale!");
		return;
	}
	insertAtCursor(txtarea, '', '', sChar);
}

// Startup variables
var imageTag 		= false;
var theSelection	= false;
var theField		= "";

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC	= navigator.userAgent.toLowerCase(); // Get client info
var clientVer	= parseInt(navigator.appVersion); // Get browser version

var is_ie	= ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav	= ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz	= 0;

var is_win	= ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac	= (clientPC.indexOf("mac")!=-1);


// Define the bbCode tags
bbcode	= new Array();
bbtags	= new Array(	'[b]','[/b]',					//	0
						'[i]','[/i]',					//	2
						'[u]','[/u]',					//	4
						'[strike]','[/strike]',			//	6
						'[quote]','[/quote]',			//	8
						'[list]\n\n','[/list]',			//	10
						'[*]','',						//	12
						'[hr]','',						//	14
						//'[img]','[/img]',				//
						//'[url]','[/url]',				//
						//'[email]','[/email]',			//
						'[back]','[/back]',				//	16
						'[t_left]','[/t_left]',			//	18
						'[t_center]','[/t_center]',		//	20
						'[t_right]','[/t_right]',		//	22
						'[t_justify]','[/t_justify]',	//	24
						'[gmaps]','[/gmaps]'
						);
imageTag	= false;


// Replacement for arrayname.length property
function getarraysize(thearray) {
	for (i = 0; i < thearray.length; i++) {
		if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
			return i;
		}
	return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
	thearray[ getarraysize(thearray) ] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
	thearraysize = getarraysize(thearray);
	retval = thearray[thearraysize - 1];
	delete thearray[thearraysize - 1];
	return retval;
}


function checkForm() {
	formErrors = false;
	if (document.forms[theForm].message.value.length < 2) {
		formErrors = "Devi scrivere un messaggio per inserirlo";
	}

	if (formErrors) {
		alert(formErrors);
		return false;
	} else {
		bbstyle(-1);
		//formObj.preview.disabled = true;
		//formObj.submit.disabled = true;
		return true;
	}
}

function emoticon(text) {
	var txtarea = document.forms[theForm].message;
	text = ' ' + text + ' ';
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		txtarea.focus();
	} else {
		txtarea.value  += text;
		txtarea.focus();
	}
}

function bbfontstyle(bbopen, bbclose,theForm,theField) {
	var MyForm 	= document.forms[theForm];
	var txtarea	= MyForm[theField];

	if (txtarea == undefined)	{
		//alert("Seleziona un campo di form dove inserire gli stili!");
		return;
	}
	insertAtCursor(txtarea, bbopen, bbclose, '');
	storeCaret(txtarea);
}

function putAllowedVars(newTXT,theForm,theField)	{
	var MyForm 	= document.forms[theForm];
	var txtarea	= MyForm[theField];
	if (txtarea == undefined)	{
		//alert("Seleziona un campo di form dove inserire gli stili!");
		return;
	}
	insertAtCursor(txtarea, '', '', newTXT);
	storeCaret(txtarea);
}

function putURLFromDropDown(formName,theField)	{
	var myForm 		= document.forms[formName];
	var selected	= myForm.ipage.options[myForm.ipage.selectedIndex].value;
	if (selected != '')	{
		var splitString	= selected.split("|&|");
		var nameT	= splitString[0];
		var linkT	= splitString[1];
		PromptUrl(formName,theField,nameT,linkT);
	}
}

function putBackButton(formName,theField)	{
	var MyForm 	= document.forms[formName];
	var txtarea	= MyForm[theField];
	if (txtarea == undefined)	{
		//alert("Seleziona un campo di form dove inserire gli stili!");
		return;
	}

	theSelection = (document.selection)	? document.selection.createRange().text : txtarea.value.substr(txtarea.selectionStart, txtarea.selectionEnd - txtarea.selectionStart); // Get text selection
	NameT		= (theSelection)	? theSelection : prompt("Enter for title URL", "");
	if (NameT == null || NameT == '')	{
		//	do nothing
	} else {
		insertAtCursor(txtarea, '[back]', '[/back]', NameT);
		storeCaret(txtarea);
	}
}

/*
	When click on BBCode button "URL", display prompt to insert a NAME and a URL
*/
function PromptUrl(theForm,theField,nameFix,linkFix)	{
	var MyForm 	= document.forms[theForm];
	var txtarea	= MyForm[theField];
	var blankOrNot;
	var target	= '';

	if (txtarea == undefined)	{
		//alert("Seleziona un campo di form dove inserire gli stili!");
		return;
	}

	//	prompt se: nameFix == '' || nameFix == 'undefined' && !theSelection
	//	NameT	= (nameFix == '' || nameFix != 'undefined' && !theSelection)	? prompt("Inserisci un testo da collegare", "") : "";
	//	NameT	= (nameFix != '' && nameFix != 'undefined')	? nameFix : NameT;
	//	NameT 	= (theSelection)	? theSelection : prompt("Inserisci un testo da collegare", "");

	theSelection = (document.selection)	? document.selection.createRange().text : txtarea.value.substr(txtarea.selectionStart, txtarea.selectionEnd - txtarea.selectionStart); // Get text selection
	if (nameFix != '' && nameFix != 'undefined')	{
		NameT	= nameFix;
	}
	if (theSelection)	{
		NameT	= theSelection
	}
	if ((nameFix == '' || nameFix == 'undefined') && !theSelection)	{
		NameT	= prompt("Enter title URL?", "");
	}

	if (NameT == null || NameT == '')	{
		//	do nothing
	} else {
		//	LinkT	= (linkFix != '' && linkFix != 'undefined')	? linkFix : "";
		//	LinkT	= (linkFix != '' && linkFix != 'undefined')	? prompt("Inserisci il collegamento", "") : LinkT;
		if (linkFix != '' && linkFix != 'undefined')	{
			LinkT	= linkFix;
		} else {
			LinkT	= prompt("Enter full URL", "");
		}

		if (LinkT == null || LinkT == '')	{
			NameT	= "";
			LinkT	= "";
		} else {
			//blankOrNot	= newConfirmBBCode('Target del collegamento','Aprire il link nella stessa finestra?',1,1,0);
			blankOrNot	= confirm('target Blank or Not?');

			if (!blankOrNot)	{
				target	= 't=_blank';
			} else {
				target	= 't=_parent';
			}
			insertAtCursor(txtarea, '[url=' + LinkT + ' ' + target + ']', '[/url]', NameT);
		}
	}
	storeCaret(txtarea);
}

/*
	When click on BBCode button "EMAIL", display prompt to insert a NAME and a MAIL ADDRESS
*/
function PromptMail(theForm,theField)	{
	var MyForm 	= document.forms[theForm];
	var txtarea	= MyForm[theField];

	if (txtarea == undefined)	{
		//alert("Seleziona un campo di form dove inserire gli stili!");
		return;
	}

	theSelection = (document.selection)	? document.selection.createRange().text : txtarea.value.substr(txtarea.selectionStart, txtarea.selectionEnd - txtarea.selectionStart); // Get text selection

	NameT = (theSelection)	? theSelection : prompt("Enter Email title?", "");
	if (NameT == null || NameT == '')	{
		//	do nothing
	} else {
		LinkT = prompt("Enter email address?", "");
		if (LinkT == null || LinkT == '')	{
			NameT	= "";
			LinkT	= "";
		} else {
			insertAtCursor(txtarea, '[email=' + LinkT + ']', '[/email]', NameT);
		}
	}
	storeCaret(txtarea);
}

function PromptQuote(theForm,theField)	{
	var MyForm 	= document.forms[theForm];
	var txtarea	= MyForm[theField];

	if (txtarea == undefined)	{
		//alert("Seleziona un campo di form dove inserire gli stili!");
		return;
	}

	theSelection = (document.selection)	? document.selection.createRange().text : txtarea.value.substr(txtarea.selectionStart, txtarea.selectionEnd - txtarea.selectionStart); // Get text selection

	txtQuoted	= (theSelection)	? theSelection : "Inserisci citazione"
	NameT = prompt("Inserisci l\'autore della citazione", "");

	if (NameT == null || NameT == '')	{
		//txtarea.value	+= '[quote]TESTO CITAZIONE[/quote]';

		fullBBCode	= ' [quote]' + txtQuoted + '[/quote] '
		insertAtCursor(txtarea, '[quote]', '[/quote]', txtQuoted);

	} else {

		fullBBCode	= ' [quote=' + NameT + ']' + txtQuoted + '[/quote] '
		insertAtCursor(txtarea, '[quote=' + NameT + ']', '[/quote]', txtQuoted);

	}
	storeCaret(txtarea);
}

function PromptGoogleMaps(theForm,theField)	{
	var MyForm 	= document.forms[theForm];
	var txtarea	= MyForm[theField];

	var FiltroNum	= /^([0-9])+$/;

	if (txtarea == undefined)	{
		//alert("Seleziona un campo di form dove inserire gli stili!");
		return;
	}

	var defMapWidth		= 425;
	var defMapHeight	= 350;
	mapLink 	= prompt("Incolla HTML da incorporare nel sito web", "");
	if (mapLink != '' && mapLink != 'undefined' && mapLink != null &&
		mapLink.indexOf("maps.google") 	!=-1	&&
		mapLink.indexOf("src=\"") 		!=-1	&&
		mapLink.indexOf("\">") 			!=-1
		)	{
		var firstPiece	= mapLink.split("src=\"");
		var secondPiece	= firstPiece[1].split("\">");
		var srcMap		= secondPiece[0];
		mapWidth		= prompt("Inserisci la larghezza", defMapWidth);
		mapHeight		= prompt("Inserisci l'altezza", defMapHeight);
		mapWidth		= (mapWidth == '' || mapWidth == null || FiltroNum.test(mapWidth) == false)		? defMapWidth : mapWidth;
		mapHeight		= (mapHeight == '' || mapHeight == null || FiltroNum.test(mapHeight) == false)	? defMapHeight : mapHeight;
		fullBBCode	= '[gmaps=' + mapWidth +'x'+ mapHeight +']'
		insertAtCursor(txtarea, fullBBCode, '[/gmaps]', srcMap);
	} else {
		//alert('Nessun inserimento');
		//	exit
	}

	storeCaret(txtarea);
}

function bbstyle(bbnumber,theForm,theField) {
	var MyForm 	= document.forms[theForm];
	var txtarea	= MyForm[theField];

	if (txtarea == undefined)	{
		//alert("Seleziona un campo di form dove inserire gli stili! (" + theField + ")");
		return;
	}

	donotinsert = false;
	theSelection = false;
	bblast = 0;

	if (bbnumber == -1) { // Close all open tags & default button names
		while (bbcode[0]) {
			butnumber = arraypop(bbcode) - 1;
			txtarea.value += bbtags[butnumber + 1];
			buttext = eval('document.forms[theForm].addbbcode' + butnumber + '.value');
			eval('document.forms[theForm].addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
		}
		imageTag = false; // All tags are closed including image tags :D
		txtarea.focus();
		return;
	}
	insertAtCursor(txtarea, bbtags[bbnumber], bbtags[bbnumber+1], '');
	storeCaret(txtarea);

}

function putSmile(tag,theForm,theField)	{
	var MyForm 	= document.forms[theForm];
	var txtarea	= MyForm[theField];
	if (txtarea == undefined)	{
		//alert("Seleziona un campo di form dove inserire gli stili!");
		return;
	}
	insertAtCursor(txtarea, tag + ' ', '', '');
	storeCaret(txtarea);
	return;
}

function checkToRemove(strSelect, open, close, newTXT)	{
//	set on new vars

	openRegExp	= bbCodeTrim(open);
	closeRegExp	= bbCodeTrim(close);
	openRegExp	= openRegExp.replace("\[","\\[").replace("\]","\\]");
	closeRegExp	= closeRegExp.replace("\[","\\[").replace("\]","\\]").replace("\/","\\/");
//	build regexp
	var regexS	= '^' + openRegExp + '(.*)' + closeRegExp + '$';
//	initialize regExp
	var myReg	= new RegExp(regexS,"gi");
//	check
	var thisCheck	= false;
	thisCheck	= myReg.test(strSelect);
//	apply
	if (thisCheck == true)	{
		//	Found a bbcode, try to remove it
		txtSel		= strSelect;
		var myReg	= new RegExp(regexS,"gi");
		txtSel		= txtSel.replace(myReg,"$1");
		//alert(regexS + ' ## ' + myReg);
	} else {
		txtSel		= (strSelect != '')	? open + strSelect + close : open + newTXT + close;
	}
	return txtSel;
}

function insertAtCursor(txtarea, open, close, newTXT)	{

	if (document.selection) {
		txtarea.focus();
		txtSel	= (document.selection.createRange().text)	? document.selection.createRange().text : newTXT;
		sel = document.selection.createRange();
		//sel.text = open + txtSel + close;
		sel.text = checkToRemove(txtSel, open, close, newTXT);

	} else if (txtarea.selectionStart || txtarea.selectionStart == '0') {

		var selLength	= txtarea.textLength;
		var selStart	= txtarea.selectionStart;
		var selEnd		= txtarea.selectionEnd;
		//alert(selLength + ' = ' + selStart + ' => ' + selEnd);

		strSelect	= bbCodeTrim(txtarea.value.substr(selStart, selEnd - selStart));

		txtSel		= checkToRemove(strSelect, open, close, newTXT);

		newTXT			= txtSel;
		var startPos	= txtarea.selectionStart;
		var endPos		= txtarea.selectionEnd;
		txtarea.value	= txtarea.value.substring(0, startPos) + newTXT + txtarea.value.substring(endPos, txtarea.value.length);

		//	Riposiziona il cursore
		txtarea.focus();
		newCursorsPosition	= selStart + newTXT.length;
		txtarea.setSelectionRange(newCursorsPosition,newCursorsPosition);

	} else {
		txtarea.value += newTXT;
	}
}

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	if (selEnd == 1 || selEnd == 2)
		selEnd = selLength;

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);
	txtarea.value = s1 + open + s2 + close + s3;
	return;
}

function macWrap(txtarea,open,close)
{
	theSelection = document.getSelection.createRange().text;
	if (!theSelection) {
		txtarea.value += bbopen + bbclose;
		txtarea.focus();
		return;
	}
	document.selection.createRange().text = bbopen + theSelection + bbclose;
	txtarea.focus();
	return;
}


// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}