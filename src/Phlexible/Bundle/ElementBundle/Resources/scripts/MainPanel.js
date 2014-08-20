Phlexible.elements.MediaResourceTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="p-elements-result-wrap" id="result-wrap-{id}" style="text-align: center">',
    '<div><img src="{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.id, template_key: \"_mm_large\"})]}" width="96" height="96" /></div>',
    '<span>{name}</span>',
    '</div>',
    '</tpl>'
);

/**
 * Input params:
 * - id
 *   Siteroot ID
 * - title
 *   Siteroot Title
 */
Phlexible.elements.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.elements.Strings.content,
    strings: Phlexible.elements.Strings,
    layout: 'border',
    closable: true,
    border: false,
    cls: 'p-elements-main-panel',
    iconCls: 'p-element-component-icon',

    baseTitle: '',

    loadParams: function (params) {
        if (params.diff) {
            this.elementPanel.getComponent(1).getComponent(1).diffParams = params.diff;
        }

        // load element
        if (params.id) {
            var loadParams = {
                id: params.id,
                version: null
            };
            if (params.language) {
                loadParams.language = params.language;
            }
            this.element.reload(loadParams);
        }
        // load tree
        if (params.start_tid_path) {
            if (params.start_tid_path && params.start_tid_path.substr(0, 3) !== '/-1') {
                params.start_tid_path = '/-1' + params.start_tid_path;
            }
            var n = this.elementsTree.getSelectionModel().getSelectedNode();
            if (!n || n.getPath() !== params.start_tid_path) {
                this.skipLoad = true;
                this.elementsTree.selectPath(params.start_tid_path, 'id');
            }
        }
    },

    initComponent: function () {
        this.addEvents('load');

        if (this.params.start_tid_path) {
            this.skipLoad = true;
            if (this.params.start_tid_path.substr(0, 3) !== '/-1') {
                this.params.start_tid_path = '/-1' + this.params.start_tid_path;
            }
        }

        if (this.params.title) {
            this.setTitle(this.params.title);
            this.baseTitle = this.params.title;
        }

        this.element = new Phlexible.elements.Element({
            siteroot_id: this.params.siteroot_id,
            language: Phlexible.Config.get('language.frontend'),
            startParams: this.params
        });

        this.element.on({
            load: this.onLoadElement,
            scope: this
        });

        this.element.on('beforeload', this.disable, this);
        this.element.on('beforeSave', this.disable, this);
        this.element.on('beforeSetOffline', this.disable, this);
        this.element.on('beforeSetOfflineAdvanced', this.disable, this);

        this.element.on('load', this.enable, this);
        this.element.on('saveFailure', this.enable, this);
        this.element.on('publishFailure', this.enable, this);
        this.element.on('publishAdvancedFailure', this.enable, this);
        this.element.on('setOfflineFailure', this.enable, this);
        this.element.on('setOfflineAdvancedFailure', this.enable, this);

        this.elementsTree = new Phlexible.elements.ElementsTree({
            region: 'center',
            header: false,
            element: this.element,
            start_tid_path: this.params.start_tid_path || false,
            listeners: {
                nodeSelect: this.onNodeSelect,
                newElement: function (node) {
                    this.element.showNewElementWindow(node);
                },
                newAlias: function (node) {
                    this.element.showNewAliasWindow(node);
                },
                scope: this
            }
        });

        //        this.layoutPanel = new Phlexible.elements.ElementLayoutPanel({
        //            element: this.element
        //        });

        this.elementPanel = new Phlexible.elements.ElementPanel({
            element: this.element,
            listeners: {
                listLoadTeaser: function (teaser_id) {
                    this.contentPanel.getLayout().setActiveItem(this.elementPanelIndex);
                    this.element.loadTeaser(teaser_id, null, null, 1);
                },
                listLoadNode: function (tid) {
                    var node = this.elementsTree.getNodeById(tid);
                    if (node) {
                        // uber-node settings gedöns
                        node.select();
                        node.expand();
                        node.ensureVisible();
                        this.element.setTreeNode(node);
                    } else {
                        this.element.setTreeNode(null);
                    }
                    this.element.load(tid, null, null, 1);
                },
                listReloadNode: function (tid) {
                    var node = this.elementsTree.getNodeById(tid);
                    if (node) {
                        // uber-node settings gedöns
                        node.select();
                        node.expand();
                        node.ensureVisible();
                        this.element.setTreeNode(node);
                        node.reload();
                    } else {
                        this.element.setTreeNode(null);
                    }
                },
                scope: this
            }
        });

        var dummyElement = new Phlexible.elements.Element({});
        dummyElement.properties = {
            et_type: 'area'
        };
        this.layoutListPanel = new Phlexible.elements.ElementListGrid({
            element: dummyElement,
            mode: 'teaser',
            listeners: {
                listLoadTeaser: function (teaser_id) {
                    this.contentPanel.getLayout().setActiveItem(this.elementPanelIndex);
                    this.element.loadTeaser(teaser_id, null, null, 1);
                },
                sortArea: function () {
                    this.layoutTree.getRootNode().reload();
                },
                scope: this
            }
        });

        this.layoutTree = new Phlexible.elements.ElementLayoutTree({
            region: 'south',
            height: 200,
            split: true,
            collapsible: true,
            // collapseMode: 'mini',
            collapsed: true,
            element: this.element,
            listeners: {
                teaserselect: function (teaser_id, node, language) {
                    // this.dataPanel.disable();
                    // this.catchPanel.disable();
                    // this.getTopToolbar().enable();
                    if (!language) language = null;

                    this.element.setTeaserNode(node);

                    this.contentPanel.getLayout().setActiveItem(this.elementPanelIndex);
                    this.element.loadTeaser(teaser_id, false, language, true);
                },
                catchselect: function (catchId, catchConfig) {
                    // this.dataPanel.disable();
                    // this.catchPanel.disable();
                    // this.getTopToolbar().disable();

                    var win = this.createCatchPanel();
                    win.show();
                    win.getComponent(0).setValues(catchId, catchConfig);
                },
                areaselect: function (area_id, node) {
                    // this.dataPanel.disable();
                    // this.catchPanel.disable();
                    // this.getTopToolbar().enable();
                    this.contentPanel.getLayout().setActiveItem(this.layoutListPanelIndex);
                    this.layoutListPanel.element.tid = this.element.tid;
                    this.layoutListPanel.element.eid = this.element.eid;
                    this.layoutListPanel.element.language = this.element.language;
                    this.layoutListPanel.element.area_id = area_id;
                    this.layoutListPanel.element.treeNode = node.getOwnerTree().getRootNode();
                    this.layoutListPanel.element.properties.sort_mode = 'free';
                    this.layoutListPanel.element.properties.sort_dir = 'asc';
                    this.layoutListPanel.doLoad(this.layoutListPanel.element);
                },
                scope: this
            }
        });

        this.contentPanel = new Ext.Panel({
            region: 'center',
            header: false,
            layout: 'card',
            activeItem: 0,
            border: false,
            hideMode: 'offsets',
            items: [
                this.elementPanel,
                this.layoutListPanel
            ]
        });

        this.elementPanelIndex = 0;
        this.layoutListPanelIndex = 1;

        this.items = [
            {
                region: 'west',
                header: false,
                width: 230,
                split: true,
                collapsible: true,
                collapseMode: 'mini',
                border: false,
                layout: 'fit',
                items: [
                    {
                        xtype: 'tabpanel',
                        activeTab: 0,
                        border: true,
                        cls: 'p-elements-resource-tabs',
                        items: [
                            {
                                title: '&nbsp;',
                                tabTip: this.strings.tree,
                                iconCls: 'p-element-tree-icon',
                                layout: 'border',
                                border: false,
                                items: [
                                    this.elementsTree,
                                    this.layoutTree
                                ]
                            },
                            {
                                xtype: 'grid',
                                tabTip: this.strings.element_search,
                                title: '&nbsp;',
                                iconCls: 'p-frontend-preview-icon',
                                cls: 'p-elements-resource-search-panel',
                                viewConfig: {
                                    forceFit: true,
                                    emptyText: this.strings.no_results,
                                    deferEmptyText: false
                                },
                                store: new Ext.data.JsonStore({
                                    url: Phlexible.Router.generate('elements_search_elements'),
                                    baseParams: {
                                        siteroot_id: this.element.siteroot_id,
                                        query: '',
                                        language: this.element.language
                                    },
                                    root: 'results',
                                    fields: ['tid', 'version', 'title', 'icon'],
                                    sortInfo: {field: 'title', direction: 'ASC'}
                                }),
                                sm: new Ext.grid.RowSelectionModel({
                                    singleSelect: true
                                }),
                                columns: [
                                    {
                                        dataIndex: 'title',
                                        header: this.strings.elements,
                                        renderer: function (v, md, r) {
                                            var icon = '<img src="' + r.data.icon + '" width="18" height="18" style="vertical-align: middle;" />';
                                            var title = r.data.title;
                                            var meta = r.data.tid;

                                            return icon + ' ' + title + ' [' + meta + ']';
                                        }
                                    }
                                ],
                                tbar: [
                                    {
                                        xtype: 'textfield',
                                        emptyText: this.strings.element_search,
                                        enableKeyEvents: true,
                                        anchor: '-10',
                                        listeners: {
                                            render: function (c) {
                                                c.task = new Ext.util.DelayedTask(c.doSearch, this);
                                            },
                                            keyup: function (c, event) {
                                                if (event.getKey() == event.ENTER) {
                                                    c.task.cancel();
                                                    c.doSearch();
                                                    return;
                                                }

                                                c.task.delay(500);
                                            },
                                            scope: this
                                        },
                                        doSearch: function () {
                                            var c = this.getComponent(0).getComponent(0).getComponent(1);
                                            var query = c.getTopToolbar().items.items[0].getValue();
                                            if (!query) return;
                                            var store = c.getStore();
                                            store.baseParams.query = query;
                                            store.baseParams.language = this.element.language;
                                            store.load();
                                        }.createDelegate(this)
                                    }
                                ],
                                listeners: {
                                    render: function (c) {
                                        Phlexible.console.log('search.onRender');
                                    },
                                    rowdblclick: function (c, itemIndex) {
                                        var r = c.getStore().getAt(itemIndex);
                                        if (!r) return;
                                        this.element.reload({id: r.data.tid, version: r.data.version, language: this.element.language, lock: 1});
                                    },
                                    scope: this
                                }
                            },
                            {
                                xtype: 'panel',
                                tabTip: this.strings.media_search,
                                title: '&nbsp;',
                                iconCls: 'p-mediamanager-component-icon',
                                layout: 'fit',
                                bodyStyle: 'padding: 5px',
                                autoScroll: true,
                                border: false,
                                items: {
                                    xtype: 'dataview',
                                    cls: 'p-elements-resource-media-panel',
                                    store: new Ext.data.JsonStore({
                                        url: Phlexible.Router.generate('elements_search_media'),
                                        baseParams: {
                                            siteroot_id: this.element.siteroot_id,
                                            query: ''
                                        },
                                        root: 'results',
                                        fields: ['id', 'version', 'name', 'folder_id'],
                                        autoLoad: false
                                    }),
                                    itemSelector: 'div.p-elements-result-wrap',
                                    overClass: 'p-elements-result-wrap-over',
                                    style: 'overflow: auto',
                                    singleSelect: true,
                                    emptyText: this.strings.no_results,
                                    deferEmptyText: false,
                                    //autoHeight: true,
                                    tpl: Phlexible.elements.MediaResourceTemplate,
                                    listeners: {
                                        render: function (c) {
                                            Phlexible.console.log('search.onRender');
                                            var v = c;
                                            this.imageDragZone = new Ext.dd.DragZone(v.getEl(), {
                                                ddGroup: 'imageDD',
                                                containerScroll: true,
                                                getDragData: function (e) {
                                                    var sourceEl = e.getTarget(v.itemSelector, 10);
                                                    if (sourceEl) {
                                                        d = sourceEl.cloneNode(true);
                                                        d.id = Ext.id();
                                                        return v.dragData = {
                                                            sourceEl: sourceEl,
                                                            repairXY: Ext.fly(sourceEl).getXY(),
                                                            ddel: d,
                                                            record: v.getRecord(sourceEl)
                                                        };
                                                    }
                                                },
                                                getRepairXY: function () {
                                                    return this.dragData.repairXY;
                                                }
                                            });
                                        },
                                        contextmenu: function (view, index, node, event) {
                                            var record = view.store.getAt(index);
                                            if (!record) {
                                                return;
                                            }

                                            if (this.imageSearchContextMenu) {
                                                this.imageSearchContextMenu.destroy();
                                            }

                                            this.imageSearchContextMenu = new Ext.menu.Menu({
                                                items: [
                                                    {
                                                        text: 'File Links',
                                                        handler: function (menu) {
                                                            var window = new Phlexible.elements.FileLinkWindow({
                                                                file_id: record.data.id,
                                                                file_name: record.data.name
                                                            });
                                                            window.show();
                                                        },
                                                        scope: this
                                                    }
                                                ]
                                            });

                                            event.stopEvent();
                                            var coords = event.getXY();

                                            this.imageSearchContextMenu.showAt([coords[0], coords[1]]);

                                        },
                                        scope: this
                                    }
                                },
                                tbar: [
                                    {
                                        xtype: 'textfield',
                                        emptyText: this.strings.media_search,
                                        enableKeyEvents: true,
                                        anchor: '-10',
                                        listeners: {
                                            render: function (c) {
                                                c.task = new Ext.util.DelayedTask(c.doSearch, this);
                                            },
                                            keyup: function (c, event) {
                                                if (event.getKey() == event.ENTER) {
                                                    c.task.cancel();
                                                    c.doSearch();
                                                    return;
                                                }

                                                c.task.delay(500);
                                            },
                                            scope: this
                                        },
                                        doSearch: function () {
                                            var viewWrap = this.getComponent(0).getComponent(0).getComponent(2);
                                            var view = viewWrap.getComponent(0);
                                            var query = viewWrap.getTopToolbar().items.items[0].getValue();
                                            if (!query) return;
                                            var store = view.getStore();
                                            store.baseParams.query = query;
                                            store.load();
                                        }.createDelegate(this)
                                    }
                                ]
                            },
                            {
                                xtype: 'grid',
                                tabTip: this.strings.history,
                                title: '&nbsp;',
                                iconCls: 'p-element-tab_history-icon',
                                cls: 'p-elements-resource-history-panel',
                                border: false,
                                viewConfig: {
                                    forceFit: true
                                },
                                store: new Ext.data.SimpleStore({
                                    fields: ['tid', 'version', 'language', 'title', 'icon', 'ts'],
                                    sortInfo: {field: 'ts', direction: 'DESC'}
                                }),
                                sm: new Ext.grid.RowSelectionModel({
                                    singleSelect: true
                                }),
                                columns: [
                                    {
                                        dataIndex: 'title',
                                        header: this.strings.history,
                                        renderer: function (v, md, r) {
                                            var icon = '<img src="' + r.data.icon + '" width="18" height="18" style="vertical-align: middle;" />';
                                            var date = Math.floor(new Date().getTime() / 1000 - r.data.ts / 1000);
                                            if (date) {
                                                date = 'Geöffnet vor ' + Phlexible.Format.age(date);
                                            } else {
                                                date = 'Gerade geöffnet';
                                            }
                                            var title = r.data.title;
                                            var meta = r.data.tid + ', v' + r.data.version + ', ' + r.data.language;

                                            return icon + ' ' + title + ' [' + meta + ']<br />' +
                                                '<span style="color: gray; font-size: 10px;">' + date + '</span>';
                                        }
                                    }
                                ],
                                listeners: {
                                    render: function (c) {
                                        Phlexible.console.log('history.onRender');
                                    },
                                    rowdblclick: function (c, itemIndex) {
                                        var r = c.getStore().getAt(itemIndex);
                                        if (!r) return;
                                        this.element.reload({id: r.data.tid, version: r.data.version, language: r.data.language, lock: 1});
                                    },
                                    scope: this
                                }
                            }
                        ]
                    }
                ]
            },
            this.contentPanel
        ];

        this.tbar = new Phlexible.elements.TopToolbar({
            element: this.element
        });

        this.element.on({
            historychange: function () {
                var store = this.getComponent(0).getComponent(0).getComponent(3).getStore();
                store.loadData(this.element.history.getRange());
            },
            scope: this
        });

        Phlexible.elements.MainPanel.superclass.initComponent.call(this);

        this.on({
            render: function () {
                if (this.params.id) {
                    this.element.reload({
                        id: this.params.id,
                        lock: 1
                    });
                }
            },
            close: function () {
                // remove lock if element is currently locked by me
                if (this.element.getLockStatus() == 'edit') {
                    this.element.unlock(Ext.emptyFn);
                }
            },
            scope: this
        });

        //        this.on('render', function() {
        //            this.mask = new Ext.LoadMask(this.el,{
        //                msg: 'Loading Element',
        //                removeMask: false
        //            });
        //        }, this);

        //    this.elementsTree.on('render', function(tree) {
        //        tree.load();
        //    });
        //    this.elementsTree.root.on('load', function(node) {
        //        node.item(0).select();
        //        this.load(node.item(0).id)
        //    }, this);

    },

    onLoadElement: function (element) {
        //var properties = element.properties;

        // update element panel title
        switch (element.properties.et_type) {
            case 'part':
                this.setTitle(this.baseTitle + ' :: ' + this.strings['part_element'] + ' "' + element.title + '" (Teaser ID: ' + element.properties.teaser_id + ' - ' + this.strings.language + ': ' + element.language + ' - ' + this.strings.version + ': ' + element.version + ')');
                break;

            case 'full':
            default:
                this.setTitle(this.baseTitle + ' :: ' + this.strings[element.properties.et_type + '_element'] + ' "' + element.title + '" (' + this.strings.tid + ': ' + element.tid + ' - ' + this.strings.language + ': ' + element.language + ' - ' + this.strings.version + ': ' + element.version + ')');
                break;

        }

        this.setIconClass(null);
        if (this.header) {
            var el = Ext.get(this.header.query('img')[0]);
            el.dom.src = element.icon;
            el.addClass('element-icon');
        }
        //this.setIcon(element.icon);
        //this.mask.hide();
    },

    onNodeSelect: function (node, doLock) {
        if (!node) {
            return;
        }

        this.contentPanel.getLayout().setActiveItem(this.elementPanelIndex);

        this.element.setTreeNode(node);
        node.expand();

        if (!this.skipLoad) {
            this.element.load(node.id, null, null, doLock);
        } else {
            this.skipLoad = false;
        }
    },

    createCatchPanel: function () {
        return new Ext.Window({
            title: this.strings.catch,
            iconCls: 'p-element-tab_data-icon',
            width: 500,
            height: 600,
            layout: 'fit',
            modal: true,
            border: false,
            items: [{
                header: false,
                xtype: 'teasers-catch-panel',
                lockElement: this.element,
                disabled: true,
                listeners: {
                    save: function () {
                        this.layoutTree.root.reload();
                    },
                    scope: this
                }
            }]
        });
    }

});

Ext.reg('elements-main', Phlexible.elements.MainPanel);