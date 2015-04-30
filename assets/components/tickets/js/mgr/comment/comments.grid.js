Tickets.grid.Comments = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		url: Tickets.config.connector_url,
		baseParams: {
			action: 'mgr/comment/getlist',
			section: config.section,
			parents: config.parents,
			threads: config.threads,
		},
		fields: this.getFields(),
		columns: this.getColumns(config),
		tbar: this.getTopBar(config),
		listeners: this.getListeners(config),
		sm: new Ext.grid.CheckboxSelectionModel(),
		autoHeight: true,
		paging: true,
		remoteSort: true,
		viewConfig: {
			forceFit: true,
			enableRowBody: true,
			showPreview: true,
			getRowClass: function(rec, ri, p) {
				var cls = [];
				if (!rec.data.published) {
					cls.push('tickets-row-unpublished');
				}
				if (rec.data.deleted) {
					cls.push('tickets-row-deleted');
				}
				return cls.join(' ');
			},
		},
		stateful: true,
		stateId: 'tickets-comments-state',
	});
	Tickets.grid.Comments.superclass.constructor.call(this,config);
	this.getStore().sortInfo = {
		field: 'createdon',
		direction: 'DESC'
	};
};
Ext.extend(Tickets.grid.Comments,MODx.grid.Grid,{

	getFields: function(config) {
		return [
			'id', 'text', 'name', 'parent', 'email', 'ip', 'thread_name',
			'createdby', 'createdon', 'editedon', 'editedby', 'deletedon', 'deletedby',
			'published', 'deleted', 'resource', 'pagetitle', 'preview_url', 'actions',
		];
	},

	getColumns: function(config) {
		return [{
			header: _('id'),
			dataIndex: 'id',
			width: 35,
			sortable: true,
		},{
			header: _('ticket_comment_text'),
			dataIndex: 'text',
			width: 100,
			sortable: true
		},{
			header: _('ticket_comment_name'),
			dataIndex: 'name',
			sortable: true,
			width: 75,
			renderer: function(value, metaData, record) {
				return Tickets.utils.userLink(value, record['data']['createdby'])
			},
		},{
			header: _('ticket_comment_createdon'),
			dataIndex: 'createdon',
			width: 75,
			sortable: true,
			renderer: Tickets.utils.formatDate
		},{
			header: _('ticket'),
			dataIndex: 'pagetitle',
			width: 75,
			sortable: true,
			renderer: function(value, metaData, record) {
				return Tickets.utils.ticketLink(value, record['data']['resource'], true)
			},
			hidden: config.parents || config.threads ? 1 : 0
		},{
			header: _('ticket_comment_thread'),
			dataIndex: 'thread_name',
			width: 75,
			sortable: true,
			hidden: config.threads != '' && config.threads != 0,
		},{
			header: _('ticket_actions'),
			dataIndex: 'actions',
			renderer: Tickets.utils.renderActions,
			sortable: false,
			width: 75,
			id: 'actions'
		}];
	},

	getTopBar: function(config) {
		return ['->', {
			xtype: 'tickets-field-search',
			width: 250,
			listeners: {
				search: {fn: function(field) {
					this._doSearch(field);
				}, scope: this},
				clear: {fn: function(field) {
					field.setValue('');
					this._clearSearch();
				}, scope: this},
			}
		}];
	},

	getListeners: function(config) {
		return {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.editComment(grid, e, row);
			}
		};
	},

	getMenu: function (grid, rowIndex) {
		var ids = this._getSelectedIds();

		var row = grid.getStore().getAt(rowIndex);
		var menu = Tickets.utils.getMenu(row.data['actions'], this, ids);

		this.addContextMenuItem(menu);
	},

	onClick: function (e) {
		var elem = e.getTarget();
		if (elem.nodeName == 'BUTTON') {
			var row = this.getSelectionModel().getSelected();
			if (typeof(row) != 'undefined') {
				var action = elem.getAttribute('action');
				if (action == 'showMenu') {
					var ri = this.getStore().find('id', row.id);
					return this._showMenu(this, ri, e);
				}
				else if (typeof this[action] === 'function') {
					this.menu.record = row.data;
					return this[action](this, e);
				}
			}
		}
		return this.processEvent('click', e);
	},

	_doSearch: function (tf) {
		this.getStore().baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
	},

	_clearSearch: function() {
		this.getStore().baseParams.query = '';
		this.getBottomToolbar().changePage(1);
	},

	editComment: function(btn, e, row) {
		var record = typeof(row) != 'undefined'
			? row.data
			: this.menu.record;

		MODx.Ajax.request({
			url: Tickets.config.connector_url,
			params: {
				action: 'mgr/comment/get',
				id: record.id,
			},
			listeners: {
				success: {fn:function(r) {
					var record = r.object;
					var w = MODx.load({
						xtype: 'tickets-window-comment-update',
						record: record,
						listeners: {
							success: {
								fn: this.refresh,
								scope: this
							},
						},
					});
					w.fp.getForm().reset();
					w.fp.getForm().setValues(record);
					w.show(e.target);
				},scope:this}
			}
		});
	},

	viewComment: function(btn,e) {
		window.open(this.menu.record['preview_url'] + '#comment-' + this.menu.record['id']);
		return false;
	},

	commentAction: function(method) {
		var ids = this._getSelectedIds();
		if (!ids.length) {
			return false;
		}
		MODx.Ajax.request({
			url: Tickets.config.connector_url,
			params: {
				action: 'mgr/comment/multiple',
				method: method,
				ids: Ext.util.JSON.encode(ids),
			},
			listeners: {
				success: {
					fn: function () {
						this.refresh();
					}, scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					}, scope: this
				},
			}
		})
	},

	publishComment: function(btn,e) {
		this.commentAction('publish');
	},

	unpublishComment: function(btn,e) {
		this.commentAction('unpublish');
	},

	deleteComment: function(btn,e) {
		this.commentAction('delete');
	},

	undeleteComment: function(btn,e) {
		this.commentAction('undelete');
	},

	removeComment: function() {
		Ext.MessageBox.confirm(
			_('ticket_comment_remove'),
			_('ticket_comment_remove_confirm'),
			function(val) {
				if (val == 'yes') {
					this.commentAction('remove');
				}
			},
			this
		);
	},

	_getSelectedIds: function() {
		var ids = [];
		var selected = this.getSelectionModel().getSelections();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push(selected[i]['id']);
		}

		return ids;
	},

	// Grid onremove fix
	remove: function() {},

});
Ext.reg('tickets-grid-comments',Tickets.grid.Comments);