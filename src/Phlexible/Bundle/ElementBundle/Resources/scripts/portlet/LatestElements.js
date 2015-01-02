Ext.ns('Phlexible.elements.portlet');

Phlexible.elements.portlet.LatestElementsRecord = Ext.data.Record.create([
    {name: 'ident', type: 'string'},
    {name: 'eid', type: 'string'},
    {name: 'language', type: 'string'},
    {name: 'version', type: 'string'},
    {name: 'title', type: 'string'},
    {name: 'icon', type: 'string'},
    {name: 'time', type: 'date'},
    {name: 'author', type: 'string'},
    {name: 'menu'}
]);

Phlexible.elements.portlet.LatestElementsPortlet = new Ext.XTemplate(
    '<table width="100%" cellpadding="0" cellspacing="0" border="0">',
    '<colgroup>',
    '<col width="20" />',
    '<col />',
    '<col width="20" />',
    '<col width="25" />',
    '<col width="80" />',
    '<col width="110" />',
    '</colgroup>',
    '<tr>',
    '<th colspan="2">{[Phlexible.elements.Strings.title]}</th>',
    '<th qtip="{[Phlexible.elements.Strings.language]}">{[Phlexible.elements.Strings.language.substring(0,1)]}.</th>',
    '<th qtip="{[Phlexible.elements.Strings.version]}">{[Phlexible.elements.Strings.version.substring(0,1)]}.</th>',
    '<th>{[Phlexible.elements.Strings.author]}</th>',
    '<th>{[Phlexible.elements.Strings.date]}</th>',
    '</tr>',
    '<tpl for=".">',
    '<tr class="elements-wrap" id="elements_last_{ident}">',
    '<td class="elements-portlet-icon"><img src="{[Phlexible.baseUrl]}{icon}" title="{title}" width="18" height="18"/></td>',
    '<td class="elements-portlet-title">{title}</td>',
    '<td class="elements-portlet-language">{[Phlexible.inlineIcon("p-flags-" + values.language + "-icon")]}</td>',
    '<td class="elements-portlet-version">{version}</td>',
    '<td class="elements-portlet-author">{author}</td>',
    '<td class="elements-portlet-date">{time:date("Y-m-d H:i:s")}</td>',
    '</tr>',
    '</tpl>',
    '</table>'
);

Phlexible.elements.portlet.LatestElements = Ext.extend(Ext.ux.Portlet, {
    title: Phlexible.elements.Strings.latest_element_changes,
    strings: Phlexible.elements.Strings,
    iconCls: 'p-element-portlet-icon',
    extraCls: 'elements-portlet',
    bodyStyle: 'padding: 5px',

    initComponent: function () {
        this.store = new Ext.data.SimpleStore({
            fields: Phlexible.elements.portlet.LatestElementsRecord,
            id: 'ident',
            sortInfo: {field: 'time', direction: 'DESC'}
        });

        var data = this.record.get('data');
        if (data) {
            Ext.each(data, function (item) {
                item.time = new Date(item.time * 1000);
                this.add(new Phlexible.elements.portlet.LatestElementsRecord(item, item.ident));
            }, this.store);
        }

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'tr.elements-wrap',
                overClass: 'elements-wrap-over',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.strings.no_recent_elements,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: Phlexible.elements.portlet.LatestElementsPortlet,
                listeners: {
                    click: {
                        fn: function (c, index, node) {
                            var r = c.getStore().getAt(index);
                            if (!r) return;
                            var menu = r.data.menu;
                            if (menu && menu.handler) {
                                var handler = menu.handler;
                                if (typeof handler == 'string') {
                                    handler = Phlexible.evalClassString(handler);
                                }
                                handler(menu);
                            }
                        },
                        scope: this
                    }
                }
            }
        ];

        Phlexible.elements.portlet.LatestElements.superclass.initComponent.call(this);
    },

    updateData: function (data) {
        var latestElementsMap = [];

        for (var i = data.length - 1; i >= 0; i--) {
            var row = data[i];
            latestElementsMap.push(row.ident);
            var r = this.store.getById(row.ident);
            if (!r) {
                row.time = new Date(row.time * 1000);
                this.store.insert(0, new Phlexible.elements.portlet.LatestElementsRecord(row, row.ident));

                Ext.fly('elements_last_' + row.ident).frame('#8db2e3', 1);
            }
        }

        for (var i = this.store.getCount() - 1; i >= 0; i--) {
            var r = this.store.getAt(i);
            if (latestElementsMap.indexOf(r.id) == -1) {
                this.store.remove(r);
            }
        }

        this.store.sort('time', 'DESC');
    }
});

Ext.reg('elements-portlet-latestelements', Phlexible.elements.portlet.LatestElements);