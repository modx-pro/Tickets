Tickets.combo.User = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		name: 'user'
		,fieldLabel: config.name || 'createdby'
		,hiddenName: config.name || 'createdby'
		,displayField: 'username'
		,valueField: 'id'
		,anchor: '99%'
		,fields: ['username','id','fullname']
		,pageSize: 20
		,url: MODx.modx23 ? MODx.config.connector_url : MODx.config.connectors_url + 'security/user.php'
		,typeAhead: true
		,editable: true
		,allowBlank: true
		,baseParams: {
			action: MODx.modx23 ? 'security/user/getlist' : 'getlist',
			combo: 1
			,id: config.value
		}
		,tpl: new Ext.XTemplate(''
			+'<tpl for="."><div class="x-combo-list-item tickets-list-item">'
				+'<span><small>({id})</small> <b>{username}</b> - {fullname}</span>'
			+'</div></tpl>',{
			compiled: true
		})
		,itemSelector: 'div.tickets-list-item'
	});
	Tickets.combo.User.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.combo.User,MODx.combo.ComboBox);
Ext.reg('tickets-combo-user',Tickets.combo.User);


MODx.combo.TicketsSection = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'tickets-combo-section'
		,fieldLabel: _('resource_parent')
		,description: '<b>[[*parent]]</b><br />'+_('resource_parent_help')
		,fields: ['id','pagetitle','parents']
		,valueField: 'id'
		,displayField: 'pagetitle'
		,name: 'parent-cmb'
		,hiddenName: 'parent-cmp'
		,allowBlank: false
		,url: Tickets.config.connector_url
		,baseParams: {
			action: 'mgr/section/getlist'
			,combo: 1
			,id: config.value
		}
		,tpl: new Ext.XTemplate(''
			+'<tpl for="."><div class="x-combo-list-item tickets-list-item">'
				+'<tpl if="parents">'
					+'<span class="parents">'
						+'<tpl for="parents">'
							+'<nobr>{pagetitle} / </nobr>'
						+'</tpl>'
					+'</span>'
				+'</tpl>'
			+'<span><small>({id})</small> <b>{pagetitle}</b></span>'
			+'</div></tpl>',{
			compiled: true
		})
		,itemSelector: 'div.tickets-list-item'
		,pageSize: 20
		,width: 300
		,editable: true
	});
	MODx.combo.TicketsSection.superclass.constructor.call(this,config);
};
Ext.extend(MODx.combo.TicketsSection,MODx.combo.ComboBox);
Ext.reg('tickets-combo-section',MODx.combo.TicketsSection);


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


Tickets.combo.TicketThread = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'tickets-combo-thread'
		,fieldLabel: _('ticket_thread')
		,fields: ['id','name','pagetitle']
		,valueField: 'id'
		,displayField: 'name'
		,name: 'thread'
		,hiddenName: 'thread'
		,allowBlank: false
		,url: Tickets.config.connector_url
		,baseParams: {
			action: 'mgr/thread/getlist'
			,combo: 1
			,id: config.value
		}
		,tpl: new Ext.XTemplate(''
			+'<tpl for="."><div class="x-combo-list-item tickets-list-item">'
				+'<span><small>({id})</small> <b>{name}</b> - {pagetitle}</span>'
			+'</div></tpl>',{
			compiled: true
		})
		,itemSelector: 'div.tickets-list-item'
		,pageSize: 20
		,width: 300
		,editable: true
	});
	Tickets.combo.TicketThread.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.combo.TicketThread,MODx.combo.ComboBox);
Ext.reg('tickets-combo-thread',Tickets.combo.TicketThread);


Tickets.combo.Template = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		name: 'properties[tickets][template]'
		,hiddenName: 'properties[tickets][template]'
		,url: MODx.modx23 ? MODx.config.connector_url : MODx.config.connectors_url + 'element/template.php'
		,baseParams: {
			action: MODx.modx23 ? 'element/template/getlist' : 'getlist'
			,combo: 1
		}
	});
	Tickets.combo.Template.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.combo.Template,MODx.combo.Template);
Ext.reg('tickets-children-combo-template',Tickets.combo.Template);