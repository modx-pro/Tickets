Tickets.panel.Subscribes = function (config) {
  config = config || {};

  Ext.applyIf(config, {
      layout: 'anchor',
      border: false,
      anchor: '100%',
      items: [{
          xtype: 'tickets-grid-subscribes',
          cls: 'main-wrapper',
          section: config.section || 0,
          parents: config.parents || 0,
          threads: config.threads || 0,
      }],
      cls: 'tickets',
  });
  Tickets.panel.Subscribes.superclass.constructor.call(this, config);
};
Ext.extend(Tickets.panel.Subscribes, MODx.Panel);
Ext.reg('tickets-panel-subscribes', Tickets.panel.Subscribes);