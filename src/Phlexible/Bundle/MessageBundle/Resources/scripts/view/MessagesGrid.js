Phlexible.messages.view.MessagesGrid = Ext.extend(Ext.grid.GridPanel, {
//    title: Phlexible.messages.Strings.messages,
    strings: Phlexible.messages.Strings,
//    iconCls: 'p-message-component-icon',
    autoExpandColumn: 'subject',
    loadMask: true,

    viewConfig : {
        enableRowBody: true,
        emptyText: Phlexible.messages.Strings.empty_msg
//        forceFit: false
    },

    initComponent: function(){
//        this.addEvents(
//            'serverChange',
//            'serverDelete'
//        );

        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('messages_messages'),
            root: 'messages',
            id: 'id',
            totalProperty: 'totalCount',
            fields: Phlexible.messages.model.Message,
            sortInfo: {field: 'createdAt', direction: 'DESC'},
            remoteSort: true,
			listeners: {
				load: function(store) {
					this.fireEvent('messages', store.reader.jsonData);
				},
				scope: this
			}
        });
        this.store.load({
            start: 0,
            limit: 25
        });

        this.bbar = new Ext.PagingToolbar({
            pageSize: 25,
            store: this.store,
            displayInfo: true,
            displayMsg: this.strings.display_msg,
            emptyMsg: this.strings.empty_msg,
            plugins: this.filters
        });

        this.selModel = new Ext.grid.RowSelectionModel();

        var expander = new Ext.grid.RowExpander({
            dataIndex: 'body',
            tpl: new Ext.Template(
                '<p style="padding: 0 10px 10px 10px;">{body}</p>'
            )
        });

        this.columns = [
            expander,
        {
            header: this.strings.id,
            dataIndex: 'id',
            sortable: false,
            hidden: true,
            width: 250
        },{
            id: 'subject',
            header: this.strings.subject,
            dataIndex: 'subject',
            sortable: true,
            width: 200
        },{
            header: this.strings.priority,
            dataIndex: 'priority',
            sortable: true,
            width: 70,
            renderer: function(s) {
                return s ? Phlexible.inlineIcon('p-message-priority_' + s + '-icon') + ' ' + Phlexible.messages.Strings['priority_' + s] : '';
            }
        },{
            header: this.strings.type,
            dataIndex: 'type',
            sortable: true,
            width: 70,
            renderer: function(s) {
                return s ? Phlexible.inlineIcon('p-message-type_' + s + '-icon') + ' ' + Phlexible.messages.Strings['type_' + s] : '';
            }
        },{
            header: this.strings.channel,
            dataIndex: 'channel',
            sortable: true,
            width: 100
        },{
            header: this.strings.resource,
            dataIndex: 'resource',
            sortable: true,
            width: 100
        },{
            header: this.strings.user,
            dataIndex: 'user',
            sortable: true,
            width: 130
        },{
            header: this.strings.created_at,
            dataIndex: 'createdAt',
            sortable: true,
            width: 130
        }];

        this.plugins = [
            expander
        ];

        Phlexible.messages.view.MessagesGrid.superclass.initComponent.call(this);
    }
});

Ext.reg('messages-view-messagesgrid', Phlexible.messages.view.MessagesGrid);