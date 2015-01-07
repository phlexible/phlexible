Ext.provide('Phlexible.messages.portlet.Messages');
Ext.provide('Phlexible.messages.portlet.MessagesRecord');

Ext.require('Ext.ux.Portlet');

Phlexible.messages.portlet.MessagesRecord = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'subject', type: 'string'},
    {name: 'time', type: 'date'}
]);

Phlexible.messages.portlet.Messages = Ext.extend(Ext.ux.Portlet, {
    title: Phlexible.messages.Strings.messages,
    strings: Phlexible.messages.Strings,
    iconCls: 'p-message-component-icon',
    extraCls: 'messages-portlet',
    bodyStyle: 'padding: 5px',

    initComponent: function () {
        this.tpl = new Ext.XTemplate(
            '<table width="100%">',
            '<colgroup>',
            '<col />',
            '<col width="20" />',
            '<col width="20" />',
            '<col width="20" />',
            '<col width="110" />',
            '</colgroup>',
            '<tr>',
            '<th>{[Phlexible.messages.Strings.subject]}</th>',
            '<th>' + this.strings.channel + '</th>',
            '<th style="text-align: center;" qtip="' + this.strings.priority + '">' + this.strings.priority.substring(0, 1) + '.</th>',
            '<th style="text-align: center;" qtip="' + this.strings.type + '">' + this.strings.type.substring(0, 1) + '.</th>',
            '<th>' + this.strings.date + '</th>',
            '</tr>',
            '<tpl for=".">',
            '<tr class="messages-wrap" id="message_{id}">',
            '<td style="vertical-align: middle;" class="messages-subject">{subject}</td>',
            '<td style="vertical-align: middle;" class="messages-icon">{channel}</td>',
            '<td style="vertical-align: middle; text-align: center;" class="messages-icon">{[Phlexible.inlineIcon("p-message-priority_" + values.priority + "-icon")]}</td>',
            '<td style="vertical-align: middle; text-align: center;" class="messages-icon">{[Phlexible.inlineIcon("p-message-type_" + values.type + "-icon")]}</td>',
            '<td style="vertical-align: middle;" class="messages-date">{time:date("Y-m-d H:i:s")}</td>',
            '</tr>',
            '</tpl>',
            '</table>'
        );

        this.store = new Ext.data.SimpleStore({
            fields: Phlexible.messages.portlet.MessagesRecord,
            id: 'id',
            sortInfo: {field: 'time', direction: 'DESC'}
        });

        var data = this.record.get('data');
        if (data) {
            Ext.each(data, function (item) {
                item.time = new Date(item.time * 1000);
                this.add(new Phlexible.messages.portlet.MessagesRecord(item, item.id));
            }, this.store);
        }

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'tr.messages-wrap',
                overClass: 'messages-wrap-over',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.strings.no_recent_messages,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: this.tpl,
                listeners: {
                    click: function (c, index, node) {
                        return;
                    },
                    scope: this
                }
            }
        ];

        Phlexible.messages.portlet.Messages.superclass.initComponent.call(this);
    },

    updateData: function (data) {
        var latestMessagesMap = [];

        for (var i = data.length - 1; i >= 0; i--) {
            var row = data[i];
            latestMessagesMap.push(row.id);
            var r = this.store.getById(row.id);
            if (!r) {
                row.time = new Date(row.time * 1000);
                this.store.insert(0, new Phlexible.messages.portlet.MessagesRecord(row, row.id));

                Ext.fly('message_' + row.id).frame('#8db2e3', 1);
            }
        }

        for (var i = this.store.getCount() - 1; i >= 0; i--) {
            var r = this.store.getAt(i);
            if (latestMessagesMap.indexOf(r.id) == -1) {
                this.store.remove(r);
            }
        }

        this.store.sort('time', 'DESC');
    }
});

Ext.reg('messages-portlet-messages', Phlexible.messages.portlet.Messages);