Ext.namespace('Phlexible.locks.portlet');

Phlexible.locks.portlet.LockRecord = Ext.data.Record.create([
    {name: 'ident', type: 'string'},
    {name: 'lock_type', type: 'string'},
    {name: 'object_type', type: 'string'},
    {name: 'object_id', type: 'string'},
    {name: 'lock_time', type: 'string'}
]);

Phlexible.locks.portlet.LocksTemplate = new Ext.XTemplate(
    '<table width="100%">',
    '<colgroup>',
    '<col width="*" />',
    '<col width="*" />',
    '<col width="*" />',
    '<col width="120" />',
    '</colgroup>',
    '<tr>',
    '<th>{[Phlexible.locks.Strings.lock_type]}</th>',
    '<th>{[Phlexible.locks.Strings.object_type]}</th>',
    '<th>{[Phlexible.locks.Strings.object_id]}</th>',
    '<th>{[Phlexible.locks.Strings.date]}</th>',
    '</tr>',
    '<tpl for=".">',
    '<tr class="locks-wrap" id="locks_{ident}">',
    '<td class="locks-type">{lock_type}</td>',
    '<td class="locks-type">{object_type}</td>',
    '<td class="locks-id">{object_id}</td>',
    '<td class="locks-time">{lock_time}</td>',
    '</tr>',
    '</tpl>',
    '</table>'
);

Phlexible.locks.portlet.Locks = Ext.extend(Ext.ux.Portlet, {
    title: Phlexible.locks.Strings.my_locked_items,
    strings: Phlexible.locks.Strings,
    iconCls: 'p-lock-lock-icon',
    extraCls: 'locks-portlet',
    bodyStyle: 'padding: 5px',

    initComponent: function () {
        this.store = new Ext.data.SimpleStore({
            fields: Phlexible.locks.portlet.LockRecord,
            id: 'ident',
            sortInfo: {field: 'lock_time', direction: 'DESC'}
        });

        var data = this.record.get('data');
        if (data) {
            Ext.each(data, function (item) {
                this.add(new Phlexible.locks.portlet.LockRecord(item, item.ident));
            }, this.store);
        }

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'tr.locks-wrap',
                overClass: 'locks-wrap-over',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.strings.no_locked_items,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: Phlexible.locks.portlet.LocksTemplate,
                listeners: {
                    click: {
                        fn: function (c, index, node) {
                            return;
                        },
                        scope: this
                    }
                }
            }
        ];

        Phlexible.locks.portlet.Locks.superclass.initComponent.call(this);
    },

    updateData: function (data) {
        var latestLocksMap = [];

        for (var i = data.length - 1; i >= 0; i--) {
            var row = data[i];
            latestLocksMap.push(row.ident);
            var r = this.store.getById(row.ident);
            if (!r) {
                //row.time = new Date(row.time * 1000);
                this.store.insert(0, new Phlexible.locks.portlet.LockRecord(row, row.ident));

                Ext.fly('locks_' + row.ident).frame('#8db2e3', 1);
            }
        }

        for (var i = this.store.getCount() - 1; i >= 0; i--) {
            var r = this.store.getAt(i);
            if (latestLocksMap.indexOf(r.id) == -1) {
                this.store.remove(r);
            }
        }

        if (!this.store.getCount()) {
            this.store.removeAll();
        }

        this.store.sort('lock_time', 'DESC');
    }
});

Ext.reg('locks-portlet-locks', Phlexible.locks.portlet.Locks);