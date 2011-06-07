/*
 * Ext JS Library 2.2
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */
var ctime;
var win = new Ext.Window();

Ext.onReady(function(){

    // NOTE: This is an example showing simple state management. During development,
    // it is generally best to disable state management as dynamically-generated ids
    // can change across page loads, leading to unpredictable results.  The developer
    // should ensure that stable state ids are set for stateful components in real apps.
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

    var myData = [
        ['3m Co',71.72,0.02,0.03,'9/1 12:00am'],
        ['Alcoa Inc',29.01,0.42,1.47,'9/1 12:00am'],
        ['Altria Group Inc',83.81,0.28,0.34,'9/1 12:00am'],
        ['American Express Company',52.55,0.01,0.02,'9/1 12:00am'],
        ['American International Group, Inc.',64.13,0.31,0.49,'9/1 12:00am'],
        ['AT&T Inc.',31.61,-0.48,-1.54,'9/1 12:00am']
    ];

    // example of custom renderer function
    function change(val){
        if(val > 0){
            return '<span style="color:green;">' + val + '</span>';
        }else if(val < 0){
            return '<span style="color:red;">' + val + '</span>';
        }
        return val;
    }

    // example of custom renderer function
    function pctChange(val){
        if(val > 0){
            return '<span style="color:green;">' + val + '%</span>';
        }else if(val < 0){
            return '<span style="color:red;">' + val + '%</span>';
        }
        return val;
    }

    // create the data store
    var store = new Ext.data.SimpleStore({
        fields: [
           {name: 'company'},
           {name: 'price', type: 'float'},
           {name: 'change', type: 'float'},
           {name: 'pctChange', type: 'float'},
           {name: 'lastChange', type: 'date', dateFormat: 'n/j h:ia'}
        ]
    });
    store.loadData(myData);

    // create the Grid
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
            {id:'company', header: "Company", width: 160, sortable: true, dataIndex: 'company'},
            {id:'netflixid', header: "Price", width: 75, sortable: true, renderer: 'usMoney', dataIndex: 'price'},
            {header: "Change", width: 75, sortable: true, renderer: change, dataIndex: 'change'},
            {header: "% Change", width: 75, sortable: true, renderer: pctChange, dataIndex: 'pctChange'},
            {header: "Last Updated", width: 85, sortable: true, renderer: Ext.util.Format.dateRenderer('m/d/Y'), dataIndex: 'lastChange'}
        ],
        stripeRows: true,
        autoExpandColumn: 'company',
        height:350,
        width:600,
        title:'Array Grid',
		plugins: new Ext.ux.grid.RowPopup({tpl:'<table style="background-image:url(\'images/tableheader.gif\'); height=\'1.7em\'" width="100%"><tr><td><div class="popupTitle">{company}</div></td></tr></table><table width="100%" height="100%" bgcolor="white"><tr><td><p><img src="http://i33.tinypic.com/2vuc3mt.jpg" width="75" height="100" class="floatLeft"><div class="popupText">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div><br><table class="popupInfo"><tr><td align="right" valign="top"><b>Starring:</b></td><td align="left">Christian Bale, Heath Ledger, Daphne Zuniga</td></tr><tr><td align="right" valign="top"><b>Director:</b></td><td align="left">Robert Riener</td></tr><tr><td align="right" valign="top"><b>Genre:</b></td><td align="left">Thriller</td></tr><tr><td align="right" valign="top"><b>Rating:</b></td><td align="left">R</td></tr></table></p><img src="images/rating.gif" width="273" height="78" border="0"></td></tr></table>'})
    });

    grid.render('grid-example');
	
	// create the Grid
    var grid2 = new Ext.grid.GridPanel({
        store: store,
        columns: [
            {id:'company', header: "Company", width: 160, sortable: true, dataIndex: 'company'},
            {id:'netflixid', header: "Price", width: 75, sortable: true, renderer: 'usMoney', dataIndex: 'price'},
            {header: "Change", width: 75, sortable: true, renderer: change, dataIndex: 'change'},
            {header: "% Change", width: 75, sortable: true, renderer: pctChange, dataIndex: 'pctChange'},
            {header: "Last Updated", width: 85, sortable: true, renderer: Ext.util.Format.dateRenderer('m/d/Y'), dataIndex: 'lastChange'}
        ],
        stripeRows: true,
        autoExpandColumn: 'company',
        height:350,
        width:600,
        title:'Array Grid',
		bbar: [],
		plugins: new Ext.ux.grid.RowPopup({tpl:'<table style="background-image:url(\'images/tableheader.gif\'); height=\'1.7em\'" width="100%"><tr><td><div class="popupTitle">{company}</div></td></tr></table><table width="100%" height="100%" bgcolor="white"><tr><td><p><img src="http://i33.tinypic.com/2vuc3mt.jpg" width="75" height="100" class="floatLeft"><div class="popupText">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div><br><table class="popupInfo"><tr><td align="right" valign="top"><b>Starring:</b></td><td align="left">Christian Bale, Heath Ledger, Daphne Zuniga</td></tr><tr><td align="right" valign="top"><b>Director:</b></td><td align="left">Robert Riener</td></tr><tr><td align="right" valign="top"><b>Genre:</b></td><td align="left">Thriller</td></tr><tr><td align="right" valign="top"><b>Rating:</b></td><td align="left">R</td></tr></table></p><img src="images/rating.gif" width="273" height="78" border="0"></td></tr></table>'})
    });
	
	
    grid2.render('grid-example2');




});