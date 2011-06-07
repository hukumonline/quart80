/**
 * madani
 * (c) 2009-2010 madaniyah.com
 *
 * $Author: Nihki Prihadi $
 * $Id: ext-admin-function.js
 */

function openActionDialog (caller, item)
{
	var selectedNode = tree.getSelectionModel().getSelectedNode();
	switch(caller)
	{
		case 'addfolder' :
			ext_get("./admin/dms_foldermanager/add/parentGuid/" + selectedNode.id,500,190);
		break;
		case 'renfolder' :
			ext_get("./admin/dms_foldermanager/edit/folderGuid/" + selectedNode.id,500,190);
		break;
		case 'delfolder' :
			deleteFile(selectedNode);
		break;
		case 'sharefolder' :
			ext_get("./admin/dms_sharing/view/guid/" + selectedNode.id,480,470,'sharefolder');
		break;
	}
}
function ext_get(url,width,height,id)
{
	dialog = new Ext.Window({ 
//    	manager: this.windows,
//    	id: Ext.id(),
    	id: id,
        title: 'Website - Dialog',
        iconCls: 'dialog',
        layout: 'fit',
		autoLoad: {
			url: url, 
			scripts: true
		},
        modal:true,
        width:width,
        height:height,
        maximizable: true,
        autoScroll:true
  	});

	dialog.show();		
}

/**
 * @uses	MessageWindow 
 * @author	Michael LeComte (mjlecomte), inspired by Ext.ux.Notification\ToastWindow (Edouard Fattal)
 */
function info(title,msg)
{
	new Ext.ux.window.MessageWindow({
		title:title
		,html:msg || 'No information available'
		,origin:{offY:-5,offX:-5}
		,autoHeight:true
		,iconCls:'icon-info'
		,help:false
		,hideFx:{delay:1000,mode:'standard'}
		,listeners:{
			render:function(){
				
			}
		}
	}).show(Ext.getDoc());
}

/**
 * @uses	Listeners
 * @todo  	For a more general purpose query to see what events are firing and when on a particular observable object you might
 */
function captureEvents(observable) {
    Ext.util.Observable.capture(
        observable,
        function(eventName) {
            console.info(eventName);
        },
        this
    );		
}
function ext_get_iframe(url,width,height,id,title)
{
       var comeHome = function(){ Ext.getCmp('sites').activeTab.setSrc();},
           printPanel = function(){
               try{
                  Ext.getCmp('sites').activeTab.iframe.print();
              }catch(ex){Ext.Msg.alert('Sorry','Print Failure!<br />'+ex);}
           },
           _urlDelim = '\/',
           getLocationAbsolute = function(){
               var d= _urlDelim = location.href.indexOf('\/') != -1 ? '\/':'\\';
               var u=location.href.split(d);
               u.pop(); //this page
               return u.join(d);
           },
           getSiteRoot = function(){
            var url = getLocationAbsolute().split(_urlDelim );
            url.pop();
                    return url.join(_urlDelim);
           };
       var buildTBar = function(){
           return [{ text:'Return',handler:function(){ /*comeHome*/ Ext.Msg.alert('Sorry','Under construction'); }},
                    '-',
                    {text: 'Print',handler:function(){ /*printPanel*/ Ext.Msg.alert('Sorry','Under construction'); }}
//                    ,'-',
//                    {
//                    split:true,
//                    text:'Drop Down Menu',
//                    iconCls: 'preview-bottom',
//                    handler: null,
//                    menu:{
//                        //id:'reading-menu',
//                        cls:'reading-menu',
//                        width:200,
//                        listeners:{        //mask all frames while menu is visible.
//                            beforeshow : Ext.ux.ManagedIFrame.Manager.showShims,
//                            hide       : Ext.ux.ManagedIFrame.Manager.hideShims,
//                            scope      : Ext.ux.ManagedIFrame.Manager
//
//                        },
//                        items: [{
//                            text:'Bottom',
//                            checked:true,
//                            group:'rpgroup',
//                            scope:this,
//                            iconCls:'preview-bottom'
//                        },{
//                            text:'Right',
//                            checked:false,
//                            group:'rpgroup',
//                            scope:this,
//                            iconCls:'preview-right'
//                        },{
//                            text:'Hidden',
//                            checked:false,
//                            group:'rpgroup',
//                            scope:this,
//                            iconCls:'preview-hide'
//                        }]
//                    }
//                }
              ];
       };

       win = new Ext.Window({
            title: 'Panel - Dialog',
            iconCls:'dialog',
            id:'paneldialog',
            layout:'fit',
            minimizable: false,
            maximizable: true,
            width:width,
            height: height,
            constrainHeader:true,
            collapsible : true,
            animCollapse  :Ext.isIE,
            //plain: false,
            items:{
              xtype:'panel',
              //id:'sites',
              layout:'fit',
              defaultType: 'iframepanel',
              defaults:{
                closable:true,
                loadMask:{msg:'Loading Site...'},
                autoScroll : true,
                autoShow:true
              },
              items: [{
                     id: id
                    ,title:title
                    ,frameConfig : {id:id}
                    ,defaultSrc : url
                    ,tbar : buildTBar ()
                       }
              ]
            }
         });
         win.show();

         Ext.EventManager.on(window, "beforeunload", function(){

           Ext.destroy(viewport, win);

         },window,{single:true});

}
function deleteFile(node)
{
	var delNode = tree.selModel.selNode;
	
	if (node.hasChildNodes())
	{		
		Ext.MessageBox.show({
			title: 'Confirm',
			msg: "Attention!! Are you sure you want to remove folder '"+node.text+"' and remove all its contents ?",
			buttons: Ext.MessageBox.YESNO,
			icon: Ext.MessageBox.WARNING,
			fn: function(btn) {
				if (btn=="yes") {
		    		var conn = new Ext.data.Connection();
					conn.on('beforerequest', function() {
						Ext.MessageBox.wait('Deleted '+node.text); 
					});						
		    		conn.request({
		    			url: "./admin/dms_foldermanager/forcedelete",
		    			params:{
		    				node: node.id
		    			},
		    			callback:function(options, success, response){
		    				if (success) {
		    					json = Ext.decode( response.responseText );
		    					if ( json.success ) {
		    						tree.selModel.selNode.parentNode.removeChild(delNode);
		    						info( 'Success', json.message );
		    					} else {
		    						info( 'Failure', json.error );
		    					}
		    				} 
		    				else {
		    					Ext.MessageBox.alert('Oops..',response.responseText);
		    				}
		    			}
		    		});
					conn.on('requestcomplete', function() {
						Ext.MessageBox.hide();
					});					
				} else {
		   	 		info('Notification', 'Deleted cancelled.');				
				}
			}
		});		
		
	} else {
		
		Ext.MessageBox.show({
			title: 'Confirm',
			msg: "Are you sure you want to remove '"+node.text+"' ?",
			buttons: Ext.MessageBox.YESNO,
			icon: Ext.MessageBox.WARNING,
			fn: function(btn) {
				if (btn=="yes") {
		    		var conn = new Ext.data.Connection();
					conn.on('beforerequest', function() {
						Ext.MessageBox.wait('Deleted '+node.text); 
					});						
		    		conn.request({
		    			url: "./admin/dms_foldermanager/delete",
		    			params:{
		    				node: node.id
		    			},
		    			callback:function(options, success, response){
		    				if (success) {
		    					json = Ext.decode( response.responseText );
		    					if ( json.success ) {
		    						tree.selModel.selNode.parentNode.removeChild(delNode);
		    						info( 'Success', json.message );
		    					} else {
		    						info( 'Failure', json.error );
		    					}
		    				} else {
		    					info( 'Error', 'Failed to connect to the server.');
		    				}
		    			}
		    		});
					conn.on('requestcomplete', function() {
						Ext.MessageBox.hide();
					});						
				} else {
		   	 		info('Notification', 'Deleted cancelled.');				
				}
			}
		});		
		
	}
}
/*
function deleteFile(node)
{
	var delNode = tree.selModel.selNode;
	
	Ext.MessageBox.show({
		title: 'Confirm',
		msg: "Are you sure you want to remove '"+node.text+"' ?",
		buttons: Ext.MessageBox.YESNO,
		icon: Ext.MessageBox.WARNING,
		fn: function(btn) {
			if (btn=="yes") {
	    		var conn = new Ext.data.Connection();
				conn.on('beforerequest', function() {
					Ext.MessageBox.wait('Deleted '+node.text); 
				});						
	    		conn.request({
	    			url: "./admin/dms_foldermanager/delete",
	    			params:{
	    				node: node.id
	    			},
	    			callback:function(options, success, response){
	    				if (success) {
	    					json = Ext.decode( response.responseText );
	    					if ( json.success ) {
	    						tree.selModel.selNode.parentNode.removeChild(delNode);
	    						Ext.Msg.alert( 'Success', json.message );
	    					} else {
	    						Ext.Msg.alert( 'Failure', json.error );
	    					}
	    				} else {
	    					Ext.Msg.alert( 'Error', 'Failed to connect to the server.');
	    				}
	    			}
	    		});
				conn.on('requestcomplete', function() {
					Ext.MessageBox.hide();
				});						
			} else {
	   	 		Ext.MessageBox.alert('Status', 'Deleted cancelled.');				
			}
		}
	});		
}
*/
function enableDnD()
{
	Ext.grid.RowSelectionModel.prototype.rsmHandleMouseDown = Ext.grid.RowSelectionModel.prototype.handleMouseDown;
	Ext.grid.CheckboxSelectionModel.override({
	    handleMouseDown : function(g, rowIndex, e){}   
	});
	Ext.grid.RowSelectionModel.override({
	    initEvents : function() {
	        if(!this.grid.enableDragDrop && !this.grid.enableDrag){
	            this.grid.on("rowmousedown", this.rsmHandleMouseDown, this);
	        }else{ // allow click to work like normal
	            this.grid.on("rowclick", function(grid, rowIndex, e) {
	                var target = e.getTarget();             
	                if(target.className !== 'x-grid3-row-checker') {
	                    this.rsmHandleMouseDown(grid,rowIndex,e);
	                    grid.view.focusRow(rowIndex);
	                }
	            }, this);
	        }
	
	        this.rowNav = new Ext.KeyNav(this.grid.getGridEl(), {
	            "up" : function(e){
	                if(!e.shiftKey){
	                    this.selectPrevious(e.shiftKey);
	                }else if(this.last !== false && this.lastActive !== false){
	                    var last = this.last;
	                    this.selectRange(this.last,  this.lastActive-1);
	                    this.grid.getView().focusRow(this.lastActive);
	                    if(last !== false){
	                        this.last = last;
	                    }
	                }else{
	                    this.selectFirstRow();
	                }
	            },
	            "down" : function(e){
	                if(!e.shiftKey){
	                    this.selectNext(e.shiftKey);
	                }else if(this.last !== false && this.lastActive !== false){
	                    var last = this.last;
	                    this.selectRange(this.last,  this.lastActive+1);
	                    this.grid.getView().focusRow(this.lastActive);
	                    if(last !== false){
	                        this.last = last;
	                    }
	                }else{
	                    this.selectFirstRow();
	                }
	            },
	            scope: this
	        });
	
	        var view = this.grid.view;
	        view.on("refresh", this.onRefresh, this);
	        view.on("rowupdated", this.onRowUpdated, this);
	        view.on("rowremoved", this.onRemove, this);     
	    }
	});
	Ext.dd.DragSource.override({
	    onBeforeDrag : function(data, e){
			return (data.selections)? (data.selections.length > 0) : '';
	    }
	});
}
