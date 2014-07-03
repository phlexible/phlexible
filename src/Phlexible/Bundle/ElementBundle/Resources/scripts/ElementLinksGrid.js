Phlexible.elements.ElementLinksGrid = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.elements.Strings.links,
    strings: Phlexible.elements.Strings,
    iconCls: 'p-element-tab_link-icon',
    autoExpandColumn: 2,
    viewConfig: {
        emptyText: Phlexible.elements.Strings.no_links_found
    },

    includeIncoming: 0,

    initComponent: function () {
        this.element.on('load', this.onLoadElement, this);

        // create the data store
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('elements_links_list'),
            root: 'links',
            totalProperty: 'total',
            id: 'id',
            fields: ['id', 'iconCls', 'type', 'title', 'content', 'link', 'raw']
        });

        // create the column model
        this.columns = [
            {
                header: this.strings.type,
                width: 100,
                dataIndex: 'type',
                renderer: function (v, md, r) {
                    if (r.data.iconCls) {
                        v = Phlexible.inlineIcon(r.data.iconCls) + ' ' + v;
                    }

                    return v;
                }
            },
            {
                header: this.strings.field,
                width: 250,
                dataIndex: 'title'
            },
            {
                header: this.strings.content,
                width: 300,
                dataIndex: 'content'
            },
            {
                header: this.strings.raw,
                width: 200,
                hidden: true,
                dataIndex: 'raw'
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


        this.tbar = [
            {
                text: this.strings.include_incoming_links,
                iconCls: 'p-fields-field_link-icon',
                enableToggle: true,
                pressed: this.includeIncoming,
                toggleHandler: function (btn, state) {
                    this.includeIncoming = state ? 1 : 0;

                    this.store.baseParams.incoming = this.includeIncoming;

                    this.store.reload();
                },
                scope: this
            }
        ];

        this.on({
            show: {
                fn: function () {
                    if (this.store.baseParams.tid != this.element.tid ||
                        this.store.baseParams.version != this.element.version ||
                        this.store.baseParams.language != this.element.language) {
                        this.onRealLoad(this.element.tid, this.element.version, this.element.language);
                    }
                },
                scope: this
            },
            rowdblclick: {
                fn: function (grid, rowIndex) {
                    var record = grid.store.getAt(rowIndex);
                    if (record) {
                        var link = record.get('link');

                        if (link && link.handler) {
                            var handler = link.handler;
                            if (typeof handler == 'string') {
                                handler = Phlexible.evalClassString(handler);
                            }
                            handler(link);
                        }
                    }
                },
                scope: this
            }
        });

        Phlexible.elements.ElementLinksGrid.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        this.store.removeAll();

        if (element.properties.et_type == Phlexible.elementtypes.TYPE_FULL) {
            this.enable();

            if (!this.hidden) {
                this.onRealLoad(this.element.tid, this.element.version, this.element.language);
            }
        } else {
            this.disable();
        }
    },

    onRealLoad: function (tid, version, language) {
        this.store.baseParams = {
            tid: tid,
            version: version,
            language: language,
            incoming: this.includeIncoming
        };

        this.store.load();
    }
});

Ext.reg('elements-elementlinksgrid', Phlexible.elements.ElementLinksGrid);