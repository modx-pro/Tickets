Tickets.window.UpdateComment = function(config) {
	config = config || {};
	this.ident = config.ident || 'meucomment'+Ext.id();
	Ext.applyIf(config,{
		title: _('tickets_comment_update')
		,id: this.ident
		,width: 700
		,height: 550
		,url: Tickets.config.connector_url
		,action: 'mgr/comment/update'
		,layout: 'anchor'
		,autoHeight: false
		,fields: [
			{xtype: 'hidden',name: 'id',id: 'tickets-'+this.ident+'-id'}
			,{xtype: 'textarea',fieldLabel: _('comment'),name: 'text',id: 'tickets-'+this.ident+'-text',anchor: '99% -180'}
			,{
				items: [{
					layout: 'form'
					,cls: 'modx-panel'
					,items: [{
						layout: 'column'
						,border: false
						,items: [{
							columnWidth: .5
							,border: false
							,layout: 'form'
							,items: this.getLeftFields(config)
						},{
							columnWidth: .5
							,border: false
							,layout: 'form'
							,items: this.getRightFields(config)
						}]
					}]
				}]
			}]
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: this.submit,scope: this}]
	});
	Tickets.window.UpdateComment.superclass.constructor.call(this,config);
};
Ext.extend(Tickets.window.UpdateComment,MODx.Window,{

	getLeftFields: function(config) {
		return [
			{xtype:'textfield', fieldLabel: _('ticket_comment_name'), name: 'name', id:'tickets-'+this.ident+'-name',anchor: '99%',hidden: config.record.createdby ? 1 : 0}
			,{xtype:'numberfield', fieldLabel: _('ticket_comment_parent'), name: 'parent', id:'tickets-'+this.ident+'-parent',anchor: '50%'}
			,{xtype:'tickets-combo-thread', fieldLabel: _('ticket_thread'), name: 'thread', id:'tickets-'+this.ident+'-thread',anchor: '75%'}
		];
	}

	,getRightFields: function(config) {
		return [
			{xtype:'textfield', fieldLabel: _('ticket_comment_email'), name: 'email', id:'tickets-'+this.ident+'-email',anchor: '99%',hidden: config.record.createdby ? 1 : 0}
			,{
				layout: 'column'
				,border: false
				,items: [{
					columnWidth: .5
					,border: false
					,layout: 'form'
					,items: [
						{xtype:'displayfield', fieldLabel: _('ticket_comment_createdon'), name: 'createdon', id:'tickets-'+this.ident+'-createdon',anchor: '99%'}
						//{xtype:'xcheckbox', fieldLabel: _('ticket_comment_deleted'), name: 'deleted', id:'tickets-'+this.ident+'-deleted',anchor: '99%'}
						,{xtype:'displayfield', fieldLabel: 'IP', name: 'ip', id:'tickets-'+this.ident+'-ip',anchor: '99%'}
					]
				},{
					columnWidth: .5
					,border: false
					,layout: 'form'
					,items: [
						{xtype:'displayfield', fieldLabel: _('ticket_comment_editedon'), name: 'editedon', id:'tickets-'+this.ident+'-editedon',anchor: '99%'}
						//,{xtype:'displayfield', fieldLabel: _('ticket_comment_deletedon'), name: 'deletedon', id:'tickets-'+this.ident+'-deletedon',anchor: '99%', hidden: config.record.deleted ? 0 : 1}
					]
				}]
			}
		];
	}

});
Ext.reg('tickets-window-comment-update',Tickets.window.UpdateComment);