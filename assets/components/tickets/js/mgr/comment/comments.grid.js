Tickets.grid.Comments = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'tickets-grid-comments'
		,url: Tickets.config.connector_url
		,baseParams: {
			action: 'mgr/comment/getlist'
			,section: config.section
			,parents: config.parents
			,threads: config.threads
		}
		,fields: ['id','text','name','createdby','parent','pagetitle','createdon','createdby','editedon','editedby','published','deleted','deletedon','deletedby','resource_url','comment_url','email','ip']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: [
			{header: _('id'),dataIndex: 'id',width: 50, sortable: true}
			,{header: _('parent'),dataIndex: 'parent',width: 50, sortable: true}
			,{header: _('text'),dataIndex: 'text',width: 300}
			,{header: _('name'),dataIndex: 'name',width: 100, renderer: this.renderUserLink}
			,{header: _('createdon'),dataIndex: 'createdon',width: 100, sortable: true}
			,{header: _('ticket'),dataIndex: 'pagetitle', width: 100, renderer: this.renderResourceLink, hidden: config.parents || config.threads ? 1 : 0}
		]
		,tbar: ['->'
		,{
			xtype: 'textfield'
			,name: 'query'
			,width: 200
			,id: 'tickets-comment-search'
			,emptyText: _('search')
			,listeners: {render: {fn: function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER, function() {this.search(tf);}, this);},scope: this}}
		},{
			xtype: 'button'
			,id: 'modx-filter-comments-clear'
			,text: _('ticket_clear')
			,listeners: {
				'click': {fn: this.clearFilter, scope: this}
			}
		}]
		,listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.updateComment(grid, e, row);
			}
		}
		,viewConfig: {
			forceFit:true,
			enableRowBody:true,
			showPreview:true,
			getRowClass : function(rec, ri, p){
				var cls = 'tickets-comment-row';
				if (rec.data.deleted) {cls += ' comment-deleted';}
				if (rec.data.published == 0) {cls += ' comment-unpublished';}
				return cls;
			}
		}
	});
	Tickets.grid.Comments.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.grid.Comments,MODx.grid.Grid,{
	windows: {}

	,remove: function() {}	// Grid onremove fix

	,getMenu: function(grid, rowIndex, event) {
		var row = grid.store.data.items[rowIndex].data;
		var m = [];
		m.push({text: _('ticket_comment_update'),handler: this.updateComment});
		if (row.comment_url) {
			m.push({text: _('ticket_comment_view'),handler: this.viewComment});
		}
		//m.push({text: _('ticket_comment_viewauthor'),handler: this.viewAuthor});
		m.push({text: row.published ? _('ticket_comment_unpublish') : _('ticket_comment_publish'),handler: this.publishComment});
		m.push('-');
		m.push({text: row.deleted ? _('ticket_comment_undelete') : _('ticket_comment_delete'),handler: this.deleteComment});
		m.push({text: _('ticket_comment_remove'),handler: this.removeComment});
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
		Ext.getCmp('tickets-comment-search').reset();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,updateComment: function(btn, e, row) {
		if (typeof(row) != 'undefined') {var record = row.data;}
		else {var record = this.menu.record;}
		MODx.Ajax.request({
			url: Tickets.config.connector_url
			,params: {
				action: 'mgr/comment/get'
				,id: record.id
			}
			,listeners: {
				'success': {fn:function(r) {
					var record = r.object;
					w = MODx.load({
						xtype: 'tickets-window-comment-update'
						,record: record
						,listeners: {
							'success': {fn:this.refresh,scope:this}
							,'hide': {fn:function() {this.getEl().remove()}}
						}
					});
					w.fp.getForm().reset();
					w.fp.getForm().setValues(record);
					w.show(e.target);
				},scope:this}
			}
		});
	}

	,deleteComment: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.Ajax.request({
			url: Tickets.config.connector_url
			,params: {
				action: 'mgr/comment/delete'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:function(r) {this.refresh();},scope:this}
			}
		})
	}

	,publishComment: function(btn,e) {
		if (!this.menu.record) return false;

		MODx.Ajax.request({
			url: Tickets.config.connector_url
			,params: {
				action: 'mgr/comment/publish'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:function(r) {this.refresh();},scope:this}
			}
		})
	}

	,removeComment: function() {
		MODx.msg.confirm({
			url: Tickets.config.connector_url
			,title: _('ticket_comment_remove')
			,text: _('ticket_comment_remove_confirm')
			,params: {
				action: 'mgr/comment/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:function(r) { this.refresh(); },scope:this}
			}
		});
	}

	,viewComment: function(btn,e) {
		if (!this.menu.record) return false;
		if (this.menu.record.comment_url) {
			var url = this.menu.record.comment_url
			window.open(url);
		}

	}

	,renderResourceLink: function(val,cell,row) {
		if (row.data.resource_url) {
			return '<a href="' + row.data.resource_url+ '" target="_blank" class="resource-link">' + val + '</a>'
		}
		else {
			return '';
		}
	}

	,renderUserLink: function(val,cell,row) {
		if (row.data.createdby) {
			var updateUser = MODx.action ? MODx.action['security/user/update'] : 'security/user/update';
			var url = 'index.php?a='+updateUser+'&id='+row.data.createdby;

			return '<a href="' + url + '" target="_blank" class="resource-link">' + val + '</a>'
		}
		else {
			return val;
		}
	}

});
Ext.reg('tickets-grid-comments',Tickets.grid.Comments);


Tickets.window.UpdateComment = function(config) {
	config = config || {};
	this.ident = config.ident || 'meucomment'+Ext.id();
	Ext.applyIf(config,{
		title: _('tickets_comment_update')
		,id: this.ident
		,width: 700
		,height: 550
		,url: Tickets.config.connector_url
		,action: 'mgr/comment/update'
		,layout: 'anchor'
		,autoHeight: false
		,fields: [
			{xtype: 'hidden',name: 'id',id: 'tickets-'+this.ident+'-id'}
			,{xtype: 'textarea',fieldLabel: _('comment'),name: 'text',id: 'tickets-'+this.ident+'-text',anchor: '99% -180'}
			,{
			items: [{
				layout: 'form'
				,cls: 'modx-panel'
				,items: [{
					layout: 'column'
					,border: false
					,items: [{
						columnWidth: .5
						,border: false
						,layout: 'form'
						,items: this.getLeftFields(config)
					},{
						columnWidth: .5
						,border: false
						,layout: 'form'
						,items: this.getRightFields(config)
					}]
				}]
			}]
		}]
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: this.submit,scope: this}]
	});
	Tickets.window.UpdateComment.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.window.UpdateComment,MODx.Window,{

	getLeftFields: function(config) {
		return [
			{xtype:'textfield', fieldLabel: _('ticket_comment_name'), name: 'name', id:'tickets-'+this.ident+'-name',anchor: '99%',hidden: config.record.createdby ? 1 : 0}
			,{xtype:'numberfield', fieldLabel: _('ticket_comment_parent'), name: 'parent', id:'tickets-'+this.ident+'-parent',anchor: '50%'}
			,{xtype:'tickets-combo-thread', fieldLabel: _('ticket_thread'), name: 'thread', id:'tickets-'+this.ident+'-thread',anchor: '75%'}
		];
	}

	,getRightFields: function(config) {
		return [
			{xtype:'textfield', fieldLabel: _('ticket_comment_email'), name: 'email', id:'tickets-'+this.ident+'-email',anchor: '99%',hidden: config.record.createdby ? 1 : 0}
			,{
				layout: 'column'
				,border: false
				,items: [{
					columnWidth: .5
					,border: false
					,layout: 'form'
					,items: [
						{xtype:'displayfield', fieldLabel: _('ticket_comment_createdon'), name: 'createdon', id:'tickets-'+this.ident+'-createdon',anchor: '99%'}
						//{xtype:'xcheckbox', fieldLabel: _('ticket_comment_deleted'), name: 'deleted', id:'tickets-'+this.ident+'-deleted',anchor: '99%'}
						,{xtype:'displayfield', fieldLabel: 'IP', name: 'ip', id:'tickets-'+this.ident+'-ip',anchor: '99%'}
					]
				},{
					columnWidth: .5
					,border: false
					,layout: 'form'
					,items: [
						{xtype:'displayfield', fieldLabel: _('ticket_comment_editedon'), name: 'editedon', id:'tickets-'+this.ident+'-editedon',anchor: '99%'}
						//,{xtype:'displayfield', fieldLabel: _('ticket_comment_deletedon'), name: 'deletedon', id:'tickets-'+this.ident+'-deletedon',anchor: '99%', hidden: config.record.deleted ? 0 : 1}
					]
				}]
			}
		];
	}

});
Ext.reg('tickets-window-comment-update',Tickets.window.UpdateComment);