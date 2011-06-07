function printPdf(guid){
	sWidth = 640;
	sHeight = 480;
	sInfo = "http://hukumonline.pl/printpdf/"+guid
	win = window.open(sInfo,'win','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=' + sWidth +',height=' + sHeight+';');
}
function printEDoc(guid){
	sWidth = 700;
	sHeight = 500;
	sLeft = 300;
	sTop = 200;
	sInfo = "http://hukumonline.pl/printedoc/"+guid
	win = window.open(sInfo,'win','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=' + sWidth +',height=' + sHeight+',left=' + sLeft+',top=' + sTop+';');
}
function sendEMail(guid){
	sWidth = 700;
	sHeight = 500;
	sLeft = 300;
	sTop = 200;
	sInfo = "http://hukumonline.pl/sendmail/"+guid
	win = window.open(sInfo,'win','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=' + sWidth +',height=' + sHeight+',left=' + sLeft+',top=' + sTop+';');
}
function openPosting(pId) {
	eval("page" + pId + " = window.open('http://hukumonline.pl/kalendaracara/" + pId + "', 'mssgDisplay', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=420,height=400');");
}
