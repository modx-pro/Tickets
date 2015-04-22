Tickets.window.Thread = function(config) {
	config = config || {};

	Ext.applyIf(config, {
		title: _('tickets_thread'),
		url: Tickets.config.connector_url,
		items: this.getItems(config),
		buttons: this.getButtons(config),
		width: 700,
		layout: 'anchor',
		autoHeight: true,
		cls: 'tickets-window ' + (MODx.modx23 ? 'modx23' : 'modx22'),
	});
	Tickets.window.Thread.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.window.Thread, MODx.Window, {

	getItems: function(config) {
		return [{
			xtype: 'tickets-grid-comments',
			section: config.section,
			parents: config.parents,
			threads: config.threads,
			pageSize: 5,
			cls: !MODx.modx23 ? 'main-wrapper' : '',
		}];
	},

	getButtons: function(config) {
		return [{
			text: _('close'),
			scope: this,
			handler: function() {
				config.closeAction !== 'close'
					? this.hide()
					: this.close();
			}
		}]
	},

});
Ext.reg('tickets-window-thread', Tickets.window.Thread);