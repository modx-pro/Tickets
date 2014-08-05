Tickets.grid.Section = function(config) {
	config = config || {};
	this.exp = new Ext.grid.RowExpander({
		tpl : new Ext.Template(
			'<p class="desc">{content}</p>'
		)
	});
	Ext.applyIf(config,{
		id: 'tickets-grid-section'
		,url: Tickets.config.connector_url
		,baseParams: {
			action: 'mgr/ticket/getlist'
			,parent: config.resource
		}
		,fields: ['id','pagetitle',
			'publishedon','publishedon_date','publishedon_time',
			'uri','uri_override','preview_url',
			'createdby','createdby_username',
			'actions','action_edit','content','comments']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,bodyCssClass: 'grid-with-buttons'
		,cls: 'tickets-grid'
		,plugins: [this.exp]
		,columns: [this.exp
			,{header: _('ticket_publishedon'),dataIndex: 'publishedon',width: 50,sortable: true,renderer: {fn:this._renderPublished,scope:this}}
			,{header: _('ticket_pagetitle'),dataIndex: 'pagetitle',id: 'main',width: 200,sortable: true,renderer: {fn:this._renderPageTitle,scope:this}}
			,{header: _('ticket_author'),dataIndex: 'createdby_username',width: 150,sortable: true,renderer: {fn:this._renderAuthor,scope:this}}
			,{header: '<img src="'+Tickets.config.assets_url+'img/comments-icon-w.png" alt="" class="tickets-comments-col-header" />',dataIndex: 'comments',width: 50,sortable: true,renderer: {fn:this._renderComments,scope:this}}
		]
		,tbar: [{
			text: _('ticket_create')
			,handler: this.createTicket
			,scope: this
		},
			'->'
		,{
			xtype: 'textfield'
			,name: 'query'
			,width: 200
			,id: 'tickets-ticket-search'
			,emptyText: _('search')
			,listeners: {render: {fn: function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER, function() {this.search(tf);}, this);},scope: this}}
		},{
			xtype: 'button'
			,id: 'modx-filter-tickets-clear'
			,text: _('ticket_clear')
			,listeners: {
				'click': {fn: this.clearFilter, scope: this}
			}
		}]
	});
	Tickets.grid.Section.superclass.constructor.call(this,config);
	this._makeTemplates();
	//this.on('rowclick',MODx.fireResourceFormChange);
	//this.on('click', this.onClick, this);
};
Ext.extend(Tickets.grid.Section,MODx.grid.Grid,{

	_makeTemplates: function() {
		this.tplPublished = new Ext.XTemplate('<tpl for=".">'
			+'<div class="tickets-grid-date">{publishedon_date}<span class="tickets-grid-time">{publishedon_time}</span></div>'
			+'</tpl>',{
			compiled: true
		});
		this.tplComments = new Ext.XTemplate('<tpl for=".">'
			+'<div class="tickets-grid-comments"><span>{comments}</span></div>'
			+'</tpl>',{
			compiled: true
		});
		this.tplPageTitle = new Ext.XTemplate('<tpl for="."><div class="ticket-title-column">'
											+'<h3 class="main-column"><a href="{action_edit}" title="Edit {pagetitle}">{pagetitle}</a><span class="ticket-id">({id})</span></h3>'
												+'<tpl if="actions">'
				 									+'<ul class="actions">'
														+'<tpl for="actions">'
															+'<li><a href="#" class="controlBtn {className}">{text}</a></li>'
														+'</tpl>'
													+'</ul>'
												+'</tpl>'
											+'</div></tpl>',{
			compiled: true
		});
	}
	,_renderPublished:function(v,md,rec) {
		return this.tplPublished.apply(rec.data);
	}
	,_renderPageTitle:function(v,md,rec) {
		return this.tplPageTitle.apply(rec.data);
	}
	,_renderComments:function(v,md,rec) {
		return this.tplComments.apply(rec.data);
	}
	,onClick: function(e) {
		var t = e.getTarget();
		var elm = t.className.split(' ')[0];
		if (elm == 'controlBtn') {
			var action = t.className.split(' ')[1];
			this.menu.record = this.getSelectionModel().getSelected();
			switch (action) {
				case 'delete':
					this.deleteTicket();
					break;
				case 'undelete':
					this.undeleteTicket();
					break;
				case 'edit':
					this.editTicket();
					break;
				case 'publish':
					this.publishTicket();
					break;
				case 'unpublish':
					this.unpublishTicket();
					break;
				case 'view':
					this.viewTicket();
					break;
				case 'duplicate':
					this.duplicateTicket();
					break;
			}
		}
		this.processEvent('click', e);
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
		Ext.getCmp('tickets-ticket-search').reset();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,editTicket: function(btn,e) {
		location.href = 'index.php?a='+MODx.request.a+'&id='+this.menu.record.id;
	}

	,createTicket: function(btn,e) {
		var createPage = MODx.action ? MODx.action['resource/create'] : 'resource/create';
		location.href = 'index.php?a='+createPage+'&class_key=Ticket&parent='+MODx.request.id+'&context_key='+MODx.ctx;
	}

	,viewTicket: function(btn,e) {
		window.open(this.menu.record.data.preview_url);
		return false;
	}

	,deleteTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.modx23 ? MODx.config.connector_url : MODx.config.connectors_url + 'resource/index.php'
			,params: {
				action: MODx.modx23 ? 'resource/delete' : 'delete'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

	,undeleteTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.modx23 ? MODx.config.connector_url : MODx.config.connectors_url + 'resource/index.php'
			,params: {
				action: MODx.modx23 ? 'resource/undelete' : 'undelete'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

	,publishTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.modx23 ? MODx.config.connector_url : MODx.config.connectors_url + 'resource/index.php'
			,params: {
				action: MODx.modx23 ? 'resource/publish' : 'publish'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:this.refresh,scope:this}
			}
		});
	}

	,unpublishTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.modx23 ? MODx.config.connector_url : MODx.config.connectors_url + 'resource/index.php'
			,params: {
				action: MODx.modx23 ? 'resource/unpublish' : 'unpublish'
				,id: this.menu.record.id
			}
			,listeners: {
				'success':{fn:this.refresh,scope:this}
			}
		});
	}

	,duplicateTicket: function(btn,e) {
		var r = this.menu.record;
		var w = MODx.load({
			xtype: 'modx-window-resource-duplicate'
			,resource: r.id
			,hasChildren: 0
			,listeners: {
				'success': {fn:function() {this.refresh();},scope:this}
			}
		});
		w.config.hasChildren = 0;
		w.setValues(r.data);
		w.show();
	}

});
Ext.reg('tickets-grid-section',Tickets.grid.Section);