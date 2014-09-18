Phlexible.elementtypes.configuration.FieldProperty = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.properties,
    iconCls: 'p-elementtype-tab_properties-icon',
    border: false,
    autoScroll: true,
    bodyStyle: 'padding:3px',
    defaultType: 'textfield',
    labelWidth: 120,

    initComponent: function () {
        var typesSort = [],
            types = [],
            key;

        for (key in Phlexible.fields.FieldTypes) {
            if (typeof Phlexible.fields.FieldTypes[key] == 'function' || key == 'root' || key == 'referenceroot') continue;
            typesSort.push(key);
        }
        typesSort.sort();

        for (var i = 0; i < typesSort.length; i++) {
            key = typesSort[i];
            if (Phlexible.fields.FieldTypes[key].allowedIn.length) {
                types.push({
                    key: key,
                    title: Phlexible.fields.FieldTypes[key].titles.de,
                    iconCls: Phlexible.fields.FieldTypes[key].iconCls
                });
            }
        }

        var dev = Phlexible.User.isGranted('debug');

        var typeCombo = {
            xtype: 'iconcombo',
            readOnly: true,
            editable: false,
            fieldLabel: this.strings.type,
            width: 230,
            listWidth: 230,
            hiddenName: 'type',
            name: 'type',
            allowBlank: true,
            store: new Ext.data.JsonStore({
                id: 'key',
                fields: ['key', 'title', 'iconCls'],
                data: types
            }),
            displayField: 'title',
            valueField: 'key',
            mode: 'local',
            iconClsField: 'iconCls',
            selectOnFocus: true,
            triggerAction: 'all',
            hideTrigger: true,
            ctCls: 'x-item-disabled',
            onTriggerClick: Ext.emptyFn,
            helpText: '',
            listeners: {
                select: this.onSelect,
                scope: this
            }
        };

        if (dev) {
            typeCombo.width -= 17;
            typeCombo.readOnly = false;
            typeCombo.hideTrigger = false;
            typeCombo.ctCls = '';
            delete typeCombo.onTriggerClick;
            typeCombo.helpText = Phlexible.inlineIcon('p-elementtype-tab_error-icon') + ' Change this value only if you know what you\'re doing. It might result in data loss.';
        }

        this.items = [
            {
                fieldLabel: this.strings.working_title,
                name: 'working_title',
                allowBlank: false,
                width: 230,
                regex: /^[0-9a-z-_]+$/,
                helpText: this.strings.working_title_help
            },
            typeCombo,
            {
                fieldLabel: this.strings.image,
                name: 'image',
                width: 230,
                hidden: true
            },
            {
                xtype: 'textarea',
                fieldLabel: this.strings.comment,
                name: 'comment',
                width: 230,
                height: 100
            }
        ];

        if (Phlexible.User.isGranted('debug')) {
            this.items.push({
                xtype: 'fieldset',
                title: 'Debug',
                autoHeight: true,
                collapsible: true,
                collapsed: true,
                items: [
                    {
                        xtype: 'textfield',
                        name: 'debug_ds_id',
                        fieldLabel: 'ds_id',
                        width: 230,
                        readOnly: true
                    },
                    {
                        xtype: 'textarea',
                        name: 'debug_dump',
                        fieldLabel: 'dump',
                        width: 400,
                        height: 300,
                        readOnly: true,
                        style: 'font-family: Courier, "Courier New", monospace'
                    }
                ]
            });
        }

        Phlexible.elementtypes.configuration.FieldProperty.superclass.initComponent.call(this);

        // HIER WIRD DIE ICONCOMBOBOX ERSTMAL DEAKTIVIERT
        // @TODO
        //this.getComponent(1).setDisabled(true);
    },

    onSelect: function (combo, record, index) {
    },

    loadData: function (field, node, fieldType) {
        var values = {
            'working_title': field.working_title,
            'type': field.type,
            'comment': field.comment,
            'image': field.image
        };

        if (Phlexible.User.isGranted('debug')) {
            values['debug_ds_id'] = node.attributes.ds_id;
            values['debug_dump'] = Phlexible.dump(node.attributes.properties);
        }

        this.getForm().setValues(values);

        this.isValid();
    },

    getSaveValues: function () {
        return this.getForm().getValues();
    },

    isValid: function () {
        if (this.getForm().isValid()) {
            this.setIconClass('p-elementtype-tab_properties-icon');

            return true;
        } else {
            this.setIconClass('p-elementtype-tab_error-icon');

            return false;
        }
    },

    isActive: function() {
        return true;
    },

    loadField: function (properties, node, fieldType) {
        // FieldProperties Accordion
        this.ownerCt.getTabEl(this).hidden = false;
        this.loadData(properties.field, node, fieldType);
    }
});

Ext.reg('elementtypes-configuration-field-property', Phlexible.elementtypes.configuration.FieldProperty);