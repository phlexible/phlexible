Ext.provide('Phlexible.dashboard.store.List');

Ext.require('Phlexible.dashboard.model.Portlet');

Phlexible.dashboard.store.List = new Ext.data.SimpleStore({
    fields: Phlexible.dashboard.model.Portlet,
    listeners: {
        remove: function (store) {
            if (!store.getCount()) {
                store.removeAll();
            }
        }
    }
});
