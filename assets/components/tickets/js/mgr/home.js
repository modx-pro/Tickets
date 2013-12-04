Tickets.page.Home = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		components: [{
			xtype: 'tickets-panel-home'
			,renderTo: 'tickets-panel-home-div'
		}]
	});
	Tickets.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.page.Home,MODx.Component);
Ext.reg('tickets-page-home',Tickets.page.Home);