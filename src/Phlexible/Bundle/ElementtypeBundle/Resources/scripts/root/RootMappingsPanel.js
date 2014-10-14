Phlexible.elementtypes.RootMappingsPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.elementtypes.Strings,
    border: false,
    layout: 'border',

    initComponent: function () {
        var actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            width: 30,
            actions: [
                {
                    iconCls: 'p-elementtype-delete-icon',
                    tooltip: this.strings.remove,
                    showIndex: 'state',
                    callback: function (grid, record, action, row, col) {
                        var r = grid.store.getAt(row);

                        r.set('fields', null);
                        r.set('pattern', null);
                        r.set('state', 0);
                        r.commit();
                        this.fireChange();
                    }.createDelegate(this)
                }
            ]
        });

        this.items = [
            {
                xtype: 'grid',
                region: 'west',
                width: 160,
                store: new Ext.data.SimpleStore({
                    fields: ['name', 'type', 'state', 'pattern', 'fields'],
                    data: [
                        ['backend', 'title', 0, null, null],
                        ['page', 'title', 0, null, null],
                        ['navigation', 'title', 0, null, null],
                        ['date', 'date', 0, null, null],
                        ['forward', 'link', 0, null, null],
                        ['description', 'field', 0, null, null],
                        ['keywords', 'field', 0, null, null],
                        ['custom1', 'field', 0, null, null],
                        ['custom2', 'field', 0, null, null],
                        ['custom3', 'field', 0, null, null],
                        ['custom4', 'field', 0, null, null],
                        ['custom5', 'field', 0, null, null]
                    ]
                }),
                columns: [
                    {
                        header: '&nbsp;',
                        dataIndex: 'state',
                        width: 30,
                        renderer: function(v) {
                            if (v === 1) {
                                return Phlexible.inlineIcon('p-elementtype-tab_error-icon');
                            } else if (v === 2) {
                                return Phlexible.inlineIcon('p-elementtype-tab_validation-icon');
                            }
                            return '';
                        }
                    },{
                        header: this.strings.field,
                        dataIndex: 'name',
                        width: 100
                    },
                    actions
                ],
                plugins: [actions],
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true,
                    listeners: {
                        selectionchange: function (sm) {
                            var r = sm.getSelected(),
                                name, type, grid;
                            if (!r) {
                                return;
                            }
                            name = r.get('name');
                            type = r.get('type');
                            if (type === 'title') {
                                this.getComponent(1).layout.setActiveItem(1);
                                grid = this.getMappedTitleGrid();
                                grid.getComponent(0).getView().refresh();
                                grid.getComponent(0).getStore().removeAll();
                                grid.getComponent(1).setValue('');
                                if (r.get('state')) {
                                    if (r.get('fields')) {
                                        grid.getComponent(0).getStore().loadData(r.get('fields'));
                                    }
                                    if (r.get('pattern')) {
                                        grid.getComponent(1).setValue(r.get('pattern'));
                                    }
                                }
                                this.updatePreview(name);
                            } else if (type === 'date') {
                                this.getComponent(1).layout.setActiveItem(2);
                                grid = this.getMappedDateGrid();
                                grid.getComponent(0).getView().refresh();
                                grid.getComponent(0).getStore().removeAll();
                                if (r.get('state') && r.get('fields')) {
                                    grid.getComponent(0).getStore().loadData(r.get('fields'));
                                }
                            } else if (type === 'link') {
                                this.getComponent(1).layout.setActiveItem(3);
                                grid = this.getMappedLinkGrid();
                                grid.getComponent(0).getView().refresh();
                                grid.getComponent(0).getStore().removeAll();
                                if (r.get('state') && r.get('fields')) {
                                    grid.getComponent(0).getStore().loadData(r.get('fields'));
                                }
                            } else if (type === 'field') {
                                this.getComponent(1).layout.setActiveItem(4);
                                grid = this.getMappedFieldGrid();
                                grid.getComponent(0).getView().refresh();
                                grid.getComponent(0).getStore().removeAll();
                                if (r.get('state') && r.get('fields')) {
                                    grid.getComponent(0).getStore().loadData(r.get('fields'));
                                }
                            } else {
                                this.getComponent(1).layout.setActiveItem(0);
                            }
                        },
                        scope: this
                    }
                })
            },
            {
                region: 'center',
                layout: 'card',
                activeItem: 0,
                bodyStyle: 'padding: 5px;',
                border: false,
                items: [
                    {
                        html: this.strings.choose_field,
                        border: false
                    },
                    {
                        xtype: 'form',
                        border: false,
                        labelAlign: 'top',
                        autoScroll: true,
                        items: [
                            {
                                xtype: 'elementtypes-root-mapped-title',
                                height: 200,
                                listeners: {
                                    change: function (fields) {
                                        var name = this.getComponent(0).getSelectionModel().getSelected().get('name');
                                        this.findRecord(name).set('fields', fields);
                                        this.validate(name);
                                        this.updatePreview(name);
                                    },
                                    scope: this
                                }
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: this.strings.pattern,
                                name: 'pattern',
                                anchor: '-10',
                                allowBlank: false,
                                listeners: {
                                    change: function (field, pattern) {
                                        var name = this.getComponent(0).getSelectionModel().getSelected().get('name');
                                        this.findRecord(name).set('pattern', pattern);
                                        this.validate(name);
                                        this.updatePreview(name);
                                    },
                                    scope: this
                                }
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: this.strings.preview,
                                name: 'preview',
                                anchor: '-10',
                                readOnly: true
                            }
                        ]
                    },
                    {
                        border: false,
                        items: [
                            {
                                xtype: 'elementtypes-root-mapped-date',
                                height: 200,
                                listeners: {
                                    change: function (fields) {
                                        var name = this.getComponent(0).getSelectionModel().getSelected().get('name');
                                        this.findRecord(name).set('fields', fields);
                                        this.validate(name);
                                    },
                                    scope: this
                                }
                            }
                        ]
                    },
                    {
                        border: false,
                        items: [
                            {
                                xtype: 'elementtypes-root-mapped-link',
                                height: 200,
                                listeners: {
                                    change: function (fields) {
                                        var name = this.getComponent(0).getSelectionModel().getSelected().get('name');
                                        this.findRecord(name).set('fields', fields);
                                        this.validate(name);
                                    },
                                    scope: this
                                }
                            }
                        ]
                    },
                    {
                        border: false,
                        items: [
                            {
                                xtype: 'elementtypes-root-mapped-title',
                                height: 200,
                                listeners: {
                                    change: function (fields) {
                                        var name = this.getComponent(0).getSelectionModel().getSelected().get('name');
                                        this.findRecord(name).set('fields', fields);
                                        this.validate(name);
                                    },
                                    scope: this
                                }
                            }
                        ]
                    }
                ]
            }
        ];

        Phlexible.elementtypes.RootMappingsPanel.superclass.initComponent.call(this);
    },

    getMappedTitleGrid: function () {
        return this.getComponent(1).getComponent(1);
    },

    getMappedDateGrid: function () {
        return this.getComponent(1).getComponent(2);
    },

    getMappedLinkGrid: function () {
        return this.getComponent(1).getComponent(3);
    },

    getMappedFieldGrid: function () {
        return this.getComponent(1).getComponent(4);
    },

    findRecord: function(name) {
        var store = this.getComponent(0).getStore(),
            index = store.find('name', name);
        if (index === -1) {
            throw new Error("Field " + name + " not found.");
        }
        return store.getAt(index);
    },

    validate: function(name) {
        var r = this.findRecord(name),
            state = 0,
            type = r.get('type'),
            fields = r.get('fields'),
            pattern = r.get('pattern');

        if (Ext.isArray(fields) && fields.length) {
            state++;
        }

        switch (type) {
            case 'title':
                if (pattern) {
                    state++;
                }
                break;
            case 'date':
                if (state) {
                    state++;
                }
                break;
            case 'link':
                if (state) {
                    state++;
                }
                break;
            case 'field':
                if (state) {
                    state++;
                }
                break;
            default:
                throw new Error("Type " + type + " not known.");
        }

        r.set('state', state);
    },

    updatePreview: function (name) {
        var r = this.findRecord(name),
            fields = r.get('fields'),
            pattern = Phlexible.clone(r.get('pattern')) || '',
            previewField = this.getMappedTitleGrid().getComponent(2);

        Ext.each(fields, function (field) {
            pattern = pattern.replace('$' + field.index, field.title);
        });

        previewField.setValue(pattern);
    },

    empty: function () {
        this.getComponent(0).getStore().each(function(r) {
            r.set('state', 0);
            r.set('pattern', '');
            r.set('fields', null);
        })
    },

    loadData: function (mappings) {
        var name, r;
        for (name in mappings) {
            if (!mappings.hasOwnProperty(name)) {
                continue;
            }
            r = this.findRecord(name);
            r.set('fields', mappings[name].fields)
            r.set('pattern', mappings[name].pattern || '');
            this.validate(name);
        }
        this.getComponent(0).getStore().commitChanges();
    },

    loadRoot: function (properties, node) {
        if (node.attributes.type == 'root' && node.attributes.properties.root.type !== 'layout') {
            this.ownerCt.getTabEl(this).hidden = false;
            this.loadData(properties.mappings, node);
        } else {
            this.ownerCt.getTabEl(this).hidden = true;
            this.empty();
        }
    },

    getSaveValues: function () {
        var mappings = {};
        this.getComponent(0).getStore().each(function(r) {
            if (r.get('state') === 2) {
                mappings[r.get('name')] = {
                    fields: r.get('fields'),
                    pattern: r.get('pattern')
                };
            }
        });

        console.log(mappings);

        return mappings;
    },

    isValid: function () {
        if (this.getComponent(0).getStore().find('state', 1) === -1) {
            //this.ownerCt.header.child('span').removeClass('error');
            this.setIconClass('p-elementtype-tab_titles-icon');

            return true;
        }
        else {
            //this.ownerCt.header.child('span').addClass('error');
            this.setIconClass('p-elementtype-tab_error-icon');

            return false;
        }
    }
});

Ext.reg('elementtypes-root-mappings', Phlexible.elementtypes.RootMappingsPanel);
