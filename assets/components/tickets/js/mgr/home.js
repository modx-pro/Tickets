Tickets.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'tickets-panel-home',
			renderTo: 'tickets-panel-home-div',
			baseCls: 'tickets-formpanel',
		}]
	});
	Tickets.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.page.Home, MODx.Component);
Ext.reg('tickets-page-home', Tickets.page.Home);


Tickets.panel.Home = function(config) {
	config = config || {};
	Ext.apply(config, {
		border: false,
		items: [{
			html: '<h2>' + _('tickets') + '</h2>',
			border: false,
			cls: 'modx-page-header container',
		}, {
			xtype: 'modx-tabs',
			id: 'tickets-home-tabs',
			defaults: {border: false , autoHeight: true},
			border: true,
			stateful: true,
			stateId: 'tickets-home-panel',
			stateEvents: ['tabchange'],
			getState: function() {
				return {
					activeTab: this.items.indexOf(this.getActiveTab())
				};
			},
			hideMode: 'offsets',
			items: [{
				title: _('comments'),
				layout: 'anchor',
				items: [{
					html: _('ticket_comments_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'tickets-panel-comments',
					preventRender: true,
				}]
			}, {
				title: _('threads'),
				layout: 'anchor',
				items: [{
					html: _('ticket_threads_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'tickets-panel-threads',
					preventRender: true,
				}]
			}, {
				title: _('tickets'),
				layout: 'anchor',
				items: [{
					html: _('ticket_tickets_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'tickets-panel-tickets',
					preventRender: true,
				}]
			}, {
				title: _('authors'),
				layout: 'anchor',
				items: [{
					html: _('ticket_authors_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'tickets-panel-authors',
					preventRender: true,
				}]
			}]
		}]
	});
	Tickets.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.panel.Home,MODx.Panel);
Ext.reg('tickets-panel-home',Tickets.panel.Home);