Tickets.grid.Threads = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'tickets-grid-threads'
		,url: Tickets.config.connector_url
		,baseParams: {
			action: 'mgr/thread/getlist'
		}
		,fields: ['id','resource','name','createdon','createdby','closed','deleted','deletedon','deletedby','pagetitle','comments','url']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: [
			{header: _('id'),dataIndex: 'id',width: 50, sortable: true}
			,{header: _('ticket_thread_resource'),dataIndex: 'resource',width: 100, sortable: true}
			,{header: _('ticket_thread_name'),dataIndex: 'name',width: 200, sortable: true}
			,{header: _('ticket_thread_createdon'),dataIndex: 'createdon',width: 100, sortable: true}
			,{header: _('ticket_thread_comments'),dataIndex: 'comments',width: 100, sortable: true}
			,{header: _('ticket'),dataIndex: 'pagetitle', width: 100, renderer: this.renderResourceLink}
		]
		,tbar: ['->'
			,{
				xtype: 'textfield'
				,name: 'query'
				,width: 200
				,id: 'tickets-thread-search'
				,emptyText: _('search')
				,listeners: {render: {fn: function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER, function() {this.search(tf);}, this);},scope: this}}
			},{
				xtype: 'button'
				,id: 'modx-filter-threads-clear'
				,text: _('ticket_clear')
				,listeners: {
					'click': {fn: this.clearFilter, scope: this}
				}
			}]
		,listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.manageComments(grid, e, row);
			}
		}
		,viewConfig: {
			forceFit:true,
			enableRowBody:true,
			showPreview:true,
			getRowClass : function(rec, ri, p){
				var cls = 'tickets-thread-row';
				if (rec.data.deleted) cls += ' thread-deleted';
				if (rec.data.closed) cls += ' thread-closed';
				return cls;
			}
		}
	});
	Tickets.grid.Threads.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.grid.Threads,MODx.grid.Grid,{
	windows: {}

	,getMenu: function(grid, rowIndex, event) {
		var row = grid.store.data.items[rowIndex].data;
		var m = [];
		m.push({text:  _('ticket_thread_manage_comments'),handler: this.manageComments});
		m.push({text: row.closed ? _('ticket_thread_open') : _('ticket_thread_close'),handler: this.closeThread});
		m.push('-');
		m.push({text: row.deleted ? _('ticket_thread_undelete') : _('ticket_thread_delete'),handler: this.deleteThread});
		m.push({text: _('ticket_thread_remove'),handler: this.removeThread});
		this.addContextMenuItem(m);
	}

	,search: function(tf, nv, ov) {
		var s = this.getStore();
		s.baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,clearFilter: function() {
		var s = this.getStore();
		s.baseParams.query = '';
		Ext.getCmp('tickets-thread-search').reset();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,deleteThread: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.Ajax.request({
			url: Tickets.config.connector_url
			,params: {
				action: 'mgr/thread/delete'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:function(r) {this.refresh();},scope:this}
			}
		})
	}

	,closeThread: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.Ajax.request({
			url: Tickets.config.connector_url
			,params: {
				action: 'mgr/thread/close'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:function(r) {this.refresh();},scope:this}
			}
		})
	}

	,removeThread: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.msg.confirm({
			url: Tickets.config.connector_url
			,title: _('ticket_thread_remove')
			,text: _('ticket_thread_remove_confirm')
			,params: {
				action: 'mgr/thread/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:function(r) { this.refresh(); },scope:this}
			}
		});
	}

	,manageComments: function(btn, e, row) {
		if (typeof(row) != 'undefined') {this.menu.record = row.data;}
		var tid = this.menu.record.id;

		var tab = Ext.getCmp('comments-tab-'+tid);
		if (typeof(tab) == 'undefined') {
			tab = Ext.getCmp('tickets-home-tabs').add({
				title: this.menu.record.name
				,id: 'comments-tab-'+tid
				,closable: true
				,items: [{
					html: _('ticket_сomment_intro')
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'tickets-grid-comments'
					,id: 'tickets-grid-comments-'+tid
					,threads: tid
				}]
			});
		}
		tab.show()
	}

	/*
	 ,viewThread: function(btn,e) {
	 if (!this.menu.record) return false;
	 if (this.menu.record.url) {
	 window.open(this.menu.record.url + '#comments');
	 }
	 }
	 */

	,renderResourceLink: function(val,cell,row) {
		if (row.data.url && val) {
			return '<a href="' + row.data.url+ '" target="_blank" class="resource-link">' + val + '</a>'
		}
		else {
			return '';
		}
	}
});
Ext.reg('tickets-grid-threads',Tickets.grid.Threads);



/*
Tickets.window.CreateThread = function(config) {
	config = config || {};
	this.ident = config.ident || 'mecitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('tickets.item_create')
		,id: this.ident
		,height: 150
		,width: 475
		,url: Tickets.config.connector_url
		,action: 'mgr/item/create'
		,fields: [
			{xtype: 'textfield',fieldLabel: _('name'),name: 'name',id: 'tickets-'+this.ident+'-name',width: 300}
			,{xtype: 'textarea',fieldLabel: _('description'),name: 'description',id: 'tickets-'+this.ident+'-description',width: 300}
		]
	});
	Tickets.window.CreateThread.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.window.CreateThread,MODx.Window);
Ext.reg('tickets-window-item-create',Tickets.window.CreateThread);


Tickets.window.UpdateThread = function(config) {
	config = config || {};
	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('tickets.item_update')
		,id: this.ident
		,height: 150
		,width: 475
		,url: Tickets.config.connector_url
		,action: 'mgr/item/update'
		,fields: [
			{xtype: 'hidden',name: 'id',id: 'tickets-'+this.ident+'-id'}
			,{xtype: 'textfield',fieldLabel: _('name'),name: 'name',id: 'tickets-'+this.ident+'-name',width: 300}
			,{xtype: 'textarea',fieldLabel: _('description'),name: 'description',id: 'tickets-'+this.ident+'-description',width: 300}
		]
	});
	Tickets.window.UpdateThread.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.window.UpdateThread,MODx.Window);
Ext.reg('tickets-window-item-update',Tickets.window.UpdateThread);
	*/