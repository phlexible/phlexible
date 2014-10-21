Phlexible.messages.filter.PreviewPanel = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.messages.Strings.preview,
    strings: Phlexible.messages.Strings,
    autoExpandColumn: 'subject',
    loadMask: true,

    parameterString: '',
    //quantityLimit: 50,

    viewConfig: {
        emptyText: Phlexible.messages.Strings.no_messages_found
    },

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('messages_filter_preview'),
            root: 'messages',
            totalProperty: 'total',
            id: 'id',
            baseParams: {
                filters: ''
            },
            fields: Phlexible.messages.model.Message
        });

        this.selModel = new Ext.grid.RowSelectionModel();

        var expander = new Ext.grid.RowExpander({
            dataIndex: 'body',
            tpl: new Ext.Template(
                '<p>{body}</p>'
            )
        });

        this.columns = [
            expander,
            {
                id: 'subject',
                header: this.strings.subject,
                dataIndex: 'subject',
                sortable: true
            }, {
                header: this.strings.priority,
                width: 50,
                dataIndex: 'priority',
                sortable: true
            }, {
                header: this.strings.type,
                width: 50,
                dataIndex: 'type',
                sortable: true
            }, {
                header: this.strings.channel,
                width: 60,
                dataIndex: 'channel',
                sortable: true
            }, {
                header: this.strings.role,
                width: 50,
                dataIndex: 'role',
                sortable: true
            }, {
                header: this.strings.user,
                width: 50,
                dataIndex: 'user',
                sortable: true
            }, {
                header: this.strings.created_at,
                width: 100,
                dataIndex: 'createdAt',
                sortable: true
            }];

        this.plugins = [expander];

        Phlexible.messages.filter.PreviewPanel.superclass.initComponent.call(this);

    },

    getParamString: function () {
        return this.parameterString;
    },

    loadData: function (criterias, title) {
        if (criterias) {
            this.parameterString = Ext.encode(criterias);
        }
        else {
            this.parameterString = '';
        }

        if (title) {
            this.setTitle(String.format(this.strings.preview_for, title));
        }

        this.store.baseParams.filters = this.parameterString;
        this.store.load();

        this.enable();
    },

    clear: function () {
        this.store.removeAll();
        this.setTitle(this.strings.preview);
    }
});

Ext.reg('messages-filter-previewpanel', Phlexible.messages.filter.PreviewPanel);