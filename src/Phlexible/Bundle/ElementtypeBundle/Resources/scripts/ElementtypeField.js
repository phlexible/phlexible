Phlexible.elementtypes.ElementtypeField = Ext.extend(Ext.TabPanel, {
    title: Phlexible.elementtypes.Strings.properties,
    strings: Phlexible.elementtypes.Strings,
    cls: 'p-elementtypes-accordion',
    disabled: true,
    autoScroll: true,
    deferredRender: false,
    enableTabScroll: true,
    activeTab: 0,

    initComponent: function () {

        this.addEvents(
            /**
             * @event beforeSaveField
             * Fires before field is saved
             * @param {Ext.tree.TreeNode} node The node that the properties will be saved to.
             */
            'beforeSaveField',
            /**
             * @event saveField
             * Fires after field is saved
             * @param {Ext.tree.TreeNode} node The node that the properties have been saved to.
             */
            'saveField'
        );

        this.initMyItems();

        this.tbar = [
            {
                text: this.strings.save_properties,
                iconCls: 'p-elementtype-property_save-icon',
                handler: function () {
                    this.saveProperties();
                    //Phlexible.console.log('save');
                },
                scope: this
            },
            '-',
            {
                text: this.strings.reset_properties,
                iconCls: 'p-elementtype-reset-icon',
                handler: function () {
                    this.loadProperties(this.node);

//                Phlexible.msg('Element Type Action', 'Properties of "' + this.node.text + '" resetted.');
                },
                scope: this
            }
        ];

        Phlexible.elementtypes.ElementtypeField.superclass.initComponent.call(this);

        //Phlexible.console.log(this.tbar);
    },

    initMyItems: function () {
        this.items = [
            {
                xtype: 'elementtypes-configuration-field-property',
                isFieldAccordion: true,
                key: 'field'
            },
            {
                xtype: 'elementtypes-configuration-field-label',
                isFieldAccordion: true,
                key: 'labels'
            },
            {
                xtype: 'elementtypes-configuration-field-configuration',
                isFieldAccordion: true,
                key: 'configuration'
            },
            {
                xtype: 'elementtypes-configuration-field-value',
                isFieldAccordion: true,
                key: 'options'
            },
            {
                xtype: 'elementtypes-configuration-field-validation',
                isFieldAccordion: true,
                key: 'validation'
            },
            {
                xtype: 'elementtypes-configuration-field-contentchannel',
                isFieldAccordion: true,
                key: 'content_channels'
            }
        ];
    },

    getFieldPropertyPanel: function () {
        return this.getComponent(2);
    },

    clear: function () {
        this.disable();

        this.setTitle(this.strings.properties);

        /*
         this.items.each(function(panel) {
         panel.hide();
         });
         */
    },

    loadProperties: function (node) {
        this.node = node;
        this.loadFieldProperties(node);
        this.enable();
    },

    loadFieldProperties: function (node) {
        var properties = Phlexible.clone(node.attributes.properties);
        var fieldType = Phlexible.fields.FieldTypes[node.attributes.type];

        this.items.each(function (panel) {
            if (panel.isFieldAccordion) {
                panel.loadField(properties, node, fieldType);
            }
        });

        if (!this.getActiveTab() || this.getTabEl(this.getActiveTab()).hidden) {
            this.setActiveTab(this.getFieldPropertyPanel());
        }
    },

    saveProperties: function () {
        if (!this.saveFieldProperties()) {
            return;
        }

        this.loadProperties(this.node);
    },

    saveFieldProperties: function () {
        var valid = true;
        this.items.each(function (panel) {
            if (panel.isFieldAccordion && !panel.isValid() && !panel.hidden && valid) {
                valid = false;
                return false;
            }
        });

        if (!valid) {
            Ext.MessageBox.alert('Error', this.strings.check_input);
            return false;
        }

        var properties = {
            field: {},
            configuration: {},
            validation: {},
            labels: {},
            options: {},
            content_channels: {}
        };

        this.items.each(function (panel) {
            if (panel.isFieldAccordion) {
                Ext.apply(properties[panel.key], panel.getSaveValues());
            }
        });

        if (this.fireEvent('beforeSaveField', this.node, properties) === false) {
            return false;
        }

        this.node.attributes.properties = properties;
        this.node.attributes.type = properties.field.type;

        var fieldType = Phlexible.fields.FieldTypes[this.node.attributes.type];

        this.node.setText(properties.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] + ' (' + properties.field.working_title + ')');
        this.node.ui.getIconEl().className = 'x-tree-node-icon ' + fieldType.iconCls;

        this.fireEvent('saveField', this.node);

        return true;
    }
});

Ext.reg('elementtypes-field', Phlexible.elementtypes.ElementtypeField);