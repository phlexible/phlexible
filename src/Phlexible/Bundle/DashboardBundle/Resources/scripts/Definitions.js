Ext.namespace('Phlexible.dashboard');

Phlexible.dashboard.PortletRecord = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'title', type: 'string'},
    {name: 'class', type: 'string'},
    {name: 'mode', type: 'string'},
    {name: 'col', type: 'integer'},
    {name: 'pos', type: 'integer'},
    {name: 'iconCls', type: 'string'},
    {name: 'data'},
    {name: 'settings'},
]);

Phlexible.dashboard.ListStore = new Ext.data.SimpleStore({
    fields: Phlexible.dashboard.PortletRecord,
    listeners: {
        remove: function(store) {
            if(!store.getCount()) {
                store.removeAll();
            }
        }
    }
});

Phlexible.dashboard.PortletStore = new Ext.data.SimpleStore({
    fields: Phlexible.dashboard.PortletRecord
});

