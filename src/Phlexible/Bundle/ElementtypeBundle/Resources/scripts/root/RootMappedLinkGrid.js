Ext.ns('Phlexible.elementtypes');

Phlexible.elementtypes.RootMappedLinkGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    strings: Phlexible.elementtypes.Strings,
    border: true,
    autoScroll: true,
    enableDragDrop: true,
    ddGroup: 'elementtypesDD',
    viewConfig: {
        forceFit: true,
        emptyText: Phlexible.elementtypes.Strings.navigation_default_link,
        deferEmptyText: false
    },
    autoExpandColumn: 1,

    initComponent: function () {
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
            fields: ['dsId', 'title']
        });

        this.columns = [
            {
                header: this.strings.ds_id,
                dataIndex: 'dsId',
                width: 200,
                hidden: true
            },
            {
                header: this.strings.field,
                dataIndex: 'field',
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
                    if (!dragData.node.attributes.properties.field) {
                        //Phlexible.console.log('NO FIELD INFO FOUND');
                        return;
                    }

                    var fieldType = dragData.node.attributes.properties.field.type;
                    var found = this.store.find('id', dragData.node.attributes.id) != -1;

                    if (found) {
                        //Phlexible.console.log('ALREADY PRESENT');
                        return;
                    }

                    //Phlexible.console.log(dragData.node);
                    //Phlexible.console.log(fieldType);
                    switch (fieldType) {
                        case 'link':
                            break;

                        default:
                            //Phlexible.console.log('INVALID TYPE: ' + fieldType);
                            return;
                    }

                    var fieldTitle = dragData.node.attributes.properties.labels.fieldLabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] + ' (' + dragData.node.attributes.properties.field.working_title + ')';
                    var r = new Ext.data.Record({
                        dsId: dragData.node.attributes.ds_id,
                        title: fieldTitle
                    });

                    this.store.removeAll();
                    this.store.add(r);

                    //this.layout();

                }.createDelegate(this);

                this.dropZone.onNodeOver = function (node, dd, e, dragData) {
                    if (dragData.node.attributes.properties.field && this.store.find('id', dragData.node.attributes.id) == -1) {
                        switch (dragData.node.attributes.properties.field.type) {
                            case 'link':
                                return "x-dd-drop-ok";
                                break;
                        }
                    }

                    return "x-dd-drop-nodrop";
                }.createDelegate(this);
            },
            scope: this
        });

        Phlexible.elementtypes.RootMappedLinkGrid.superclass.initComponent.call(this);
    },

    fireChange: function() {
        var fields = [];
        Ext.each(this.store.getRange(), function (r) {
            fields.push({dsId: r.get('dsId'), title: r.get('title'), index: r.get('index')});
        });
        this.fireEvent('change', fields);
    }
});

Ext.reg('elementtypes-root-mapped-link', Phlexible.elementtypes.RootMappedLinkGrid);
