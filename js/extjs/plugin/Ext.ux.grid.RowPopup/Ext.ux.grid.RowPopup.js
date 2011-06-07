/*global Ext */

Ext.ns('Ext.ux.grid');

/**
 * @class Ext.ux.grid.RowPopup
 * @extends Ext.util.Observable
 * @param {Object} config configuration object
 * @constructor
 */
Ext.ux.grid.RowPopup = function(config) {
	Ext.apply(this, config);
	Ext.ux.grid.RowPopup.superclass.constructor.call(this);
	
	if(this.tpl){
        if(typeof this.tpl == 'string'){
            this.tpl = new Ext.Template(this.tpl);
        }
        this.tpl.compile();
    }
	this.bodyContent = {};
	this.actualRecord = 0;
  this.enablePopup = true;
}; // eo constructor

Ext.extend(Ext.ux.grid.RowPopup, Ext.util.Observable, {

	/**
	 * @cfg {String} mode Use 'remote' for remote stores or 'local' for local stores. If mode is local
	 * no data requests are sent to server the grid's store is filtered instead (defaults to 'locale')
	 */
	mode:'locale'
	
	/**
	 * @cfg {String} tpl mode esetén a megjelenítendõ szöveg template-je
	 */

	/**
	 * @cfg {Number} width Width of popupwindow in pixels (defaults to 400)
	 */
	,width:400
	
		/**
	 * @cfg {Number} height Height of popupwindow in pixels (defaults to 500)
	 */
	,height:500

	
	/**
	 * @cfg {string} colId Column id where the windows is shown
	 */
	,colId:false
	
	/**
	 * @cfg {Numeric} timeOut Time before the window is shown
	 */
	,timeOut:500
	
	/**
	 * @cfg {String} remoteUrl Te url where we can download the remote html
	 */
	,remoteUrl:false
	
	/**
	 * @cfg {String} enableCaching Enable caching
	 */
	,enableCaching:true

	/**
	 * @cfg {String} enableButton Enable bbar button which can disable the popup
	 */
	,enableButton:true
	
	// {{{
	/**
	 * private
	 */
	,init:function(grid) {
		this.grid = grid;
		
		if(this.mode=="locale" && !this.tpl)
			return;
		if(this.mode=="remote" && !this.remoteUrl)
			return;
		// do our processing after grid render and reconfigure
		this.grid.onRender = grid.onRender.createSequence(this.onRender, this);
		this.grid.on('mouseover',this.onMouseOver.createDelegate(this));

	} // eo function init
	// }}}
	,onRender:function() {
		this.win = new Ext.Window({
                    layout      : 'fit',
                    width		: this.width,
	                height		: this.height,
					autoScroll	: true,
                    closeAction :'close',
                    plain       : false,
	                closable 	: false,
	                constrain	: true
	            });
    if(this.enableButton && this.grid.getBottomToolbar()) {
      var bbar = this.grid.getBottomToolbar();
      bbar.add("-");
      bbar.addButton({id: 'diableRowPopup',
						text: this.i18n.rowPopupButtonText,
						tooltip: this.i18n.rowPopupButtonTooltipText,
            pressed: false,
            enableToggle:true,
            scope: this,
						iconCls: 'diableRowPopup',
						toggleHandler: function(btn,state){
              this.enablePopup = !state;
            }
          });
    }
		this.grid.getEl().on('mousemove',this.onMouseMove.createDelegate(this));
	} // eo function onRender
	// }}}
	,getRecord: function(t){
            var index = this.grid.getView().findRowIndex(t);
            this.record = this.grid.store.getAt(index);
            return this.record;
	}
	,getBodyContent : function(record){
        if(!this.enableCaching){
            return this.tpl.apply(record.data);
        }
        var content = this.bodyContent[record.id];
        if(!content){
            content = this.tpl.apply(record.data);
            this.bodyContent[record.id] = content;
        }
        return content;
    }
	// {{{
	/**
	 * private onMouseOver
	 */
	,onMouseOver:function(e,t){
    if(!this.enablePopup) {
      return;
    }
      //'e' is the event object
      //'t' is undocumented, but looks like it is just a shortcut to e.target
      //'this' of course is the GridPanel
    var row = e.getTarget('.x-grid3-row');
		if(this.colId) {
			var Col = e.getTarget('.x-grid3-td-'+this.colId); // this is a custom div
    } else {
        var Col = true
    }
    //if you want this to show on another column, must create another custom div
    // both must be non null, or it will try to execute on header
    if(Col != null && row != null) {
      var rec = this.getRecord(t);
			
			if(this.actualRecord == rec.id) {
				if(this.win.isVisible())
					this.win.doLayout();
				return;
			}
			this.hideWin();
			this.actualRecord = rec.id;
			
            //now create the window to show the detail info
            //used window because of constrain - not on tooltips
            if(rec){
				var win = this.win;
				var self = this;
				var x = e.getXY()[0]+5;
				var y = e.getXY()[1];
				var clientH = Ext.lib.Dom.getViewHeight();
				var clientW = Ext.lib.Dom.getViewWidth();
				if(e.getXY()[0]+5+this.width>clientW)
					x = e.getXY()[0]-this.width-5;
				win.doLayout();
				if(this.mode == 'locale') {
					if(!win.rendered)
						win.html = this.getBodyContent(rec);
					else {
						win.body.update(this.getBodyContent(rec));
					}
					
					this.ctime = setTimeout(function(){self.showWin(x,y)}, this.timeOut);
				} else if(this.mode == 'remote') {
					if(!win.rendered) {
						win.show();
						win.hide();
					}
					//console.log(this.bodyContent);
					if(this.enableCaching && this.bodyContent[this.actualRecord]) {
						win.body.update(this.bodyContent[this.actualRecord]);
						this.ctime = setTimeout(function(){self.showWin(x,y)}, this.timeOut);
						return;
					}
					this.updater = win.getUpdater();
					this.updater.showLoading();
					this.updater.update({url:this.remoteUrl,
												params:{"id":rec.id},
												callback: function(e,s,r){
													if(this.enableCaching)
														this.bodyContent[this.actualRecord] = r.responseText;
												},
												scope:this
											});
					this.updater.on("update",function(){win.doLayout();});
					this.ctime = setTimeout(function(){self.showWin(x,y)}, this.timeOut);
				} else {
					return;
				}
            }            
        }
	}// eo function onMouseOver
	// }}}
	/**
	 * private onMouseOver
	 */
	,onMouseMove:function(e,t){
    if(!this.enablePopup) {
      this.hideWin();
      return;
    }
		if(this.colId)
			var Col = e.getTarget('.x-grid3-td-'+this.colId); // this is a custom div
		else
			var Col = true;
        //if you want this to show on another column, must create another custom div
        // both must be non null, or it will try to execute on header
        if(Col)
        {  
			var rec = this.getRecord(t);
			if(!rec || this.actualRecord != rec.id) {
				this.hideWin();
			}
		} else {
			this.hideWin();
		}
    }// eo function onMouseOut
	// }}}
	
	/**
	 * private hideWin
	 */
	,hideWin:function(){
		this.actualRecord = 0;
		if(this.ctime)
		{
			clearTimeout(this.ctime);
		}
		if(this.win && this.win.isVisible()) {
			this.win.hide();
		} 
		if(this.updater && this.updater.isUpdating()) {
			this.updater.abort();
		}
    }// eo function hideWin
	// }}}
	
	/**
	 * private hideWin
	 */
	,showWin:function(x,y){
		this.win.setPosition(x,y);
		this.win.doLayout();
		this.win.show();
    }// eo function hideWin
	// }}}
	

}); // eo extend

Ext.ux.grid.RowPopup.prototype.i18n = {
  rowPopupButtonText: 'Hide popup',
  rowPopupButtonTooltipText: 'Do not show the popup'
};
// eof
