Ext.provide('Phlexible.elementtypes.RootMappedTitleGrid');

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

    allowedTypes: [],

    initComponent: function () {
        this.allowedTypes = this.allowedTypes || [];

        var actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            width: 150,
            actions: [
                {
                    iconCls: 'p-elementtype-delete-icon',
                    tooltip: this.strings.remove,
                    callback: function (grid, record, action, row, col) {
                        var r = grid.store.getAt(row);

                        this.store.remove(r);
                        this.fireChange();
                    }.createDelegate(this)
                }
            ]
        });

        this.store = new Ext.data.JsonStore({
            fields: ['dsId', 'title', 'index']
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
            },
            actions
        ];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.plugins = [actions];

        this.tbar = [
            {
                text: this.strings.clear,
                iconCls: 'p-elementtype-clear-icon',
                handler: function () {
                    this.store.removeAll();
                    this.fireChange();
                },
                scope: this
            }
        ];

        this.on({
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
                    if (!Phlexible.fields.FieldTypes[dragData.node.attributes.properties.field.type]) {
                        return;
                    }

                    if (
                        !dragData.node.attributes.properties.field ||
                        this.store.find('id', dragData.node.attributes.id) !== -1 ||
                        !Phlexible.fields.FieldTypes[dragData.node.attributes.properties.field.type].allowMap
                    ) {
                        return;
                    }

                    var fieldTitle = dragData.node.attributes.properties.field.working_title;

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
                    this.fireChange();
                }.createDelegate(this);

                this.dropZone.onNodeOver = function (node, dd, e, dragData) {
                    if (!Phlexible.fields.FieldTypes[dragData.node.attributes.properties.field.type]) {
                        return "x-dd-drop-nodrop";
                    }

                    if (
                        dragData.node.attributes.properties.field &&
                        this.store.find('id', dragData.node.attributes.id) == -1 &&
                        Phlexible.fields.FieldTypes[dragData.node.attributes.properties.field.type].allowMap
                    ) {
                        return "x-dd-drop-ok";
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
    },

    fireChange: function() {
        var fields = [];
        Ext.each(this.store.getRange(), function (r) {
            fields.push({dsId: r.get('dsId'), title: r.get('title'), index: r.get('index')});
        });
        this.fireEvent('change', fields);
    }
});

Ext.reg('elementtypes-root-mapped-title', Phlexible.elementtypes.RootMappedTitleGrid);
