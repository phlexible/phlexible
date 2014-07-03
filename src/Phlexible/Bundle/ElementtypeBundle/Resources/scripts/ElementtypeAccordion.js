Phlexible.elementtypes.ElementtypeAccordion = Ext.extend(Ext.Panel, {
    title: Phlexible.elementtypes.Strings.properties,
    strings: Phlexible.elementtypes.Strings,
    cls: 'p-elementtypes-accordion',
    layout: 'accordion',
    collapseFirst: false,
    layoutConfig: {
        // layout-specific configs go here
        titleCollapse: true,
        animate: false,
        fill: true
    },
    disabled: true,
    autoScroll: true,

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
            'saveRoot',
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

        this.items = [
            {
                xtype: 'elementtypes-root-properties',
                hidden: true,
                isRootAccordion: true
            },
            {
                title: this.strings.field_mappings,
                iconCls: 'p-elementtype-tab_mappings-icon',
                border: false,
                hidden: true,
                items: [
                    {
                        xtype: 'elementtypes-root-navigations'
                    }
                ],
                isRootAccordion: true
            },
            {
                xtype: 'elementtypes-field-properties',
                hidden: true,
                isFieldAccordion: true,
                key: 'field'
            },
            {
                xtype: 'elementtypes-field-labels',
                hidden: true,
                isFieldAccordion: true,
                key: 'labels'
            },
            {
                xtype: 'elementtypes-field-configurations',
                hidden: true,
                isFieldAccordion: true,
                key: 'configuration'
            },
            {
                xtype: 'elementtypes-field-values',
                hidden: true,
                isFieldAccordion: true,
                key: 'options'
            },
            {
                xtype: 'elementtypes-field-validations',
                hidden: true,
                isFieldAccordion: true,
                key: 'validation'
            },
            {
                xtype: 'elementtypes-field-content-channels',
                hidden: true,
                isFieldAccordion: true,
                key: 'content_channels'
            },
            {
                xtype: 'elementtypes-field-link',
                hidden: true,
                isFieldAccordion: true,
                key: 'configuration'
            },
            {
                xtype: 'elementtypes-field-table',
                hidden: true,
                isFieldAccordion: true,
                key: 'configuration'
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

        Phlexible.elementtypes.ElementtypeAccordion.superclass.initComponent.call(this);

        //Phlexible.console.log(this.tbar);
    },

    clear: function () {
        this.disable();

        this.setTitle(this.strings.properties);

        this.items.each(function (panel) {
            panel.hide();
        })
    },

    loadProperties: function (node) {

        this.node = node;

        this.setTitle(this.strings.properties_of + ' "' + node.text + '" [' + node.attributes.type + ']');

        if (this.node.attributes.type == 'root' || this.node.attributes.type == 'referenceroot') {
            this.loadRootProperties(node);
        } else {
            this.loadFieldProperties(node);
        }

        this.enable();
    },

    getProperties: function (node) {

        var properties = {
            root: {
                title: '',
                unique_id: '',
                icon: '',
                type: 'full',
                default_tab: '',
                default_content_tab: '',
                metaset: '',
                comment: '',
                hide_children: ''
            },
            navigation: [],
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
            if (node.attributes.properties.navigation) {
                if (Ext.isArray(node.attributes.properties.navigation)) {
                    for (var j = 0; j < node.attributes.properties.navigation.length; j++) {
                        properties.navigation.push(node.attributes.properties.navigation[j]);
                    }
                } else if (typeof node.attributes.properties.navigation == 'object') {
                    for (var j in node.attributes.properties.navigation) {
                        properties.navigation[j] = node.attributes.properties.navigation[j];
                    }
                }
            }
            if (node.attributes.properties.metasets) {
                for (var j = 0; j < node.attributes.properties.metasets.length; j++) {
                    properties.metasets.push(node.attributes.properties.metasets[j]);
                }
            }
            /*
             if(node.attributes.properties.versions)
             {
             for(var j=0; j<node.attributes.properties.versions.length; j++)
             {
             properties.versions.push(node.attributes.properties.versions[j]);
             }
             }
             */
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

    getRootPropertyPanel: function () {
        return this.getComponent(0);
    },

    getRootNavigationWrap: function () {
        return this.getComponent(1);
    },

    getRootNavigationPanel: function () {
        return this.getRootNavigationWrap().getComponent(0);
    },

    /*
     getRootViabilityWrap: function() {
     return this.getComponent(2);
     },

     getRootViabilityPanel: function() {
     return this.getRootViabilityWrap().getComponent(0);
     },

     getRootVersionsWrap: function() {
     return this.getComponent(3);
     },

     getRootVersionsPanel: function() {
     return this.getRootVersionsWrap().getComponent(0);
     },
     */

    getFieldPropertyPanel: function () {
        return this.getComponent(2);
    },

    loadRootProperties: function (node) {

        var properties = this.getProperties(node);

        this.items.each(function (panel) {
            if (panel.isFieldAccordion) {
                panel.hide();
            }
        })

        // RootProperties Accordion
        this.getRootPropertyPanel().show();
        this.getRootPropertyPanel().loadValues(properties, node);

        // RootNavigation Accordion
        if (node.attributes.type == 'root' && node.attributes.properties.root.type !== 'layout') {
            this.getRootNavigationWrap().show();
            this.getRootNavigationPanel().loadData(properties.navigation);
        }
        else {
            this.getRootNavigationWrap().hide();
            this.getRootNavigationPanel().empty();
        }

        /*
         // RootViability Accordion
         if (node.attributes.type == 'root') {
         this.getRootViabilityWrap().show();
         this.getRootViabilityPanel().loadData(properties.viability);
         this.getRootViabilityPanel().setType(properties.root.type);
         }
         else {
         this.getRootViabilityWrap().hide();
         this.getRootViabilityPanel().empty();
         }

         // RootVersions Accordion
         //if (node.attributes.type == 'root') {
         this.getRootVersionsWrap().show();
         this.getRootVersionsPanel().loadData(properties.versions);
         //}
         //else {
         //    this.getComponent(5).hide();
         //    this.rootVersions.empty();
         //}
         */

        var expanded = this.getRootNavigationWrap().isVisible() && !this.getRootNavigationWrap().collapsed;
        /*
         (this.getRootNavigationWrap().isVisible() && !this.getRootNavigationWrap().collapsed) ||
         (this.getRootViabilityWrap().isVisible() && !this.getRootViabilityWrap().collapsed) ||
         (this.getRootVersionsWrap().isVisible() && !this.getRootVersionsWrap().collapsed);
         */

        if (!expanded) {
            this.getRootPropertyPanel().expand();
            this.getRootPropertyPanel().collapse();
            this.getRootPropertyPanel().expand();
        }
    },

    loadFieldProperties: function (node) {

        var properties = Phlexible.clone(node.attributes.properties);

        this.items.each(function (panel) {
            if (panel.isRootAccordion) {
                panel.hide();
            }
        });

        var fieldType = Phlexible.fields.FieldTypes[node.attributes.type];

        var expanded = false;
        this.items.each(function (panel) {
            if (panel.isFieldAccordion) {
                panel.loadField(properties, node, fieldType);
                if (!panel.collapsed) {
                    expanded = true;
                }
            }
        });

        if (!expanded) {
            this.getFieldPropertyPanel().expand();
        }
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
        } else {
            if (!this.saveFieldProperties()) {
                return;
            }
        }

        this.loadProperties(this.node);
    },

    saveRootProperties: function () {
        var valid = true;
        valid = (this.getRootPropertyPanel().hidden || this.getRootPropertyPanel().isValid()) && valid;
        valid = (this.getRootNavigationWrap().hidden || this.getRootNavigationPanel().isValid()) && valid;
        //valid = (this.getRootViabilityWrap().hidden  || this.getRootViabilityPanel().isValid())  && valid;

        if (!valid) {
            Ext.MessageBox.alert('Error', this.strings.check_input);
            return;
        }

        var properties = {
            root: this.getRootPropertyPanel().getSaveValues(),
            //viability: this.getRootViabilityPanel().getSaveValues(),
            navigation: this.getRootNavigationPanel().getSaveValues()
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
            //viability: {},
            navigation: {}
        };

        if (!this.fireEvent('beforeSaveRoot', this.node, properties)) {
            return;
        }

        this.node.attributes.properties = properties;

        this.fireEvent('saveRoot', this.node);
    },

    saveFieldProperties: function () {
        var valid = true;
        this.items.each(function (panel) {
            if (panel.isFieldAccordion && !panel.hidden && !panel.isValid() && valid) {
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

Ext.reg('elementtypes-accordion', Phlexible.elementtypes.ElementtypeAccordion);