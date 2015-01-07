Ext.provide('Phlexible.users.portlet.OnlineRecord');
Ext.provide('Phlexible.users.portlet.Online');

Ext.require('Ext.ux.Portlet');

Phlexible.users.portlet.OnlineRecord = Ext.data.Record.create([
    {name: 'uid', type: 'string'},
    {name: 'username', type: 'string'},
    {name: 'login_ts', type: 'string'},
    {name: 'login_seconds', type: 'string'},
    {name: 'ts', type: 'string'}
]);

Phlexible.users.portlet.Online = Ext.extend(Ext.ux.Portlet, {
    title: Phlexible.users.Strings.who_is_online,
    strings: Phlexible.users.Strings,
    bodyStyle: 'padding: 5px',
    iconCls: 'p-user-portlet-icon',
    extraCls: 'online-portlet',

    initComponent: function () {
        this.tpl = new Ext.XTemplate(
            '<tpl for=".">',
            '<div id="portal_online_{uid}" class="portlet-online">',
//        '<div class="user-image"><img src="{image}" width="48" height="48" /></div>',
            '<div class="user-name">{name}</div>',
            '<div class="user-age">' + this.strings.online_for + ' {[Phlexible.Format.age(values.login_seconds)]}.</div>',
            '<div style="clear: both;"></div>',
            '</div>',
            '</tpl>'
        );

        this.store = new Ext.data.SimpleStore({
            fields: Phlexible.users.portlet.OnlineRecord,
            id: 'uid',
            sortInfo: {field: 'username', username: 'ASC'}
        });

        var data = this.record.get('data');
        if (data) {
            Ext.each(data, function (item) {
                this.add(new Phlexible.users.portlet.OnlineRecord(item, item.uid));
            }, this.store);
        }

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'div.portlet-online',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.strings.no_online_users,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: this.tpl
            }
        ];

        Phlexible.users.portlet.Online.superclass.initComponent.call(this);
    },

    updateData: function (data) {
        var onlineMap = [];
        var i, r;

        for (i = 0; i < data.length; i++) {
            var row = data[i];
            onlineMap.push(row.uid);
            r = this.store.getById(row.uid);
            if (r) {
                r.set('login_seconds', row.login_seconds);
            } else {
                this.store.add(new Phlexible.users.portlet.OnlineRecord(row, row.uid));

                Phlexible.msg('Online', this.strings.user + ' "' + row.username + '" ' + this.strings.logged_in);
                Ext.fly('portal_online_' + row.uid).frame('#8db2e3', 1);
            }
        }

        for (i = 0; i < this.store.getCount(); i++) {
            r = this.store.getAt(i);
            if (onlineMap.indexOf(r.id) == -1) {
                Phlexible.msg('Online', this.strings.user + ' "' + r.get('username') + '" ' + this.strings.logged_out);
                this.store.remove(r);
            }
        }

        this.store.sort('username', 'ASC');
    }
});

Ext.reg('users-portlet-online', Phlexible.users.portlet.Online);