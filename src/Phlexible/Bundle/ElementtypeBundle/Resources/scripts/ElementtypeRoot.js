Phlexible.elementtypes.ElementtypeRoot = Ext.extend(Ext.TabPanel, {
    title: Phlexible.elementtypes.Strings.properties,
    strings: Phlexible.elementtypes.Strings,
    cls: 'p-elementtypes-accordion',
    disabled: true,
    autoScroll: true,
    deferredRender: false,
    activeTab: 0,

    initComponent: function () {

        this.addEvents(
            /**
             * @event beforeSaveRoot
             * Fires before root is saved
             * @param {Ext.tree.TreeNode} node The node that the properties will be saved to.
             */
            'beforeSaveRoot',
            /**
             * @event saveRoot
             * Fires after root is saved
             * @param {Ext.tree.TreeNode} node The node that the properties have been saved to.
             */
            'saveRoot'
        );

        this.items = [
            {
                xtype: 'elementtypes-root-properties',
                isRootAccordion: true
            },
            {
                xtype: 'elementtypes-root-mappings',
                title: this.strings.field_mappings,
                iconCls: 'p-elementtype-tab_mappings-icon',
                border: false,
                isRootAccordion: true
            }
        ];

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

        Phlexible.elementtypes.ElementtypeRoot.superclass.initComponent.call(this);
    },

    getRootPropertyPanel: function () {
        return this.getComponent(0);
    },

    getRootMappingsPanel: function () {
        return this.getComponent(1);
    },

    clear: function () {
        this.disable();

        this.setTitle(this.strings.properties);
    },

    loadProperties: function (node) {
        this.node = node;
        this.setTitle(this.strings.properties_of + ' "' + node.text + '" [' + node.attributes.type + ']');
        this.loadRootProperties(node);
        this.enable();
    },

    getProperties: function (node) {

        var properties = {
            root: {
                title: '',
                unique_id: '',
                icon: '',
                type: '',
                default_tab: '',
                default_content_tab: '',
                metaset: '',
                comment: '',
                hide_children: ''
            },
            mappings: {},
            metasets: []
        };

        if (node.attributes.properties) {
            if (node.attributes.properties.root) {
                for (var j in properties.root) {
                    properties.root[j] = node.attributes.properties.root[j];
                }
            }
            /*
             if(node.attributes.properties.viability)
             {
             for(var j=0; j<node.attributes.properties.viability.length; j++)
             {
             properties.viability.push(node.attributes.properties.viability[j]);
             }
             }
             */
            if (node.attributes.properties.mappings) {
                if (Ext.isArray(node.attributes.properties.mappings)) {
                    for (var j = 0; j < node.attributes.properties.mappings.length; j++) {
                        properties.mappings.push(node.attributes.properties.mappings[j]);
                    }
                } else if (typeof node.attributes.properties.mappings == 'object') {
                    for (var j in node.attributes.properties.mappings) {
                        properties.mappings[j] = node.attributes.properties.mappings[j];
                    }
                }
            }
            if (node.attributes.properties.metasets) {
                for (var j = 0; j < node.attributes.properties.metasets.length; j++) {
                    properties.metasets.push(node.attributes.properties.metasets[j]);
                }
            }
        }

//        if (node.attributes.properties) {
//            Ext.applyIf(properties, node.attributes.properties);
//            for (var i in properties) {
//                Ext.applyIf(properties[i], node.attributes.properties[i]);
//            }
//        }

//        Phlexible.console.log(properties);

        return properties;
    },

    loadRootProperties: function (node) {

        var properties = this.getProperties(node);

        this.items.each(function (panel) {
            if (panel.isFieldAccordion) {
                panel.hide();
            }
        })

        // RootProperties Accordion
        this.getRootPropertyPanel().loadRoot(properties, node);

        // RootMappings Accordion
        this.getRootMappingsPanel().loadRoot(properties, node);
    },

    saveProperties: function () {
        if (this.node.attributes.type == 'root') {
            if (!this.saveRootProperties()) {
                return;
            }
        } else if (this.node.attributes.type == 'referenceroot') {
            if (!this.saveReferenceRootProperties()) {
                return;
            }
        }

        this.loadProperties(this.node);
    },

    saveRootProperties: function () {
        var valid = true;
        valid = (this.getRootPropertyPanel().hidden || this.getRootPropertyPanel().isValid()) && valid;
        valid = (this.getRootMappingsPanel().hidden || this.getRootMappingsPanel().isValid()) && valid;

        if (!valid) {
            Ext.MessageBox.alert('Error', this.strings.check_input);
            return;
        }

        var properties = {
            root: this.getRootPropertyPanel().getSaveValues(),
            mappings: this.getRootMappingsPanel().getSaveValues()
        };

        if (!this.fireEvent('beforeSaveRoot', this.node, properties)) {
            return;
        }

        if (this.node.attributes.properties.root.icon !== properties.root.icon) {
            this.node.getUI().getIconEl().src = '/bundles/elementtypes/elementtypes/' + properties.root.icon;
        }

        this.node.attributes.properties = properties;

        this.fireEvent('saveRoot', this.node);
    },

    saveReferenceRootProperties: function () {
        var propertiesValid = this.getRootPropertyPanel().isValid();

        if (!propertiesValid) {
            Ext.MessageBox.alert('Error', this.strings.check_input);
            return;
        }

        var properties = {
            root: this.getRootPropertyPanel().getSaveValues(),
            mappings: {}
        };

        if (!this.fireEvent('beforeSaveRoot', this.node, properties)) {
            return;
        }

        this.node.attributes.properties = properties;

        this.fireEvent('saveRoot', this.node);
    }
});

Ext.reg('elementtypes-root', Phlexible.elementtypes.ElementtypeRoot);