Ext.provide('Phlexible.elementtypes.ElementtypeField');

Ext.require('Phlexible.elementtypes.configuration.FieldConfiguration');
Ext.require('Phlexible.elementtypes.configuration.FieldContentchannel');
Ext.require('Phlexible.elementtypes.configuration.FieldLabel');
Ext.require('Phlexible.elementtypes.configuration.FieldProperty');
Ext.require('Phlexible.elementtypes.configuration.FieldValidation');

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
                handler: this.saveProperties,
                scope: this
            },
            '-',
            {
                text: this.strings.reset_properties,
                iconCls: 'p-elementtype-reset-icon',
                handler: this.reset,
                scope: this
            }
        ];

        Phlexible.elementtypes.ElementtypeField.superclass.initComponent.call(this);
    },

    initMyItems: function () {
        this.items = [
            {
                xtype: 'elementtypes-configuration-field-property',
                key: 'field'
            },
            {
                xtype: 'elementtypes-configuration-field-label',
                key: 'labels'
            },
            {
                xtype: 'elementtypes-configuration-field-configuration',
                key: 'configuration'
            },
            {
                xtype: 'elementtypes-configuration-field-validation',
                key: 'validation'
            },
            {
                xtype: 'elementtypes-configuration-field-contentchannel',
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

    reset: function() {
        this.loadFieldProperties(this.node);
        this.enable();
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
            panel.loadField(properties, node, fieldType);
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
            if (valid && !panel.isValid() && panel.isActive()) {
                valid = false;
                return false;
            }
        });

        var properties = {
            field: {},
            configuration: {},
            validation: {},
            labels: {},
            options: {},
            content_channels: {}
        };

        this.items.each(function (panel) {
            Ext.apply(properties[panel.key], panel.getSaveValues());
        });

        // TODO: only disallow for siblings
        /*
        var root = this.node.getOwnerTree().getRootNode();
        if (this.node.getOwnerTree().findWorkingTitle(root, this.node.id, properties.field.working_title)) {
            valid = false;
        }
        */

        if (this.fireEvent('beforeSaveField', this.node, properties, valid) === false) {
            return false;
        }

        if (!valid) {
            this.node.ui.removeClass('valid');
            this.node.ui.addClass('invalid');
            this.node.attributes.invalid = true;
            Ext.MessageBox.alert('Error', this.strings.check_input);
            return;
        }

        this.node.ui.removeClass('invalid');
        this.node.ui.addClass('valid');
        this.node.attributes.invalid = false;

        this.node.attributes.properties = properties;
        this.node.attributes.type = properties.field.type;

        var fieldType = Phlexible.fields.FieldTypes[this.node.attributes.type];

        this.node.setText(properties.labels.fieldLabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] + ' (' + properties.field.working_title + ')');
        this.node.ui.getIconEl().className = 'x-tree-node-icon ' + fieldType.iconCls;

        this.fireEvent('saveField', this.node);

        return true;
    }
});

Ext.reg('elementtypes-field', Phlexible.elementtypes.ElementtypeField);