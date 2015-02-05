Tickets.page.CreateTicketsSection = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'tickets-panel-section'
	});
	config.canDuplicate = false;
	config.canDelete = false;
	Tickets.page.CreateTicketsSection.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.page.CreateTicketsSection,MODx.page.CreateResource,{});
Ext.reg('tickets-page-section-create',Tickets.page.CreateTicketsSection);





Tickets.panel.Section = function(config) {
	config = config || {};
	Tickets.panel.Section.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.panel.Section,MODx.panel.Resource,{

	getFields: function(config) {
		var it = [];
		it.push({
			title: _('tickets_section')
			,id: 'modx-resource-settings'
			,cls: 'modx-resource-tab'
			,layout: 'form'
			,labelAlign: 'top'
			,labelSeparator: ''
			,bodyCssClass: 'tab-panel-wrapper main-wrapper'
			,autoHeight: true
			,defaults: {
				border: false
				,msgTarget: 'side'
				,width: 400
			}
			,items: this.getMainFields(config)
		});
		it.push({
			title: _('tickets_section_settings')
			,id: 'modx-tickets-template'
			,cls: 'modx-resource-tab'
			,bodyCssClass: 'tab-panel-wrapper form-with-labels'
			,autoHeight: true
			//,layout: 'form'
			,labelAlign: 'top'
			,labelSeparator: ''
			,items: this.getSectionSettings(config)
		});
		if (config.show_tvs && MODx.config.tvs_below_content != 1) {
			it.push(this.getTemplateVariablesPanel(config));
		}
		if (MODx.perm.resourcegroup_resource_list == 1) {
			it.push(this.getAccessPermissionsTab(config));
		}
		var its = [];
		its.push(this.getPageHeader(config),{
			id:'modx-resource-tabs'
			,xtype: 'modx-tabs'
			,forceLayout: true
			,deferredRender: false
			,collapsible: true
			,itemId: 'tabs'
			,stateful: true
			,stateId: 'tickets-section-new-tabpanel'
			,stateEvents: ['tabchange']
			,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: it
		});
		if (MODx.config.tvs_below_content == 1) {
			var tvs = this.getTemplateVariablesPanel(config);
			tvs.style += ';margin-top: 10px;';
			its.push(tvs);
		}
		return its;
	}

	,getPageHeader: function(config) {
		config = config || {record:{}};
		return {
			html: '<h2>'+_('tickets_section_new')+'</h2>'
			,id: 'modx-resource-header'
			,cls: 'modx-page-header'
			,border: false
			,forceLayout: true
			,anchor: '100%'
		};
	}

	,getSectionSettings: function(config) {
		return [{
			xtype: 'tickets-section-tab-settings'
			,record: config.record
		}];
	}

	,getMainFields: function(config) {
		var fields = MODx.panel.Resource.prototype.getMainFields.apply(this, [config]);
		fields.push({xtype: 'hidden',name: 'class_key',id: 'modx-resource-class-key',value: 'TicketsSection'});
		fields.push({xtype: 'hidden',name: 'content_type',id: 'modx-resource-content-type', value: MODx.config.default_content_type || 1});

		return fields;
	}
});
Ext.reg('tickets-panel-section',Tickets.panel.Section);
