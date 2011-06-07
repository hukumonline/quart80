	/**
	 * simulate firebug console method for IE
	 */

	if(document.all){
		console={};
		console.log=console.time=console.timeEnd=console.trace=function(){};		
	}

	/**
	 * global name space for all JS functions
	 */
	
	BI={
		isSafari: navigator.userAgent.toLowerCase().indexOf("webkit") > -1,
		isIE: navigator.userAgent.toLowerCase().indexOf("msie") > -1,
		isIE7: navigator.userAgent.toLowerCase().indexOf("msie 7") > -1,
		isOpera: navigator.userAgent.toLowerCase().indexOf('opera') > -1
	};

	BI.infoView={
		animating:false,
		
		toggle: function(el){
			if(this.animating)
				return;
			var el=Ext.get(el);
			var title_el=el.child("div");
			var content_el=Ext.get(el.next());
			var toolbar_el=content_el.child("div.toolbar");
			this.animating=true;
			
			if(title_el.hasClass("x-layout-expand-north"))
				BI.util.collapseElement(content_el.dom, function(view){
					title_el.removeClass("x-layout-expand-north");
					title_el.addClass("x-layout-expand-west");
					view.animating=false;
		        }.createCallback(this));
			else BI.util.expandElement(content_el.dom, function(view){
				title_el.addClass("x-layout-expand-north");
				title_el.removeClass("x-layout-expand-west");
				view.animating=false;
			}.createCallback(this));		
			
		},
		
		expand: function(el, callback){		
			var el=Ext.get(el);
			var title_el=el.child("div");
			var content_el=Ext.get(el.next());
			
			if(title_el.hasClass("x-layout-expand-north"))
				return;
				
			this.animating=true;
			BI.util.expandElement(content_el.dom, function(view){
				title_el.addClass("x-layout-expand-north");
				title_el.removeClass("x-layout-expand-west");
				view.animating=false;
				if(callback)
					callback();
			}.createCallback(this));		
		}	
	}

	if(typeof(BI)=="undefined")
		BI={};
	
	BI.util={}

	BI.util.anim=new function(){
		this.animating={};
		
		this.slideIn=function(el, callback){
			var el=Ext.get(el);
			if(this.animating[el.dom])
				return;
			this.animating[el.dom]={cb:callback};
			el.slideIn("t", {
				scope: this,
				callback: function(){
					if(this.animating[el.dom].cb)
						this.animating[el.dom].cb();
					delete this.animating[el.dom];
				}
			})			
		};
			
		this.slideOut=function(el, callback){
			var el=Ext.get(el);
			if(this.animating[el.dom])
				return;
			this.animating[el.dom]={cb:callback};
			el.slideOut("t", {
				useDisplay: true,
				scope: this,
				callback: function(){
					if(this.animating[el.dom].cb)
						this.animating[el.dom].cb();
					delete this.animating[el.dom];
				}
			})			
		}
		
	}();
	
	BI.util.expandElement=function(el, callback){
		Ext.get(el).slideIn("t", {
			scope: el,
			callback: callback
		})
	}

	BI.util.collapseElement=function(el, callback){	
		Ext.get(el).slideOut("t", {
			scope: el,
			useDisplay: true,
			callback: callback
		})
	}	
