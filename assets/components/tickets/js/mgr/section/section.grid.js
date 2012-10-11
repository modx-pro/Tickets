Tickets.grid.Section = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'tickets-grid-section'
		,url: Tickets.connector_url
		,baseParams: {
			action: 'mgr/ticket/getlist'
		}
		,fields: ['id','pagetitle','longtitle']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: [
			{header: _('id'),dataIndex: 'id',width: 70}
			,{header: _('pagetitle'),dataIndex: 'pagetitle',width: 200}
			,{header: _('longtitle'),dataIndex: 'longtitle',width: 250}
		]
		/*,tbar: [{
			text: _('tickets.item_create')
			,handler: this.createItem
			,scope: this
		}]*/
	});
	Tickets.grid.Section.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.grid.Section,MODx.grid.Grid,{});
Ext.reg('tickets-grid-section',Tickets.grid.Section);