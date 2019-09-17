Tickets.page.UpdateTicket = function (config) {
    config = config || {record: {}};
    config.record = config.record || {};
    Ext.applyIf(config, {
        panelXType: 'modx-panel-ticket'
    });
    config.canDuplicate = false;
    config.canDelete = false;
    Tickets.page.UpdateTicket.superclass.constructor.call(this, config);
};

Ext.extend(Tickets.page.UpdateTicket, MODx.page.UpdateResource, {

    getButtons: function (config) {
        var buttons = [];
        var originals = MODx.page.UpdateResource.prototype.getButtons.call(this, config);
        for (var i in originals) {
            if (!originals.hasOwnProperty(i)) {
                continue;
            }
            var button = originals[i];
            switch (button.id) {
                case 'modx-abtn-help':
                    buttons.push(this.getAdditionalButtons(config));
                    button.text = '<i class="icon icon-question-circle"></i>';
                    if (MODx.config.manager_language == 'ru')
                        MODx.helpUrl = 'https://docs.modx.pro/komponentyi/tickets/interfejs/sozdanie-tiketa';
                    else
                        MODx.helpUrl = 'https://docs.modx.pro/en/components/tickets/interface/create-ticket';
                    break;
            }
            buttons.push(button)
        }

        return buttons;
    },

    getAdditionalButtons: function (config) {
        return [{
            text: '<i class="icon icon-arrow-left"></i>',
            handler: this.prevPage,
            disabled: !config['prev_page'],
            scope: this,
            tooltip: _('tickets_btn_prev'),
            keys: [{key: 37, alt: true, scope: this, fn: this.prevPage}]
        }, {
            text: '<i class="icon icon-arrow-up"></i>',
            handler: this.cancel,
            scope: this,
            tooltip: _('tickets_btn_back'),
            keys: [{key: 38, alt: true, scope: this, fn: this.upPage}]
        }, {
            text: '<i class="icon icon-arrow-right"></i>',
            handler: this.nextPage,
            disabled: !config['next_page'],
            scope: this,
            tooltip: _('tickets_btn_next'),
            keys: [{key: 39, alt: true, scope: this, fn: this.nextPage}]
        }];
    },

    prevPage: function () {
        if (this.config['prev_page'] > 0) {
            MODx.loadPage('resource/update', 'id=' + this.config['prev_page'])
        }
    },

    nextPage: function () {
        if (this.config['next_page'] > 0) {
            MODx.loadPage('resource/update', 'id=' + this.config['next_page'])
        }
    },

    cancel: function () {
        var id = this.config['up_page'];
        var action = id != 0
            ? 'resource/update'
            : 'welcome';

        var fp = Ext.getCmp(this.config.formpanel);
        if (fp && fp.isDirty() && MODx.config['confirm_navigation'] == 1) {
            Ext.Msg.confirm(_('warning'), _('resource_cancel_dirty_confirm'), function (e) {
                if (e == 'yes') {
                    fp.warnUnsavedChanges = false;
                    MODx.loadPage(action, 'id=' + id)
                }
            }, this);
        } else {
            MODx.loadPage(action, 'id=' + id)
        }
    },

});

Ext.reg('tickets-page-ticket-update', Tickets.page.UpdateTicket);