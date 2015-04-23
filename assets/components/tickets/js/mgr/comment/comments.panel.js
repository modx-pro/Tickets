Tickets.panel.Comments = function(config) {
	config = config || {};

	Ext.applyIf(config, {
		layout: 'anchor',
		border: false,
		anchor: '100%',
		items: [{
			xtype: 'tickets-grid-comments',
			cls: 'main-wrapper',
			section: config.section || 0,
			parents: config.parents || 0,
			threads: config.threads || 0,
		}],
		cls: MODx.modx23 ? 'modx23' : 'modx22',
	});
	Tickets.panel.Comments.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.panel.Comments, MODx.Panel);
Ext.reg('tickets-panel-comments', Tickets.panel.Comments);