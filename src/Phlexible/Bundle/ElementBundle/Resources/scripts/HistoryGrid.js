Phlexible.elements.HistoryGrid = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.elements.Strings.history,
    strings: Phlexible.elements.Strings,
    iconCls: 'p-element-tab_history-icon',
    autoExpandColumn: 6,
    loadMask: true,

    initComponent: function () {
        // create the data store
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('elements_history'),
            baseParams: {},
            root: 'history',
            totalProperty: 'total',
            id: 'id',
            fields: Phlexible.elements.ElementHistoryRecord,
            remoteSort: true,
            sortInfo: {field: 'create_time', direction: 'DESC'}
        });

        // create the column model
        this.columns = [
            {
                header: this.strings.type,
                width: 60,
                dataIndex: 'type',
                sortable: true
            },
            {
                header: this.strings.action,
                width: 170,
                dataIndex: 'action',
                sortable: true,
                renderer: this.renderAction
            },
            {
                header: this.strings.tid,
                width: 55,
                dataIndex: 'tid',
                sortable: true
            },
            {
                header: this.strings.eid,
                width: 55,
                dataIndex: 'eid',
                sortable: true
            },
            {
                header: this.strings.version,
                width: 55,
                dataIndex: 'version',
                sortable: true
            },
            {
                header: this.strings.language,
                width: 100,
                dataIndex: 'language',
                sortable: true,
                renderer: this.renderLanguage
            },
            {
                header: this.strings.comment,
                width: 200,
                dataIndex: 'comment',
                sortable: true
            },
            {
                header: this.strings.username,
                width: 100,
                dataIndex: 'username',
                sortable: true
            },
            {
                header: this.strings.date,
                width: 130,
                dataIndex: 'create_time',
                sortable: true
            }
        ];

        // create the selection model
        this.selModel = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.bbar = new Ext.PagingToolbar({
            pageSize: 25,
            store: this.store,
            displayInfo: true,
            displayMsg: this.strings.paging_display_msg,
            emptyMsg: this.strings.paging_empty_msg,
            beforePageText: this.strings.paging_before_page_text,
            afterPageText: this.strings.paging_after_page_text
        });

        Phlexible.elements.HistoryGrid.superclass.initComponent.call(this);
    },

    renderLanguage: function (v) {
        var s = v;
        var len = Phlexible.Config.get('set.language.frontend').length;
        for (var i = 0; i < len; i++) {
            if (Phlexible.Config.get('set.language.frontend')[i][0] == v) {
                s = Phlexible.Config.get('set.language.frontend')[i][1];
            }
        }
        return Phlexible.inlineIcon('p-flags-' + v + '-icon') + ' ' + s;
    },

    renderAction: function (v) {
        var icon = '';

        switch (v) {
            case 'createElement':
            case 'createElementVersion':
            case 'createNode':
            case 'createTeaser':
                icon = Phlexible.inlineIcon('p-element-add-icon') + ' ';
                break;

            case 'saveElement':
                icon = Phlexible.inlineIcon('p-element-save-icon') + ' ';
                break;

            case 'deleteNode':
            case 'deleteTeaser':
                icon = Phlexible.inlineIcon('p-element-delete-icon') + ' ';
                break;

            case 'createInstance':
            case 'createNodeInstance':
            case 'createTeaserInstance':
                icon = Phlexible.inlineIcon('p-element-alias_add-icon') + ' ';
                break;

            case 'saveElementMaster':
            case 'saveElementSlave':
                icon = Phlexible.inlineIcon('p-element-save-icon') + ' ';
                break;

            case 'moveNode':
                icon = Phlexible.inlineIcon('p-element-publish-icon') + ' ';
                break;

            case 'publishNode':
            case 'publishTeaser':
                icon = Phlexible.inlineIcon('p-element-publish-icon') + ' ';
                break;

            case 'setNodeOffline':
            case 'setTeaserOffline':
                icon = Phlexible.inlineIcon('p-element-set_offline-icon') + ' ';
                break;

            case 'lock':
                icon = Phlexible.inlineIcon('p-element-set_offline-icon') + ' ';
                break;

            case 'unlock':
                icon = Phlexible.inlineIcon('p-element-set_offline-icon') + ' ';
                break;
        }

        return icon + v;
    }
});

Ext.reg('elements-historygrid', Phlexible.elements.HistoryGrid);
