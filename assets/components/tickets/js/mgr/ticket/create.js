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
			,height: 500
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
			,items: [/*{
				xtype: 'tickets-combo-publish-status'
				,fieldLabel: _('ticket_status')
				,name: 'published'
				,id: 'modx-resource-published'
				,hiddenName: 'published'
				,inputValue: 0
				,value: 0
			},*/
			{
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
				xtype: 'modx-combo-template'
				,fieldLabel: _('resource_template')
				,description: '<b>[[*template]]</b><br />'+_('resource_template_help')
				,name: 'template'
				,id: 'modx-resource-template'
				,anchor: '90%'
				,editable: false
				,baseParams: {
					action: 'getList'
					,combo: '1'
					,limit: 0
				}
				,value: MODx.config['tickets.default_template'] > 0 ? MODx.config['tickets.default_template'] : config.record.template
				,listeners: {select: {fn: this.templateWarning,scope: this}}
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
				,anchor: '90%'
				,value: config.record.createdby || MODx.user.id
			},{
				xtype: MODx.config.publish_document ? 'tickets-combo-section' : 'hidden'
				,id: 'tickets-combo-section'
				,fieldLabel: _('resource_parent')
				,description: '<b>[[*parent]]</b><br />'+_('resource_parent_help')
				,value: config.record.parent
				,url: Tickets.config.connector_url
				,listeners: {
					'select': {
						fn:function(data) {
							Ext.getCmp('modx-resource-parent-hidden').setValue(data.value);
						}
					}
				}
				,anchor: '90%'
			},{
				xtype: 'textfield'
				,id: 'modx-resource-alias'
				,fieldLabel: _('resource_alias')
				,description: '<b>[[*alias]]</b><br />'+_('resource_alias_help')
				,name: 'alias'
				,anchor: '90%'
				,value: config.record.alias || ''
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
				xtype: 'checkboxgroup'
				,columns: 2
				,items: this.getCheckboxes(config)
			}]
		}]
	}

	,getCheckboxes: function(config) {
		var fields = [];

		var tmp = {
			searchable: {}
			,disable_jevix: {name: 'properties[disable_jevix]',boxLabel: _('ticket_disable_jevix'),description: _('ticket_disable_jevix_help'), checked: parseInt(config.record.disable_jevix)}
			,cacheable: {}
			,process_tags: {name: 'properties[process_tags]',boxLabel: _('ticket_process_tags'),description: _('ticket_process_tags_help'), checked: parseInt(config.record.process_tags)}
			,published: {}
			,private: {name: 'privateweb',boxLabel: _('ticket_private'),description: _('ticket_private_help')}
			,richtext: {}
			,hidemenu: {boxLabel: _('resource_hide_from_menus'),description: _('resource_hide_from_menus_help')}
			//,show_in_tree: {}
			,isfolder: {boxLabel: _('resource_folder'),description: _('resource_folder_help')}
			,menutitle: {xtype: 'hidden', value: config.record.menutitle || ''}
			,link_attributes: {xtype: 'hidden',name: 'link_attributes',id: 'modx-resource-link-attributes'}
			,content_type: {xtype: 'hidden',name: 'content_type',id: 'modx-resource-content-type', value: MODx.config.default_content_type || 1}
			,class_key: {xtype: 'hidden',id: 'modx-resource-class-key',value: 'Ticket'}
		};

		for (var i in tmp) {
			if (tmp.hasOwnProperty(i)) {
				fields.push(Ext.apply({
						xtype: 'xcheckbox'
						,name: i
						,boxLabel: _('resource_' + i)
						,description: '<b>[[*' + i + ']]</b><br/>' + _('resource_' + i + '_help')
						,id: 'modx-resource-' + i
						,inputValue: 1
						,checked: parseInt(config.record[i])
					}
					,tmp[i]
				));
			}
		}

		return fields;
	}

	,success: function(o) {
		var g = Ext.getCmp('modx-grid-resource-security');
		if (g) { g.getStore().commitChanges(); }
		var t = Ext.getCmp('modx-resource-tree');

		if (t) {
			var ctx = Ext.getCmp('modx-resource-context-key').getValue();
			var pa = Ext.getCmp('modx-resource-parent-hidden').getValue();
			var pao = Ext.getCmp('modx-resource-parent-old-hidden').getValue();
			var v = ctx+'_'+pa;
			if(pa !== pao) {
				t.refresh();
				Ext.getCmp('modx-resource-parent-old-hidden').setValue(pa);
			} else {
				var n = t.getNodeById(v);
				if (n) {n.leaf = false;}
				t.refreshNode(v,true);
			}
		}
		if (o.result.object.class_key != this.defaultClassKey && this.config.resource != '' && this.config.resource != 0) {
			location.href = location.href;
		} else if (o.result.object['parent'] != this.defaultValues['parent'] && this.config.resource != '' && this.config.resource != 0) {
			location.href = location.href;
		} else {
			this.getForm().setValues(o.result.object);
			Ext.getCmp('modx-page-update-resource').config.preview_url = o.result.object.preview_url;
		}
	}

	,templateWarning: function() {
		var t = Ext.getCmp('modx-resource-template');
		if (!t) { return false; }
		if(t.getValue() !== t.originalValue) {
			Ext.Msg.confirm(_('warning'), _('resource_change_template_confirm'), function(e) {
				if (e == 'yes') {
					var nt = t.getValue();
					var f = Ext.getCmp('modx-page-update-resource');
					f.config.action = 'reload';
					MODx.activePage.submitForm({
						success: {fn:function(r) {
							var page = MODx.action ? MODx.action[r.result.object.action] : r.result.object.action;
							MODx.loadPage(page, '&id='+r.result.object.id+'&reload='+r.result.object.reload+'&class_key='+this.config.record.class_key);
						},scope:this}
					},{
						bypassValidCheck: true
					},{
						reloadOnly: true
					});
				} else {
					t.setValue(this.config.record.template);
				}
			},this);
		}
	}

});
Ext.reg('modx-panel-ticket',Tickets.panel.Ticket);