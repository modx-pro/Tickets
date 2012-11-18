var Tickets = function(config) {
	config = config || {};
	Tickets.superclass.constructor.call(this,config);
};
Ext.extend(Tickets,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}
});
Ext.reg('tickets',Tickets);

Tickets = new Tickets();

Tickets.PanelSpacer = { html: '<br />' ,border: false, cls: 'tickets-panel-spacer' };

Tickets.combo.PublishStatus = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		store: [[1,_('published')],[0,_('unpublished')]]
		,name: 'published'
		,hiddenName: 'published'
		,triggerAction: 'all'
		,editable: false
		,selectOnFocus: false
		,preventRender: true
		,forceSelection: true
		,enableKeyEvents: true
	});
	Tickets.combo.PublishStatus.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.combo.PublishStatus,MODx.combo.ComboBox);
Ext.reg('tickets-combo-publish-status',Tickets.combo.PublishStatus);

Tickets.combo.FilterStatus = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		store: [['',_('ticket_all')],['published',_('published')],['unpublished',_('unpublished')],['deleted',_('deleted')]]
		,name: 'filter'
		,hiddenName: 'filter'
		,triggerAction: 'all'
		,editable: false
		,selectOnFocus: false
		,preventRender: true
		,forceSelection: true
		,enableKeyEvents: true
		,emptyText: _('select')
	});
	Tickets.combo.FilterStatus.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.combo.FilterStatus,MODx.combo.ComboBox);
Ext.reg('tickets-combo-filter-status',Tickets.combo.FilterStatus);

Tickets.PanelSpacer = { html: '<br />' ,border: false, cls: 'tickets-panel-spacer' };