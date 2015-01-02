Ext.ns('Phlexible.dashboard.store');

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
