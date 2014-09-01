Phlexible.elements.EidNodeUi = Ext.extend(Ext.tree.TreeNodeUI, {
    // private
    onClick: function (e) {
        if (this.node.attributes.disabled) {
            e.stopEvent();
            e.preventDefault();

            return;
        }
        Phlexible.elements.EidNodeUi.superclass.onClick.call(this, e);
    }
});

Phlexible.elements.EidLoader = Ext.extend(Ext.tree.TreeLoader, {
    clearOnLoad: true,
    preloadChildren: true,
    createNode: function (attr) {
        // apply baseAttrs, nice idea Corey!
        if (this.baseAttrs) {
            Ext.applyIf(attr, this.baseAttrs);
        }
        if (this.applyLoader !== false) {
            attr.loader = this;
        }
        //if(typeof attr.uiProvider == 'string'){
        //   attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        //}
        attr.uiProvider = Phlexible.elements.EidNodeUi;
        var node;
        if (attr.nodeType) {
            node = new Ext.tree.TreePanel.nodeTypes[attr.nodeType](attr);
        } else {
            node = attr.leaf ?
                new Ext.tree.TreeNode(attr) :
                new Ext.tree.AsyncTreeNode(attr);
        }
        this.doPreload(node);

        return node;
    }
});

Phlexible.elements.EidSelectorMenu = Ext.extend(Ext.menu.TreeMenu, {
    cls: 'x-tree-menu m-eid-selector'
});

// Implementation class for created the tree powered form field
Phlexible.elements.EidSelector = Ext.extend(Ext.ux.TreeSelector, {
    maxHeight: 200,
    elementTypeIds: '',
    nodeId: '',
    language: '',

    initComponent: function () {
        if (this.element) {
            this.siteroot_id = this.element.siteroot_id;
        }

        if (this.intrasiteroot) {
            var url = Phlexible.Router.generate('tree_link_intrasiteroot');
            var rootText = 'Siteroots';
        } else if (this.allSiteroots) {
            var url = Phlexible.Router.generate('tree_link');
            var rootText = 'Siteroots';
        } else {
            var url = Phlexible.Router.generate('tree_link_internal');
            var rootText = 'This Siteroot';
        }

        this.tree = new Ext.tree.TreePanel({
            animate: false,
            border: false,
            width: this.treeWidth || 180,
            autoScroll: true,
            useArrows: true,
            selModel: new Ext.tree.ActivationModel(),
            rootVisible: false,
            loader: new Phlexible.elements.EidLoader({
                url: url,
                baseParams: {
                    language: this.language,
                    siteroot_id: this.siteroot_id,
                    //recursive: this.recursive || 0,
                    element_type_ids: Ext.isArray(this.elementTypeIds) && this.elementTypeIds.length ? this.elementTypeIds : '',
                    value: this.value ? this.value : ''
                },
                listeners: {
                    load: {
                        fn: function () {
                            this.fireEvent('load', this);
                        },
                        scope: this
                    }
                }
            })
        });

        if (!this.nodeId) {
            var root = new Ext.tree.AsyncTreeNode({
                text: 'Root',
                id: 'root',
                leaf: false,
                iconCls: 'icon-folder',
                expanded: true,
                isFolder: true
            });
        }
        else {
            var root = new Ext.tree.AsyncTreeNode({
                text: '',
                id: this.nodeId,
                leaf: false,
                iconCls: 'icon-folder',
                expanded: true,
                isFolder: true
            });
        }

        this.tree.setRootNode(root);

        if (this.value) {
            this.tree.loader.on('load', function (v) {
                this.setValue(v);
            }.createDelegate(this, [this.value], false), this);
        }

//        this.tree.loader.load(root);
//        , function(loader, node) {
//                debugger;
//            loader.doPreload(node);
//        });

        Phlexible.elements.EidSelector.superclass.initComponent.call(this);

        // selecting folders is not allowed, so filter them
        this.tree.getSelectionModel().on('beforeselect', this.beforeSelection, this);
    },

    setSiterootId: function (siteroot_id) {
        this.tree.loader.baseParams.siteroot_id = siteroot_id;
    },

    onRender: function () {
        Ext.ux.TreeSelector.superclass.onRender.apply(this, arguments);
        this.menu = new Phlexible.elements.EidSelectorMenu(Ext.apply(this.menuConfig || {}, {tree: this.tree}));
        this.menu.render();

        this.tree.body.addClass('x-tree-selector');
        this.tree.body.addClass('p-eid-selector');
    },

    beforeSelection: function (tree, node) {
        if (node && node.attributes.isFolder) {
            node.toggle();
            return false;
        }
    }
});

Ext.reg('tidselector', Phlexible.elements.EidSelector);

