Tickets.combo.User = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		name: 'user',
		fieldLabel: config.name || 'createdby',
		hiddenName: config.name || 'createdby',
		displayField: 'username',
		valueField: 'id',
		anchor: '99%',
		fields: ['username', 'id', 'fullname'],
		pageSize: 20,
		url: MODx.modx23
			? MODx.config.connector_url
			: MODx.config.connectors_url + 'security/user.php',
		typeAhead: false,
		editable: true,
		allowBlank: false,
		baseParams: {
			action: MODx.modx23
				? 'security/user/getlist'
				: 'getlist',
			combo: 1,
			id: config.value
		},
		tpl: new Ext.XTemplate('\
			<tpl for=".">\
				<div class="x-combo-list-item tickets-list-item">\
					<span>\
						<small>({id})</small>\
						<b>{username}</b>\
						<tpl if="fullname"> - {fullname}</tpl>\
					</span>\
				</div>\
			</tpl>',
			{compiled: true}
		),
	});
	Tickets.combo.User.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.combo.User, MODx.combo.ComboBox);
Ext.reg('tickets-combo-user', Tickets.combo.User);


MODx.combo.TicketsSection = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		fieldLabel: _('resource_parent'),
		description: '<b>[[*parent]]</b><br />' + _('resource_parent_help'),
		fields: ['id', 'pagetitle', 'parents'],
		valueField: 'id',
		displayField: 'pagetitle',
		name: 'parent-cmb',
		hiddenName: 'parent-cmp',
		url: Tickets.config.connector_url,
		baseParams: {
			action: 'mgr/section/getlist',
			combo: 1,
			id: config.value
		},
		pageSize: 10,
		width: 300,
		typeAhead: false,
		editable: true,
		allowBlank: false,
		tpl: new Ext.XTemplate('\
			<tpl for=".">\
				<div class="x-combo-list-item tickets-list-item">\
					<tpl if="parents">\
						<span class="parents">\
							<tpl for="parents">\
								<nobr>{pagetitle} / </nobr>\
							</tpl>\
						</span>\
					</tpl>\
					<span>\
						<small>({id})</small>\
						<b>{pagetitle}</b>\
					</span>\
				</div>\
			</tpl>',
			{compiled: true}
		),
	});
	MODx.combo.TicketsSection.superclass.constructor.call(this, config);
};
Ext.extend(MODx.combo.TicketsSection, MODx.combo.ComboBox);
Ext.reg('tickets-combo-section', MODx.combo.TicketsSection);

/*
Tickets.combo.PublishStatus = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		store: [[1, _('published')], [0, _('unpublished')]],
		name: 'published',
		hiddenName: 'published',
		triggerAction: 'all',
		editable: false,
		selectOnFocus: false,
		preventRender: true,
		forceSelection: true,
		enableKeyEvents: true
	});
	Tickets.combo.PublishStatus.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.combo.PublishStatus, MODx.combo.ComboBox);
Ext.reg('tickets-combo-publish-status', Tickets.combo.PublishStatus);


Tickets.combo.FilterStatus = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		store: [['', _('ticket_all')], ['published', _('published')], ['unpublished', _('unpublished')], ['deleted', _('deleted')]],
		name: 'filter',
		hiddenName: 'filter',
		triggerAction: 'all',
		editable: false,
		selectOnFocus: false,
		preventRender: true,
		forceSelection: true,
		enableKeyEvents: true,
		emptyText: _('select')
	});
	Tickets.combo.FilterStatus.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.combo.FilterStatus,MODx.combo.ComboBox);
Ext.reg('tickets-combo-filter-status',Tickets.combo.FilterStatus);
*/

Tickets.combo.TicketThread = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		fieldLabel: _('ticket_thread'),
		fields: ['id', 'name', 'pagetitle'],
		valueField: 'id',
		displayField: 'name',
		name: 'thread',
		hiddenName: 'thread',
		url: Tickets.config.connector_url,
		baseParams: {
			action: 'mgr/thread/getlist',
			combo: 1,
			id: config.value
		},
		pageSize: 10,
		width: 300,
		typeAhead: false,
		editable: true,
		allowBlank: false,
		tpl: new Ext.XTemplate('\
			<tpl for=".">\
				<div class="x-combo-list-item tickets-list-item">\
					<span>\
						<small>({id})</small>\
						<b>{name}</b> - {pagetitle}\
					</span>\
				</div>\
			</tpl>',
			{compiled: true}
		),
	});
	Tickets.combo.TicketThread.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.combo.TicketThread, MODx.combo.ComboBox);
Ext.reg('tickets-combo-thread', Tickets.combo.TicketThread);


Tickets.combo.Template = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		name: 'properties[tickets][template]',
		hiddenName: 'properties[tickets][template]',
		url: MODx.modx23
			? MODx.config.connector_url
			: MODx.config.connectors_url + 'element/template.php',
		baseParams: {
			action: MODx.modx23
				? 'element/template/getlist'
				: 'getlist',
			combo: 1,
		}
	});
	Tickets.combo.Template.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.combo.Template, MODx.combo.Template);
Ext.reg('tickets-children-combo-template', Tickets.combo.Template);


Tickets.combo.Search = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		xtype: 'twintrigger',
		ctCls: 'x-field-search',
		allowBlank: true,
		msgTarget: 'under',
		emptyText: _('search'),
		name: 'query',
		triggerAction: 'all',
		clearBtnCls: 'x-field-search-clear',
		searchBtnCls: 'x-field-search-go',
		onTrigger1Click: this._triggerSearch,
		onTrigger2Click: this._triggerClear,
	});
	Tickets.combo.Search.superclass.constructor.call(this, config);
	this.on('render', function() {
		this.getEl().addKeyListener(Ext.EventObject.ENTER, function() {
			this._triggerSearch();
		}, this);
	});
	this.addEvents('clear', 'search');
};
Ext.extend(Tickets.combo.Search, Ext.form.TwinTriggerField, {

	initComponent: function() {
		Ext.form.TwinTriggerField.superclass.initComponent.call(this);
		this.triggerConfig = {
			tag: 'span',
			cls: 'x-field-search-btns',
			cn: [
				{tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
				{tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls}
			]
		};
	},

	_triggerSearch: function() {
		this.fireEvent('search', this);
	},

	_triggerClear: function() {
		this.fireEvent('clear', this);
	},

});
Ext.reg('tickets-field-search', Tickets.combo.Search);