/*-----------------------------*/
MODx.combo.Section = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'tickets-combo-section'
		,fieldLabel: _('resource_parent')
		,description: '<b>[[*parent]]</b><br />'+_('resource_parent_help')
		,fields: ['id','pagetitle']
		,valueField: 'id'
		,displayField: 'pagetitle'
		,name: 'parent-cmb'
		,hiddenName: 'parent-cmp'
		,allowBlank: false
		,baseParams: {
			action: 'mgr/section/getlist'
		}
		,pageSize: 20
		,width: 300
		,typeAhead: true
		,editable: true
	});
	MODx.combo.Section.superclass.constructor.call(this,config);
};
Ext.extend(MODx.combo.Section,MODx.combo.ComboBox);
Ext.reg('tickets-combo-section',MODx.combo.Section);