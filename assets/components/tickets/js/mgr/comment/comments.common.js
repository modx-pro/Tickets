Tickets.panel.SectionTemplateSettings = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'tickets-panel-container-comments'
		,layout: 'column'
		,border: false
		,anchor: '100%'
		,defaults: {
			layout: 'form'
			,labelAlign: 'top'
			,anchor: '100%'
			,border: false
			,labelSeparator: ''
		}
		,items: {
			xtype: 'tickets-grid-comments'
			,preventRender: true
			,section: config.section || 0
			,parents: config.parents || 0
		}
	});
	Tickets.panel.SectionTemplateSettings.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.panel.SectionTemplateSettings,MODx.Panel);
Ext.reg('tickets-tab-comments',Tickets.panel.SectionTemplateSettings);