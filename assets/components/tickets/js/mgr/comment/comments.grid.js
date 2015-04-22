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