var lilmenu = 
{
init : function() {
	var e, i = 0;
	while (e = document.getElementById('lilmenu').getElementsByTagName ('DIV') [i++]) {
		if (e.className == 'hidup' || e.className == 'mati') {
		e.onclick = function () {
			var getEls = document.getElementsByTagName('DIV');
				for (var z=0; z<getEls.length; z++) {
				getEls[z].className=getEls[z].className.replace('buka', 'tutup');
				getEls[z].className=getEls[z].className.replace('hidup', 'mati');
				}
			this.className = 'hidup';
			var max = this.getAttribute('title');
			document.getElementById(max).className = "buka";
			}	
			}
			}
	
}
}