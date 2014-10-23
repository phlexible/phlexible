Phlexible.elementtypes.MainPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.elementtypes,
    layout: 'border',
    cls: 'p-elementtypes-panel',
    iconCls: 'p-elementtype-component-icon',

    loadParams: function (params) {
        if (params.type && params.elementtype_id && params.version) {
            this.getListGrid().load(params.type, params.elementtype_id, params.version);
        }
    },

    initComponent: function () {
        element = new Phlexible.elements.Element({});
        element.lockinfo = {status: 'edit'};

        this.items = [
            {
                xtype: 'elementtypes-list',
                region: 'west',
                width: 300,
                viewConfig: {
                    forceFit: true
                },
                collapsible: true,
                params: this.params,
                listeners: {
                    beforeElementtypeChange: function (id, title, fn) {
                        if (this.dirty) {
                            Ext.MessageBox.show({
                                title: 'Warning',
                                msg: 'You have unsaved changes in your Element Type.<br />Would you like to publish your changes?',
                                buttons: Ext.MessageBox.YESNOCANCEL,
                                fn: function (btn, text, x, fn) {
                                    if (btn == 'cancel') {
                                        return;
                                    } else if (btn == 'yes') {
                                        this.getEditTreePanel().publish();
                                    } else if (btn == 'no') {
                                        fn();
                                    }
                                }.createDelegate(this, [fn], true),
                                icon: Ext.MessageBox.QUESTION
                            });
                            return false;
                        }
                    },
                    elementtypeChange: this.onElementtypeChange,
                    elementtypeTemplateChange: this.onElementtypeTemplateChange,
                    scope: this
                }
            },
            {
                xtype: 'tabpanel',
                region: 'center',
                activeTab: 0,
                deferredRender: false,
                disabled: true,
                items: [
                    {
                        title: this.strings.structure,
                        iconCls: 'p-elementtype-tree-icon',
                        layout: 'border',
                        cls: 'p-elementtypes-2-panel',
                        margins: '0 0 0 0',
                        frame: false,
                        border: false,
                        items: [
                            {
                                xtype: 'elementtypes-tree',
                                region: 'west',
                                title: Phlexible.elementtypes.Strings.template_elementtype,
                                iconCls: 'p-elementtype-tree_template-icon',
                                width: 300,
                                split: true,
                                collapsible: true,
                                collapsed: true,
                                mode: 'template'
                            },
                            {
                                region: 'center',
                                layout: 'border',
                                margins: '0 0 0 0',
                                frame: false,
                                border: false,
                                items: [
                                    {
                                        xtype: 'elementtypes-tree',
                                        region: 'west',
                                        iconCls: 'p-elementtype-tree_edit-icon',
                                        width: 300,
                                        split: true,
                                        collapsible: false,
                                        listeners: {
                                            nodeChange: this.onNodeChange,
                                            beforemovenode: function (tree, node, oldParent, newParent, index) {
//                                Phlexible.msg('Element Type Action', 'Node "' + node.text + '" moved.');

                                                this.getEditTreePanel().setDirty();
                                            },
                                            beforenodedrop: function (e) {
                                                // check if source node comes from another tree
                                                if (e.dropNode.ownerTree !== e.target.ownerTree) {
                                                    e.dropNode = e.dropNode.clone();

                                                    if (e.dropNode.attributes.type == 'referenceroot') {
                                                        e.dropNode.text = e.dropNode.attributes.properties.root.reference_title;
                                                        e.dropNode.allowChildren = true;
                                                        e.dropNode.attributes.type = 'reference';
                                                        e.dropNode.attributes.ds_id = new Ext.ux.GUID().toString();
                                                        e.dropNode.attributes.reference = {
                                                            refID: e.dropNode.attributes.element_type_id,
                                                            refVersion: e.dropNode.attributes.element_type_version
                                                        };
                                                        e.dropNode.attributes.editable = false;
                                                        e.dropNode.attributes.cls = 'p-elementtypes-type-reference';
                                                        e.dropNode.attributes.iconCls = 'p-elementtype-field_reference-icon';

                                                        e.dropNode.firstChild.cascade(function (node) {
                                                            node.allowChildren = true;
                                                            node.attributes.reference = true;
                                                            node.attributes.editable = false;
                                                            node.attributes.cls += ' p-elementtypes-reference';
                                                        });
//                                                    Phlexible.msg('Element Type Action', 'Reference Element Type "' + e.dropNode.text + '" added from Template Tree to Edit Tree.');
                                                    } else {
                                                        e.dropNode.allowChildren = true;
                                                        e.dropNode.attributes.ds_id = new Ext.ux.GUID().toString();
                                                        e.dropNode.cascade(function (node) {
                                                            node.allowChildren = true;
                                                            node.attributes.ds_id = new Ext.ux.GUID().toString();
                                                        });
//                                                    Phlexible.msg('Element Type Action', 'Node "' + e.dropNode.text + '" copied from Template Tree to Edit Tree.');
                                                    }
                                                    this.getEditTreePanel().setDirty();
                                                }
                                            },
                                            nodedrop: function(e) {
                                                var hasInvalidWorkingTitle = function(node) {
                                                    var invalid = false;
                                                    node.getOwnerTree().getRootNode().cascade(function(cascadeNode) {
                                                        if (node.id !== -1
                                                                && node.id !== cascadeNode.id
                                                                && node.attributes.properties
                                                                && cascadeNode.attributes.properties
                                                                && node.attributes.properties.field
                                                                && cascadeNode.attributes.properties.field
                                                                && node.attributes.properties.field.working_title == cascadeNode.attributes.properties.field.working_title) {
                                                            invalid = true;
                                                            return false;
                                                        }
                                                    });
                                                    return invalid;
                                                }

                                                e.dropNode.attributes.invalid = hasInvalidWorkingTitle(e.dropNode);
                                                e.dropNode.attributes.editable = true;

                                                e.dropNode.ui.addClass('dirty');
                                                if (e.dropNode.attributes.invalid) {
                                                    e.dropNode.ui.addClass('invalid');
                                                }

                                                e.dropNode.cascade(function(node) {
                                                    node.attributes.invalid = hasInvalidWorkingTitle(e.dropNode);
                                                    e.dropNode.attributes.editable = true;

                                                    node.ui.addClass('dirty');
                                                    if (node.attributes.invalid) {
                                                        node.ui.addClass('invalid');
                                                    }
                                                });
                                            },
                                            publish: this.onElementtypePublish,
                                            beforereset: this.onElementtypeReset,
                                            dirty: function () {
                                                this.dirty = true;
                                            },
                                            clean: function () {
                                                this.dirty = false;
                                            },
                                            elementtypeload: this.onElementtypeLoad,
                                            scope: this
                                        }
                                    },
                                    {
                                        layout: 'card',
                                        border: true,
                                        region: 'center',
                                        activeItem: 0,
                                        items: [
                                            {
                                                xtype: 'elementtypes-root',
                                                border: false,
                                                listeners: {
                                                    saveRoot: this.onSaveRoot,
                                                    scope: this
                                                }
                                            },
                                            {
                                                xtype: 'elementtypes-field',
                                                border: false,
                                                listeners: {
                                                    saveField: this.onSaveField,
                                                    scope: this
                                                }
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'elementtypes-viability'
                    },
                    {
                        xtype: 'elementtypes-versions',
                        hidden: true,
                        disabled: true,
                        listeners: {
                            loadVersion: this.onElementtypeChange,
                            scope: this
                        }
                    },
                    {
                        xtype: 'elementtypes-usage'
                    },
                    {
                        cls: 'p-elements-data-panel',
                        title: this.strings.preview,
                        iconCls: 'p-elementtype-preview-icon',
                        border: false,
                        layout: 'fit',
                        autoScroll: true,
                        items: [
                            {
                                xtype: 'elements-elementcontentpanel',
                                autoScroll: true,
                                element: element
                            }
                        ],
                        tbar: [{
                            text: 'master',
                            enableToggle: true,
                            pressed: true,
                            handler: function() {
                                this.preview(true);
                            },
                            scope: this
                        }],
                        listeners: {
                            show: this.preview,
                            scope: this
                        }
                    }
                ]
            }
        ];

        Phlexible.elementtypes.MainPanel.superclass.initComponent.call(this);
    },

    getListGrid: function () {
        return this.getComponent(0);
    },

    getMainTabPanel: function () {
        return this.getComponent(1);
    },

    getStructureTab: function () {
        return this.getMainTabPanel().getComponent(0);
    },

    getViabilityTab: function () {
        return this.getMainTabPanel().getComponent(1);
    },

    getVersionsTab: function () {
        return this.getMainTabPanel().getComponent(2);
    },

    getUsageTab: function () {
        return this.getMainTabPanel().getComponent(3);
    },

    getPropertyCards: function () {
        return this.getStructureTab().getComponent(1).getComponent(1);
    },

    getRootTabs: function () {
        return this.getPropertyCards().getComponent(0);
    },

    getFieldTabs: function () {
        return this.getPropertyCards().getComponent(1);
    },

    getEditTreePanel: function () {
        return this.getStructureTab().getComponent(1).getComponent(0);
    },

    getTemplateTreePanel: function () {
        return this.getStructureTab().getComponent(0);
    },

    getPreviewWrap: function () {
        return this.getMainTabPanel().getComponent(4);
    },

    getPreviewPanel: function () {
        return this.getPreviewWrap().getComponent(0);
    },

    onElementtypeChange: function (id, title, version, type) {
        this.getRootTabs().clear();
        this.getFieldTabs().clear();
        this.getEditTreePanel().load(id, title, version, type);
        //this.getVersionsTab().load(id, title, version, type);
        this.getViabilityTab().load(id, title, version, type);
        this.getUsageTab().load(id, title, version, type);
    },

    onElementtypeTemplateChange: function (id, title) {
        this.getTemplateTreePanel().expand();
        this.getTemplateTreePanel().load(id, title);
    },

    onElementtypeReset: function (id, title) {
        this.setClean();
    },

    onNodeChange: function (node) {
        if (node.attributes.editable) {
            if (node.attributes.type == 'root' || node.attributes.type == 'referenceroot') {
                this.getPropertyCards().layout.setActiveItem(0);
                this.getRootTabs().loadProperties(node);
                this.getRootTabs().enable();
            } else {
                this.getPropertyCards().layout.setActiveItem(1);
                this.getFieldTabs().loadProperties(node);
                this.getFieldTabs().enable();
            }
        } else {
            this.getRootTabs().disable();
            this.getFieldTabs().disable();
        }
    },

    onSaveField: function (node) {
        node.ui.addClass('dirty');
        this.getEditTreePanel().setDirty();

        //this.getPreviewPanel().ownerCt.doLayout();
//        Phlexible.msg('Element Type Action', 'Properties of field "' + node.text + '" saved.');

        this.needPreviewRefresh = true;
    },

    onSaveRoot: function (node) {
        this.getEditTreePanel().setDirty();

//        Phlexible.msg('Element Type Action', 'Properties of root node saved.');

        this.needPreviewRefresh = true;
    },

    onElementtypePublish: function (tree) {
        this.getListGrid().getStore().reload();
        this.getEditTreePanel().getRootNode().reload();
        if (this.getTemplateTreePanel().getRootNode().isLoaded()) {
            this.getTemplateTreePanel().getRootNode().reload();
        }
    },

    onElementtypeLoad: function (tree, node) {
        //Phlexible.console.log(node.id);
        //if (node.id <= 0) {
        //    // reset loaded flag
        //    this.firstElementTreeNodeLoaded = false;
        //}
        //else if (!this.firstElementTreeNodeLoaded) {
        // show properties accordeon after elementtype load
        //    this.firstElementTreeNodeLoaded = true;
        this.getPropertyCards().layout.setActiveItem(0);
        if (!tree.disabled) {
            this.getRootTabs().loadProperties(node);
            //this.getFieldTabs().loadProperties(node);
        }
        else {
            this.getRootTabs().clear();
        }
        this.getFieldTabs().clear();

        this.needPreviewRefresh = true;
        if (this.getMainTabPanel().getActiveTab() === this.getPreviewWrap()) {
            this.preview();
        }

        this.getMainTabPanel().enable();
    },

    preview: function (needPreviewRefresh) {
        if (!needPreviewRefresh && !this.needPreviewRefresh) {
            return;
        }

        var rootNode = this.getEditTreePanel().getRootNode(),
            previewPanel = this.getPreviewPanel();

        previewPanel.element.data = {
            default_content_tab: rootNode.firstChild.attributes.properties.root.default_content_tab,
            properties: {
                et_id: rootNode.firstChild.attributes.element_type_id
            }
        };
        previewPanel.element.master = this.getPreviewWrap().getTopToolbar().items.items[0].pressed;
        previewPanel.structure = Phlexible.elements.ElementDataTabHelper.fixStructure(this.processPreviewNodes(rootNode));
        previewPanel.valueStructure = {
            structures: [],
            values: {}
        };
        previewPanel.removeAll();
        previewPanel.lateRender();

        this.needPreviewRefresh = false;
    },

    processPreviewNodes: function (node) {
        if (!node.childNodes || !node.childNodes.length) {
            return [];
        }

        var childNodes = node.childNodes,
            childNode,
            data = [];

        for (var i = 0; i < childNodes.length; i++) {
            childNode = childNodes[i];
            data.push({
                id: childNode.attributes.id,
                dsId: childNode.attributes.ds_id,
                type: childNode.attributes.type,
                configuration: childNode.attributes.properties.configuration,
                contentchannels: childNode.attributes.properties.content_channels,
                labels: childNode.attributes.properties.labels,
                options: childNode.attributes.properties.options,
                validation: childNode.attributes.properties.validation,
                children: this.processPreviewNodes(childNode)
            });
        }

        return data;
    }
});

Ext.reg('elementtypes-main', Phlexible.elementtypes.MainPanel);