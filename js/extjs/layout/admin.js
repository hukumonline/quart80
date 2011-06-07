/**
 * madani
 * (c) 2009-2010 madaniyah.com
 *
 * $Author: Nihki Prihadi $
 * $Id: ext-admin.js 322 2009-12-08 10:00:57Z Nihki Prihadi $
 * $Date: 2009-12-08 11:00:57 +0100 (Mo, 08 Dez 2009) $
 * $Revision: 322 $
 * $LastChangedDate: 2009-12-08 11:00:57 +0100 (Mo, 08 Dez 2009) $
 * $LastChangedBy: Nihki Prihadi $
 */

function ext_init() {
	Ext.Ajax.timeout = 60000;
	Ext.Ajax.on('requestexception', function(conn, response, options){
		switch(response.status){
			case 401:
				delete options.failure;
				Ext.Msg.alert('Error','Your Session has expired please login again', function(){
					window.location.reload();
				}, this);
				break;
			case 400:
				delete options.failure;
				Ext.Msg.alert('Error','Invalid Server function call please login and try again', function(){
					window.location.reload();
				}, this);
				break;
			case 503:
				delete options.failure;
				Ext.Msg.alert('Error','Unable to connect to Mail Server please login and try again', function(){
					window.location.reload();
				}, this);
				break;
		} 	
	},this);
	
	/**
     * @param {Object}
     */
	
    // var _pingTask = null;

	Ext.state.Manager.setProvider(new Ext.state.CookieProvider);
	Ext.BLANK_IMAGE_URL = './js/extjs/resources/images/default/s.gif';	
	
	var loading=Ext.get('loading');
	var mask=Ext.get('loading-mask');
	mask.setOpacity(0.8);
	mask.shift({
		xy:loading.getXY(),
		width:loading.getWidth(),
		height:loading.getHeight(),
		remove:true,
		duration:2,
		opacity:0.3,
		easing:'bounceOut',
		callback:function(){
			loading.fadeOut({duration:0.2,remove:true});
		}
	});
	
	profile = new Ext.data.GroupingStore({
		proxy: new Ext.data.HttpProxy({
			url: './services/catalog/fetch-profile-in-folder'
		}),	
		reader: new Ext.data.JsonReader({
			root: 'profile',
			totalProperty: 'totalCount',
			id: 'guid'
		}, [
				{name:'guid'},
				{name:'title'}
		]),
		remoteSort: true
	});
	
	folder = new Ext.data.GroupingStore({
		proxy: new Ext.data.HttpProxy({
			url: './services/folder/fetch-folder'
		}),	
		reader: new Ext.data.JsonReader({
			root: 'folder',
			totalProperty: 'totalCount',
			id: 'guid'
		}, [
			{name:'guid'},
			{name:'title'}
		]),
		remoteSort: true
	});
	
	store = new Ext.data.Store({ 
		proxy: new Ext.data.HttpProxy({
			url: './services/catalog/fetch-catalogs-in-folder'
		}),
		reader: new Ext.data.JsonReader({
			root: 'catalogs',
			totalProperty: 'totalCount',
			id: 'guid'
		}, [
				'title', 'createdby', 'modifiedby',
				{name:'createdDate', mapping:'createdDate'},
				{name:'guid',mapping:'guid'},
				//'headline',
				'subtitle','profile_column','status'
		]),
		remoteSort: true
	});
	
    function renderTopic(value, p, record){ 
        return String.format(
                '<div class="topic"><b>{0}</b><br /><span class="author">by {1}</span></div>',
                value, record.data.createdby, record.id, record.data.guid);
    }
    function renderTitleSearch(value, p, record){ 
    	if (value.indexOf('File') > -1) {
        return String.format(
                '<div class="topic"><span style="color:green;"><b>{0}</b></span></div>',
                value, record.id, record.data.guid);
    	} else {
        return String.format(
                '<div class="topic"><b>{0}</b></div>',
                value, record.id, record.data.guid);
    	}
    }
    function renderStatus(val)
    {
    	if (val == 'publish_y') {
    		return '<img src="./js/extjs/resources/images/default/silk/icons/publish_y.png">';
    	} else if (val == 'publish_g') {
    		return '<img src="./js/extjs/resources/images/default/silk/icons/publish_g.png">';
    	} else if (val == 'publish_r') {
    		return '<img src="./js/extjs/resources/images/default/silk/icons/publish_r.png">';
    	} else if (val == 'publish_x') {
    		return '<img src="./js/extjs/resources/images/default/silk/icons/publish_x.png">';
    	} else if (val == 'disabled') {
    		return '<img src="./js/extjs/resources/images/default/silk/icons/disabled.png">';
    	}
    }
    /*
    function renderHeadline(val)
    {
    	if (val == 1)
    	{
    		return '<img src="./js/extjs/resources/images/default/silk/icons/accept.png">';
    	}
    	else
    	{
    		return '';
    	}
    }
    */
    function renderCreatedDate(value, p, r){
        return String.format('<span class="post-date">{0}</span><br/>modifiedBy {1}', value, r.data['modifiedby']);
    }

	Ext.ux.grid.PagingRowNumberer = Ext.extend(Ext.grid.RowNumberer, {
	  renderer : function(v, p, record, rowIndex, colIndex, store){
	    if (this.rowspan) {
	    	p.cellAttr = 'rowspan="' + this.rowspan + '"';
	    }
	
	    var so = store.lastOptions;
	    var sop = so? so.params : null;
	    return ((sop && sop.start)? sop.start : 0) + rowIndex + 1;
	  }
	});

    var xg = Ext.grid;
    
    var sm = new xg.CheckboxSelectionModel();
    var pg = new Ext.ux.grid.PagingRowNumberer({header:'No.',width:30});
    var cm = new xg.ColumnModel
   	([
   		sm,
   		pg,
		{id: 'catalog', header: "Title", dataIndex: 'title', width: 370, sortable: true, renderer:renderTopic},
	    {header: "Section", dataIndex: 'profile_column', width: 50, sortable: true},
	    //{header: "Headline", dataIndex: 'headline', align:'center', width: 30, sortable: true, renderer:renderHeadline},
	    {header: "Published", dataIndex: 'status', align:'center', width: 30, sortable: true, renderer:renderStatus},
	    {header: "Created on", dataIndex: 'createdDate', width: 130, sortable: true, renderer:renderCreatedDate}
   	]);
	var paging = new Ext.ux.PagingToolbar({
		pageSize: 25,
		store: store,
		displayInfo: true,
		displayMsg: 'Displaying topics {0} - {1} of {2}',
		emptyMsg: "No data to display"
	});

    comboProfile = new Ext.form.ComboBox({
    	id:'comboprofile',
		typeAhead: false,
		triggerAction: 'all',
		emptyText:'Select a profile...',
		lazyRender:true,
		store: profile,
		displayField:'title',
		valueField:'guid'	    	
    });
    
    var ctf = new Ext.ux.form.LovCombo({
    	id:'comboctf',
    	hideOnSelect:false,
    	store:folder,
    	triggerAction:'all',
    	emptyText:'Copy to Folder...',
    	disabled:true,
    	displayField:'title',
    	valueField:'guid'
    });
    
    mtf = new Ext.ux.form.LovCombo({
    	id:'combomtf',
    	hideOnSelect:false,
    	store:folder,
    	triggerAction:'all',
    	emptyText:'Move to Folder...',
    	disabled:true,
    	displayField:'title',
    	valueField:'guid'
    });
    
    /*
    var master_menu = new Ext.menu.Menu({
    	id: 'mainMenu',
    	items:[
	    {
	    	text: 'Delete catalog',
	    	icon: './js/extjs/resources/images/default/silk/icons/bin_empty.png',
	    	cls: 'x-btn-text-icon',
	    	tooltip: 'Click to delete selected rows',
	    	handler: function(){
	    		var selectedKeys = grid.selModel.selections.keys;
	    		if (selectedKeys.length > 0)
	    		{
	    			Ext.MessageBox.confirm('Confirm','Do you really want to delete selection?',function(btn){
	    				if (btn=='yes')
	    				{
	    					var selections = grid.selModel.getSelections();
			    			for(j = 0; j < grid.selModel.getCount(); j++){
				    			var conn = new Ext.data.Connection();
								conn.on('beforerequest', function() {
									Ext.MessageBox.wait('delete catalog [' + selections[j].json.title + ']'); 
								});						
				    			conn.request({
				    				url: './admin/api_catalog/delete/format/json',
				    				params:{
				    					guid: selections[j].json.guid
				    				},
				    				callback:function(options, success, response){
				    					if (success) {
				    						json = Ext.decode( response.responseText );
				    						if ( json.success ) {
				    							store.reload(); 
				    							info( 'success', json.message );
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
			    			}
	    				}
	    			});
	    		}
	    		else
	    		{
	    			Ext.MessageBox.alert('Oops ...','Please select at least one item to delete');
	    		}
	    	}
	    },
	    {
	    	text: 'Refresh',
	    	icon: './js/extjs/resources/images/default/silk/icons/database_refresh.png',
	    	cls: 'x-btn-text-icon',
	    	tooltip: 'Click to Refresh',
	    	handler: function(){ store.reload(); }
	    },'-',{
	    	text: 'Catalog',
	    	iconCls: 'mfolder',
	    	menu: {
	    		items:[{
	    			text: 'Copy to Folder',
	    			icon: './js/extjs/resources/images/default/silk/icons/folder_add.png',
	    			cls: 'x-btn-text-icon',
	    			tooltip: 'Click to add to folder',
				    handler: function(){ 
					    var selectedKeys = grid.selModel.selections.keys;
					    if (selectedKeys.length > 0)
					    {
					    	ext_get("./admin/dms_catalogmanager/manage-folder",400,105);
					    }
					    else
					    {
					    	Ext.MessageBox.alert('Oops ...','Please select at least one item');
					    }
				    }						
	    		},{
	    			text: 'Move to Folder',
	    			icon: './js/extjs/resources/images/default/silk/icons/folder_go.png',
	    			cls: 'x-btn-text-icon',
	    			tooltip: 'Click to move catalog',
					handler: function(){
			    		var selectedNode = tree.getSelectionModel().getSelectedNode().id;
						var selectedKeys = grid.selModel.selections.keys;
						if (selectedKeys.length > 0)
						{
							ext_get("./admin/dms_catalogmanager/manage-folder-move",400,105);
						}
						else
						{
							Ext.MessageBox.alert('Oops ...','Please select at least one item');
						}
					}
	    		}
	    		,{
	    			 text:'Indexing'
	    			,tooltip:'Click to index catalog'
	    			,handler:function(){
	    				var selectedKeys = grid.selModel.selections.keys;
	    				if (selectedKeys.length > 0)
	    				{
			    			Ext.MessageBox.confirm('Confirm','Index catalog?',function(btn){
			    				if (btn=='yes')
			    				{
			    					var selections = grid.selModel.getSelections();
					    			for(j = 0; j < grid.selModel.getCount(); j++){
						    			var conn = new Ext.data.Connection();
										conn.on('beforerequest', function() {
											Ext.MessageBox.wait('index catalog [' + selections[j].json.title + ']'); 
										});						
						    			conn.request({
						    				url: './admin/widgets_indexing/indexing-catalog/format/json',
						    				params:{
						    					guid: selections[j].json.guid
						    					,profile:'peraturan'
						    				},
						    				callback:function(options, success, response){
						    					if (success) {
						    						json = Ext.decode( response.responseText );
						    						if ( json.success ) {
						    							store.reload(); 
						    						} else {
						    							try{
						    							Ext.Msg.alert( 'Failure', json.error.message );
						    							}
						    							catch (Exception)
						    							{
						    							Ext.Msg.alert( 'Failure', json.error );
						    							}
						    						}
						    					} else {
						    						Ext.Msg.alert( 'Error', 'Failed to connect to the server.');
						    					}
						    				}
						    			});
										conn.on('requestcomplete', function() {
											Ext.MessageBox.hide();
										});						
					    			}
			    				}
			    			});
	    				}
						else
						{
							Ext.MessageBox.alert('Oops ...','Please select at least one item');
						}
	    			}
	    		}
	    		]
	    	}
	    },'-',{
	    	text: 'Laporan',
	    	iconCls: 'report',
	    	menu:{
	    		items:[{
	    			 text:'Bahan masuk'
	    			,handler:function(){
	    				ext_get("./admin/dms_catalogmanager/report/wr/msk",420,145,'dialog_window1');
	    			}
	    		},{
	    			 text:'Bahan terbit'
	    			,handler:function(){
	    				ext_get("./admin/dms_catalogmanager/report/wr/tbt",420,145,'dialog_window1');
	    			}
	    		},{
	    			 text:'Kategori'
	    			,handler:function(){
	    				ext_get("./admin/dms_catalogmanager/report-kategori",420,175,'dialog_window1');
	    			}
	    		},{
	    			 text:'Author'
	    			,handler:function(){
	    				ext_get("./admin/dms_catalogmanager/report-author",420,145,'dialog_window1');	
	    			}
	    		}
	    		]
	    	}
	    }
    	]
    });
    */
    
	// create the editor grid
	grid = new xg.GridPanel({
		region: 'center',
		id:'catalog-grid',
	    store: store,
	    cm: cm,
	    sm: sm,
	    bbar: paging,
		viewConfig: {
			forceFit:true,
			enableRowBody:true,
			showPreview:true,
			getRowClass : function(record, rowIndex, p, ds)
			{
              	if (this.showPreview) { 
           			p.body = '<p> ' + record.data.subtitle +'</p>';
                	return 'x-grid3-row-expanded';
            	}
            	return 'x-grid3-row-collapsed';
			}
        },
	    tbar: [{
	    	text:'Open All',
	    	ToolTip: {title:'Open All',text:'Opens all item in tabs'},
	    	iconCls: 'tabs',
	    	handler: openAll
	    },'-',
	    comboProfile,'-',
	    /*
	    {
            text:'Master',
            iconCls: 'master',
            menu: master_menu 
	    }
	    */
	    {
	    	text: 'Delete catalog',
	    	icon: './js/extjs/resources/images/default/silk/icons/bin_empty.png',
	    	cls: 'x-btn-text-icon',
	    	tooltip: 'Click to delete selected rows',
	    	handler: function(){
	    		var selectedKeys = grid.selModel.selections.keys;
	    		if (selectedKeys.length > 0)
	    		{
	    			Ext.MessageBox.confirm('Confirm','Do you really want to delete selection?',function(btn){
	    				if (btn=='yes')
	    				{
	    					var selections = grid.selModel.getSelections();
			    			for(j = 0; j < grid.selModel.getCount(); j++){
				    			var conn = new Ext.data.Connection();
								conn.on('beforerequest', function() {
									Ext.MessageBox.wait('delete catalog [' + selections[j].json.title + ']'); 
								});						
				    			conn.request({
				    				url: './admin/api_catalog/delete/format/json',
				    				params:{
				    					guid: selections[j].json.guid
				    				},
				    				callback:function(options, success, response){
				    					if (success) {
				    						json = Ext.decode( response.responseText );
				    						if ( json.success ) {
				    							store.reload(); 
				    							info( 'success', json.message );
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
			    			}
	    				}
	    			});
	    		}
	    		else
	    		{
	    			Ext.MessageBox.alert('Oops ...','Please select at least one item to delete');
	    		}
	    	}
	    },
	    {
	    	text: 'Refresh',
	    	icon: './js/extjs/resources/images/default/silk/icons/database_refresh.png',
	    	cls: 'x-btn-text-icon',
	    	tooltip: 'Click to Refresh',
	    	handler: function(){ store.reload(); }
	    },'-',{
	    	text: 'Catalog',
	    	iconCls: 'mfolder',
	    	menu: {
	    		items:[{
	    			text: 'Copy to Folder',
	    			icon: './js/extjs/resources/images/default/silk/icons/folder_add.png',
	    			cls: 'x-btn-text-icon',
	    			tooltip: 'Click to add to folder',
				    handler: function(){ 
					    var selectedKeys = grid.selModel.selections.keys;
					    if (selectedKeys.length > 0)
					    {
					    	ext_get("./admin/dms_catalogmanager/manage-folder",400,105);
					    }
					    else
					    {
					    	Ext.MessageBox.alert('Oops ...','Please select at least one item');
					    }
				    }						
	    		},{
	    			text: 'Move to Folder',
	    			icon: './js/extjs/resources/images/default/silk/icons/folder_go.png',
	    			cls: 'x-btn-text-icon',
	    			tooltip: 'Click to move catalog',
					handler: function(){
			    		var selectedNode = tree.getSelectionModel().getSelectedNode().id;
						var selectedKeys = grid.selModel.selections.keys;
						if (selectedKeys.length > 0)
						{
							ext_get("./admin/dms_catalogmanager/manage-folder-move",400,105);
						}
						else
						{
							Ext.MessageBox.alert('Oops ...','Please select at least one item');
						}
					}
	    		}
	    		,{
	    			 text:'Indexing'
	    			,tooltip:'Click to index catalog'
	    			,handler:function(){
	    				var selectedKeys = grid.selModel.selections.keys;
	    				if (selectedKeys.length > 0)
	    				{
			    			Ext.MessageBox.confirm('Confirm','Index catalog?',function(btn){
			    				if (btn=='yes')
			    				{
			    					var selections = grid.selModel.getSelections();
					    			for(j = 0; j < grid.selModel.getCount(); j++){
						    			var conn = new Ext.data.Connection();
										conn.on('beforerequest', function() {
											Ext.MessageBox.wait('index catalog [' + selections[j].json.title + ']'); 
										});						
						    			conn.request({
						    				url: './admin/widgets_indexing/indexing-catalog/format/json',
						    				params:{
						    					guid: selections[j].json.guid
						    					,profile:'peraturan'
						    				},
						    				callback:function(options, success, response){
						    					if (success) {
						    						json = Ext.decode( response.responseText );
						    						if ( json.success ) {
						    							store.reload(); 
						    						} else {
						    							try{
						    							Ext.Msg.alert( 'Failure', json.error.message );
						    							}
						    							catch (Exception)
						    							{
						    							Ext.Msg.alert( 'Failure', json.error );
						    							}
						    						}
						    					} else {
						    						Ext.Msg.alert( 'Error', 'Failed to connect to the server.');
						    					}
						    				}
						    			});
										conn.on('requestcomplete', function() {
											Ext.MessageBox.hide();
										});						
					    			}
			    				}
			    			});
	    				}
						else
						{
							Ext.MessageBox.alert('Oops ...','Please select at least one item');
						}
	    			}
	    		}
	    		]
	    	}
	    },'-',{
	    	text: 'Laporan',
	    	iconCls: 'report',
	    	menu:{
	    		items:[{
	    			 text:'Bahan masuk'
	    			,handler:function(){
	    				ext_get("./admin/dms_catalogmanager/report/wr/msk",420,145,'dialog_window1');
	    			}
	    		},{
	    			 text:'Bahan terbit'
	    			,handler:function(){
	    				ext_get("./admin/dms_catalogmanager/report/wr/tbt",420,145,'dialog_window1');
	    			}
	    		},{
	    			 text:'Kategori'
	    			,handler:function(){
	    				ext_get("./admin/dms_catalogmanager/report-kategori",420,175,'dialog_window1');
	    			}
	    		},{
	    			 text:'Author'
	    			,handler:function(){
	    				ext_get("./admin/dms_catalogmanager/report-author",420,145,'dialog_window1');	
	    			}
	    		}
	    		]
	    	}
	    }
	    ,'-'
	    ,{
	    	iconCls:'exprt'
	    	,tooltip:'Export to excel'
	    	,handler:function()
	    	{
	    		var selectedKeys = grid.selModel.selections.keys;
	    		var encoded_keys = Ext.encode(selectedKeys);
	    		if (selectedKeys.length > 0)
	    		{
	    			Ext.MessageBox.confirm('Confirm','Export selection?',function(btn){
	    				if (btn=='yes')
	    				{
	    					window.location.href = './admin/api_export/report.by.selection/format/excel/guid/'+encoded_keys;
	    					/*
				    			var conn = new Ext.data.Connection();
								conn.on('beforerequest', function() {
									Ext.MessageBox.wait('Export catalog'); 
								});						
				    			conn.request({
				    				url: './admin/api_export/report.by.selection/format/excel',
				    				params:{
				    					guid: encoded_keys
				    				}
				    			});
								conn.on('requestcomplete', function() {
									Ext.MessageBox.hide();
								});
							*/						
	    				}
	    			});
	    		}
	    		else
	    		{
	    			Ext.MessageBox.alert('Oops ...','Please select at least one item to export');
	    		}
	    	}
	    }
	    ,'-',
	    {
	        	pressed: true,
	            enableToggle:true,
	            text:'&nbsp;Preview',
	            tooltip: 'View a short summary of each post in the list',
	            iconCls: 'summary',
	            toggleHandler: toggleDetails
		}
	    ],
	    selModel: new xg.RowSelectionModel({singleSelect:false}),
	    layout: 'fit',
	    forceFit:'fit',
	    ddGroup: 'TreeDD',
	    loadMask: true,
	    enableColLock:false,
	    enableDragDrop: true,
		frame:false,
		border: true
	});
	
	store.load();
	
	dssearch = new Ext.data.Store({ 
		proxy: new Ext.data.HttpProxy({
			url: './services/catalog/search'
		}),
		reader: new Ext.data.JsonReader({
			root: 'search',
			totalProperty: 'totalCount',
			id: 'guid'
		}, [
				'title', 
				{name:'guid',mapping:'guid'},
				'subtitle','folderGuid'
		])
	});
	
	sfind = new Ext.data.SimpleStore({
		id:'selectit',
		fields: ['pns','cval'],
		data:[['Artikel','1'],['Klinik','2'],['Peraturan','3']]
	});
	
	var scbox = new Ext.form.ComboBox({
				emptyText:'-- Please choose --',
				mode: 'local',
				triggerAction:"all",
				selectOnFocus: true,
				store:sfind,
				valueField: 'cval',
				displayField: 'pns'
			});

    var sm2 = new xg.CheckboxSelectionModel();
    var pg2 = new Ext.ux.grid.PagingRowNumberer({header:'No.',width:30});
    var cmsearch = new xg.ColumnModel
   	([
   		sm2,
   		pg2,
		{id: 'catalog', header: "Title", dataIndex: 'title', width: 370, renderer:renderTitleSearch}
   	]);
   	
	var pagingsearch = new Ext.ux.PagingToolbar({
		pageSize: 25,
		store: dssearch,
		displayInfo: true,
		displayMsg: 'Displaying topics {0} - {1} of {2}',
		emptyMsg: "No data to display"
	});

    var master_menu_search = new Ext.menu.Menu({
    	id: 'mainMenuSearch',
    	items:[
	    {
	    	text: 'Delete catalog',
	    	icon: './js/extjs/resources/images/default/silk/icons/bin_empty.png',
	    	cls: 'x-btn-text-icon',
	    	tooltip: 'Click to delete selected rows',
	    	handler: function(){
	    		var selectedKeys = gridsearch.selModel.selections.keys;
	    		if (selectedKeys.length > 0)
	    		{
	    			Ext.MessageBox.confirm('Confirm','Do you really want to delete selection?',function(btn){
	    				if (btn=='yes')
	    				{
	    					var selections = gridsearch.selModel.getSelections();
			    			for(j = 0; j < gridsearch.selModel.getCount(); j++){
				    			var conn = new Ext.data.Connection();
								conn.on('beforerequest', function() {
									Ext.MessageBox.wait('delete catalog [' + selections[j].json.title + ']'); 
								});						
				    			conn.request({
				    				url: './admin/api_catalog/delete/format/json',
				    				params:{
				    					guid: selections[j].json.guid
				    				},
				    				callback:function(options, success, response){
				    					if (success) {
				    						json = Ext.decode( response.responseText );
				    						if ( json.success ) {
				    							store.reload(); 
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
			    			}
	    				}
	    			});
	    		}
	    		else
	    		{
	    			Ext.MessageBox.alert('Oops ...','Please select at least one item to delete');
	    		}
	    	}
	    },
	    {
	    	text: 'Refresh',
	    	icon: './js/extjs/resources/images/default/silk/icons/database_refresh.png',
	    	cls: 'x-btn-text-icon',
	    	tooltip: 'Click to Refresh',
	    	handler: function(){ dssearch.reload(); }
	    },'-',{
	    	text: 'Catalog',
	    	iconCls: 'mfolder',
	    	menu: {
	    		items:[{
	    			text: 'Copy to Folder',
	    			icon: './js/extjs/resources/images/default/silk/icons/folder_add.png',
	    			cls: 'x-btn-text-icon',
	    			tooltip: 'Click to add to folder',
				    handler: function(){ 
					    var selectedKeys = gridsearch.selModel.selections.keys;
					    if (selectedKeys.length > 0)
					    {
					    	ext_get("./admin/dms_catalogmanager/manage-folder",400,105);
					    }
					    else
					    {
					    	Ext.MessageBox.alert('Oops ...','Please select at least one item');
					    }
				    }						
	    		},{
	    			text: 'Move to Folder',
	    			icon: './js/extjs/resources/images/default/silk/icons/folder_go.png',
	    			cls: 'x-btn-text-icon',
	    			tooltip: 'Click to move catalog',
					handler: function(){
			    		var selectedNode = tree.getSelectionModel().getSelectedNode().id;
						var selectedKeys = gridsearch.selModel.selections.keys;
						if (selectedKeys.length > 0)
						{
							ext_get("./admin/dms_catalogmanager/manage-folder-move",400,105);
						}
						else
						{
							Ext.MessageBox.alert('Oops ...','Please select at least one item');
						}
					}
	    		}
	    		]
	    	}
	    }
    	]
    });
    
	var filter = new Ext.form.TextField({ 
		name: 'query',
		emptyText:'Search here...',
		width:180,
		fireKey : function(e){
			if(e.getKey() == e.ENTER){
				var filterVal = this.getValue();
				if( filterVal.length > 1 ) {
					dssearch.baseParams = {query:filterVal};
					dssearch.load({
						 params:{start:0, limit:25, query:filterVal }
						,callback:function (rec, options, success){
							if (success)
							{
								Ext.Ajax.request({
									url:'./services/catalog/suggestioncollation',
									params:{collation:filterVal},
									callback: function(options, success, response ) {
										if( success ) {
											var result = Ext.decode( response.responseText );
											if (result.success == true) {
											Ext.MessageBox.show({
												title:'Suggestion',
												msg:"Did you mean: <b>'"+result.collation+"'</b> ?",
												buttons: Ext.MessageBox.YESNO,
												icon: Ext.MessageBox.QUESTION,
												fn: function(btn) {
													if (btn=="yes") {
														dssearch.baseParams = {query:result.collation};
														dssearch.load({params:{start:0, limit:25, query:result.collation }});
													} 
													else
													{
														dssearch.load();
													}
												}
											});
											}
										}
									}
								});
							}
							else
							{
								Ext.Msg.alert( 'Notification', 'Failed to connect to the server!\nTry to check Solr service.');
							}
						}
					});
				} else {
					dssearch.clearFilter();
				}
			}
		}
	});
	
	// create the editor grid
	gridsearch = new xg.GridPanel({
		region: 'center',
		id:'catalog-search-grid',
	    store: dssearch,
	    cm: cmsearch,
	    sm: sm2,
	    bbar: pagingsearch,
		viewConfig: {
			forceFit:true,
			enableRowBody:true,
			showPreview:true,
			getRowClass : function(record, rowIndex, p, ds)
			{
              	if (this.showPreview) { 
           			p.body = '<p> ' + record.data.subtitle + ' </p>';
                	return 'x-grid3-row-expanded';
            	}
            	return 'x-grid3-row-collapsed';
			}
        },
	    tbar: [{
	    	text:'Open All',
	    	ToolTip: {title:'Open All',text:'Opens all item in tabs'},
	    	iconCls: 'tabs',
	    	handler: openAllSearch
	    },'-',
	    {
            text:'Master',
            iconCls: 'master',
            menu: master_menu_search 
	    },'-',
	    {
	        	pressed: true,
	            enableToggle:true,
	            text:'&nbsp;Preview',
	            tooltip: 'View a short summary of each post in the list',
	            iconCls: 'summary',
	            toggleHandler: toggleSearchDetails
		},'-',
		filter,
		scbox,
		new Ext.Toolbar.Button({
			text:'Go',
			iconCls:'find',
			handler: function(btn,e) {
				var filterVal = filter.getValue();
				var boxVal = scbox.getValue();
				if( filterVal.length > 1 ) {
					dssearch.baseParams = {query:filterVal,qbox:boxVal};
					dssearch.load({
						 params:{start:0, limit:25, query:filterVal, qbox:boxVal }
						,callback:function (rec, options, success){
							if (success)
							{
								Ext.Ajax.request({
									url:'./services/catalog/suggestioncollation',
									params:{collation:filterVal},
									callback: function(options, success, response ) {
										if( success ) {
											var result = Ext.decode( response.responseText );
											if (result.success == true) {
											Ext.MessageBox.show({
												title:'Suggestion',
												msg:"Did you mean: <b>'"+result.collation+"'</b> ?",
												buttons: Ext.MessageBox.YESNO,
												icon: Ext.MessageBox.QUESTION,
												fn: function(btn) {
													if (btn=="yes") {
														dssearch.baseParams = {query:result.collation};
														dssearch.load({params:{start:0, limit:25, query:result.collation }});
													} 
													else
													{
														dssearch.load();
													}
												}
											});
											} 
										}
									}
								});
							}
							else
							{
								Ext.Msg.alert( 'Notification', 'Failed to connect to the server!\nTry to check Solr service.');
							}
						}
					});
				} else {
					dssearch.clearFilter();
				}
			}
		}
		)
	    ],
	    selModel: new xg.RowSelectionModel({singleSelect:false}),
	    layout: 'fit',
	    forceFit:'fit',
	    ddGroup: 'TreeDD',
	    loadMask: true,
	    enableColLock:false,
	    enableDragDrop: true,
		frame:false,
		border: true
	});
	
	dssearch.load();
	
	enableDnD();	
	
	Ext.getCmp('comboprofile').on('select',function(combo_profile){
		var selectedNode = tree.getSelectionModel().getSelectedNode();
		ext_get("./admin/dms_catalogmanager/add/profileGuid/"+combo_profile.getValue()+"/folderGuid/" + selectedNode.id,750,500);
	}.createDelegate(this));

    function toggleDetails(btn, pressed){
        var view = Ext.getCmp('catalog-grid').getView();
        view.showPreview = pressed;
        view.refresh();
    }
    
    function toggleSearchDetails(btn, pressed){
        var view = Ext.getCmp('catalog-search-grid').getView();
        view.showPreview = pressed;
        view.refresh();
    }
    
  	var preview = new Ext.Panel({
  		id:'preview',
  		region:'south',
  		autoScroll: true,
  		tbar:[{
  			id:'tab',
  			text:'View in New Tab',
  			iconCls:'new-tab',
  			disabled:true,
			handler:openTab			
  		},
  		'-',
  		{
  			id:'catalog_action',
  			text:'Catalog',
  			iconCls:'catalog',
  			disabled:true,
  			menu: new Ext.menu.Menu({
  				items:[{
  					text:'Edit',
  					handler: function(){ ext_get("./admin/dms_catalogmanager/edit/catalogGuid/"+grid.getSelectionModel().getSelected().data.guid,750,500); }
  				},{
  					text:'Upload File(s)',
			    	handler:function(){
			    		ext_get("./admin/dms_fileuploader/add/catalogGuid/"+grid.getSelectionModel().getSelected().data.guid,580,430);
			    	}
  				},{
  					text:'Add Relation',
  					iconCls:'relation',
			    	handler: function() { 
			    		var selectedKeys = grid.selModel.selections.keys;
			    		if (selectedKeys.length > 0)
			    		{
			    			ext_get("./admin/widgets_relation/viewsearch/guid/"+selectedKeys,830,500,'viewsearch');
			    		}
			    		else
			    		{
			    			Ext.MessageBox.alert('Oops ...','Please select at least one item');
			    		}
			    	}			
  				}
  				]
  			})
  		}
  		]
  	});
  	
  	var previewsearch = new Ext.Panel({
  		id:'previewsearch',
  		region:'south',
  		autoScroll: true,
  		tbar:[{
  			id:'tab_search',
  			text:'View in New Tab',
  			iconCls:'new-tab',
  			disabled:true,
			handler:openTabSearch			
  		},
  		'-',
  		{
  			id:'catalog_search_action',
  			text:'Catalog',
  			iconCls:'catalog',
  			disabled:true,
  			menu: new Ext.menu.Menu({
  				items:[{
  					text:'Edit',
  					handler: function(){ ext_get("./admin/dms_catalogmanager/edit/catalogGuid/"+gridsearch.getSelectionModel().getSelected().data.guid,750,500); }
  				},{
  					text:'Upload File(s)',
			    	handler:function(){
			    		ext_get("./admin/dms_fileuploader/add/catalogGuid/"+gridsearch.getSelectionModel().getSelected().data.guid,580,430);
			    	}
  				},{
  					text:'Add Relation',
  					iconCls:'relation',
			    	handler: function() { 
			    		var selectedKeys = gridsearch.selModel.selections.keys;
			    		if (selectedKeys.length > 0)
			    		{
			    			ext_get("./admin/widgets_relation/viewsearch/guid/"+selectedKeys,820,500,'viewsearch');
			    		}
			    		else
			    		{
			    			Ext.MessageBox.alert('Oops ...','Please select at least one item');
			    		}
			    	}			
  				}
  				]
  			})
  		}
  		]
  	});
  	
  	grid.on('rowcontextmenu',function(grid, rowIndex, e){
  		var coords = e.getXY();
  		rowRecord = store.getAt(rowIndex);
  		e.stopEvent();
  		menuG.showAt([coords[0],coords[1]]);
  	});
	
    grid.on('rowclick',function(grid, rowIndex, e){
    	var selectedId = store.data.items[rowIndex].id;
    	var selectedNode = tree.getSelectionModel().getSelectedNode();
    	var items = preview.topToolbar.items;
    	items.get('tab').enable();
    	items.get('catalog_action').enable();
    	mtf.enable();
		preview.load({
			url:"./admin/browser/view-in-new-tab",
			params:{ 
				catalogGuid: selectedId,
				folderGuid: selectedNode.id 
			},
			scripts:true,
			nocache: false
		});
    });
    
    gridsearch.on('rowclick',function(gridsearch, rowIndex, e){
    	var selectedId = dssearch.data.items[rowIndex].id;
    	var record = gridsearch.getStore().getAt(rowIndex);
    	var items = previewsearch.topToolbar.items;
    	items.get('tab_search').enable();
    	items.get('catalog_search_action').enable();
		previewsearch.load({
			url:"./admin/browser/view-in-new-tab",
			params:{ 
				catalogGuid: selectedId,
				folderGuid: record.get('folderGuid')
			},
			scripts:true,
			nocache: false
		});
    });
    
    grid.on('rowdblclick', openTab);
    gridsearch.on('rowdblclick', openTabSearch);
    
    var menuG = new Ext.menu.Menu('mainGridRowContext');
    menuG.add({
    	text:'Edit',
    	handler: function(){ ext_get("./admin/dms_catalogmanager/edit/catalogGuid/"+rowRecord['id'],750,500); }
    },
    {
    	text:'Upload File(s)',
    	handler:function(){
    		ext_get("./admin/dms_fileuploader/add/catalogGuid/"+rowRecord['id'],580,430);
    	}
    },{
  		text:'Add Relation',
  		iconCls:'relation',
	   	handler: function() { 
   			ext_get("./admin/widgets_relation/viewsearch/guid/"+rowRecord['id'],830,500,'viewsearch');
	   	}			
  	});

	var tb = new Ext.Toolbar("header-toolbar",{height: 24});
	tb.add({
    	text:'Reading pane',
    	id:'read_panel',
    	tooltip:'click to preview right',
    	iconCls: 'preview-right',
    	menu:{
    		id:'reading-menu',
    		width:100,
    		items:[{
    			text:'Bottom',
                checked:true,
                group:'rp-group',
                checkHandler:movePreview,
                iconCls:'preview-bottom'	    			
    		},{
       			text: 'Right',
       			checked:false,
       			group:'rp-group',
       			checkHandler:movePreview,
       			tooltip: 'Click to preview right',
       			iconCls:'preview-right'	    			
    		},{
       			text: 'Hide',
       			checked:false,
       			group:'rp-group',
       			checkHandler:movePreview,
       			tooltip: 'Click to preview hide',
       			iconCls:'preview-hide'                			
    		}
	   		]
    	}
	},
	'-',
	{
		  text:'Site'
		 ,menu:{
		 	items:[{
				 text:'Global configuration'
				,iconCls:'config'
				,handler:function(){ ext_get("./admin/setting_manager/setting",700,500); }
			},
			/*
			{
				text:'Banner Management'
				,iconCls:'banner'
				,handler:function(){
					ext_get_iframe("./pbanner/pbmadmin/admin.php",1060,550,'banner','Banner Management');
				}
			},
			*/
			{
				text: 'Cache'
				,menu:{
					items:[{
						text:'Clear cache'
						,handler:function(){
							Ext.Ajax.on('beforerequest', function() {
								Ext.MessageBox.wait('Clear cache...'); 
							});						
							Ext.Ajax.request({
								url: './admin/api_catalog/clearcache/format/json',
								failure:function(response,options){
									Ext.MessageBox.hide();
									Ext.MessageBox.alert('Warning','Oops...');
								},
								success: function(response,options)
								{
									var responseData = Ext.util.JSON.decode(response.responseText);
									Ext.MessageBox.hide();
									info( 'success', responseData.message );
								}
							});
							Ext.Ajax.on('requestcomplete', function() {
								Ext.MessageBox.hide();
							});						
						
						}
					},
					{
						text:'Clear all cache'
						,handler:function(){
							Ext.Ajax.on('beforerequest', function() {
								Ext.MessageBox.wait('Clear all cache...'); 
							});						
							Ext.Ajax.request({
								url: './admin/api_catalog/clearallcache/format/json',
								failure:function(response,options){
									Ext.MessageBox.hide();
									Ext.MessageBox.alert('Warning','Oops...');
								},
								success: function(response,options)
								{
									var responseData = Ext.util.JSON.decode(response.responseText);
									Ext.MessageBox.hide();
									info( 'success', responseData.message );
								}
							});
							Ext.Ajax.on('requestcomplete', function() {
								Ext.MessageBox.hide();
							});						
					
						}
					}
					]
				}
			},
			{
				 text:'Indexing'
				,menu:{
					items:[{
						 text:'Empty index'
						,disabled:true
						,handler:function(){
			    			Ext.MessageBox.confirm('Confirm','Do you really want to empty index?',function(btn){
			    				if (btn=='yes')
			    				{
						    		var conn = new Ext.data.Connection();
									conn.on('beforerequest', function() {
										Ext.MessageBox.wait('Empty index'); 
									});						
						    		conn.request({
						    			url: './admin/widgets_indexing/index/format/json',
					    				callback:function(options, success, response){
					    					if (success) {
					    						json = Ext.decode( response.responseText );
					    						if ( json.success ) {
					    							store.reload(); 
					    						}
					    					} else {
					    						Ext.Msg.alert( 'Error', 'Failed to connect to the server.');
					    					}
					    				}
					    			});
									conn.on('requestcomplete', function() {
										Ext.MessageBox.hide();
									});						
			    				}
			    			});
						}
					},{
						 text:'Indexing Catalog'
						,handler:function(){ ext_get("./admin/widgets_indexing/index",1000,500,'dialog_indexing'); }
					}
					]
				}
			},
		 	{
		 		 text:'Preview'
		 		,iconCls:'dialog' 
		 		,menu:{
		 			items:[{
		 				 text:'In New Window'
		 				,iconCls:'dialog' 
		 				,handler:function(){
		 					window.open('./');
		 				}
		 			}
		 			]
		 		}
		 	},
		 	{
		 		 text:'User Manager'
		 		,iconCls:'user' 
		 		,menu:{
		 			items:[{
		 				 text:'Access Control'
				    	,icon: './js/extjs/resources/images/default/silk/icons/group.png'
				    	,cls:'x-btn-text-icon'
				    	,tooltip:'Click to Set ACL'
		 				,handler:function(){
		 					ext_get_iframe("./library/phpgacl/admin/acl_admin.php",1000,500,'dialog_access_control','Access Control List');
		 				}
		 			}
		 			]
		 		}
		 	}
		 	]
		 }
	},
	'-'
	);
	
	var home_menu = new Ext.menu.Menu({
		id:'home_menu',
		items:[{
			text:'Catalog',
			iconCls:'mfolder',
			menu:{
				items:[{
					text: 'Indexing catalog',
					icon: './js/extjs/resources/images/default/silk/icons/server_link.png',
					cls: 'x-btn-text-icon',
					tooltip: 'Click to indexing catalog',
					handler: function() {
						Ext.Ajax.on('beforerequest', function() {
							Ext.MessageBox.wait('Indexing catalog...'); 
						});						
						Ext.Ajax.request({
							url: '../admin/catalog/catalog-index-updater',
							failure:function(response,options){
								Ext.MessageBox.alert('Warning','Oops...');
							},
							success: function(response,options)
							{
								var responseData = Ext.util.JSON.decode(response.responseText);
								Ext.MessageBox.alert('Status', responseData.message);
							}
						});
						Ext.Ajax.on('requestcomplete', function() {
							Ext.MessageBox.hide();
						});						
					}
				},{
					text: 'Optimizing index catalog',
					icon: './js/extjs/resources/images/default/silk/icons/folder_add.png',
					cls: 'x-btn-text-icon',
					tooltip:'Click to optimizing index catalog',
					handler:function() {
						Ext.Ajax.on('beforerequest', function() {
							Ext.MessageBox.wait('Optimizing catalog...'); 
						});						
						Ext.Ajax.request({
							url: '../admin/catalog/catalog-index-optimizer',
							failure:function(response,options){
								Ext.MessageBox.alert('Warning','Oops...');
							},
							success: function(response,options)
							{
								var responseData = Ext.util.JSON.decode(response.responseText);
								Ext.MessageBox.alert('Status', responseData.message);
							}
						});
						Ext.Ajax.on('requestcomplete', function() {
							Ext.MessageBox.hide();
						});						
					}
				}
				]
			}
		}
		]
	});
	
	tabpanel = new Ext.TabPanel({
		region:'center',
		resizeTabs:true,
		minTabWidth: 80,
	    deferredRender:false,
	    activeTab:0,
	    enableTabScroll:true,
	    monitorResize: true,
	    border:false,
	    autoScroll:true,
	    layoutOnTabChange:true,
	    plugins: new Ext.ux.TabCloseMenu(),
	    items:[{
		    title: 'Home',
		    iconCls: 'home',
		    contentEl:'center1',
		    tbar:[{
		    	text:'Master',
		    	iconCls:'master',
		    	menu:home_menu
		    },
		    {
		    	text:'Email',
		    	iconCls:'email',
		    	handler:function(){ ext_get("./admin/misc_globalmanager/email",700,540); }
		    }
		    ]
	   	}, {
	    	id:'main-view',
	    	layout:'border',
	    	title:'Catalog',
	    	iconCls:'catalog',
	    	hideMode:'offsets',
	    	items:[
	    		grid, {
				id:'bottom-preview',
				layout:'fit',
				height:250,
				split:true,
				border:false,
				region:'south',
				items:preview
			},{
				id:'right-preview',
				layout:'fit',
				border:false,
				region:'east',
				width:400,
				split:true,
				hidden:true
			}
	    	]
	   	},{
	   		id:'search-grid',
	   		title:'Search',
	   		iconCls:'find',
	   		layout:'border',
	   		hideMode:'offsets',
	    	items:[
	    		gridsearch, {
				id:'bottom-search-preview',
				layout:'fit',
				height:250,
				split:true,
				border:false,
				region:'south',
				items:previewsearch
			},{
				id:'right-search-preview',
				layout:'fit',
				border:false,
				region:'east',
				width:400,
				split:true,
				hidden:true
			}
	    	]
	   	}
	   	]
  	});  	 	

	var northToolbar = new Ext.Toolbar({
		height: 24
	});		
	
	var viewport = new Ext.Viewport({
		id:'viewport',
    	layout:'border',
        items:[
        { 
			region:'north',
			contentEl: 'north',
	        height:49,
			bbar: northToolbar
     	},{
	        region:'west',
	        id:'west-panel',
	        title:'General Settings',
	        iconCls:'gs',
	        split:true,
	        width: 210,
	        collapsible: true,
	        layout:'accordion',
	        layoutConfig:{
	        	animate:true
       		},
        	items: [{
        		contentEl: 'west',
	            title:'Folder Navigation',
	            id: 'tree-div',
	            border:false,
	            iconCls:'nav'
	       	},{
	       		contentEl:'info',
	        	title:'Details Information',
	        	iconCls:'dei'
       		},{
       			contentEl:'calendar',
       			title:'Calendar',
       			iconCls:'calendar'
       		},{
       			contentEl:'legend',
       			title:'Legend',
       			iconCls:'legend'
       		}
       		]
        },
			new Ext.Panel({
				region:'center',
				margins:'0 0 0 -5',
				border: false,
				layout: 'card',
				layoutConfig : {
			    	deferredRender : false
			   	},
				activeItem: 0,
				items: [
					tabpanel
				]
			})        
        ]
	});
	
	authButton = new Ext.Toolbar.Button({
		id: 'tb_authentication',
		icon: './js/extjs/resources/images/default/custom/logout.gif',
		text: '&nbsp;Logout',
		cls: 'x-btn-text-icon',
		tooltip: 'Logout',
		handler: function() { 
			var waitMsg = Ext.MessageBox.wait('Please Wait...', 'Logging Out...',{text:'Logging Out...'});
			window.location.href = './identity/logout'
//			Ext.Ajax.request({
//	            url:'./identity/logout',
//				timeout: 120000,
//	            success: function (response, options){
//					 var responseData = Ext.util.JSON.decode(response.responseText);
//					 if (responseData.success == true){
//					 	waitMsg.hide();
//						window.location.reload();
//					 }
//    				 else {
//    				 	waitMsg.hide();
//				    	Ext.Msg.alert( 'Error', 'Failed to connect to the server.');
//				    }
//	            },
//	            failure: function(response, options){
//	            	waitMsg.hide();
//	                Ext.Msg.alert('Error','Unable to Logout!');
//			    },
//			    scope: this
//	        });
		}
	});
	tb.addItem(authButton);
	
	registerButton = new Ext.Toolbar.Button({
		id: 'tb_register',
  		icon: './js/extjs/resources/images/default/custom/users.png',
   		text:'&nbsp;&nbsp;Signup',
   		cls: 'x-btn-text-icon',
  		tooltip: 'Register',
   		handler: function() { ext_get("./admin/membership_manager/signup",700,500); }
	});
	tb.addItem(registerButton);
	
	comboTheme = new Ext.ux.ThemeCombo();
    comboTheme.on('select', function(combo){
		Ext.util.CSS.swapStyleSheet('theme', combo.getValue());
		if (Ext.state.Manager.getProvider()) {
			Ext.state.Manager.set('theme',combo.getValue());
		}
    }.createDelegate(this));
    
    tb.setHeight(28);
	tb.addFill();
	tb.addText('Theme:');
	tb.addSpacer();
	tb.addSpacer();
	tb.addField(comboTheme);
	tb.add('-');
	tb.addElement('datenow');
	tb.addSpacer();
	tb.addSpacer();
	tb.add('-');
	tb.addElement('clock');
	
	northToolbar.addElement('chdir');

	// main context
	var menuC = new Ext.menu.Menu('mainContext');
	menuC.add(
		new Ext.menu.Item
		({
			id: 'reload',
			icon: './js/extjs/resources/images/default/silk/icons/arrow_refresh.png',
			text: 'Reload (Ctrl+E)',
			handler: onItemClick	
		}),
		new Ext.menu.Item
		({
			id: 'exp',
			icon: './js/extjs/resources/images/default/custom/expand-all.gif',
			text: 'Expand All (Ctrl+&nbsp;&rarr;)',
			handler: onItemClick	
		}),
		new Ext.menu.Item
		({
			id: 'coll',
			icon: './js/extjs/resources/images/default/custom/collapse-all.gif',
			text: 'Collapse All (Ctrl+&nbsp;&larr;)',
			handler: onItemClick	
		}),
		new Ext.menu.Separator(),
		new Ext.menu.Item
		({
			id: 'renfolder',
			icon: './js/extjs/resources/images/default/silk/icons/pencil.png',
			text: 'Rename (F2)',
			handler: function() { menuC.hide(); openActionDialog('renfolder'); }
		}),
		new Ext.menu.Item
		({
			id: 'delfolder',
			icon: './js/extjs/resources/images/default/silk/icons/cross.png',
			text: 'Delete (Delete Key)',
			handler: function() { menuC.hide(); openActionDialog('delfolder'); }
		}),
		new Ext.menu.Item
		({
			id: 'addfolder',
			icon: './js/extjs/resources/images/default/silk/icons/folder_add.png',
			text: 'New folder ... (Ctrl+N)',
			handler: function() { menuC.hide(); openActionDialog('addfolder'); }
		}),
		new Ext.menu.Separator(),
		new Ext.menu.Item
		({
			id: 'sharing_folder',
			icon: './js/extjs/resources/images/default/silk/icons/bullet_wrench.png',
			disabled:true,
			text: 'Share this folder',
			handler: function() { menuC.hide(); openActionDialog('sharefolder'); }
		}),
		new Ext.menu.Separator(),
		new Ext.menu.Item
		({
			id: 'upload',
			text: 'Upload file',
			disabled:true,
			handler: function() { 
				var selectedNode = tree.getSelectionModel().getSelectedNode();
				menuC.hide(); 
				getDialog('../../app/servers/upload/manager/uploader','gallery', selectedNode.id); 
			}
		}),
		new Ext.menu.Separator(),
		new Ext.menu.Item
		({
			id: 'export',
			text: 'Export file',
			iconCls:'exprt',
			handler: function() { 
				var selectedNode = tree.getSelectionModel().getSelectedNode();
				menuC.hide(); 
    			Ext.MessageBox.confirm('Confirm','Export selection?',function(btn){
    				if (btn=='yes')
    				{
    					window.location.href = './admin/api_export/peraturan/format/excel/folderGuid/'+ selectedNode.id;
    					/*
		    			var conn = new Ext.data.Connection();
						conn.on('beforerequest', function() {
							Ext.MessageBox.wait('Export catalog'); 
						});						
		    			conn.request({
		    				url: './admin/api_export/peraturan/format/excel',
		    				params:{
		    					folderGuid: selectedNode.id
		    				},
		    				callback:function(options, success, response){
		    					if (success) {
		    						json = Ext.decode( response.responseText );
		    						if ( json.success ) {
		    							store.reload(); 
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
						*/						
    				}
    			});
			}
		}),
		new Ext.menu.Separator(),
		new Ext.menu.Item
		({
			id:'doc'
			,text:'Download document'
			,iconCls:'download'
			,handler:function(){
				var selectedNode = tree.getSelectionModel().getSelectedNode();
				menuC.hide();
				ext_get("./admin/dms_filedownloader/browse/folderGuid/"+selectedNode.id,580,430);
			}
		})
	);	
	
	// build tree
	tree = new Ext.tree.TreePanel({
		el: 'tree-div',
		autoScroll:true,
		animate: true,
		loader: new Ext.tree.TreeLoader({dataUrl: './services/folder/fetch-children', baseParams:{parentGuid:'root'}}),
		enableDD: true,
		ddGroup: 'TreeDD',
		timeout: 90000,
		containerScroll: true
	});        
	
	tree.on('dblclick', myFunction);
	tree.on('contextmenu', menuShow);
	tree.on('beforenodedrop',function(e){
		dropEvent = e;
		copymoveCtx(e);
	});
	tree.on('beforemovenode', function() { return false; });
	
	var tsm = tree.getSelectionModel();
	tsm.on('selectionchange', handleNodeClick);
	
	var root = new Ext.tree.AsyncTreeNode({
		text: 'Root',
		draggable: false,
		id: 'root'
	});
	
	tree.setRootNode(root);
	
	tree.render();
	root.expand(false, true);
	
	var map = new Ext.KeyMap('tree-div',[
	{
		// Ctrl + E = reload
		key:69, ctrl:true, stopEvent:true, scope:this
	   ,fn:function(key,e) {
	   		var sm = tree.getSelectionModel();
	   		var node = sm.getSelectedNode();
			if(node) {
				node = node.isLeaf() ? node.parentNode : node;
				sm.select(node);
				node.reload();
			}
	   }
	}, {
		// Ctrl + -> = expand deep
		key:39, ctrl:true, stopEvent:true, scope:this
	   ,fn:function(key, e) {
		var sm = tree.getSelectionModel();
		var node = sm.getSelectedNode();
		if(node && !node.isLeaf()) {
			sm.select(node);
			node.expand.defer(1, node, [true]);
		}}
	}, {
		// Ctrl + <- = collapse deep
		key:37, ctrl:true, scope:this, stopEvent:true
	   ,fn:function(key, e) {
		var sm = tree.getSelectionModel();
		var node = sm.getSelectedNode();
		if(node && !node.isLeaf()) {
			sm.select(node);
			node.collapse.defer(1, node, [true]);
		}}
	}, {
		// F2 = edit
		key:113, scope:this
	   ,fn:function(key, e) {
		var sm = tree.getSelectionModel();
		var node = sm.getSelectedNode();
		if(node) {
			e.stopEvent();
			openActionDialog('renfolder');
		}}
	}, {
		// Delete Key = Delete
		key:46, stopEvent:true, scope:this
	   ,fn:function(key, e) {
		var sm = tree.getSelectionModel();
		var node = sm.getSelectedNode();
		if(node) {
			openActionDialog('delfolder');
		}}
	}, {
		// Ctrl + N = New Directory
		key:78, ctrl:true, scope:this, stopEvent:true
	   ,fn:function(key, e) {
		var sm = tree.getSelectionModel();
		var node = sm.getSelectedNode();
		if(node) {
			e.stopEvent();
			openActionDialog('addfolder');
		}}
	}]);
    var copymoveCtxMenu = new Ext.menu.Menu({
        id:'copyCtx',
        items: [{
        	id: 'copy',
    		icon: './js/extjs/resources/images/default/custom/editcopy.png',
    		text: 'Copy',
    		handler: function() {copymoveCtxMenu.hide();copymove('copy');}
    	},
    	{
        	id: 'copysearch',
    		icon: './js/extjs/resources/images/default/custom/editcopy.png',
    		text: 'Copy from Search',
    		handler: function() {copymoveCtxMenu.hide();copyMoveSearch('copy');}
    	},
    	{
    		id: 'move',
    		icon: './js/extjs/resources/images/default/custom/move.png',
    		text: 'Move',
    		handler: function() { copymoveCtxMenu.hide();copymove('move'); }
    	},
    	{
    		id: 'movesearch',
    		icon: './js/extjs/resources/images/default/custom/move.png',
    		text: 'Move from Search',
    		handler: function() { copymoveCtxMenu.hide();copyMoveSearch('move'); }
    	},'-', 
		{
			id: 'cancel',
    		icon: './js/extjs/resources/images/default/custom/cancel.png',
    		text: 'Cancel',
    		handler: function() { copymoveCtxMenu.hide(); }
    	}
	]
    });
    function copymove( action ) {
    	var s = dropEvent.data.selections, r = [];
    	if( s ) {
    		// Dragged from the Grid
    		var selectedKeys = grid.selModel.selections.keys;
    		if (selectedKeys.length > 0)
    		{
    			var selections = grid.selModel.getSelections();
    			var gridCount = grid.selModel.getCount();
    			for(i = 0; i < gridCount; i++){
	    			var conn = new Ext.data.Connection();
					conn.on('beforerequest', function() {
						Ext.MessageBox.wait(action + ' catalog [' + s[i].data.title + '] to folder [' + dropEvent.target.text + ']'); 
					});						
	    			conn.request({
	    				url: './admin/api_folder/copy-move-items/format/json',
	    				params:{
	    					selitem: selections[i].json.guid,
	    					folderGuid: tree.getSelectionModel().getSelectedNode().id,
	    					targetGuid: dropEvent.target.id,
	    					act: action,
	    					option: 'grid'
	    				},
	    				callback:function(options, success, response){
	    					if (success) {
	    						json = Ext.decode( response.responseText );
	    						if ( json.success ) {
	    							store.reload(); 
	    							info( 'Success', json.message );
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
    			}
    		}
    	} else {
    		// Dragged from inside the tree
    		if (dropEvent.data.node)
    		{
    			var conn = new Ext.data.Connection();
    			conn.request({
    				url: './admin/api_folder/copy-move-items/format/json',
    				params:{
    					folderGuid: dropEvent.data.node.id,
    					targetGuid: dropEvent.target.id,
    					act: action,
    					option: 'tree'
    				},
    				callback:function(options, success, response){
    					if (success) {
    						json = Ext.decode( response.responseText );
    						if ( json.success ) {
    							info( 'success', json.message );
    							try {
    								if ( dropEvent ) {
    									dropEvent.target.parentNode.reload();
    									dropEvent = null;
    								}
    							} catch(e) { store.reload(); }
    						} else {
    							Ext.Msg.alert( 'Failure', json.error );
    						}
    					} else {
    						Ext.Msg.alert( 'Error', 'Failed to connect to the server.');
    					}
    				}
    			});
    		}
    	}
    }
    function copyMoveSearch( action ) {
    	var s = dropEvent.data.selections, r = [];
    	if( s ) {
    		// Dragged from the Grid
    		var selectedKeys = gridsearch.selModel.selections.keys;
    		if (selectedKeys.length > 0)
    		{
    			var selections = gridsearch.selModel.getSelections();
    			var gridCount = gridsearch.selModel.getCount();
    			for(i = 0; i < gridCount; i++){
	    			var conn = new Ext.data.Connection();
					conn.on('beforerequest', function() {
						Ext.MessageBox.wait(action + ' catalog [' + s[i].data.title + '] to folder [' + dropEvent.target.text + ']'); 
					});						
	    			conn.request({
	    				url: './admin/api_folder/copy-move-items/format/json',
	    				params:{
	    					selitem: selections[i].json.guid,
//	    					folderGuid: tree.getSelectionModel().getSelectedNode().id,
	    					folderGuid: gridsearch.getSelectionModel().getSelected().data.folderGuid,
	    					targetGuid: dropEvent.target.id,
	    					act: action,
	    					option: 'grid'
	    				},
	    				callback:function(options, success, response){
	    					if (success) {
	    						json = Ext.decode( response.responseText );
	    						if ( json.success ) {
	    							store.reload(); 
	    							info( 'Success', json.message );
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
    			}
    		}
    	}
    }
    function copymoveCtx(e){
        copymoveCtxMenu.showAt(e.rawEvent.getXY());
    }
	function w_move(node,e,oldParent,newParent)
	{
		moveNode(e,newParent);
	}
	function menuShow(node,e)
	{
		e.stopEvent();
		e.preventDefault();
		tree.getSelectionModel().select(node);
		menuC.show(node.ui.getAnchor());
	}
	function myFunction(a,b)
	{
		var selectedNode = tree.getSelectionModel().getSelectedNode();
		var selnode = selectedNode.text.trim().charAt(0).toLowerCase() + selectedNode.text.trim().substr(1).toLowerCase().replace(' ', '');
		
		if (selectedNode.text == 'Tanggapan') 
		{
			loadTab("./admin/comment_manager/comment", selectedNode.text);
		}
		else if (selectedNode.text == 'Approved')
		{
			loadTab("./admin/clinic_manager/clinic/status/1", 'Klinik');
		}
		else if (selectedNode.text == 'Draft')
		{
			loadTab("./admin/clinic_manager/clinic/status/0", 'Klinik');
		}
		else if (selectedNode.text == 'NA')
		{
			loadTab("./admin/clinic_manager/clinic/status/2", 'Klinik');
		}
		else if (selectedNode.text == 'Published')
		{
			loadTab("./admin/clinic_manager/clinic/status/99", 'Klinik');
		}
		/*
		else if (selectedNode.text == 'Klinik')
		{
			loadTab("./admin/widgets_clinic/clinic", selectedNode.text);
		}
		*/
		else if (selectedNode.text == 'Event calendar')
		{
			loadTab("./admin/calendar_manager/eventcalendar",'Calendar');
		}
		else if (selectedNode.text == 'Banner')
		{
			loadTab("./admin/banner_manager/banner",'Banner');
		}
		else if (selectedNode.text == 'Polling')
		{
			loadTab("./admin/polling_manager/polling",'Polling');
		}
		else
		{
		switch(tree.selModel.selNode.parentNode.text)
		{
			case 'Root' :
				loadTab("../../app/servers/"+ selnode +"/manager/"+ selnode,selectedNode.text);				
			break;
//			case 'Management User' :
			case 'User' :
				loadTab("./admin/membership_manager/"+ selnode, selectedNode.text);
			break;
			case 'Promotion' :
				loadTab("widget/promomanager/"+ selnode, selectedNode.text);
			break;
			case 'Setting' :
				ext_get("../../app/servers/setting/manager/setting/folderGuid/"+ selectedNode.id,750,500);
			break;
			default:
			tabpanel.activate(Ext.getCmp('main-view'));
			store.baseParams = {folderGuid:selectedNode.id};
			store.load({params:{start:0,limit:25,folderGuid:selectedNode.id}});
		}
		}
	}
	function handleNodeClick(sm, node)
	{
		if (!node) return;
		try {
		if (tree.selModel.selNode.text == 'Tanggapan')
		{
			return;
		}
		else if (tree.selModel.selNode.text == 'Approved')
		{
			return;
		}
		else if (tree.selModel.selNode.text == 'NA')
		{
			return;
		}
		else if (tree.selModel.selNode.text == 'Draft')
		{
			return;
		}
		else if (tree.selModel.selNode.text == 'Published')
		{
			return;
		}
		/*
		else if (tree.selModel.selNode.text == 'Klinik')
		{
			return;
		}
		*/
		else if (tree.selModel.selNode.text == 'Event calendar')
		{
			return;
		}
		else if (tree.selModel.selNode.text == 'Banner')
		{
			return;
		}
		else
		{
		switch(tree.selModel.selNode.parentNode.text)
		{
//			case 'Management User' :
			case 'User' :
				return;
			break;
			case 'Promotion' :
				return;
			break;
			case 'Setting' :
				return;
			break;
			default:
			tabpanel.activate(Ext.getCmp('main-view'));
			store.baseParams = {folderGuid:node.id};
			store.load({params:{start:0,limit:25,folderGuid:node.id}});
			chDir(node.id);
		}
		}
		} catch (Exception) {
			
		}
	}
	chDir = function ( directory )
	{
		Ext.Ajax.request({
			url: './api/folder/chdir-event',
			params: { dir: directory },
			callback: function(options, success, response) {
				if (success) {
					var result = Ext.decode(response.responseText);
					Ext.get('chdir').update('Location : &nbsp;&nbsp;' + result.dirselect);
				}
			}
		});
		expandTreeToDir(null, directory);
	}
	function expandTreeToDir(node, dir)
	{
		node = tree.getNodeById(dir);
		if (!node) return;
		if( node.isExpanded() ) {
			node.select();
			return;
		}
		node.on('load', function() { node.select(); });
		node.expand;
	}
	function moveNode(node,parent)
	{
		var moveNode = tree.selModel.selNode;
		if (node.hasChildNodes())
		{
			Ext.MessageBox.show({
	        	title: 'Confirm',
	           	msg: "Attention!! Are you sure you want to move this folder '"+node.text+"' into '"+parent.text+"' and move all its content ?",
	           	buttons: Ext.MessageBox.YESNO,
	           	icon: Ext.MessageBox.WARNING,			
	           	fn: function(btn) {
	           		if (btn=="yes") {
						Ext.Ajax.request
						({ 
							url: "./admin/dms_foldermanager/node/folderGuid/" + node.id+"/parentGuid/" + parent.id,
							success: function()
							{
								Ext.MessageBox.alert('Status', 'Move '+node.text+' successfully.');
							}
						});
	           		} else {
						Ext.MessageBox.alert('Status', 'Move canceled.');
	           		}
	           		tree.root.reload();
	           	}
			});
		} else {
			Ext.MessageBox.show({
				title: 'Confirm',
				msg: "You're about to move this folder '"+node.text+"' into '"+parent.text+"'. Continue ?",
				buttons: Ext.MessageBox.YESNO,
				icon: Ext.MessageBox.WARNING,
				fn: function(btn) {
					if (btn=="yes") {
						Ext.Ajax.request
						({ 
							url: "./admin/dms_foldermanager/node/folderGuid/" + node.id+"/parentGuid/" + parent.id,
							success: function()
							{
					   			Ext.MessageBox.alert('Status', 'Move '+node.text+' successfully.');
					   	 		node.parentNode.reload();
					   	 		node.parentNode.select();
							}
						});
					} else {
			   	 		Ext.MessageBox.alert('Status', 'Move cancelled.');				
			   			tree.root.reload();
					}
				}
			});		
		}
	}
	function onItemClick(item)
	{
		menuC.hide();
		var selectedNode = tree.getSelectionModel().getSelectedNode();
		switch(item.id)
		{
			case 'exp' :
				tree.expandAll();
			break;
			case 'coll' :
				tree.collapseAll();
			break;
			case 'reload' :
				tree.root.reload();
			break;
		}
	}
	function movePreview(m, pressed)
	{
		if (pressed) {
			var right = Ext.getCmp('right-preview');
			var rightsearch = Ext.getCmp('right-search-preview');
            var bot = Ext.getCmp('bottom-preview');
            var botsearch = Ext.getCmp('bottom-search-preview');
            switch(m.text) {
            	case 'Bottom':
                	right.hide();
                	rightsearch.hide();
                    bot.add(preview);
                    botsearch.add(previewsearch);
                    bot.show();
                    botsearch.show();
                    bot.ownerCt.doLayout();
                    botsearch.ownerCt.doLayout();
              	break;
                case 'Right':
                	bot.hide();
                	botsearch.hide();
                    right.add(preview);
                    rightsearch.add(previewsearch);
                    right.show();
                    rightsearch.show();
                    right.ownerCt.doLayout();
                    rightsearch.ownerCt.doLayout();
                break;
                case 'Hide':
                	preview.ownerCt.hide();
                	previewsearch.ownerCt.hide();
                    preview.ownerCt.ownerCt.doLayout();
                    previewsearch.ownerCt.ownerCt.doLayout();
              	break;
        	}
    	}
	}
	loadTab = function( url, title ) 
	{ 
		var id = 'kutu-' + title;
		var tab = tabpanel.getComponent(id);
		var autoLoad = {url: url, scripts:true};
		
		if (tab)
		{ 
			tabpanel.remove(id,true);
            n = tabpanel.add({
                id: id,
                cclass : title,
                autoLoad: autoLoad,
                title: title,
                closable: true,
                //autoScroll:true,
                layout:'fit'
            });
			tabpanel.setActiveTab(n);
		} else 
		{ 
            var p = tabpanel.add({
                id: id,
                cclass : title,
                autoLoad: autoLoad,
                title: title,
                closable: true,
                //autoScroll:true,
                layout:'fit'
            });
            tabpanel.setActiveTab(p);			
		}
	}
	function openTab(record) {
        record = (record && record.data) ? record : grid.getSelectionModel().getSelected();
        var d = record.data;
        var selectedNode = tree.getSelectionModel().getSelectedNode();
        var id = d.title;
        var autoLoad = {url: "./admin/browser/view-in-new-tab", params:{catalogGuid:d.guid,folderGuid:selectedNode.id}, scripts:true};
        var tab;
        if(!(tab = tabpanel.getItem(id))){
            tab = new Ext.Panel({
                id: id,
                cls:'preview single-preview',
                title: d.title,
                tabTip: d.title,
                autoLoad: autoLoad,
                closable:true,
                autoScroll:true,
                border:true,

                tbar: [{
		  			text:'Catalog',
		  			iconCls:'catalog',
		  			menu: new Ext.menu.Menu({
		  				items:[{
		  					text:'Edit',
		  					handler: function(){ ext_get("./admin/dms_catalogmanager/edit/catalogGuid/"+d.guid,750,500); }
		  				},{
		  					text:'Upload File(s)',
					    	handler:function(){
					    		ext_get("./admin/dms_fileuploader/add/catalogGuid/"+d.guid,580,430);
					    	}
		  				},{
		  					text:'Add Relation',
		  					iconCls:'relation',
					    	handler: function() { 
					    		ext_get("./admin/widgets_relation/viewsearch/guid/"+d.guid,830,500,'viewsearch');
					    	}			
		  				}
		  				]
		  			})
                }
                ]
            });
            tabpanel.add(tab);
        }
        tabpanel.setActiveTab(tab);
	}
	function openTabSearch(record) {
        record = (record && record.data) ? record : gridsearch.getSelectionModel().getSelected();
        var d = record.data;
        var id = d.title;
        var autoLoad = {url: "./admin/browser/view-in-new-tab", params:{catalogGuid:d.guid,folderGuid:d.folderGuid}, scripts:true};
        var tab;
        if(!(tab = tabpanel.getItem(id))){
            tab = new Ext.Panel({
                id: id,
                cls:'preview single-preview',
                title: d.title,
                tabTip: d.title,
                autoLoad: autoLoad,
                closable:true,
                autoScroll:true,
                border:true,

                tbar: [{
		  			text:'Catalog',
		  			iconCls:'catalog',
		  			menu: new Ext.menu.Menu({
		  				items:[{
		  					text:'Edit',
		  					handler: function(){ ext_get("./admin/dms_catalogmanager/edit/catalogGuid/"+d.guid,750,500); }
		  				},{
		  					text:'Upload File(s)',
					    	handler:function(){
					    		ext_get("./admin/dms_fileuploader/add/catalogGuid/"+d.guid,580,430);
					    	}
		  				},{
		  					text:'Add Relation',
		  					iconCls:'relation',
					    	handler: function() { 
					    		ext_get("./admin/widgets_relation/viewsearch/guid/"+d.guid,830,500,'viewsearch');
					    	}			
		  				}
		  				]
		  			})
                }
                ]
            });
            tabpanel.add(tab);
        }
        tabpanel.setActiveTab(tab);
	}
	function openAll(){
		tabpanel.beginUpdate();
		grid.store.data.each(openTab, tabpanel);
		tabpanel.endUpdate();
	}
	function openAllSearch(){
		tabpanel.beginUpdate();
		gridsearch.store.data.each(openTabSearch, tabpanel);
		tabpanel.endUpdate();
	}
	
    /**
     * Calls the ping action from the reception's controller.
     */
    /*
    var _pingServer = function()
    {
        Ext.Ajax.request({
            url            : 'reception/ping/format/json',
            disableCaching : true,
            failure        : function(){
				Ext.MessageBox.show({
					title:'Warning',
					msg:"Your session has expired!",
					buttons: Ext.MessageBox.OK,
					icon: Ext.MessageBox.ERROR,
					fn: function() {
						_stopPing();
						window.location.reload(true);
					}
				});
            }
        });
    };
	*/
    /**
     * Starts pinging the server in a frequent interval to keep the user's session
     * alive.
     */
    /*
    var _startPing = function()
    {
        if (_pingTask !== null) {
            return;
        }
        _pingTask = {
            run      : _pingServer,
            interval : 300000 // 5 minute
        }
        Ext.TaskMgr.start(_pingTask);
    };
	*/
    /**
     * Stops pinging the server.
     */
    /*
    var _stopPing = function()
    {
        if (_pingTask === null) {
            return;
        }
        Ext.TaskMgr.stop(_pingTask);
        _pingTask = null;
    };
    
	setTimeout(function(){_startPing();}, 80000);
    */
}

if (Ext.isIE)
{
	Ext.EventManager.addListener(window, "load", ext_init);
} else { 
    // The Quicktips are used for the toolbar and Tree mouseover tooltips!
    Ext.QuickTips.init();
	Ext.onReady(ext_init);
}