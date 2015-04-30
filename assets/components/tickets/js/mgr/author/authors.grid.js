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
		stateful: true,
		stateId: 'tickets-authors-state',
	});
	Tickets.grid.Authors.superclass.constructor.call(this,config);
	this.getStore().sortInfo = {
		field: 'rating',
		direction: 'DESC'
	};
};
Ext.extend(Tickets.grid.Authors,MODx.grid.Grid,{

	getFields: function(config) {
		return [
			'id', 'fullname', 'createdon', 'visitedon', 'active', 'blocked',
			'rating', 'tickets', 'comments', 'views', 'stars',
			'votes_tickets', 'votes_comments', 'stars_tickets', 'stars_comments',
			'votes_tickets_up', 'votes_tickets_down', 'votes_comments_up', 'votes_comments_down',
		];
	},

	getColumns: function (config) {
		var columns = [{
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
		}];

		var add = {
			rating: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-star-half-o' : 'fa fa-star-half-o') + '">',
			},
			tickets: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-files-o' : 'fa fa-files-o') + '">',
			},
			comments: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-comments-o' : 'fa fa-comments-o') + '">',
			},
			views: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-eye' : 'fa fa-eye') + '">',
			},
			votes_tickets: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-thumbs-up' : 'fa fa-thumbs-up') + '"> ' +
					'<i class="' + (MODx.modx23 ? 'icon icon-file-o' : 'fa fa-file-o') + '">',
				renderer: {fn:function(value, metaData, record) {return this._renderRating(value, record, 'tickets')}, scope:this}
			},
			votes_comments: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-thumbs-up' : 'fa fa-thumbs-up') + '"> ' +
					'<i class="' + (MODx.modx23 ? 'icon icon-comment-o' : 'fa fa-comment-o') + '">',
				renderer: {fn:function(value, metaData, record) {return this._renderRating(value, record, 'comments')}, scope:this}
			},
			stars: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-star' : 'fa fa-star') + '">',
				renderer: {fn:function(value, metaData, record) {return this._renderStars(record)}, scope:this},
			},
			stars_tickets: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-star' : 'fa icon-star') + '"> ' +
					'<i class="' + (MODx.modx23 ? 'icon icon-file-o' : 'fa fa-file-o') + '">',
				hidden: true
			},
			stars_comments: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-star' : 'fa icon-star') + '"> ' +
					'<i class="' + (MODx.modx23 ? 'icon icon-comment-o' : 'fa fa-comment-o') + '">',
				hidden: true
			},
			votes_tickets_up: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-thumbs-up' : 'fa icon-thumbs-up') + '"> ' +
					'<i class="' + (MODx.modx23 ? 'icon icon-file-o' : 'fa fa-file-o') + '"> ' +
					'<i class="' + (MODx.modx23 ? 'icon icon-arrow-up' : 'fa fa-arrow-up') + '">',
				hidden: true
			},
			votes_tickets_down: {header: '<i class="' + (MODx.modx23 ? 'icon icon-thumbs-up' : 'fa icon-thumbs-up') + '"> ' +
			'<i class="' + (MODx.modx23 ? 'icon icon-file-o' : 'fa fa-file-o') + '"> ' +
			'<i class="' + (MODx.modx23 ? 'icon icon-arrow-down' : 'fa fa-arrow-down') + '">',
				hidden: true,},
			votes_comments_up: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-thumbs-up' : 'fa icon-thumbs-up') + '"> ' +
				'<i class="' + (MODx.modx23 ? 'icon icon-comment-o' : 'fa fa-comment-o') + '"> ' +
				'<i class="' + (MODx.modx23 ? 'icon icon-arrow-up' : 'fa fa-arrow-up') + '">',
				hidden: true,
			},
			votes_comments_down: {
				header: '<i class="' + (MODx.modx23 ? 'icon icon-thumbs-up' : 'fa icon-thumbs-up') + '"> ' +
				'<i class="' + (MODx.modx23 ? 'icon icon-comment-o' : 'fa fa-comment-o') + '"> ' +
				'<i class="' + (MODx.modx23 ? 'icon icon-arrow-down' : 'fa fa-arrow-down') + '">',
				hidden: true,
			},
		};
		for (var i in add) {
			if (!add.hasOwnProperty(i)) {
				continue;
			}
			columns.push(Ext.apply({
					header: _('ticket_author_' + i),
					tooltip: _('ticket_author_' + i),
					dataIndex: i,
					width: 35,
					sortable: true,
				},
				add[i]
			));
		}

		return columns;
	},

	getTopBar: function(config) {
		return [{
			text: (MODx.modx23
				? '<i class="icon icon-refresh"></i> '
				: '<i class="fa fa-refresh"></i> ')
			+ _('ticket_authors_rebuild'),
			handler: this.rebuildRating,
			scope: this,
		}, '->', {
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

	rebuildRating: function() {
		Ext.MessageBox.confirm(
			_('ticket_authors_rebuild'),
			_('ticket_authors_rebuild_confirm'),
			function(val) {
				if (val == 'yes') {
					this._rebuildRating(0);
				}
			},
			this
		);
	},

	_rebuildRating: function(start) {
		if (!this._wait) {
			this._wait = Ext.MessageBox.wait(
				_('ticket_authors_rebuild_wait'),
				_('please_wait')
			);
		}
		MODx.Ajax.request({
			url: Tickets.config.connector_url,
			params: {
				action: 'mgr/author/rebuild',
				start: start || 0,
			},
			listeners: {
				success: {
					fn: function(response) {
						if (response.object['total'] == response.object['processed']) {
							this._wait.hide();
							this._wait = null;
							this.refresh();
						}
						else {
							this._wait.updateText(
								_('ticket_authors_rebuild_wait_ext')
									.replace('[[+processed]]', response.object['processed'])
									.replace('[[+total]]', response.object['total'])
							);
							this._rebuildRating(response.object['processed']);
						}
					}, scope: this
				}
			}
		});
	},

	_renderRating: function(value, record, type) {
		var up = record.data['votes_' + type + '_up'];
		var down = record.data['votes_' + type + '_down'];

		return value || up || down
			? value + '<div><small title="' + _('ticket_author_rating_desc') + '">' +
				up + ' / ' + down + '</small></div>'
			: '-';
	},

	_renderStars: function(record) {
		var tickets = record.data['stars_tickets'];
		var comments = record.data['stars_comments'];

		return tickets || comments
			? '<div title="' + _('ticket_author_stars_desc') + '">' +
				tickets + ' / ' + comments + '</div>'
			: '-';
	},

	_wait: null,

});
Ext.reg('tickets-grid-authors', Tickets.grid.Authors);