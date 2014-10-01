Phlexible.elementtypes.RootPropertyPanel = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.properties,
    iconCls: 'p-elementtype-tab_properties-icon',
    autoScroll: true,
    border: false,
    bodyStyle: 'padding:3px',
    defaultType: 'textfield',
    labelWidth: 120,

    initComponent: function () {
        this.items = [
            {
                // 0
                xtype: 'textfield',
                fieldLabel: this.strings.title,
                name: 'title',
                width: 200,
                allowBlank: false
            },
            {
                // 1
                fieldLabel: this.strings.uniqueid,
                name: 'unique_id',
                width: 200,
                allowBlank: false,
                regex: /^[a-z0-9-_]+$/
            },
            {
                // 2
                xtype: 'trigger',
                fieldLabel: this.strings.icon,
                name: 'icon',
                width: 200,
                onTriggerClick: function () {
                    var www = new Phlexible.gui.util.ImageSelectWindow({
                        storeUrl: Phlexible.Router.generate('elementtypes_data_images'),
                        value: this.getComponent(2).getValue(),
                        listeners: {
                            imageSelect: function (image) {
                                //Phlexible.console.log(image);

                                this.getComponent(2).setValue(image);
                            },
                            scope: this
                        }
                    });
                    www.show();
                }.createDelegate(this)
            },
            {
                // 3
                xtype: 'twincombobox',
                fieldLabel: this.strings.default_tab,
                emptyText: this.strings.no_default_tab,
                hiddenName: 'default_tab',
                width: 200,
                listWidth: 200,
                store: new Ext.data.SimpleStore({
                    fields: ['id', 'title'],
                    data: [
                        ['0', 'List'],
                        ['1', 'Data'],
                        ['4', 'Links'],
                        ['5', 'History']
                    ]
                }),
                displayField: 'title',
                valueField: 'id',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                allowEmpty: true
            },
            {
                // 4
                xtype: 'twincombobox',
                fieldLabel: this.strings.default_content_tab,
                emptyText: this.strings.no_default_content_tab,
                hiddenName: 'default_content_tab',
                width: 200,
                listWidth: 200,
                store: new Ext.data.SimpleStore({
                    fields: ['id', 'title'],
                    data: [
                        ['', 'No Tab defined']
                    ]
                }),
                forceSelection: true,
                displayField: 'title',
                valueField: 'id',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                allowEmpty: true
            },
            {
                // 5
                xtype: 'twincombobox',
                fieldLabel: this.strings.metaset,
                emptyText: this.strings.no_metaset,
                hiddenName: 'metaset',
                width: 200,
                listWidth: 200,
                store: new Ext.data.JsonStore({
                    fields: ['id', 'name'],
                    url: Phlexible.Router.generate('metasets_sets_list'),
                    root: 'sets',
                    id: 'id',
                    autoLoad: true,
                    listeners: {
                        load: function() {
                            var value = this.getComponent(5).getValue();
                            if (value) {
                                this.getComponent(5).setValue(value);
                            }
                        },
                        scope: this
                    }
                }),
                displayField: 'name',
                valueField: 'id',
                editable: false,
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                allowEmpty: true
            },
            {
                xtype: 'textarea',
                fieldLabel: this.strings.comment,
                name: 'comment',
                width: 200
            },
            {
                hidden: true
            },
            {
                xtype: 'checkbox',
                fieldLabel: '',
                labelSeparator: '',
                boxLabel: this.strings.hide_children,
                name: 'hide_children'
            }
        ];

        Phlexible.elementtypes.RootPropertyPanel.superclass.initComponent.call(this);
    },

    loadValues: function (properties, node) {
        this.node = node;
        this.updateDefaultContentTabStore();

        if (properties.root.default_content_tab !== null) {
            properties.root.default_content_tab += '';
        }

        this.getForm().setValues(properties.root);
    },

    loadRoot: function (properties, node) {
        this.loadValues(properties, node);
    },

    getSaveValues: function () {
        return this.getForm().getValues();
    },

    updateDefaultContentTabStore: function() {
        var store = this.getComponent(4).store;
        store.removeAll();
        for (var i = 0; i < this.node.childNodes.length; i++) {
            store.add(new Ext.data.Record({id: i + '', title: this.node.childNodes[i].text}));
        }
    },

    isValid: function () {
        var valid = this.getForm().isValid(),
            defaultContentTab = this.getComponent(4).getValue();

        if (defaultContentTab) {
            this.updateDefaultContentTabStore();
            valid = valid && this.node.childNodes[defaultContentTab] !== undefined;
        }

        if (valid) {
            //this.header.child('span').removeClass('error');
            this.setIconClass('p-elementtype-tab_properties-icon');

            return true;
        } else {
            //this.header.child('span').addClass('error');
            this.setIconClass('p-elementtype-tab_error-icon');

            return false;
        }
    }
});

Ext.reg('elementtypes-root-properties', Phlexible.elementtypes.RootPropertyPanel);