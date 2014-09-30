Phlexible.elementtypes.configuration.SelectValueGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    title: Phlexible.elementtypes.Strings.options,
    strings: Phlexible.elementtypes.Strings,
    border: true,
    viewConfig: {
        forceFit: true
    },
    autoHeight: true,
    cls: 'p-elementtypes-value-grid',

    default_value: '',

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
                    if (sm.getCount()) {
                        this.getTopToolbar().items.items[1].enable();
                        this.getTopToolbar().items.items[2].enable();
                    } else {
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

        Phlexible.elementtypes.configuration.SelectValueGrid.superclass.initComponent.call(this);
    },

    loadData: function (sourceList, defaultValue) {
        this.store.removeAll();

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
    },

    isValid: function() {
        var valid = true;
        this.store.each(function(r) {
            if (!r.data.key || !r.data.value_de || !r.data.value_en) {
                valid = false;
                return false;
            }
        });
        return valid;
    }
});
Ext.reg('elementtypes-configuration-select-value-grid', Phlexible.elementtypes.configuration.SelectValueGrid);