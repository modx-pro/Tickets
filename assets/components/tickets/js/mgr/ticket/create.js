Tickets.page.CreateTicket = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'modx-panel-ticket'
	});
	config.canDuplicate = false;
	config.canDelete = false;
	Tickets.page.CreateTicket.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.page.CreateTicket,MODx.page.CreateResource,{

});
Ext.reg('tickets-page-ticket-create',Tickets.page.CreateTicket);

Tickets.panel.Ticket = function(config) {
	config = config || {};
	Ext.applyIf(config,{
	});
	Tickets.panel.Ticket.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.panel.Ticket,MODx.panel.Resource,{
	getFields: function(config) {
		var it = [];
		it.push({
			title: _('ticket')
			,id: 'modx-resource-settings'
			,cls: 'modx-resource-tab'
			,layout: 'form'
			,labelAlign: 'top'
			,labelSeparator: ''
			,bodyCssClass: 'tab-panel-wrapper main-wrapper'
			,autoHeight: true
			,defaults: {
				border: false
				,msgTarget: 'under'
				,width: 400
			}
			,items: this.getMainFields(config)
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
			,stateId: 'tickets-ticket-new-tabpanel'
			,stateEvents: ['tabchange']
			,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: it
		});

		if (MODx.config.tvs_below_content == 1) {
			var tvs = this.getTemplateVariablesPanel(config);
			tvs.style = 'margin-top: 10px';
			its.push(tvs);
		}
		return its;
	}

	,getMainLeftFields: function(config) {
		config = config || {record:{}};
		var mlf = [{
			xtype: 'textfield'
			,fieldLabel: _('resource_pagetitle')+'<span class="required">*</span>'
			,description: '<b>[[*pagetitle]]</b><br />'+_('resource_pagetitle_help')
			,name: 'pagetitle'
			,id: 'modx-resource-pagetitle'
			,maxLength: 255
			,anchor: '100%'
			,allowBlank: false
			,enableKeyEvents: true
			,listeners: {
				'keyup': {scope:this,fn:function(f,e) {
					var title = Ext.util.Format.stripTags(f.getValue());
					Ext.getCmp('modx-resource-header').getEl().update('<h2>'+title+'</h2>');
				}}
			}
		}];

		mlf.push({
			xtype: 'textfield'
			,fieldLabel: _('resource_longtitle')
			,description: '<b>[[*longtitle]]</b><br />'+_('resource_longtitle_help')
			,name: 'longtitle'
			,id: 'modx-resource-longtitle'
			,anchor: '100%'
			,value: config.record.longtitle || ''
		});

		mlf.push({
			xtype: 'textarea'
			,fieldLabel: _('resource_summary')
			,description: '<b>[[*introtext]]</b><br />'+_('resource_summary_help')
			,name: 'introtext'
			,id: 'modx-resource-introtext'
			,anchor: '100%'
			,value: config.record.introtext || ''
		});

		var ct = this.getContentField(config);
		if (ct) {
			mlf.push(ct);
		}
		return mlf;
	}

	,getContentField: function(config) {
		return [{
			id: 'modx-content-above'
			,border: false
		},{
			xtype: 'textarea'
			,fieldLabel: _('content')
			,name: 'ta'
			,id: 'ta'
			,anchor: '100%'
			,height: 400
			,grow: false
			,value: (config.record.content || config.record.ta) || ''
		},{
			id: 'modx-content-below'
			,border: false
		}];
	}


	,getMainRightFields: function(config) {
		config = config || {};
		return [{
			xtype: 'fieldset'
			,title: _('ticket_publishing_information')
			,id: 'tickets-box-publishing-information'
			,defaults: {
				msgTarget: 'under'
			}
			,items: [{
				xtype: 'tickets-combo-publish-status'
				,fieldLabel: _('ticket_status')
				,name: 'published'
				,hiddenName: 'published'
				,inputValue: 0
				,value: 0
			},{
				xtype: 'xdatetime'
				,fieldLabel: _('resource_publishedon')
				,description: '<b>[[*publishedon]]</b><br />'+_('resource_publishedon_help')
				,name: 'publishedon'
				,id: 'modx-resource-publishedon'
				,allowBlank: true
				,dateFormat: MODx.config.manager_date_format
				,timeFormat: MODx.config.manager_time_format
				,dateWidth: 120
				,timeWidth: 120
				,value: config.record.publishedon
			},{
				xtype: MODx.config.publish_document ? 'xdatetime' : 'hidden'
				,fieldLabel: _('resource_publishdate')
				,description: '<b>[[*pub_date]]</b><br />'+_('resource_publishdate_help')
				,name: 'pub_date'
				,id: 'modx-resource-pub-date'
				,allowBlank: true
				,dateFormat: MODx.config.manager_date_format
				,timeFormat: MODx.config.manager_time_format
				,dateWidth: 120
				,timeWidth: 120
				,value: config.record.pub_date
			},{
				xtype: MODx.config.publish_document ? 'xdatetime' : 'hidden'
				,fieldLabel: _('resource_unpublishdate')
				,description: '<b>[[*unpub_date]]</b><br />'+_('resource_unpublishdate_help')
				,name: 'unpub_date'
				,id: 'modx-resource-unpub-date'
				,allowBlank: true
				,dateFormat: MODx.config.manager_date_format
				,timeFormat: MODx.config.manager_time_format
				,dateWidth: 120
				,timeWidth: 120
				,value: config.record.unpub_date
			},{
				xtype: MODx.config.publish_document ? 'modx-combo-user' : 'hidden'
				,fieldLabel: _('resource_createdby')
				,description: '<b>[[*createdby]]</b><br />'+_('resource_createdby_help')
				,name: 'created_by'
				,hiddenName: 'createdby'
				,id: 'modx-resource-createdby'
				,allowBlank: true
				,baseParams: {
					action: 'getList'
					,combo: '1'
					,limit: 0
				}
				,width: 300
				,value: config.record.createdby || MODx.user.id
			}]
		},{
			html: '<hr />'
			,border: false
		},{
			xtype: 'fieldset'
			,title: _('ticket_ticket_options')
			,id: 'tickets-box-options'
			,defaults: {
				msgTarget: 'under'
			}
			,items: [{
				xtype: 'modx-combo-template'
				,fieldLabel: _('resource_template')
				,description: '<b>[[*template]]</b><br />'+_('resource_template_help')
				,name: 'template'
				,id: 'modx-resource-template'
				,anchor: '100%'
				,editable: false
				,value: MODx.config.default_template
				,baseParams: {
					action: 'getList'
					,combo: '1'
				}
			},{
				xtype: 'xcheckbox'
				,name: 'richtext'
				,boxLabel: _('resource_richtext')
				,description: '<b>[[*richtext]]</b><br />'+_('resource_richtext_help')
				,id: 'modx-resource-richtext'
				,inputValue: 1
				,checked: parseInt(config.record.richtext)
			},{
				xtype: 'xcheckbox'
				,name: 'properties[disable_jevix]'
				,boxLabel: _('ticket_disable_jevix')
				,description: _('ticket_disable_jevix_help')
				,id: 'modx-resource-disablejevix'
				,inputValue: 1
				,checked: parseInt(config.record.disable_jevix)
			},{
				xtype: 'xcheckbox'
				,name: 'properties[process_tags]'
				,boxLabel: _('ticket_process_tags')
				,description: _('ticket_process_tags_help')
				,id: 'modx-resource-process_tags'
				,inputValue: 1
				,checked: parseInt(config.record.process_tags)
			},{
				xtype: 'hidden'
				,name: 'alias'
				,id: 'modx-resource-alias'
				,value: config.record.alias || ''
			},{
				xtype: 'hidden'
				,name: 'menutitle'
				,id: 'modx-resource-menutitle'
				,value: config.record.menutitle || ''
			},{
				xtype: 'hidden'
				,name: 'link_attributes'
				,id: 'modx-resource-link-attributes'
				,value: config.record.link_attributes || ''
			},{
				xtype: 'hidden'
				,name: 'hidemenu'
				,id: 'modx-resource-hidemenu'
				,value: config.record.hidemenu
			},{
				xtype: 'hidden'
				,name: 'class_key'
				,value: 'Ticket'
			}]
		}]
	}

});
Ext.reg('modx-panel-ticket',Tickets.panel.Ticket);