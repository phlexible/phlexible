Phlexible.dashboard.StartListTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="portlets-wrap" id="{title}">',
    '<div class="x-panel-tl">',
    '<div class="x-panel-tr">',
    '<div class="x-panel-tc">',
    '<div class="x-panel-header x-panel-icon {iconCls}" style="cursor: pointer;" title="Click to add.">',
    '<span class="x-panel-header-text">{title}</span>',
    '</div>',
    '</div>',
    '</div>',
    '</div>',
    '</div>',
    '</tpl>'
);

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
            tpl: Phlexible.dashboard.StartListTemplate,
            style: 'margin: 4px 4px 0 4px;',
            emptyText: this.strings.no_available_portlets,
            deferEmptyText: false,
            itemSelector: 'div.portlets-wrap',
            overClass: 'x-view-over',
            singleSelect: true,
            listeners: {
                click: {
                    fn: function (view, index, el) {
                        var r = view.store.getAt(index);
                        this.fireEvent('portletOpen', view.store, r);
                    },
                    scope: this
                }
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