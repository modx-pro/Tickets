Tickets.panel.Tickets = function(config) {
	config = config || {};
	if (typeof config.standalone == 'undefined') {
		config.standalone = true;
	}

	Ext.applyIf(config, {
		layout: 'anchor',
		border: false,
		anchor: '100%',
		items: [{
			xtype: 'tickets-grid-tickets',
			cls: 'main-wrapper',
			standalone: config.standalone,
			parent: config.parent || 0,
		}],
		cls: MODx.modx23 ? 'modx23' : 'modx22',
	});
	Tickets.panel.Tickets.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.panel.Tickets, MODx.Panel);
Ext.reg('tickets-panel-tickets', Tickets.panel.Tickets);