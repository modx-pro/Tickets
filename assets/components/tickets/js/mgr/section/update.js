Tickets.page.UpdateTicketsSection = function (config) {
    config = config || {record: {}};
    config.record = config.record || {};
    Ext.applyIf(config, {
        panelXType: 'tickets-panel-section-update',
    });
    config.canDuplicate = false;
    config.canDelete = false;
    Tickets.page.UpdateTicketsSection.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.page.UpdateTicketsSection, MODx.page.UpdateResource);
Ext.reg('tickets-page-section-update', Tickets.page.UpdateTicketsSection);


Tickets.panel.UpdateTicketsSection = function (config) {
    config = config || {};
    Tickets.panel.UpdateTicketsSection.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.panel.UpdateTicketsSection, MODx.panel.Resource, {

    getFields: function (config) {
        var fields = [];
        var originals = MODx.panel.Resource.prototype.getFields.call(this, config);
        for (var i in originals) {
            if (!originals.hasOwnProperty(i)) {
                continue;
            }
            var item = originals[i];

            if (item.id == 'modx-resource-tabs') {
                item.stateful = true;
                item.stateId = 'tickets-section-upd-tabpanel';
                item.stateEvents = ['tabchange'];
                item.getState = function () {
                    return {activeTab: this.items.indexOf(this.getActiveTab())};
                };
                var tabs = [];
                for (var i2 in item.items) {
                    if (!item.items.hasOwnProperty(i2)) {
                        continue;
                    }
                    var tab = item.items[i2];
                    if (tab.id == 'modx-resource-settings') {
                        tab.title = _('tickets_section');
                        tab.items = this.getMainFields(config);
                    }
                    else if (tab.id == 'modx-page-settings') {
                        tab.title = _('tickets_section_settings');
                        tab.items = this.getSectionSettings(config);
                        tab.cls = 'modx-resource-tab';
                        tab.bodyCssClass = 'tab-panel-wrapper form-with-labels';
                        tab.labelAlign = 'top';
                    }
                    tabs.push(tab);
                    if (tab.id == 'modx-page-settings') {
                        tabs.push(this.getComments(config));
                    }
                }
                item.items = tabs;
            }
            if (item.id == 'modx-resource-content') {
                fields.push(this.getTickets(config));
            }
            else {
                fields.push(item);
            }
        }

        return fields;
    },

    getMainFields: function (config) {
        var fields = MODx.panel.Resource.prototype.getMainFields.call(this, config);
        fields.push({
            xtype: 'hidden',
            name: 'class_key',
            id: 'modx-resource-class-key',
            value: 'TicketsSection'
        });
        fields.push({
            xtype: 'hidden',
            name: 'content_type',
            id: 'modx-resource-content-type',
            value: MODx.config['default_content_type'] || 1
        });

        return fields;
    },

    getSectionSettings: function (config) {
        return [{
            xtype: 'tickets-section-tab-settings',
            record: config.record,
        }];
    },

    getTickets: function (config) {
        return [{
            xtype: 'tickets-panel-tickets',
            parent: config.resource,
            standalone: false,
        }];
    },

    getComments: function (config) {
        return {
            title: _('comments'),
            items: [{
                xtype: 'tickets-panel-comments',
                record: config.record,
                section: config.record.id,
            }]
        };
    },

});
Ext.reg('tickets-panel-section-update', Tickets.panel.UpdateTicketsSection);
