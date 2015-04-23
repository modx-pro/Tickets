Tickets.page.UpdateTicket = function(config) {
	config = config || {record: {}};
	config.record = config.record || {};
	Ext.applyIf(config, {
		panelXType: 'modx-panel-ticket'
	});
	config.canDuplicate = false;
	config.canDelete = false;
	Tickets.page.UpdateTicket.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.page.UpdateTicket, MODx.page.UpdateResource);
Ext.reg('tickets-page-ticket-update', Tickets.page.UpdateTicket);