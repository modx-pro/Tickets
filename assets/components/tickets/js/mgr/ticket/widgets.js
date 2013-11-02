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
		,url: MODx.config.connectors_url + 'security/user.php'
		,typeAhead: true
		,editable: true
		,action: 'getList'
		,allowBlank: true
		,baseParams: {
			action: 'getlist'
			,combo: 1
			,id: config.value
		}
		,tpl: new Ext.XTemplate(''
			+'<tpl for="."><div class="tickets-user-list-item">'
				+'<span><small>({id})</small> <b>{username}</b> ({fullname})</span>'
			+'</div></tpl>',{
			compiled: true
		})
		,itemSelector: 'div.tickets-user-list-item'
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
			+'<tpl for="."><div class="tickets-section-list-item">'
				+'<tpl if="parents">'
					+'<span class="parents">'
						+'<tpl for="parents">'
							+'<nobr><small>{pagetitle} / </small></nobr>'
						+'</tpl>'
					+'</span>'
				+'</tpl>'
				+'<span><small>({id})</small> <b>{pagetitle}</b></span>'
			+'</div></tpl>',{
			compiled: true
		})
		,itemSelector: 'div.tickets-section-list-item'
		,pageSize: 20
		,width: 300
		,editable: true
	});
	MODx.combo.TicketsSection.superclass.constructor.call(this,config);
};
Ext.extend(MODx.combo.TicketsSection,MODx.combo.ComboBox);
Ext.reg('tickets-combo-section',MODx.combo.TicketsSection);