Tickets.window.UpdateComment = function(config) {
	config = config || {};
	if (!config.id) {
		config.id = 'ticket-comment-' + Ext.id();
	}

	Ext.applyIf(config, {
		title: _('tickets_comment_update'),
		url: Tickets.config.connector_url,
		action: 'mgr/comment/update',
		fields: this.getFields(config),
		keys: this.getKeys(config),
		closeAction: 'close',
		width: 700,
		height: 550,
		layout: 'anchor',
		autoHeight: false,
		cls: 'tickets-window ' + (MODx.modx23 ? 'modx23' : 'modx22'),
	});
	Tickets.window.UpdateComment.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.window.UpdateComment, MODx.Window, {

	getKeys: function() {
		return [{
			key: Ext.EventObject.ENTER,
			shift: true,
			fn: this.submit,
			scope: this
		}];
	},

	getFields: function(config) {
		return [{
			xtype: 'hidden',
			name: 'id',
			id: config.id + '-id',
		},{
			xtype: 'textarea',
			fieldLabel: _('comment'),
			name: 'text',
			id: config.id + '-text',
			anchor: '99% -210'
		},{
			items: [{
				layout: 'form',
				cls: 'modx-panel',
				items: [{
					layout: 'column',
					border: false,
					items: [{
						columnWidth: .5,
						border: false,
						layout: 'form',
						items: this.getLeftFields(config),
					}, {
						columnWidth: .5,
						border: false,
						layout: 'form',
						cls: 'right-column',
						items: this.getRightFields(config),
					}]
				}]
			}]
		}];
	},

	getLeftFields: function(config) {
		return [{
			xtype: 'textfield',
			fieldLabel: _('ticket_comment_name'),
			name: 'name',
			id: config.id + '-name',
			anchor: '99%',
			disabled: config.record.createdby != 0
		},{
			xtype: 'numberfield',
			fieldLabel: _('ticket_comment_parent'),
			name: 'parent',
			id: config.id + '-parent',
			anchor: '75%'
		},{
			xtype: 'tickets-combo-thread',
			fieldLabel: _('ticket_thread'),
			name: 'thread',
			id: config.id + '-thread',
			anchor: '75%'
		}];
	}

	,getRightFields: function(config) {
		return [{
			xtype: 'textfield',
			fieldLabel: _('ticket_comment_email'),
			name: 'email',
			id: config.id + '-email',
			anchor: '99%',
			disabled: config.record.createdby != 0
		},{
			layout: 'column',
			border: false,
			items: [{
				columnWidth: .5,
				border: false,
				layout: 'form',
				items: [{
					xtype: 'displayfield',
					fieldLabel: _('ticket_comment_createdon'),
					name: 'createdon',
					id: config.id + '-createdon',
					anchor: '99%',
				},{
					xtype: 'displayfield',
					fieldLabel: 'IP',
					name: 'ip',
					id: config.id + '-ip',
					anchor: '99%',
				}]
			}, {
				columnWidth: .5,
				border: false,
				layout: 'form',
				cls: 'right-column',
				items: [{
					xtype: 'displayfield',
					fieldLabel: _('ticket_comment_editedon'),
					name: 'editedon',
					id: config.id + '-editedon',
					anchor: '99%',
				},{
					xtype: 'displayfield',
					fieldLabel: _('ticket_comment_deletedon'),
					name: 'deletedon',
				 	id: config.id + '-deletedon',
					anchor: '99%',
				}]
			}]
		}];
	}

});
Ext.reg('tickets-window-comment-update', Tickets.window.UpdateComment);