Tickets.grid.Subscribes = function (config) {
  config = config || {};

  Ext.applyIf(config, {
      url: Tickets.config.connector_url,
      baseParams: {
          action: 'mgr/subscribe/getlist',
          parents: config.parents,
      },
      fields: this.getFields(),
      columns: this.getColumns(config),
      tbar: this.getTopBar(config),
      sm: new Ext.grid.CheckboxSelectionModel(),
      autoHeight: true,
      paging: true,
      remoteSort: false,
      viewConfig: {
          forceFit: true,
          enableRowBody: true,
          showPreview: true,
      },
      stateful: true,
      stateId: 'tickets-subscribe-state',
  });
  Tickets.grid.Subscribes.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.grid.Subscribes, MODx.grid.Grid, {

  getFields: function () {
      return [
          'id', 'username', 'fullname', 'email', 'actions'
      ];
  },

  getColumns: function (config) {
      return [{
          header: _('id'),
          dataIndex: 'id',
          width: 35,
          sortable: true,
      }, {
          header: _('username'),
          dataIndex: 'username',
          sortable: true,
          width: 75,
          renderer: Ext.util.Format.htmlEncode
      }, {
          header: _('user_full_name'),
          dataIndex: 'fullname',
          width: 100,
          sortable: true,
          renderer: Ext.util.Format.htmlEncode
      }, {
          header: _('email'),
          dataIndex: 'email',
          width: 75,
          sortable: true,
          renderer: Ext.util.Format.htmlEncode
      }, {
          header: _('ticket_actions'),
          dataIndex: 'actions',
          renderer: Tickets.utils.renderActions,
          sortable: false,
          width: 75,
          id: 'actions'
      }];
  },

  getTopBar: function (config) {
      return ['->', {
          xtype: 'tickets-field-search',
          width: 250,
          listeners: {
              search: {
                  fn: function (field) {
                      this._doSearch(field);
                  }, scope: this
              },
              clear: {
                  fn: function (field) {
                      field.setValue('');
                      this._clearSearch();
                  }, scope: this
              },
          }
      }];
  },

  onClick: function (e) {
      var elem = e.getTarget();
      if (elem.nodeName == 'BUTTON') {
          var row = this.getSelectionModel().getSelected();
          if (typeof(row) != 'undefined') {
              var action = elem.getAttribute('action');
              if (action == 'showMenu') {
                  var ri = this.getStore().find('id', row.id);
                  return this._showMenu(this, ri, e);
              }
              else if (typeof this[action] === 'function') {
                  this.menu.record = row.data;
                  return this[action](this, e);
              }
          }
      }
      return this.processEvent('click', e);
  },

  _doSearch: function (tf) {
      this.getStore().baseParams.query = tf.getValue();
      this.getBottomToolbar().changePage(1);
  },

  _clearSearch: function () {
      this.getStore().baseParams.query = '';
      this.getBottomToolbar().changePage(1);
  },

  subscribeAction: function (method) {
      var ids = this._getSelectedIds();
      var parents = this.config.parents;
      if (!ids.length) {
          return false;
      }
      MODx.Ajax.request({
          url: Tickets.config.connector_url,
          params: {
              action: 'mgr/subscribe/multiple',
              method: method,
              ids: Ext.util.JSON.encode(ids),
              parents: parents
          },
          listeners: {
              success: {
                  fn: function () {
                      //noinspection JSUnresolvedFunction
                      this.refresh();
                  }, scope: this
              },
              failure: {
                  fn: function (response) {
                      MODx.msg.alert(_('error'), response.message);
                  }, scope: this
              },
          }
      })
  },

  unsubscribeSection: function () {
      this.subscribeAction('unsubscribe');
  },

  _getSelectedIds: function () {
      var ids = [];
      var selected = this.getSelectionModel().getSelections();

      for (var i in selected) {
          if (!selected.hasOwnProperty(i)) {
              continue;
          }
          ids.push(selected[i]['id']);
      }

      return ids;
  },

  // Grid onremove fix
  remove: function () {
  },

});
Ext.reg('tickets-grid-subscribes', Tickets.grid.Subscribes);