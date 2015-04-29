Tickets.panel.SectionSettings = function(config) {
	config = config || {};

	Ext.applyIf(config, {
		anchor: '100%',
		autoWidth: true,
		border: false,
		deferredRender: false,
		forceLayout: true,
		stateful: true,
		stateId: 'tickets-section-settings-tabs',
		stateEvents: ['tabchange'],
		getState: function() {
			return {activeTab: this.items.indexOf(this.getActiveTab())};
		},
		items: this.getTabs(config),
		bodyCssClass: 'tickets-section-settings ' + (MODx.modx23 ? 'modx23' : 'modx22')
	});
	Tickets.panel.SectionSettings.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.panel.SectionSettings, MODx.VerticalTabs, {

	getTabs: function(config) {
		config.listeners = {
			change: {fn: MODx.fireResourceFormChange},
			select: {fn: MODx.fireResourceFormChange},
			keydown: {fn: MODx.fireResourceFormChange},
			check: {fn: MODx.fireResourceFormChange},
			uncheck: {fn: MODx.fireResourceFormChange}
		};

		var tabs = [{
			title: _('tickets_section_tab_main'),
			hideMode: 'offsets',
			anchor: '100%',
			layout: 'form',
			defaults: {
				layout: 'form',
				labelAlign: 'top',
				anchor: '100%',
				border: false,
				labelSeparator: ''
			},
			items: this.getMainFields(config)
		}];

		tabs.push({
			title: _('tickets_section_tab_tickets'),
			hideMode: 'offsets',
			anchor: '100%',
			layout: 'form',
			defaults: {
				layout: 'form',
				labelAlign: 'top',
				anchor: '100%',
				border: false,
				labelSeparator: ''
			},
			items: this.getTicketsSettings(config)
		});

		tabs.push({
			title: _('tickets_section_tab_ratings'),
			hideMode: 'offsets',
			anchor: '100%',
			layout: 'form',
			defaults: {
				layout: 'form',
				labelAlign: 'top',
				anchor: '100%',
				border: false,
				labelSeparator: ''
			},
			items: this.getTicketsRatings(config)
		});

		return tabs;
	},

	getMainFields: function(config) {
		return [this.getContentField(config), {
			layout: 'column',
			defaults: {
				layout: 'form',
				labelAlign: 'top',
				anchor: '100%',
				border: false,
				labelSeparator: ''
			},
			items: [{
				columnWidth: .5,
				items: this.getMainDates(config)
			}, {
				columnWidth: .5,
				items: this.getMainCheckboxes(config)
			}]
		},{
			name: 'content_type',
			xtype: 'hidden',
			id: 'modx-resource-content-type-hidden',
			value: config.record.content_type || (MODx.config['default_content_type'] || 1)
		},{
			name: 'class_key',
			xtype: 'hidden',
			id: 'modx-resource-class-key-hidden',
			value: 'TicketsSection'
		}];
	},

	getContentField: function(config) {
		return [{
			xtype: 'textarea',
			name: 'ta',
			id: 'ta',
			fieldLabel: _('content'),
			anchor: '100%',
			height: 200,
			grow: false,
			value: config.record && config.record.content
				? config.record.content
				: MODx.config['tickets.section_content_default'],
			listeners: config.listeners
		}];
	},

	getMainDates: function(config) {
		return [{
			xtype: 'xdatetime',
			fieldLabel: _('resource_publishedon'),
			description: '<b>[[*publishedon]]</b><br />' + _('resource_publishedon_help'),
			name: 'publishedon',
			id: 'modx-resource-publishedon',
			allowBlank: true,
			dateFormat: MODx.config.manager_date_format,
			timeFormat: MODx.config.manager_time_format,
			startDay: parseInt(MODx.config.manager_week_start),
			dateWidth: 120,
			timeWidth: 120,
			value: config.record.publishedon,
			listeners: config.listeners
		}, {
			xtype: MODx.config.publish_document ? 'xdatetime' : 'hidden',
			fieldLabel: _('resource_publishdate'),
			description: '<b>[[*pub_date]]</b><br />' + _('resource_publishdate_help'),
			name: 'pub_date',
			id: 'modx-resource-pub-date',
			allowBlank: true,
			dateFormat: MODx.config.manager_date_format,
			timeFormat: MODx.config.manager_time_format,
			startDay: parseInt(MODx.config.manager_week_start),
			dateWidth: 120,
			timeWidth: 120,
			value: config.record.pub_date,
			listeners: config.listeners
		}, {
			xtype: MODx.config.publish_document ? 'xdatetime' : 'hidden',
			fieldLabel: _('resource_unpublishdate'),
			description: '<b>[[*unpub_date]]</b><br />' + _('resource_unpublishdate_help'),
			name: 'unpub_date',
			id: 'modx-resource-unpub-date',
			allowBlank: true,
			dateFormat: MODx.config.manager_date_format,
			timeFormat: MODx.config.manager_time_format,
			startDay: parseInt(MODx.config.manager_week_start),
			dateWidth: 120,
			timeWidth: 120,
			value: config.record.unpub_date,
			listeners: config.listeners
		}];
	},

	getMainCheckboxes: function(config) {
		var items = [];

		var tmp = {
			isfolder: {boxLabel: _('resource_folder'), description: _('resource_folder_help'), disabled: 1},
			cacheable: {},
			searchable: {},
			deleted: {boxLabel: _('deleted')},
			syncsite: {},
			richtext: {},
			uri_override: {id: 'modx-resource-uri-override'}
		};

		for (var i in tmp) {
			if (tmp.hasOwnProperty(i)) {
				items.push(Ext.apply({
						xtype: 'xcheckbox',
						name: i,
						boxLabel: _('resource_' + i),
						description: '<b>[[*' + i + ']]</b><br/>' + _('resource_' + i + '_help'),
						id: 'modx-resource-' + i,
						inputValue: 1,
						hideLabel: true,
						checked: parseInt(config.record[i]),
						listeners: config.listeners
					},
					tmp[i]
				));
			}
		}

		var fields = [{
			xtype: 'checkboxgroup',
			columns: 2,
			items: items
		}];
		fields.push({
			xtype: 'textfield',
			name: 'uri',
			id: 'modx-resource-uri',
			fieldLabel: _('resource_uri'),
			description: '<b>[[*uri]]</b><br />' + _('resource_uri_help'),
			value: config.record.uri || '',
			hidden: !config.record.uri_override,
			anchor: '100%'
		});

		return fields;
	},

	getTicketsSettings: function(config) {
		var items = [{
			html: _('tickets_section_tab_tickets_intro'),
			border: false,
			bodyCssClass: 'panel-desc',
		}];

		var tmp = {
			template: {xtype: 'tickets-children-combo-template', anchor: '50%'},
			uri: {xtype: 'textfield', anchor: '75%'},
			show_in_tree: {},
			hidemenu: {},
			disable_jevix: {},
			process_tags: {}
		};

		for (var i in tmp) {
			if (tmp.hasOwnProperty(i)) {
				items.push(Ext.apply({
						xtype: 'modx-combo-boolean',
						name: 'properties[tickets][' + i + ']',
						hiddenName: 'properties[tickets][' + i + ']',
						fieldLabel: _('tickets_section_settings_' + i),
						id: 'tickets-settings-children-' + i,
						value: config.record.properties['tickets'][i],
						listeners: config.listeners,
						anchor: '25%'
					},
					tmp[i]
				));
				items.push({
					xtype: 'label',
					html: _('tickets_section_settings_' + i + '_desc'),
					cls: 'desc-under'
				});
			}
		}

		return items;
	},

	getTicketsRatings: function(config) {
		var items = [{
			html: _('tickets_section_tab_ratings_intro'),
			border: false,
			bodyCssClass: 'panel-desc',
		}];

		var tmp = {
			ticket: {},
			comment: {},
			view: {},
			vote_ticket: {},
			vote_comment: {},
			star_ticket: {},
			star_comment: {},
		};

		for (var i in tmp) {
			if (tmp.hasOwnProperty(i)) {
				items.push(Ext.apply({
						xtype: 'numberfield',
						name: 'properties[ratings][' + i + ']',
						hiddenName: 'properties[ratings][' + i + ']',
						fieldLabel: _('tickets_section_rating_' + i),
						id: 'tickets-settings-rating-' + i,
						value: config.record.properties['ratings'][i],
						listeners: config.listeners,
						anchor: '25%'
					},
					tmp[i]
				));
				items.push({
					xtype: 'label',
					html: _('tickets_section_rating_' + i + '_desc'),
					cls: 'desc-under'
				});
			}
		}

		return items;
	},

});
Ext.reg('tickets-section-tab-settings', Tickets.panel.SectionSettings);