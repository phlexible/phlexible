Ext.provide('Phlexible.dashboard.ListPanel');

Ext.require('Phlexible.dashboard.tpl.StartListTemplate');

Phlexible.dashboard.ListPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.dashboard.Strings.available_portlets,
    strings: Phlexible.dashboard.Strings,
    width: 200,
    cls: 'p-dashboard-list-panel',

    initComponent: function () {
        this.addEvents(
            'portletOpen'
        );

        this.items = [{
            xtype: 'dataview',
            store: Phlexible.dashboard.store.List,
            tpl: Phlexible.dashboard.tpl.StartListTemplate,
            style: 'margin: 4px 4px 0 4px;',
            emptyText: this.strings.no_available_portlets,
            deferEmptyText: false,
            itemSelector: 'div.portlets-wrap',
            overClass: 'x-view-over',
            singleSelect: true,
            listeners: {
                click: function (view, index, el) {
                    var r = view.store.getAt(index);
                    this.fireEvent('portletOpen', view.store, r);
                },
                scope: this
            }
        }];

        this.tbar = [
            {
                text: this.strings.save_layout,
                handler: function () {
                    this.fireEvent('portletSave');
                },
                scope: this
            }
        ];

        Phlexible.dashboard.ListPanel.superclass.initComponent.call(this);
    }
});

Ext.reg('dashboard-listpanel', Phlexible.dashboard.ListPanel);