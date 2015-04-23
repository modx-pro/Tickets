Tickets.page.CreateTicket = function(config) {
	config = config || {record: {}};
	config.record = config.record || {};
	Ext.applyIf(config, {
		panelXType: 'modx-panel-ticket',
	});
	config.canDuplicate = false;
	config.canDelete = false;
	Tickets.page.CreateTicket.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.page.CreateTicket, MODx.page.CreateResource);
Ext.reg('tickets-page-ticket-create', Tickets.page.CreateTicket);