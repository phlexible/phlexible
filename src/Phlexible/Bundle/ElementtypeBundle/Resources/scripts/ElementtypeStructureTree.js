Phlexible.elementtypes.ElementtypeStructureTree = Ext.extend(Ext.tree.TreePanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.elementtype_tree,
    cls: 'p-elementtype-tree',
    rootVisible: false,
    border: true,
    loadMask: true,
    margins: '0 0 0 0',
//    useArrows: true,
    viewConfig: {
        forceFit: true
    },
    lines: true,
    autoScroll: true,
    collapseFirst: false,
    animate: false,
    enableDD: true,
    ddGroup: 'elementtypesDD',
    ddScroll: true,
    containerScroll: true,

    mode: 'edit',
    dirty: false,

    initComponent: function () {
        this.addEvents(
            /**
             * @event activate
             * Fires before any action is performed. Return false to cancel the action.
             * @param {Form} this
             * @param {Action} action The action to be performed
             */
            'activate',
            /**
             * @event beforePublish
             * Fires before the Element Type is published
             * @param {Ext.tree.TreePanel} this
             */
            'beforepublish',
            /**
             * @event publish
             * Fires after the Element Type has been published
             * @param {Ext.tree.TreePanel} this
             */
            'publish',
            /**
             * @event beforeReset
             * Fires before the Element Type is resetted
             * @param {Ext.tree.TreePanel} this
             */
            'beforereset',
            /**
             * @event reset
             * Fires after the Element Type has been resetted
             * @param {Ext.tree.TreePanel} this
             */
            'reset',
            /**
             * @event dirty
             * Fires when this Tree is marked dirty
             * @param {Ext.tree.TreePanel} this
             */
            'dirty',
            /**
             * @event clean
             * Fires when this Tree is marked clean
             * @param {Ext.tree.TreePanel} this
             */
            'clean'
        );

        this.loader = new Phlexible.elementtypes.ElementtypeStructureTreeLoader({
            dataUrl: Phlexible.Router.generate('elementtypes_tree', {mode: this.mode}),
            applyLoader: false,
            listeners: {
                load: function (loader, node, response) {
                    if (!node.hasChildNodes()) {
                        var data = Ext.decode(response.responseText);
                        if (data.error) {
                            this.disable();
                            Ext.MessageBox.alert('Failure', data.msg);
                        }
                    } else {
                        this.fireEvent('elementtypeload', this, this.root.firstChild);
                    }
                },
                scope: this
            }
        });

        this.root = new Ext.tree.AsyncTreeNode({
            text: 'ich_bin_eigentlich_gar_nicht_da',
            draggable: false,
            id: -1,
            expanded: false,
            uiProvider: Phlexible.elementtypes.ElementtypeStructureRootTreeNodeUI,
            listeners: {
                load: this.enable,
                scope: this
            }
        });

        // toolbar only in edit mode
        if (this.mode == 'edit') {
            this.tbar = [
                {
                    text: this.strings.publish,
                    iconCls: 'p-elementtype-elementtype_publish-icon',
                    handler: this.publish,
                    scope: this,
                    disabled: true
                },
                '-',
                {
                    text: this.strings.reset_element_type,
                    iconCls: 'p-elementtype-reset-icon',
                    handler: function () {
                        Ext.Msg.confirm('Warning', 'Do you really want to reset? All changed will be lost.', function (btn) {
                            if (btn == 'yes') {
                                this.onReset();
                            }
                        }, this);
                    },
                    scope: this,
                    disabled: true
                }
            ];
        } else {
            this.tbar = [
                {
                    text: 'dummy'
                }
            ];
        }

        // context menu only in edit mode
        if (this.mode == 'edit') {

            this.submenuAddBefore = new Ext.menu.Menu();

            this.submenuAddAfter = new Ext.menu.Menu();

            this.submenuAddChild = new Ext.menu.Menu();

            this.contextMenu = new Ext.menu.Menu({
                items: [
                    {
                        cls: 'x-btn-text-icon-bold',
                        text: '.',
                        canActivate: false
                    },
                    '-',
                    {
                        text: this.strings.add_field_before,
                        iconCls: 'p-elementtype-drop_over-icon',
                        menu: this.submenuAddBefore
                    },
                    {
                        text: this.strings.add_field_after,
                        iconCls: 'p-elementtype-drop_under-icon',
                        menu: this.submenuAddAfter

                    },
                    {
                        text: this.strings.add_field_as_child,
                        iconCls: 'p-elementtype-drop_add-icon',
                        menu: this.submenuAddChild

                    },
                    '-',
                    {
                        text: this.strings.copy,
                        iconCls: 'p-elementtype-copy-icon',
                        handler: function () {
                            alert('copy');
                        },
                        disabled: true
                    },
                    {
                        text: this.strings.cut,
                        iconCls: 'p-elementtype-cut-icon',
                        handler: function () {
                            alert('cut');
                        },
                        disabled: true
                    },
                    {
                        text: this.strings.paste,
                        iconCls: 'p-elementtype-paste-icon',
                        handler: function () {
                            alert('paste');
                        },
                        disabled: true
                    },
                    '-',
                    {
                        text: 'Transform to reference', //this.strings.remove,
                        iconCls: 'p-elementtype-transform-icon',
                        handler: function (item) {
                            this.transform(item.parentMenu.node);

                            this.setDirty();
                        },
                        scope: this
                    },
                    '-',
                    {
                        text: this.strings.remove,
                        iconCls: 'p-elementtype-delete-icon',
                        handler: function (item) {
                            item.parentMenu.node.remove();

//                        Phlexible.msg('Element Type Action', 'Node "' + item.parentMenu.node.text + '" removed.');

                            this.setDirty();
                        },
                        scope: this
                    }
                ]
            });

            this.addListener({
                contextmenu: this.onContextMenu,
                click: this.nodeChange,
                scope: this
            });
        }

        Phlexible.elementtypes.ElementtypeStructureTree.superclass.initComponent.call(this);
    },

    initEvents: function () {
        this.dropZone = new Phlexible.elementtypes.ElementtypeStructureTreeDropZone(this, {
            ddGroup: this.ddGroup,
            appendOnly: this.ddAppendOnly === true
        });

        this.dropZone.setPadding(0, 0, this.getInnerHeight(), 0);

        Phlexible.elementtypes.ElementtypeStructureTree.superclass.initEvents.call(this);
    },

    onSubmenuClick: function (item, event) {

        var activeNode = item.nodeObject;
        var parentNode = activeNode.parentNode;

        var newNode = new Ext.tree.TreeNode({
            text: 'Neu: ' + item.id,
            type: item.id,
            ds_id: new Ext.ux.GUID().toString(),
            cls: 'p-elementtypes-type-' + item.id,
            iconCls: item.iconCls
        });

        newNode.attributes.editable = true;
        newNode.attributes.invalid = true;

        newNode.attributes.properties = Phlexible.clone(Phlexible.elementtypes.FieldMap);

        newNode.attributes.properties.field.working_title = '';
        newNode.attributes.properties.field.type = item.id;

        if (item.appendMode == 'child') {
            activeNode.appendChild(newNode);
            activeNode.expand();
        }

        if (item.appendMode == 'before') {
            parentNode.insertBefore(newNode, activeNode);
        }

        if (item.appendMode == 'after') {
            var nextSiblingNode = activeNode.nextSibling;
            parentNode.insertBefore(newNode, nextSiblingNode);
        }

        newNode.select();
        newNode.ui.addClass('dirty');
        newNode.ui.addClass('invalid');
        newNode.getOwnerTree().setDirty();
        this.nodeChange(newNode);
    },

    onContextMenu: function (node, event) {
        event.stopEvent();

        if (node.attributes.type === 'root' && node.attributes.properties.root.type === 'layout') {
            this.contextMenu.items.items[2].disable();
            this.contextMenu.items.items[3].disable();
            this.contextMenu.items.items[4].disable();

            // CLEANING
            this.submenuAddBefore.removeAll();
            this.submenuAddAfter.removeAll();
            this.submenuAddChild.removeAll();
        } else if (node.attributes.reference) {
            this.contextMenu.items.items[2].disable();
            this.contextMenu.items.items[3].disable();
            this.contextMenu.items.items[4].disable();
            this.contextMenu.items.items[10].disable();

            if (node.attributes.reference === true) {
                this.contextMenu.items.items[12].disable();
            } else {
                this.contextMenu.items.items[12].enable();
            }
            this.contextMenu.items.items[12].setText(this.strings.remove_reference);
        } else {
            // ###################### CREATE SUBMENU

            // INIT
            this.contextMenu.items.items[2].enable();
            this.contextMenu.items.items[3].enable();
            this.contextMenu.items.items[4].enable();

            // CLEANING
            this.submenuAddBefore.removeAll();
            this.submenuAddAfter.removeAll();
            this.submenuAddChild.removeAll();

            // BEFORE AND AFTER
            var parentNode = node.parentNode;
            var fieldTypeMatrixParent = Phlexible.fields.FieldTypes[parentNode.attributes.type];
            var fieldTypeMatrix = Phlexible.fields.FieldTypes[node.attributes.type];

            var hasSibling = false;
            var hasChild = false;

            var parentId = parentNode.attributes.type;
            var currentId = node.attributes.type;
            for (var fieldId in Phlexible.fields.FieldTypes) {
                if (typeof Phlexible.fields.FieldTypes[fieldId] == 'function') {
                    continue;
                }

                if (fieldTypeMatrixParent && node.attributes.type != 'root' &&
                    Phlexible.fields.FieldTypes[fieldId].allowedIn.indexOf(parentId) != -1) {
                    if (node.parentNode.attributes.type != 'referenceroot' || !node.parentNode.firstChild) {
                        hasSibling = true;
                        var attrTitle = Phlexible.fields.FieldTypes[fieldId].titles[Phlexible.Config.get('user.property.interfaceLanguage', 'en')];
                        var attrIconCls = Phlexible.fields.FieldTypes[fieldId].iconCls;
                        this.submenuAddBefore.add({
                            iconCls: attrIconCls,
                            text: attrTitle,
                            id: fieldId,
                            nodeObject: node,
                            appendMode: 'before',
                            handler: this.onSubmenuClick,
                            scope: this
                        });
                        this.submenuAddAfter.add({
                            iconCls: attrIconCls,
                            text: attrTitle,
                            id: fieldId,
                            nodeObject: node,
                            appendMode: 'after',
                            handler: this.onSubmenuClick,
                            scope: this
                        });
                    }
                }
                if (fieldTypeMatrix && Phlexible.fields.FieldTypes[fieldId].allowedIn.indexOf(currentId) != -1) {
                    if (node.attributes.type == 'referenceroot' && node.firstChild) {
                        break;
                    }
                    hasChild = true;
                    var attrTitle = Phlexible.fields.FieldTypes[fieldId].titles[Phlexible.Config.get('user.property.interfaceLanguage', 'en')];
                    var attrIconCls = Phlexible.fields.FieldTypes[fieldId].iconCls;
                    this.submenuAddChild.add({
                        iconCls: attrIconCls,
                        text: attrTitle,
                        id: fieldId,
                        nodeObject: node,
                        appendMode: 'child',
                        handler: this.onSubmenuClick,
                        scope: this
                    });
                }
            }

            if (!hasSibling) {
                this.contextMenu.items.items[2].disable();
                this.contextMenu.items.items[3].disable();
            }
            if (!hasChild) {
                this.contextMenu.items.items[4].disable();
            }

            this.contextMenu.items.items[10].enable();
            this.contextMenu.items.items[12].enable();
            this.contextMenu.items.items[12].setText(this.strings.remove);

            // ###################### END SUBMENU
        }

        this.contextMenu.items.items[0].setText(node.text);

        if (node.attributes.type == 'root' || node.attributes.type == 'referenceroot') {
            this.contextMenu.items.items[10].disable();
            this.contextMenu.items.items[12].disable();
        }
        else if (node.ownerTree.root.firstChild.attributes.type == 'referenceroot') {
            this.contextMenu.items.items[10].disable();
            this.contextMenu.items.items[12].enable();
        }

        this.contextMenu.node = node;

        var coords = event.getXY();
        this.contextMenu.showAt([coords[0], coords[1]]);
    },

    setDirty: function () {
        this.dirty = true;

        this.fireEvent('dirty', this);
        this.onDirty();
    },

    onDirty: function () {
        var tb = this.getTopToolbar();
        tb.items.items[0].enable();
        tb.items.items[2].enable();
    },

    setClean: function () {
        this.dirty = false;

        this.fireEvent('clean', this);
        this.onClean();
    },

    onClean: function () {
        var tb = this.getTopToolbar();
        tb.items.items[0].disable();
        tb.items.items[2].disable();
    },

    onActivate: function (view, id, node, event) {
//        Phlexible.console.log(view);
//        Phlexible.console.log(id);
//        Phlexible.console.log(node);
//        Phlexible.console.log(event);
//        event.stopEvent();
//        Ext.get(node).addClass('x-view-selected');
//        Phlexible.console.log(node);
//        this.load(id);
    },

    load: function (id, title, version) {
        this.disable();

        if (title) {
            if (this.mode == 'edit') {
                this.setTitle(this.strings.elementtype + ' "' + title + '"');
            } else {
                this.setTitle(this.strings.template_elementtype + ' "' + title + '"');
            }
        }
        this.elementTypeID = id;

        var parameters = {id: id, mode: this.mode};
        if (version) {
            parameters.version = version;
        }
        this.loader.dataUrl = Phlexible.Router.generate('elementtypes_tree', parameters);

        this.root.reload();
        this.expandAll();

        if (this.mode == 'edit') {
            this.setClean();
        }

//        Phlexible.msg('Element Type Action', 'Element Type "' + title + '" loaded to ' + (this.mode == 'edit' ? 'Edit' : 'Template') + ' Tree.');
    },

    onReset: function () {
        if (this.fireEvent('beforereset', this) === false) {
            return;
        }

        Phlexible.msg('Element Type Action', 'Element Type "' + this.root.firstChild.text + '" reset.');

        this.setClean();
        this.disable();
        this.root.reload();
        this.expandAll();

        this.fireEvent('reset', this);
    },

    transform: function (node) {
        if (this.fireEvent('beforetransform', this, node) === false) {
            return;
        }

        var parentNode = node.parentNode;
        var dsId = new Ext.ux.GUID().toString();
        var reference = new Ext.tree.TreeNode({
            text: 'Reference ' + node.attributes.text + ' [v1]',
            ds_id: dsId,
            cls: 'p-elementtypes-node p-elementtypes-type-reference',
            leaf: false,
            expanded: true,
            type: 'reference',
            iconCls: 'p-elementtype-container_reference-icon',
            reference: {new: true},
            allowDrag: true,
            allowDrop: false,
            editable: false,
            properties: {
                field: {
                    title: '',
                    type: 'reference',
                    working_title: '',
                    comment: '',
                    image: ''
                },
                configuration: {},
                labels: {},
                options: {},
                validation: {},
                content_channels: {}
            }
        });

        parentNode.insertBefore(reference, node);
        reference.appendChild(node);
        reference.attributes.editable = false;
        reference.attributes.allowDrop = false;
        node.attributes.parent_ds_id = dsId;
        node.cascade(function(node) {
            node.getUI().addClass('p-elementtypes-reference');
            node.attributes.editable = false;
            node.attributes.allowDrag = false;
            node.attributes.allowDrop = false;
            node.draggable = false;
        });
        reference.expand();

        this.fireEvent('transform', this, node)
    },

    publish: function () {
        if (this.fireEvent('beforepublish', this) === false) {
            return;
        }

        var rootNode = this.getRootNode();
        if (!this.validateSaveNodes(rootNode)) {
            Ext.Msg.alert('Invalid nodes', 'Tree contains invalid nodes. Please correct them and publish again.')
            return;
        }
        var data = Ext.encode(this.processSaveNodes(rootNode));

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elementtypes_tree_save'),
            timeout: 600000,
            params: {
                element_type_id: this.elementTypeID,
                data: data
            },
            success: this.onPublishSuccess,
            scope: this
        });
    },

    onPublishSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.setClean();

            var rootNode = this.getRootNode();
            this.cleanNodes(rootNode);

            Phlexible.success(data.msg);

            this.fireEvent('publish', this);
        } else {
            Ext.Msg.alert('Failure', data.msg);
        }
    },

    validateSaveNodes: function(node) {
        var valid = true;
        node.eachChild(function() {
            if (node.attributes.invalid) {
                valid = false;
                return false;
            }
        });
        return valid;
    },

    /*
     Recursive function
     sweeps the tree in all levels
     */
    processSaveNodes: function (node) {
        var childNodes = node.childNodes,
            saveNodes = [];

        for (var i = 0; i < childNodes.length; i++) {
            var childNode = childNodes[i],
                nodeData = {
                    id: childNode.id,
                    ds_id: childNode.attributes.ds_id || 0,
                    parent_id: node.id,
                    parent_ds_id: node.attributes.ds_id,
                    type: childNode.attributes.type,
                    reference: childNode.attributes.reference,
                    properties: childNode.attributes.properties
                };
            if (childNode.attributes.type !== 'reference' || childNode.attributes.reference.new) {
                var children = this.processSaveNodes(childNode);
                if (children.length) {
                    nodeData.children = children;
                }
            }
            saveNodes.push(nodeData);
        }

        return saveNodes;
    },

    nodeChange: function (node) {
        //Phlexible.console.log(node);
        this.fireEvent('nodeChange', node);
    },

    cleanNodes: function (node) {
        var child = node.childNodes;

        for (var i = 0; i < child.length; i++) {
            child[i].ui.removeClass('dirty');
            child[i].ui.removeClass('error');
            this.cleanNodes(child[i]);
        }
    },

    findWorkingTitle: function (node, id, wt) {
        var child = node.childNodes;

        for (var i = 0; i < child.length; i++) {
            //Phlexible.console.log(child[i].attributes.type);
            if (child[i].attributes.type != 'root' && child[i].attributes.type != 'referenceroot' &&
                child[i].id != id && child[i].attributes.properties.field.working_title == wt) {
                return true;
            }
            if (this.findWorkingTitle(child[i], id, wt)) {
                return true;
            }
        }

        return false;
    }
});

Ext.reg('elementtypes-tree', Phlexible.elementtypes.ElementtypeStructureTree);