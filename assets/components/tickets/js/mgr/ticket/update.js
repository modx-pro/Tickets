Tickets.page.UpdateTicket = function(config) {
	config = config || {record:{}};
	config.record = config.record || {};
	Ext.applyIf(config,{
		panelXType: 'modx-panel-ticket'
	});
	config.canDuplicate = false;
	config.canDelete = false;
	Tickets.page.UpdateTicket.superclass.constructor.call(this,config);
};

Ext.extend(Tickets.page.UpdateTicket,MODx.page.UpdateResource,{
	/*
	getButtons: function(cfg) {
		var btns = [];
		if (cfg.canSave == 1) {
			btns.push({
				process: 'update'
				,text: _('save')
				,method: 'remote'
				,checkDirty: cfg.richtext || MODx.request.activeSave == 1 ? false : true
				,keys: [{
					key: MODx.config.keymap_save || 's'
					,ctrl: true
				}]
			});
			btns.push('-');
		} else if (cfg.locked) {
			btns.push({
				text: cfg.lockedText || _('locked')
				,handler: Ext.emptyFn
				,disabled: true
			});
			btns.push('-');
		}
		btns.push({
			text: _('resource_publish')
			,id: 'modx-ticket-publish'
			,hidden: cfg.record.published ? true : false
			,handler: this.publishTicket
		});
		btns.push({
			text: _('resource_unpublish')
			,id: 'modx-ticket-unpublish'
			,hidden: cfg.record.published ? false : true
			,handler: this.unpublishTicket
		});
		btns.push('-');
		btns.push({
			process: 'preview'
			,text: _('view')
			,handler: this.preview
			,scope: this
		});
		btns.push('-');
		btns.push({
			process: 'cancel'
			,text: _('cancel')
			,handler: this.cancel
			,scope: this
		});
		return btns;
	}

	,publishTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.config.connectors_url+'resource/index.php'
			,params: {
				action: 'update'
				,id: MODx.request.id
			}
			,listeners: {
				success:{fn:function(r) {
					console.log(r.object)
					var p = Ext.getCmp('modx-resource-published');
					if (p) {
						p.setValue(1);
					}
					var po = Ext.getCmp('modx-resource-publishedon');
					if (po) {
						po.setValue(r.object.publishedon);
					}
					var bp = Ext.getCmp('modx-ticket-publish');
					if (bp) {
						bp.hide();
					}
					var bu = Ext.getCmp('modx-ticket-unpublish');
					if (bu) {
						bu.show();
					}
				},scope:this}
			}
		});
	}

	,unpublishTicket: function(btn,e) {
		MODx.Ajax.request({
			url: MODx.config.connectors_url+'resource/index.php'
			,params: {
				action: 'unpublish'
				,id: MODx.request.id
			}
			,listeners: {
				'success':{fn:function(r) {
					var p = Ext.getCmp('modx-resource-published');
					if (p) {
						p.setValue(0);
					}
					var po = Ext.getCmp('modx-resource-publishedon');
					if (po) {
						po.setValue('');
					}
					var bp = Ext.getCmp('modx-ticket-publish');
					if (bp) {
						bp.show();
					}
					var bu = Ext.getCmp('modx-ticket-unpublish');
					if (bu) {
						bu.hide();
					}
				},scope:this}
			}
		});
	}
	*/
});
Ext.reg('tickets-page-ticket-update',Tickets.page.UpdateTicket);