Tickets.page.UpdateTicketsSection = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'tickets-panel-section'
		,actions: {
			'new': MODx.action ? MODx.action['resource/create'] : 'resource/create'
			,edit: MODx.action ? MODx.action['resource/update'] : 'resource/update'
			,preview: MODx.action ? MODx.action['resource/preview'] : 'resource/preview'
		}
	});
	config.canDuplicate = false;
	config.canDelete = false;
	Tickets.page.UpdateTicketsSection.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.page.UpdateTicketsSection,MODx.page.UpdateResource);
Ext.reg('tickets-page-section-update',Tickets.page.UpdateTicketsSection);



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
			 title: _('settings')
			 ,id: 'modx-tickets-template'
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
		 	,items: this.getTemplateSettings(config)
		 });
		 it.push({
			 title: _('comments')
			 ,id: 'modx-tickets-comments'
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
		 	,items: this.getComments(config)
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
			,stateId: 'tickets-section-upd-tabpanel'
			,stateEvents: ['tabchange']
			,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: it
		});

		var ct = this.getTickets(config);
		if (ct) {
			its.push(Tickets.PanelSpacer);
			its.push(ct);
			its.push(Tickets.PanelSpacer);
		}
		if (MODx.config.tvs_below_content == 1) {
			var tvs = this.getTemplateVariablesPanel(config);
			tvs.style = 'margin-top: 10px';
			its.push(tvs);
		}
		return its;
	}

	,getTickets: function(config) {
		return [{
			xtype: 'tickets-grid-section'
			,resource: config.resource
			,border: false
		}];
	}

	 ,getTemplateSettings: function(config) {
		 return [{
		 	xtype: 'tickets-tab-template-settings'
		 	,record: config.record
		 }];
	 }

	 ,getComments: function(config) {
		 return [{
		 	xtype: 'tickets-tab-comments'
		 	,record: config.record
			,layout: 'form'
		 }];
	 }

});
Ext.reg('tickets-panel-section',Tickets.panel.Section);
