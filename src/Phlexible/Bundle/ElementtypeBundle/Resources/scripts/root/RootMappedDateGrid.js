Ext.ns('Phlexible.elementtypes');

Phlexible.elementtypes.RootMappedDateGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    strings: Phlexible.elementtypes.Strings,
    border: true,
    autoScroll: true,
    enableDragDrop: true,
    ddGroup: 'elementtypesDD',
    viewConfig: {
        forceFit: true,
        emptyText: Phlexible.elementtypes.Strings.navigation_default_date,
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
            fields: ['dsId', 'title', 'type'],
            listeners: {
                datachanged: function (store) {
                    var fields = [];
                    Ext.each(store.getRange(), function (r) {
                        fields.push({dsId: r.get('dsId'), title: r.get('title'), type: r.get('type')});
                    });
                    this.fireEvent('change', fields);
                },
                scope: this
            }
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
                        case 'date':
                        case 'time':
                            break;

                        default:
                            //Phlexible.console.log('INVALID TYPE: ' + fieldType);
                            return;
                    }

                    var datePresent = false;
                    this.store.each(function (r) {
                        if (r.data.type == fieldType) {
                            //Phlexible.console.log('REMOVE');
                            this.store.remove(r);
                        }
                        if (r.data.type == 'date') {
                            datePresent = true;
                        }
                        if (!r.data.type) {
                            datePresent = true;
                            r.set('type', 'date');
                        }
                    }, this);

                    if (fieldType == 'time' && !datePresent) {
                        //Phlexible.console.log('TIME INVALID WITHOUT DATE');
                        return;
                    }

                    var fieldTitle = dragData.node.attributes.properties.labels.fieldLabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] + ' (' + dragData.node.attributes.properties.field.working_title + ')';
                    var r = new Ext.data.Record({
                        dsId: dragData.node.attributes.ds_id,
                        title: fieldTitle,
                        type: fieldType
                    });

                    //Phlexible.console.log('ADD');
                    this.store.add(r);
                    this.fireChange();

                    //this.layout();

                }.createDelegate(this);

                this.dropZone.onNodeOver = function (node, dd, e, dragData) {
                    if (dragData.node.attributes.properties.field && this.store.find('id', dragData.node.attributes.id) == -1) {
                        switch (dragData.node.attributes.properties.field.type) {
                            case 'date':
                            case 'time':
                                return "x-dd-drop-ok";
                                break;
                        }
                    }

                    return "x-dd-drop-nodrop";
                }.createDelegate(this);
            },
            scope: this
        });

        Phlexible.elementtypes.RootMappedDateGrid.superclass.initComponent.call(this);
    },

    fireChange: function() {
        var fields = [];
        Ext.each(this.store.getRange(), function (r) {
            fields.push({dsId: r.get('dsId'), title: r.get('title'), type: r.get('type')});
        });
        this.fireEvent('change', fields);
    }
});

Ext.reg('elementtypes-root-mapped-date', Phlexible.elementtypes.RootMappedDateGrid);
