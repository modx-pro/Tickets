Tickets.grid.Authors = function(config) {
	config = config || {};

	Ext.applyIf(config, {
		url: Tickets.config.connector_url,
		baseParams: {
			action: 'mgr/author/getlist',
		},
		fields: this.getFields(),
		columns: this.getColumns(config),
		tbar: this.getTopBar(config),
		listeners: this.getListeners(config),
		autoHeight: true,
		paging: true,
		remoteSort: true,
		viewConfig: {
			forceFit: true,
			enableRowBody: true,
			showPreview: true,
			getRowClass: function(rec, ri, p) {
				var cls = [];
				if (rec.data.active != 1) {
					cls.push('tickets-row-unpublished');
				}
				if (rec.data.blocked == 1) {
					cls.push('tickets-row-deleted');
				}
				return cls.join(' ');
			},
		},
	});
	Tickets.grid.Authors.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.grid.Authors,MODx.grid.Grid,{

	getFields: function(config) {
		return [
			'id', 'fullname', 'createdon', 'visitedon', 'active', 'blocked',
			'rating', 'tickets', 'comments', 'views', 'stars', 'votes',
		];
	},

	getColumns: function (config) {
		return [{
			header: _('id'),
			dataIndex: 'id',
			width: 35,
			sortable: true,
		}, {
			header: _('ticket_author'),
			dataIndex: 'fullname',
			width: 100,
			sortable: true,
			renderer: function(value, metaData, record) {
				return Tickets.utils.userLink(value, record['data']['id'])
			},
		}, {
			header: _('ticket_author_createdon'),
			dataIndex: 'createdon',
			width: 75,
			sortable: true,
			renderer: Tickets.utils.formatDate,
		}, {
			header: _('ticket_author_visitedon'),
			dataIndex: 'visitedon',
			width: 75,
			sortable: true,
			renderer: Tickets.utils.formatDate,
		}, {
			header: _('ticket_author_rating'),
			dataIndex: 'rating',
			width: 50,
			sortable: true,
		}, {
			header: _('ticket_author_tickets'),
			dataIndex: 'tickets',
			width: 50,
			sortable: true,
		}, {
			header: _('ticket_author_comments'),
			dataIndex: 'comments',
			width: 50,
			sortable: true,
		}, {
			header: _('ticket_author_views'),
			dataIndex: 'views',
			width: 50,
			sortable: true,
		}, {
			header: _('ticket_author_stars'),
			dataIndex: 'stars',
			width: 50,
			sortable: true,
		}, {
			header: _('ticket_author_votes'),
			dataIndex: 'votes',
			width: 50,
			sortable: true,
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
		return {};
	},

	getMenu: function (grid, rowIndex) {
		var ids = this._getSelectedIds();

		var row = grid.getStore().getAt(rowIndex);
		var menu = Tickets.utils.getMenu(row.data['actions'], this, ids);

		this.addContextMenuItem(menu);
	},

	_doSearch: function (tf) {
		this.getStore().baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
	},

	_clearSearch: function() {
		this.getStore().baseParams.query = '';
		this.getBottomToolbar().changePage(1);
	},

});
Ext.reg('tickets-grid-authors', Tickets.grid.Authors);