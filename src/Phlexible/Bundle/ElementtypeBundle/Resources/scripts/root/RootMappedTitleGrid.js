Phlexible.elementtypes.RootMappedTitleGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    strings: Phlexible.elementtypes.Strings,
    border: true,
    autoScroll: true,
    enableDragDrop: true,
    ddGroup: 'elementtypesDD',
    viewConfig: {
        forceFit: true,
        emptyText: Phlexible.elementtypes.Strings.navigation_default_title,
        deferEmptyText: false
    },
    autoExpandColumn: 2,

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            fields: ['dsId', 'field', 'index'],
            listeners: {
                add: function (store) {
                    var fields = [];
                    Ext.each(store.getRange(), function (r) {
                        fields.push({dsId: r.get('dsId'), title: r.get('title'), index: r.get('index')});
                    });
                    this.fireEvent('change', fields);
                },
                remove: function (store) {
                    var fields = [];
                    Ext.each(store.getRange(), function (r) {
                        fields.push({dsId: r.get('dsId'), title: r.get('title'), index: r.get('index')});
                    });
                    this.fireEvent('change', fields);
                },
                clear: function (store) {
                    var fields = [];
                    Ext.each(store.getRange(), function (r) {
                        fields.push({dsId: r.get('dsId'), title: r.get('title'), index: r.get('index')});
                    });
                    this.fireEvent('change', fields);
                },
                scope: this
            }
        });

        this.columns = [
            {
                header: this.strings.index,
                dataIndex: 'index',
                width: 50,
                renderer: function (v) {
                    return '$' + v;
                }
            },
            {
                header: this.strings.ds_id,
                dataIndex: 'dsId',
                width: 200,
                hidden: true
            },
            {
                header: this.strings.field,
                dataIndex: 'title',
                width: 200
            }
        ];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.tbar = [
            {
                text: this.strings.clear,
                iconCls: 'p-elementtype-clear-icon',
                handler: function () {
                    this.store.removeAll();
                },
                scope: this
            }
        ];

        this.contextMenu = new Ext.menu.Menu({
            items: [
                {
                    text: this.strings.remove,
                    iconCls: 'p-elementtype-delete-icon',
                    handler: function (item) {
                        item.parentMenu.store.remove(item.parentMenu.record);
                        if (!item.parentMenu.store.getCount()) {
                            item.parentMenu.store.removeAll();
                        }
                    },
                    scope: this
                }
            ]
        });

        this.on({
            rowcontextmenu: function (grid, index, event) {
                event.stopEvent();
                var coords = event.getXY();

                this.record = grid.store.getAt(index);
                this.store = grid.store;

                this.showAt([coords[0], coords[1]]);
            },
            render: function (grid) {
                this.addEvents("beforetooltipshow");

                var v = this.view;
                this.dropZone = new Ext.dd.DropZone(this.view.mainBody, {
                    ddGroup: 'elementtypesDD'
                });

                this.dropZone.getTargetFromEvent = function (e) {
                    return this.el.dom;
                };

                this.dropZone.onNodeDrop = function (node, dd, e, dragData) {
                    if (dragData.node.attributes.properties.field && this.store.find('id', dragData.node.attributes.id) == -1) {
                        switch (dragData.node.attributes.properties.field.type) {
                            case 'textfield':
                            case 'numberfield':
                            case 'date':
                            case 'select':
                                break;

                            default:
                                return;
                        }
                    }

                    var fieldTitle = dragData.node.attributes.properties.labels.fieldLabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] + ' (' + dragData.node.attributes.properties.field.working_title + ')';

                    var index = 0;
                    Ext.each(this.store.getRange(), function (r) {
                        r.set('index', ++index);
                        r.commit();
                    });

                    var r = new Ext.data.Record({
                        dsId: dragData.node.attributes.ds_id,
                        title: fieldTitle,
                        index: ++index
                    });
                    this.store.add(r);
                }.createDelegate(this);

                this.dropZone.onNodeOver = function (node, dd, e, dragData) {
                    if (dragData.node.attributes.properties.field && this.store.find('id', dragData.node.attributes.id) == -1) {
                        switch (dragData.node.attributes.properties.field.type) {
                            case 'textfield':
                            case 'numberfield':
                            case 'date':
                            case 'select':
                                return "x-dd-drop-ok";
                                break;
                        }
                    }

                    return "x-dd-drop-nodrop";
                }.createDelegate(this);

                this.ddrow = new Ext.ux.dd.GridReorderDropTarget(grid, {
                    copy: false
                });
            },
            scope: this
        });

        Phlexible.elementtypes.RootMappedTitleGrid.superclass.initComponent.call(this);
    }
});

Ext.reg('elementtypes-root-mapped-title', Phlexible.elementtypes.RootMappedTitleGrid);
