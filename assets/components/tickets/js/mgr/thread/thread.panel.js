Tickets.panel.Home = function(config) {
	config = config || {};
	Ext.apply(config,{
		border: false
		,baseCls: 'tickets-formpanel'
		,items: [{
			html: '<h2>'+_('tickets')+'</h2>'
			,border: false
			,cls: 'modx-page-header container'
		},{
			xtype: 'modx-tabs'
			,id: 'tickets-home-tabs'
			,bodyStyle: 'padding: 10px'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,activeItem: 0
			,hideMode: 'offsets'
			,items: [{
				title: _('comments_all')
				,items: [{
					html: _('ticket_comment_all_intro')
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'tickets-grid-comments'
					,preventRender: true
					,section: 0
					,parents: 0
					,threads: 0
				}]
			},{
				title: _('threads')
				,items: [{
					html: _('ticket_thread_intro')
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'tickets-grid-threads'
					,preventRender: true
				}]
			}]
		}]
	});
	Tickets.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.panel.Home,MODx.Panel);
Ext.reg('tickets-panel-home',Tickets.panel.Home);