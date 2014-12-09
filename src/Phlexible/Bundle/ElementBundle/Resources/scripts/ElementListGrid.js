Phlexible.elements.ElementListGrid = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.list,
    iconCls: 'p-element-tab_list-icon',
    cls: 'p-elements-elements-list',
    border: false,
    layout: 'border',

    sortMode: 'title',
    sortDir: 'asc',
    mode: 'node',

    initComponent: function () {

        this.element.on({
            load: this.onLoadElement,
            getlock: this.onGetLock,
            islocked: this.onRemoveLock,
            removelock: this.onRemoveLock,
            scope: this
        });

        var url = Phlexible.Router.generate('tree_list');
        if (this.mode == 'teaser') {
            url = Phlexible.Router.generate('teasers_layout_list');
        }

        // create the data store
        this.store = new Ext.data.JsonStore({
            url: url,
            root: 'list',
            totalProperty: 'total',
            id: 'id',
            fields: Phlexible.elements.ElementListRecord,
            remoteSort: true,
            sortInfo: {field: 'title', direction: 'desc'},
            baseParams: {
                limit: 25
            },
            listeners: {
                load: function (store) {
                    var data = store.reader.jsonData.parent;
                    var cols = this.getComponent(1).getView().mainHd.select('.x-grid3-cell-inner', true);
                    if (data.teaser_id) {
                        cols.elements[0].update(data.teaser_id);
                    }
                    else {
                        cols.elements[0].update(data.tid);
                    }
                    cols.elements[1].update(data.element_type);
                    cols.elements[2].set({'ext:qtip': data.qtip});
                    cols.elements[2].update('<img src="' + data.icon + '" width="18" height="18" style="vertical-align: middle;" /> ' + data.title);
                    cols.elements[3].update(Phlexible.Format.date(Date.parseDate(data.create_time, 'Y-m-d H:i:s')));
                    cols.elements[4].update(Phlexible.Format.date(Date.parseDate(data.publish_time, 'Y-m-d H:i:s')));
                    cols.elements[5].update(this.dateRenderer(Date.parseDate(data.custom_date, 'Y-m-d')));

                    var sortIcon;
                    if (data.sort_mode == 'title') {
                        if (data.sort_dir == 'desc') {
                            sortIcon = 'p-element-sort_alpha_up-icon';
                        } else {
                            sortIcon = 'p-element-sort_alpha_down-icon';
                        }
                    }
                    else if (data.sort_mode == 'createdate' || data.sort_mode == 'publishdate' || data.sort_mode == 'customdate') {
                        if (data.sort_dir == 'desc') {
                            sortIcon = 'p-element-sort_date_up-icon';
                        } else {
                            sortIcon = 'p-element-sort_date_down-icon';
                        }
                    }
                    else {
                        sortIcon = 'p-element-sort_free-icon';
                    }
                    cols.elements[6].update(Phlexible.inlineIcon(sortIcon));
                    //cols.elements[7].update(data.version_latest);
                    //cols.elements[8].update(data.version_online);

                    // hide / show "show all" checkbox
                    var pager = this.getComponent(1).getBottomToolbar();
                    if (pager.getPageData().pages > 1) {
                        pager.items.items[12].enable();
                    }
                    else {
                        pager.items.items[12].disable();
                    }
                },
                scope: this
            }
        });

        this.items = [
            {
                region: 'north',
                xtype: 'elements-listgridfilter',
                collapsible: true,
                collapsed: true,
                collapseMode: 'mini',
                //height: 90,
                element: this.element,
                mode: this.mode,
                listeners: {
                    filter: function (values) {
                        this.store.baseParams.filter = Ext.encode(values);
                        this.store.reload();
                    },
                    collapse: function (panel) {
                        this.getTopToolbar().items.items[6].toggle(false, true);
                    },
                    expand: function (panel) {
                        this.getTopToolbar().items.items[6].toggle(true, true);
                    },
                    scope: this
                }
            },
            {
                region: 'center',
                xtype: 'grid',
                autoExpandColumn: 'title',
                enableColumnHide: false,
                loadMask: {
                    msg: Phlexible.elements.Strings.loading_list + '...'
                },
                enableDragDrop: true,
                ddGroup: 'testtest',
                viewConfig: {
                    templates: {
                        header: new Ext.Template(
                            '<table border="0" cellspacing="0" cellpadding="0" style="{tstyle}">',
                            '<thead><tr class="x-grid3-hd-row">{cells}</tr></thead>',
                            '<tbody><tr class="x-grid3-hd-row m-parent-row">',
                            '<td class="x-grid3-col x-grid3-cell x-grid3-td-0 x-grid3-cell-first"><div class="x-grid3-cell-inner x-grid3-col-0">a</div></td>',
                            '<td class="x-grid3-col x-grid3-cell x-grid3-td-1"><div class="x-grid3-cell-inner x-grid3-col-1">b</div></td>',
                            '<td class="x-grid3-col x-grid3-cell x-grid3-td-2"><div class="x-grid3-cell-inner x-grid3-col-2">c</div></td>',
                            '<td class="x-grid3-col x-grid3-cell x-grid3-td-3"><div class="x-grid3-cell-inner x-grid3-col-3">d</div></td>',
                            '<td class="x-grid3-col x-grid3-cell x-grid3-td-4"><div class="x-grid3-cell-inner x-grid3-col-4">e</div></td>',
                            '<td class="x-grid3-col x-grid3-cell x-grid3-td-5"><div class="x-grid3-cell-inner x-grid3-col-5">f</div></td>',
                            '<td class="x-grid3-col x-grid3-cell x-grid3-td-6 x-grid3-cell-last"><div class="x-grid3-cell-inner x-grid3-col-6">g</div></td>',
                            '</tr></tbody>',
                            '</table>'
                        )
                    }
                },
                store: this.store,
                cm: new Ext.grid.ColumnModel({
                    columns: [
                        {
                            header: this.strings.tid,
                            dataIndex: 'tid',
                            width: 40,
                            sortable: true,
                            hidden: this.mode != 'node'
                        },
                        {
                            header: this.strings.id,
                            dataIndex: 'teaser_id',
                            width: 40,
                            sortable: true,
                            hidden: this.mode != 'teaser'
                        },
                        {
                            header: this.strings.elementtype,
                            dataIndex: 'element_type',
                            width: 120
                        },
                        {
                            id: 'title',
                            header: this.strings.title,
                            dataIndex: 'title',
                            width: 150,
                            sortable: true,
                            hideable: false,
                            renderer: function (value, metadata, r) {
                                if (r.get('restricted')) {
                                    value = '[' + value + ']';
                                }
                                if (r.get('navigation')) {
                                    value = '<span style="color: red; padding: 0;">»</span> ' + value;
                                }
                                metadata.attr = 'ext:qtip="' + r.get('qtip') + '"';
                                return '<img src="' + r.get('icon') + '" width="18" height="18" style="vertical-align: middle;" /> ' + value;
                            }
                        },
                        {
                            header: this.strings.created,
                            dataIndex: 'create_time',
                            width: 120,
                            sortable: true,
                            renderer: Phlexible.Format.date
                        },
                        {
                            header: this.strings.published,
                            dataIndex: 'publish_time',
                            width: 120,
                            sortable: true,
                            renderer: Phlexible.Format.date
                        },
                        {
                            header: this.strings.custom_date,
                            dataIndex: 'custom_date',
                            width: 120,
                            sortable: true,
                            renderer: this.dateRenderer
                        },
                        {
                            header: 'Sort',
                            dataIndex: 'sort',
                            width: 30,
                            sortable: true
                        },
                        {
                            header: 'LV',
                            dataIndex: 'version_latest',
                            hidden: true,
                            width: 40,
                            sortable: true
                        },
                        {
                            header: 'OV',
                            dataIndex: 'version_online',
                            hidden: true,
                            width: 40,
                            sortable: true,
                            renderer: function (s) {
                                if (!s) {
                                    return '-';
                                }

                                return s;
                            }
                            //        },{
                            //            header: 'Status',
                            //            width: 40,
                            //            dataIndex: 'status',
                            //            sortable: true
                        }
                    ]
                }),
                selModel: new Ext.grid.RowSelectionModel({
                    multiSelect: true,
                    listeners: {
                        selectionchange: function (sm) {
                            var records = sm.getSelections();

                            var tb = this.getTopToolbar();
                            if (records.length && this.element.data) {
                                var allowDuplicate = this.element.data.rights.indexOf('CREATE') !== -1,
                                    allowPublish = true,
                                    allowDelete = true;

                                if (this.mode == 'node') {
                                    Ext.each(records, function (r) {
                                        if (r.data.rights.indexOf('PUBLISH') === -1) {
                                            allowPublish = false;
                                        }
                                        if (r.data.rights.indexOf('DELETE') === -1) {
                                            allowDelete = false;
                                        }
                                    });
                                }
                                else {
                                    allowPublish = this.element.data.rights.indexOf('PUBLISH') !== -1;
                                    allowDelete = this.element.data.rights.indexOf('DELETE') !== -1;
                                }

                                if (allowDuplicate && records.length === 1) {
                                    tb.items.items[4].menu.items.items[0].enable();
                                }
                                else {
                                    tb.items.items[4].menu.items.items[0].disable();
                                }

                                if (allowPublish) {
                                    tb.items.items[4].menu.items.items[2].enable();
                                }
                                else {
                                    tb.items.items[4].menu.items.items[2].disable();
                                }

                                if (allowPublish && records.length === 1) {
                                    tb.items.items[4].menu.items.items[3].enable();
                                }
                                else {
                                    tb.items.items[4].menu.items.items[3].disable();
                                }

                                if (allowDelete) {
                                    tb.items.items[4].menu.items.items[5].enable();
                                }
                                else {
                                    tb.items.items[4].menu.items.items[5].disable();
                                }
                            }
                            else {
                                tb.items.items[4].menu.items.items[0].disable();
                                tb.items.items[4].menu.items.items[2].disable();
                                tb.items.items[4].menu.items.items[3].disable();
                                tb.items.items[4].menu.items.items[5].disable();
                            }
                        },
                        scope: this
                    }
                }),
                bbar: new Ext.PagingToolbar({
                    pageSize: 25,
                    store: this.store,
                    displayInfo: true,
                    displayMsg: this.strings.paging_display_msg,
                    emptyMsg: this.strings.paging_empty_msg,
                    beforePageText: this.strings.paging_before_page_text,
                    afterPageText: this.strings.paging_after_page_text,
                    items: [
                        '-',
                        {
                            xtype: 'checkbox',
                            boxLabel: this.strings.show_all_items,
                            handler: function (checkbox, checked) {
                                var pager = this.getComponent(1).getBottomToolbar();

                                if (checked) {
                                    pager.pageSize = 999999;
                                }
                                else {
                                    pager.pageSize = 25;
                                }

                                this.store.baseParams.limit = pager.pageSize;

                                this.store.reload({
                                    params: {
                                        start: 0
                                    }
                                });
                            },
                            scope: this
                        }]
                }),
                listeners: {
                    rowdblclick: function (grid, index) {
                        var r = grid.store.getAt(index);

                        if (!r) {
                            return;
                        }

                        if (r.data.teaser_id) {
                            this.fireEvent('listLoadTeaser', r.data.teaser_id);
                        }
                        else {
                            this.fireEvent('listLoadNode', r.data.tid);
                        }
                    },
                    render: function (grid) {
                        this.ddrow = new Ext.ux.dd.GridReorderDropTarget(grid, {
                            copy: false,
                            locked: true,
                            listeners: {
                                afterrowmove: {
                                    fn: function (objThis, oldIndex, newIndex, records) {
                                        if (this.sortMode !== 'free') {
                                            this.changeSort('free', 'asc', true);
                                        }
                                    },
                                    scope: this
                                }
                            }
                        });
                        // if you need scrolling, register the grid view's scroller with the scroll manager
                        //            Ext.dd.ScrollManager.register(grid.getView().getEditorParent());

                        grid.getView().dragZone.onValidDrop = function (target, e, id) {
                            this.hideProxy();
                            if (this.afterValidDrop) {
                                /**
                                 * An empty function by default, but provided so that you can perform a custom action
                                 * after a valid drop has occurred by providing an implementation.
                                 * @param {Object} target The target DD
                                 * @param {Event} e The event object
                                 * @param {String} id The id of the dropped element
                                 * @method afterInvalidDrop
                                 */
                                this.afterValidDrop(target, e, id);
                            }
                        };
                    },
                    xbeforedestroy: function (grid) {
                        // if you previously registered with the scroll manager, unregister it (if you don't it will lead to problems in IE)
                        //Ext.dd.ScrollManager.unregister(g.getView().getEditorParent());
                    },
                    scope: this
                }
            }
        ];

        this.tbar = [
            {
                // 0
                xtype: 'tbtext',
                text: this.strings.sort + ':'
            },
            {
                // 1
                text: this.strings.sort,
                iconCls: 'p-element-sort_alpha_down-icon',
                disabled: true,
                menu: [
                    {
                        // 0
                        text: this.strings.title_asc,
                        menu: [
                            {
                                text: this.strings.ascending,
                                iconCls: 'p-element-sort_alpha_down-icon',
                                handler: function () {
                                    this.changeSort('title', 'asc');
                                },
                                scope: this
                            },
                            {
                                text: this.strings.descending,
                                iconCls: 'p-element-sort_alpha_up-icon',
                                handler: function () {
                                    this.changeSort('title', 'desc');
                                },
                                scope: this
                            }
                        ]
                    },
                    {
                        // 1
                        text: this.strings.create_date,
                        menu: [
                            {
                                text: this.strings.ascending,
                                iconCls: 'p-element-sort_date_down-icon',
                                handler: function () {
                                    this.changeSort('createdate', 'asc');
                                },
                                scope: this
                            },
                            {
                                text: this.strings.descending,
                                iconCls: 'p-element-sort_date_up-icon',
                                handler: function () {
                                    this.changeSort('createdate', 'desc');
                                },
                                scope: this
                            }
                        ]
                    },
                    {
                        // 2
                        text: this.strings.publish_date,
                        menu: [
                            {
                                text: this.strings.ascending,
                                iconCls: 'p-element-sort_date_down-icon',
                                handler: function () {
                                    this.changeSort('publishdate', 'asc');
                                },
                                scope: this
                            },
                            {
                                text: this.strings.descending,
                                iconCls: 'p-element-sort_date_up-icon',
                                handler: function () {
                                    this.changeSort('publishdate', 'desc');
                                },
                                scope: this
                            }
                        ]
                    },
                    {
                        // 3
                        text: this.strings.custom_date,
                        menu: [
                            {
                                text: this.strings.ascending,
                                iconCls: 'p-element-sort_date_down-icon',
                                handler: function () {
                                    this.changeSort('customdate', 'asc');
                                },
                                scope: this
                            },
                            {
                                text: this.strings.descending,
                                iconCls: 'p-element-sort_date_up-icon',
                                handler: function () {
                                    this.changeSort('customdate', 'desc');
                                },
                                scope: this
                            }
                        ]
                    },
                    {
                        // 4
                        text: this.strings.free,
                        iconCls: 'p-element-sort_free-icon',
                        handler: function () {
                            this.changeSort('free', 'asc');
                        },
                        scope: this
                    }
                ]
            },
            {
                // 2
                text: this.strings.publish_sort,
                iconCls: 'p-element-publish-icon',
                disabled: true,
                handler: function () {
                    this.onPublishSort();
                },
                scope: this
            },
            '-',
            {
                // 4
                text: this.strings.actions,
                iconCls: 'p-element-action-icon',
                menu: [
                    {
                        text: this.strings.duplicate,
                        iconCls: 'p-element-copy-icon',
                        disabled: true,
                        handler: this.duplicateNode,
                        scope: this
                    },
                    '-',
                    {
                        text: this.strings.publish,
                        iconCls: 'p-element-publish-icon',
                        disabled: true,
                        handler: this.publishNode,
                        scope: this
                    },
                    {
                        text: this.strings.set_offline,
                        iconCls: 'p-element-set_offline-icon',
                        disabled: true,
                        handler: this.setNodeOffline,
                        scope: this
                    },
                    '-',
                    {
                        text: this.strings.delete_element,
                        iconCls: 'p-element-delete-icon',
                        disabled: true,
                        handler: this.deleteNode,
                        scope: this
                    }
                ]
            },
            '-',
            {
                // 6
                text: this.strings.filter,
                iconCls: 'p-element-filter-icon',
                enableToggle: true,
                pressed: false,
                toggleHandler: function (btn, state) {
                    if (state) {
                        this.getComponent(0).expand();
                    }
                    else {
                        this.getComponent(0).collapse();
                    }
                },
                scope: this
            }
        ];

        this.on({
            show: function () {
                if (this.store.baseParams.tid != this.element.tid) {
                    // lazy load
                    this.doLoad(this.element);
                }
            },
            scope: this
        });

        Phlexible.elements.ElementListGrid.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        if (element.properties.et_type == 'part') {
            this.disable();
            this.store.removeAll();
            return;
        } else {
            this.enable();
        }

        if (!this.hidden) {
            this.doLoad(element);
        }
    },

    doLoad: function (element) {
        if (element.properties.et_type == 'area') {
            this.store.baseParams.tid = element.tid;
            this.store.baseParams.language = element.language;
            this.store.baseParams.area_id = element.area_id;
            this.onGetLock();
        }
        /*else if (element.properties.et_type == 'part') {
         this.store.proxy.conn.url = Phlexible.Router.generate('teasers_layout_list');
         this.store.baseParams = {
         tid: element.tid,
         teaser_id: element.properties.teaser_id
         };
         this.getColumnModel().setHidden(0, true);
         this.getColumnModel().setHidden(1, false);

         var tb = this.getTopToolbar();
         tb.items.items[0].hide();
         }*/
        else {
            this.enable();
            this.store.baseParams.tid = element.tid;
            this.store.baseParams.language = element.language;

            //var tb = this.getTopToolbar();
            //tb.items.items[0].menu.items.items[0].enable();
        }

        this.updateSortButton(element.properties.sort_mode, element.properties.sort_dir);

        this.store.load();
    },

    updateSortButton: function (mode, dir) {
        var sortItem = this.getTopToolbar().items.items[1];
        var sortItems = sortItem.menu.items.items;
        var text = '';
        var iconCls = '';

        this.sortMode = mode;
        this.sortDir = dir;

        switch (mode) {
            case 'title':
                if (dir == 'desc') {
                    text = this.strings.title_desc;
                    iconCls = 'p-element-sort_alpha_up-icon';
                } else {
                    text = this.strings.title_asc;
                    iconCls = 'p-element-sort_alpha_down-icon';
                }
                this.store.setDefaultSort('title', dir.toUpperCase());
                this.ddrow.lock();
                break;

            case 'createdate':
                if (dir == 'desc') {
                    text = this.strings.createdate_desc;
                    iconCls = 'p-element-sort_date_up-icon';
                }
                else {
                    text = this.strings.createdate_asc;
                    iconCls = 'p-element-sort_date_down-icon';
                }
                this.store.setDefaultSort('create_time', dir.toUpperCase());
                this.ddrow.lock();
                break;

            case 'publishdate':
                if (dir == 'desc') {
                    text = this.strings.publishdate_desc;
                    iconCls = 'p-element-sort_date_up-icon';
                }
                else {
                    text = this.strings.publishdate_asc;
                    iconCls = 'p-element-sort_date_down-icon';
                }
                this.store.setDefaultSort('publish_time', dir.toUpperCase());
                this.ddrow.lock();
                break;

            case 'customdate':
                if (dir == 'desc') {
                    text = this.strings.customdate_desc;
                    iconCls = 'p-element-sort_date_up-icon';
                }
                else {
                    text = this.strings.customdate_asc;
                    iconCls = 'p-element-sort_date_down-icon';
                }
                this.store.setDefaultSort('custom_date', dir.toUpperCase());
                this.ddrow.lock();
                break;

            case 'sort':
            default:
                text = this.strings.free;
                iconCls = 'p-element-sort_free-icon';

                this.store.setDefaultSort('sort', 'ASC');
                if (!this.ddrow.isLocked()) this.ddrow.unlock();
                break;
        }

        sortItem.setText(text);
        sortItem.setIconClass(iconCls);
    },

    doSort: function (mode, dir) {
        switch (mode) {
            case 'title':
                this.store.sort('title', dir.toUpperCase());
                break;

            case 'createdate':
                this.store.sort('create_time', dir.toUpperCase());
                break;

            case 'publishdate':
                this.store.sort('publish_time', dir.toUpperCase());
                break;

            case 'customdate':
                this.store.sort('custom_date', dir.toUpperCase());
                break;

            case 'free':
            default:
                this.store.sort('sort', dir.toUpperCase());
        }
    },

    changeSort: function (mode, dir, noSort, forcePublish) {
        var tb = this.getTopToolbar();

        if (this.sortMode != mode) {
            this.sortMode = mode;
            tb.items.items[2].enable();
        }

        if (this.sortDir != dir) {
            this.sortDir = dir;
            tb.items.items[2].enable();
        }

        if (forcePublish) {
            tb.items.items[2].enable();
        }

        if (mode === 'free') { // && this.mode == 'node') {
            tb.items.items[2].enable();
        }

        this.updateSortButton(mode, dir);
        this.doSort(mode, dir);
    },

    onPublishSort: function () {
        var records = this.store.getRange();
        var sortIds = [];

        var url = Phlexible.Router.generate('tree_list_sort');
        var id_field = 'tid';
        if (this.element.properties.et_type == 'area') {
            url = Phlexible.Router.generate('teasers_layout_sort');
            id_field = 'teaser_id';
        }

        for (var i = 0; i < records.length; i++) {
            sortIds.push(records[i].get(id_field));
        }
        var params = {
            tid: this.element.tid,
            eid: this.element.eid,
            area_id: this.element.area_id || null,
            mode: this.sortMode,
            dir: this.sortDir,
            sort_ids: Ext.encode(sortIds)
        };

        Ext.Ajax.request({
            url: url,
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    var tb = this.getTopToolbar();
                    tb.items.items[2].disable();
                    this.store.reload();

                    if (this.element.properties.et_type == 'area') {
                        this.fireEvent('sortArea');
                    }
                    else {
                        var node = this.element.getTreeNode();
                        node.attributes.sort_mode = this.sortMode;
                        node.attributes.children = false;
                        node.reload();
                    }
                    Phlexible.success(data.msg);
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    duplicateNode: function () {
        var records = this.getComponent(1).getSelectionModel().getSelections();

        if (!records.length || records.length > 1) {
            return;
        }

        var tid = records[0].data.tid;
        var parent_tid = this.element.tid;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('tree_copy'),
            params: {
                for_tree_id: tid,
                id: parent_tid,
                prev_id: tid
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.fireEvent('listReloadNode', this.element.tid);

                    this.getComponent(1).getStore().reload();

                    Phlexible.success(data.msg);
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    publishNode: function () {
        var records = this.getComponent(1).getSelectionModel().getSelections();

        if (!records.length) {
            return;
        }

        var params = {
            tid: this.element.tid,
            version: this.element.version,
            language: this.element.language,
            data: []
        };

        Ext.each(records, function (r) {
            params.data.push({
                tid: r.data.tid,
                version: r.data.version_latest
            });
        });

        params.data = Ext.encode(params.data);

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_publish_advancedpublish'),
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.fireEvent('listReloadNode', this.element.tid);

                    this.getComponent(1).getStore().reload();

                    Phlexible.success(data.msg);
                }
                else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    setNodeOffline: function () {
        var records = this.getComponent(1).getSelectionModel().getSelections();

        if (!records.length || records.length > 1) {
            return;
        }

        var params = {
            tid: records[0].data.tid,
            language: this.element.language
        };

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_publish_setoffline'),
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.fireEvent('listReloadNode', this.element.tid);

                    this.getComponent(1).getStore().reload();

                    Phlexible.success(data.msg);
                }
                else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    deleteNode: function (menu) {
        var records = this.getComponent(1).getSelectionModel().getSelections();

        if (!records.length) {
            return;
        }

        if (records.length === 1) {
            var msg = this.strings.confirm_delete_element;
        }
        else {
            var msg = this.strings.confirm_delete_elements;
        }
        Ext.MessageBox.confirm(this.strings.warning, msg, function (btn) {
            if (btn == 'yes') {
                var params = {}, i = 0;

                Ext.each(records, function (r) {
                    params['id[' + i + ']'] = r.data.tid;
                    i++;
                });

                /* check for instances if only one node selected */
                if (i == 1) {
                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('tree_delete_check'),
                        params: params,
                        success: function (response) {
                            var result = Ext.decode(response.responseText);

                            // no instances
                            if (!result.data.length) {
                                Ext.Ajax.request({
                                    url: Phlexible.Router.generate('tree_delete'),
                                    params: params,
                                    success: function (response) {
                                        var data = Ext.decode(response.responseText);

                                        if (data.success) {
                                            this.fireEvent('listReloadNode', this.element.tid);

                                            this.getComponent(1).getStore().reload();

                                            Phlexible.success(data.msg);
                                        }
                                        else {
                                            Ext.MessageBox.alert('Failure', data.msg);
                                        }
                                    },
                                    scope: this
                                });
                            } else {
                                var w = new Phlexible.elements.DeleteInstancesWindow({
                                    data: result.data,
                                    parentId: this.element.tid
                                });
                                w.show();
                            }
                        },
                        scope: this
                    });
                } else {
                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('tree_delete'),
                        params: params,
                        success: function (response) {
                            var data = Ext.decode(response.responseText);

                            if (data.success) {
                                this.fireEvent('listReloadNode', this.element.tid);

                                this.getComponent(1).getStore().reload();

                                Phlexible.success(data.msg);
                            }
                            else {
                                Ext.MessageBox.alert('Failure', data.msg);
                            }
                        },
                        scope: this
                    });
                }
            }
        }, this);
    },

    dateRenderer: function (date) {
        var newDate = "";
        if (date) {
            if (date.length == 10) {
                newDate = Date.parseDate(date, "Y-m-d", true).format('Y-m-d');
            }
            else if (date.length == 19) {
                newDate = Date.parseDate(date, "Y-m-d H:i:s", true).format('Y-m-d H:i:s');
            }
            else {
                newDate = 'Invalid date';
            }
        }
        return newDate;
    },

    onGetLock: function () {
        var tb = this.getTopToolbar();
        tb.enable();

        if (this.mode == 'node') {
            tb.items.items[1].enable();
            tb.items.items[2].enable();
        } else if (this.mode == 'teaser') {
            tb.items.items[1].disable();
            tb.items.items[2].enable();
        } else {
            tb.items.items[1].disable();
            tb.items.items[2].disable();
        }

        this.ddrow.unlock();
    },

    onRemoveLock: function () {
        var tb = this.getTopToolbar();
        tb.disable();

        tb.items.items[1].disable();

        this.ddrow.lock();
        tb.items.items[6].enable();

    }
});

Ext.reg('elements-elementlistgrid', Phlexible.elements.ElementListGrid);
