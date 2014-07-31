Phlexible.elementtypes.configuration.FieldValue = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.values,
    iconCls: 'p-elementtype-tab_values-icon',
    border: false,
    autoScroll: true,
    bodyStyle: 'padding:3px',
    defaultType: 'textfield',
    labelWidth: 100,

    initComponent: function () {
        this.items = [
            {
                fieldLabel: this.strings.default_value,
                name: 'default_value_textfield',
                hidden: true,
                disabled: true,
                width: 230
            },
            {
                xtype: 'numberfield',
                fieldLabel: this.strings.default_value,
                name: 'default_value_numberfield',
                hidden: true,
                disabled: true,
                width: 230
            },
            {
                xtype: 'textarea',
                fieldLabel: this.strings.default_value,
                name: 'default_value_textarea',
                hidden: true,
                disabled: true,
                width: 230,
                height: 100
            },
            {
                xtype: 'datefield',
                fieldLabel: this.strings.default_value,
                name: 'default_value_datefield',
                hidden: true,
                disabled: true,
                width: 183,
                format: 'Y-m-d'
            },
            {
                xtype: 'timefield',
                fieldLabel: this.strings.default_value,
                name: 'default_value_timefield',
                format: 'H:i:s',
                hidden: true,
                disabled: true,
                width: 183,
                listWidth: 200
            },
            {
                xtype: 'checkbox',
                fieldLabel: this.strings.default_value,
                boxLabel: 'checked',
                name: 'default_value_checkbox',
                hidden: true,
                disabled: true
            },
            {
                xtype: 'combo',
                fieldLabel: this.strings.source,
                name: 'source',
                hiddenName: 'source',
                hideMode: 'display',
                allowBlank: false,
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'title'],
                    data: [
                        ['list', this.strings.editable_list],
                        ['function', this.strings.component_function]
                    ]
                }),
                displayField: 'title',
                valueField: 'key',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                width: 183,
                listWidth: 200,
                listeners: {
                    select: function (combo, record, index) {
                        var source = record.get('key');
                        this.updateSelectSourceVisibility(source);
                    },
                    scope: this
                }
            },
            {
                xtype: 'elementtypes-configuration-field-value-grid',
                hidden: true,
                listeners: {
                    defaultchange: function (key) {
                        this.getComponent(0).setValue(key);
                    },
                    scope: this
                }
            },
            {
                xtype: 'combo',
                hidden: true,
                editable: false,
                hiddenName: 'source_function',
                name: 'source_function',
                fieldLabel: 'Function',
                hideMode: 'display',
                allowBlank: false,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('elementtypes_data_select'),
                    root: 'functions',
                    fields: ['function', 'title']
                }),
                displayField: 'title',
                valueField: 'function',
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                listWidth: 200,
                width: 182
            },
            {
                xtype: 'combo',
                hidden: true,
                editable: false,
                hiddenName: 'source_source',
                name: 'source_source',
                fieldLabel: 'Source',
                hideMode: 'display',
                allowBlank: false,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('datasources_list'),
                    root: 'sources',
                    fields: ['id', 'title']
                }),
                displayField: 'title',
                valueField: 'id',
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                listWidth: 200,
                width: 182
            },
            {
                xtype: 'fieldset',
                title: this.strings.text,
                autoHeight: true,
                width: 350,
                items: [
                    {
                        xtype: 'textarea',
                        fieldLabel: Phlexible.inlineIcon('p-flags-de-icon') + ' ' + this.strings.values_german,
                        name: 'text_de',
                        width: 200
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: Phlexible.inlineIcon('p-flags-de-icon') + ' ' + this.strings.values_english,
                        name: 'text_en',
                        width: 200
                    }
                ]
            }
        ];

        Phlexible.elementtypes.configuration.FieldValue.superclass.initComponent.call(this);
    },

    getValueGrid: function () {
        return this.getComponent(7);
    },

    updateVisibility: function (fieldType, source) {
        // default_text
        if (fieldType.config.values.default_text || fieldType.config.values.default_select || fieldType.config.values.default_link) {
            this.getComponent(0).enable();
            this.getComponent(0).show();
        }
        else {
            this.getComponent(0).disable();
            this.getComponent(0).hide();
        }

        // default_number
        if (fieldType.config.values.default_number) {
            this.getComponent(1).enable();
            this.getComponent(1).show();
        }
        else {
            this.getComponent(1).disable();
            this.getComponent(1).hide();
        }

        // default_textarea
        if (fieldType.config.values.default_textarea || fieldType.config.values.default_editor) {
            this.getComponent(2).enable();
            this.getComponent(2).show();
        }
        else {
            this.getComponent(2).disable();
            this.getComponent(2).hide();
        }

        // default_date
        if (fieldType.config.values.default_date) {
            this.getComponent(3).enable();
            this.getComponent(3).show();
        }
        else {
            this.getComponent(3).disable();
            this.getComponent(3).hide();
        }

        // default_time
        if (fieldType.config.values.default_time) {
            this.getComponent(4).enable();
            this.getComponent(4).show();
        }
        else {
            this.getComponent(4).disable();
            this.getComponent(4).hide();
        }

        // default_checkbox
        if (fieldType.config.values.default_checkbox) {
            this.getComponent(5).enable();
            this.getComponent(5).show();
        }
        else {
            this.getComponent(5).disable();
            this.getComponent(5).hide();
        }

        // source
        if (fieldType.config.values.source) {
            this.getComponent(6).enable();
            this.getComponent(6).show();
            if (source) {
                this.updateSelectSourceVisibility(source);
            }
        }
        else {
            this.getComponent(6).disable();
            this.getComponent(7).disable();
            this.getComponent(8).disable();
            this.getComponent(6).hide();
            this.getComponent(7).hide();
            this.getComponent(8).hide();

            if (fieldType.config.values.source_single) {
                this.getValueGrid().enable();
                this.getValueGrid().show();
            }
        }

        // source_datasource
        if (fieldType.config.values.source_datasource) {
            this.getComponent(9).enable();
            this.getComponent(9).show();
        }
        else {
            this.getComponent(9).disable();
            this.getComponent(9).hide();
        }

        // source
        if (fieldType.config.values.text) {
            this.getComponent(10).enable();
            this.getComponent(10).show();
        }
        else {
            this.getComponent(10).disable();
            this.getComponent(10).hide();
        }
    },

    updateSelectSourceVisibility: function (source) {
        switch (source) {
            case 'list':
                this.getValueGrid().enable();
                this.getValueGrid().show();
                this.getComponent(8).disable();
                this.getComponent(8).hide();
                break;

            case 'function':
                this.getValueGrid().disable();
                this.getValueGrid().hide();
                this.getComponent(8).enable();
                this.getComponent(8).show();
                break;
        }
    },

    loadData: function (fieldData, fieldType) {
        this.updateVisibility(fieldType, fieldData.source);

        this.getForm().setValues([
            //{id: 'default_value',   value: fieldData.default_value},
            {id: 'source', value: fieldData.source},
            {id: 'source_function', value: fieldData.source_function},
            {id: 'source_source', value: fieldData.source_source},
            {id: 'text_de', value: fieldData.text_de},
            {id: 'text_en', value: fieldData.text_en}
        ]);
        this.items.each(function (item) {
            if (!item.isFormField) return;

            var name = item.getName();
            if (name == fieldType.defaultValueField) {
                item.setValue(fieldData.default_value);
            }
            else if (name.match(/^default_value/)) {
                item.reset();
            }
        });
        //if (fieldType.config.values.default_checkbox) {
        //    this.getComponent(5).setValue(fieldData.default_value);
        //}

        if (fieldType.config.values.source_single) {
            this.getValueGrid().loadData('single', fieldData.source_list, fieldData.default_value);
        }
        else if (fieldType.config.values.source_multi) {
            this.getValueGrid().loadData('multi', fieldData.source_list, fieldData.default_value);
        }

        this.isValid();
    },

    getSaveValues: function () {
        var values = this.getForm().getValues();
        for (var i in values) {
            if (i.match(/^default_value_/)) {
                values.default_value = values[i];
                delete values[i];
            }
        }
        //Phlexible.console.info(values);

        if (this.getValueGrid().isVisible()) {
            delete values.source_function;

            var list = [];

            for (var i = 0; i < this.getValueGrid().store.getCount(); i++) {
                var r = this.getValueGrid().store.getAt(i);
                list.push({
                    key: r.get('key'),
                    de: r.get('value_de'),
                    en: r.get('value_en')
                });
            }

            this.getValueGrid().store.commitChanges();

            values.source_list = list;
        }
//        debugger;
        return values;
    },

    isValid: function () {
        if (this.getForm().isValid()) {
            //this.header.child('span').removeClass('error');
            this.setIconClass('p-elementtype-tab_values-icon');

            return true;
        } else {
            //this.header.child('span').addClass('error');
            this.setIconClass('p-elementtype-tab_error-icon');

            return false;
        }
    },

    loadField: function (properties, node, fieldType) {
        if (fieldType.config.values) {
            this.ownerCt.getTabEl(this).hidden = false;
            this.loadData(properties.options, fieldType);
        }
        else {
            this.ownerCt.getTabEl(this).hidden = true;
        }
    }
});
Ext.reg('elementtypes-configuration-field-value', Phlexible.elementtypes.configuration.FieldValue);

Phlexible.elementtypes.configuration.FieldValueGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    title: Phlexible.elementtypes.Strings.options,
    strings: Phlexible.elementtypes.Strings,
    border: true,
    viewConfig: {
        forceFit: true
    },
    autoHeight: true,
    cls: 'p-elementtypes-value-grid',

    default_value: '',
    mode: 'multi',

    enableDragDrop: true,
    ddGroup: 'fieldvalue',

    initComponent: function () {

        this.addEvents(
            /**
             * @event defaultchange
             * Fires when new default value is to be set
             * @param {String} key The new default key
             */
            'defaultchange'
        );

        this.store = new Ext.data.SimpleStore({
            fields: ['key', 'value_de', 'value_en']
        });

        this.columns = [
            {
                id: 'key',
                header: this.strings.values_key,
                dataIndex: 'key',
                width: 100,
                sortable: false,
                editor: new Ext.form.TextField({
                    allowBlank: true
                })
            },
            {
                id: 'de',
                header: this.strings.values_german,
                dataIndex: 'value_de',
                width: 100,
                sortable: false,
                editor: new Ext.form.TextField({
                    allowBlank: false
                })
            },
            {
                id: 'en',
                header: this.strings.values_english,
                dataIndex: 'value_en',
                width: 100,
                sortable: false,
                editor: new Ext.form.TextField({
                    allowBlank: false
                })
            }
        ];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners: {
                selectionchange: function (sm) {
                    var r = sm.getSelected();

                    if (this.mode == 'multi' && r) {
                        this.getTopToolbar().items.items[1].enable();
                        this.getTopToolbar().items.items[2].enable();
                    }
                    else {
                        this.getTopToolbar().items.items[1].disable();
                        this.getTopToolbar().items.items[2].disable();
                    }
                },
                scope: this
            }
        });

        this.tbar = [
            {
                text: this.strings.add_option,
                iconCls: 'p-elementtype-option_add-icon',
                handler: function () {
                    this.store.add(new Ext.data.Record({key: 'new key', value_de: '', value_en: ''}));
                },
                scope: this
            },
            {
                text: this.strings.remove_option,
                iconCls: 'p-elementtype-option_delete-icon',
                disabled: true,
                handler: function () {
                    var recordToBeRemoved = this.getSelectionModel().getSelected();
                    if (!recordToBeRemoved) {
                        return;
                    }
                    var key = recordToBeRemoved.get('key');
                    if (this.default_value == key) {
                        this.default_value = '';
                        this.fireEvent('defaultchange', this.default_value);
                    }

                    this.store.remove(recordToBeRemoved);
                },
                scope: this
            },
            {
                text: this.strings.set_as_default,
                iconCls: 'p-elementtype-elementtype_edit-icon',
                disabled: true,
                handler: function (item) {
                    var record = this.getSelectionModel().getSelected();
                    if (!record) {
                        return;
                    }

                    var key = record.data.key;

                    var defaultIndex = this.store.find('key', this.default_value);
                    if (defaultIndex != -1) {
                        var row = Ext.get(this.view.getRow(defaultIndex));
                        row.removeClass('p-elementtype-value-grid-default');
                    }

                    this.default_value = key;
                    var selectedIndex = this.store.indexOf(record);
                    var row = Ext.get(this.view.getRow(selectedIndex));
                    row.addClass('p-elementtype-value-grid-default');

                    this.fireEvent('defaultchange', this.default_value);
                },
                scope: this
            }
        ];

        this.on({
            validateedit: function (validationobject) {
                if (validationobject.field == 'key') {
                    var recordQuery = this.store.find('key', new RegExp('^' + validationobject.value + '$'));

                    if (recordQuery != -1) {
                        validationobject.value = validationobject.originalValue;
                        return;
                    }

                    if (this.default_value == validationobject.originalValue) {
                        this.default_value = validationobject.value;
                        this.fireEvent('defaultchange', this.default_value);
                    }
                }
            },
            afteredit: function (validationobject) {
                var defaultIndex = this.store.find('key', this.default_value);
                if (defaultIndex != -1) {
                    var row = Ext.get(this.view.getRow(defaultIndex)); //validationobject.row));
                    row.addClass('p-elementtype-value-grid-default');
                }
            },
            render: function (grid) {
                this.ddrow = new Ext.ux.dd.GridReorderDropTarget(grid, {
                    copy: false
                });
                // if you need scrolling, register the grid view's scroller with the scroll manager
                //            Ext.dd.ScrollManager.register(g.getView().getEditorParent());
            },
            scope: this
        });

        Phlexible.elementtypes.configuration.FieldValueGrid.superclass.initComponent.call(this);
    },

    loadData: function (mode, sourceList, defaultValue) {
        this.mode = mode;
        this.store.removeAll();
        if (mode == 'single') {
            if (!sourceList.length) {
                sourceList = [
                    ['key', 'an', 'on']
                ];
            } else if (sourceList.length > 1) {
                sourceList = [sourceList[0]];
            }

            this.getTopToolbar().items.items[0].disable();
        } else {
            this.getTopToolbar().items.items[0].enable();
        }

        if (sourceList && sourceList[0]) {
            if (sourceList[0].key) {
                var modified_list = [];
                Ext.each(sourceList, function (item) {
                    modified_list.push([item.key, item.de, item.en]);
                });
                this.store.loadData(modified_list);
            }
            else {
                this.store.loadData(sourceList);
            }
        }

        var defaultIndex = this.store.find('key', defaultValue);
        if (defaultIndex != -1) {
            var row = Ext.get(this.view.getRow(defaultIndex));
            row.addClass('p-elementtype-value-grid-default');
        }

        this.default_value = defaultValue;
    }
});
Ext.reg('elementtypes-configuration-field-value-grid', Phlexible.elementtypes.configuration.FieldValueGrid);