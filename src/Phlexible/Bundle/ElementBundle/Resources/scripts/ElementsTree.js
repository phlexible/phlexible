Phlexible.elements.ElementsTree = Ext.extend(Ext.tree.TreePanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.elements,
    border: true,
    loadMask: true,
    lines: true,
    autoScroll: true,
    rootVisible: false,
    collapseFirst: true,
    animate: false,
    cls: 'p-elements-elements-tree',

    enableDD: true,
    ddGroup: 'testtest',
    containerScroll: true,

    initComponent: function () {
        this.dataUrl = Phlexible.Router.generate('tree_tree');

        this.element.on({
            createElement: function (element, data, node) {
                if (element.language === data.master_language) {
                    node.attributes.children = false;
                    node.reload(function () {
                        var newNode = node.findChild('id', data.tid);
                        if (newNode) {
                            newNode.select();
                            element.setTreeNode(newNode);
                            newNode.expand();
                        } else {
                            element.setTreeNode(null);
                        }
                    }.createDelegate(this));
                } else {
                    // this is needed when we have a language switch and want to append the new node
                    // in the beforeload-event
                    var path = node.getPath();
                    // if new element is created append it to path
                    path += '/' + data.tid;

                    this.getLoader().baseParams.language = data.master_language;

                    this.getRootNode().reload(function (node) {
                        if (path) {
                            this.selectPath(path, 'id', function (success, selNode) {
                                if (success) {
                                    this.element.setTreeNode(selNode);
                                    // this.onNodeSelect(selNode, true);
                                } else {
                                    this.element.setStatusIdle();
                                }
                            }.createDelegate(this));
                        } else {
                            this.element.setStatusIdle();
                        }
                    }.createDelegate(this));
                }
            },
            setLanguage: function (element, language) {
                var path = false;
                if (this.element.getTreeNode() && this.element.getTreeNode().getOwnerTree()) {
                    path = this.element.getTreeNode().getPath();

                    // if new element is created append it to path
                    //if (params.id != this.element.tid) {
                    //    path += '/' + params.id;
                    //}
                }

                this.getLoader().baseParams.language = language;

                this.getRootNode().reload(function (node) {
                    if (path) {
                        this.selectPath(path, 'id', function (success, selNode) {
                            if (success) {
                                this.element.setTreeNode(selNode);
                                // this.onNodeSelect(selNode, true);
                            } else {
                                this.element.setStatusIdle();
                            }
                        }.createDelegate(this));
                    } else {
                        this.element.setStatusIdle();
                    }
                }.createDelegate(this));
            },
            xxxpublish: function (element, result) {
                if (!element.properties.teaser_id) {
                    if (element.treeNode) {
                        var data = result.data;
                        element.treeNode.attributes.navigation = data.navigation;
                        element.treeNode.attributes.restricted = data.restricted;
                        element.treeNode.setText(data.title);
                        var iconEl = element.treeNode.getUI().getIconEl();
                        if (data.status) {
                            if (iconEl.src.match(/\/status\/[a-z]+/)) {
                                iconEl.src = iconEl.src.replace(/\/status\/[a-z]+/, '/status/' + data.status);
                            } else {
                                iconEl.src += '/status/' + data.status;
                            }
                        } else {
                            if (iconEl.src.match(/\/status\/[a-z]+/)) {
                                iconEl.src = iconEl.src.replace(/\/status\/[a-z]+/, '');
                            }
                        }
                    }
                }
            },
            setOffline: function (element) {
                if (this.element.treeNode) {
                    var iconEl = this.element.treeNode.getUI().getIconEl();
                    if (iconEl.src.match(/\/status\/[a-z]+/)) {
                        iconEl.src = iconEl.src.replace(/\/status\/[a-z]+/, '');
                    }
                }
            },
            save: function (element, result) {
                if (!element.properties.teaser_id) {
                    if (element.treeNode) {
                        var data = result.data;
                        element.treeNode.attributes.navigation = data.navigation;
                        element.treeNode.attributes.restricted = data.restricted;
                        element.treeNode.setText(data.title);
                        var iconEl = element.treeNode.getUI().getIconEl();
                        if (data.icon) {
                            iconEl.src = data.icon;
                        }
                    }
                }
            },
            scope: this
        });

        this.loader = new Phlexible.elements.ElementsTreeLoader({
            dataUrl: this.dataUrl,
            uiProvider: Phlexible.elements.ElementsTreeNodeUI,
            baseParams: {
                siteroot_id: this.element.siteroot_id,
                language: this.element.language
            },
            preloadChildren: false
        });
        this.root = new Ext.tree.AsyncTreeNode({
            text: 'Root',
            draggable: false,
            id: -1,
            cls: 'node_level_0',
            type: 'root',
            expanded: true,
            listeners: {
                load: function (loader, node) {
                    if (this.element.startParams.start_tid_path) {
                        this.selectPath(this.element.startParams.start_tid_path, 'id', function (success, node) {
                            if (success) this.fireEvent('nodeSelect', node, true);
                        }.createDelegate(this));
                    }
                    this.enable();
                },
                scope: this
            }
        });

        this.populateMenuConfig();

        this.contextMenu = new Ext.menu.Menu({
            id: Ext.id(),
            element: this.element,
            items: this.menuConfig
        });

        this.on('contextmenu', function (node, event) {
            event.stopEvent();
            var coords = event.getXY();

            this.node = node;

            if (node.attributes.rights.indexOf('EDIT') !== -1) {
                this.items.items[1].enable();
            }
            else {
                this.items.items[1].disable();
            }

            if (Phlexible.User.isGranted('ROLE_ELEMENT_CREATE') &&
                node.attributes.rights.indexOf('CREATE') !== -1) {
                this.items.items[3].enable();
                this.items.items[4].enable();
            }
            else {
                this.items.items[3].disable();
                this.items.items[4].disable();
            }

            if (node.attributes.element_type_type == 'full') {
                this.items.items[6].enable();
            }
            else {
                this.items.items[6].disable();
            }

            if (Phlexible.Clipboard.isActive() &&
                Phlexible.Clipboard.getType() == 'element' &&
                Phlexible.User.isGranted('ROLE_ELEMENT_CREATE') &&
                node.attributes.rights.indexOf('CREATE') !== '-1') {
                this.items.items[7].menu.items.items[0].setText(String.format(Phlexible.elements.Strings.paste_as, Phlexible.Clipboard.getText()));
                this.items.items[7].enable();
            }
            else {
                this.items.items[7].disable();
            }

            if (Phlexible.User.isGranted('ROLE_ELEMENT_DELETE') &&
                node.attributes.rights.indexOf('DELETE') !== -1) {
                if (!node.attributes.is_published ||
                    (Phlexible.User.isGranted('ROLE_ELEMENT_PUBLISH') &&
                        node.attributes.rights.indexOf('PUBLISH') !== -1)) {
                    this.items.items[9].enable();
                }
                else {
                    this.items.items[9].disable();
                }
            }
            else {
                this.items.items[9].disable();
            }

            if (node.attributes.alias) {
                this.items.items[9].setText(Phlexible.elements.Strings.delete_alias);
            }
            else {
                this.items.items[9].setText(Phlexible.elements.Strings.delete_element);
            }

            this.showAt([coords[0], coords[1]]);
        }, this.contextMenu);

//		this.addListener('click', function(node) {
//	        //Ext.getCmp('elementtype_accordion').loadProperties(node.id);
//	    });

        Phlexible.elements.ElementsTree.superclass.initComponent.call(this);

//        this.getSelectionModel().on('selectionchange', function(selModel, node) {
//            if (this.dragStart) {
//                return;
//            }
//
//            if (!node) {
//                return;
//            }
//
//            this.fireEvent('nodeSelect', node);
//        }, this);

        if (!this.noClickHandling) {
            this.on('beforeclick', function () {
                return false;
            });

            this.on('dblclick', function (node) {
                node.select();

                if (node.attributes.rights.indexOf('EDIT') !== -1) {
                    this.fireEvent('nodeSelect', node, true);
                } else {
                    this.fireEvent('nodeSelect', node);
                }
            }, this);
        }

//        this.on('startdrag', function(sel, node) {
//            this.dragStart = 1;
//        }, this);
//
//        this.on('enddrag', function(sel, node) {
//            if (this.dragStart === 1) {
//                this.dragStart = 0;
//            }
//        }, this);

        /*this.on('beforemovenode', function(tree, node, oldParent, newParent, index) {
         Phlexible.console.log('beforemovenode');
         this.dragStart = 2;
         Ext.Ajax.request({
         url: Phlexible.Router.generate('tree_move'),
         params: {
         id: node.id,
         target: newParent.id
         },
         success: function(response) {
         var data = Ext.decode(response.responseText);
         //                    debugger;
         var node = this.getNodeById(data.data.id);
         var newParentNode = this.getNodeById(data.data.parent_id);

         node.parentNode.removeChild(node);
         //newParentNode.expand();
         newParentNode.appendChild(node);

         this.dragStart = 0;
         },
         scope: this
         });

         return false;
         }, this);*/

        this.root.reload();
        this.disable();
    },

    // private
    initEvents: function () {
        Ext.tree.TreePanel.superclass.initEvents.call(this);

        if (this.containerScroll) {
            Ext.dd.ScrollManager.register(this.body);
        }
        if ((this.enableDD || this.enableDrop) && !this.dropZone) {
            /**
             * The dropZone used by this tree if drop is enabled
             * @type Ext.tree.TreeDropZone
             */
            this.dropZone = new Phlexible.elements.ElementsTreeDropZone(this, {
                ddGroup: this.ddGroup,
                appendOnly: this.ddAppendOnly === true
            });
            this.dropZone.setPadding(0, 0, this.getInnerHeight(), 0);
        }
        if ((this.enableDD || this.enableDrag) && !this.dragZone) {
            /**
             * The dragZone used by this tree if drag is enabled
             * @type Ext.tree.TreeDragZone
             */
            this.dragZone = new Ext.tree.TreeDragZone(this, this.dragConfig || {
                ddGroup: this.ddGroup || "TreeDD",
                scroll: this.ddScroll
            });
        }
        this.getSelectionModel().init(this);
    },

    xinitEvents: function () {
        this.dropZone = new Phlexible.elements.ElementsTreeDropZone(this, {
            ddGroup: this.ddGroup,
            appendOnly: this.ddAppendOnly === true
        });

        //this.dropZone.setPadding(0, 0, this.getInnerHeight(), 0);
//        debugger;
        Phlexible.elements.ElementsTree.superclass.initEvents.call(this);
    },

    populateMenuConfig: function () {
        this.menuConfig = [
            {
                text: this.strings.open_element,
                iconCls: 'p-element-open-icon',
                handler: function (menu) {
                    var node = menu.parentMenu.node;
                    node.select();
                    this.fireEvent('nodeSelect', node);
                },
                scope: this
            },
            {
                text: this.strings.edit_element,
                iconCls: 'p-element-edit-icon',
                handler: function (menu) {
                    var node = menu.parentMenu.node;
                    node.select();
                    this.fireEvent('nodeSelect', node, true);
                },
                scope: this
            },
            '-',
            {
                text: this.strings.add_element,
                iconCls: 'p-element-add-icon',
                handler: function (menu) {
                    var node = menu.parentMenu.node;
                    this.fireEvent('newElement', node);
                },
                scope: this
            },
            {
                text: this.strings.add_alias,
                iconCls: 'p-element-alias_add-icon',
                handler: function (menu) {
                    var node = menu.parentMenu.node;
                    this.fireEvent('newAlias', node);
                },
                scope: this
            },
            '-',
            {
                text: this.strings.copy,
                iconCls: 'p-element-copy-icon',
                handler: function (menu) {
                    var node = menu.parentMenu.node;
                    Phlexible.Clipboard.set(node.text, node, 'element');
                }
            },
            {
                text: this.strings.paste,
                iconCls: 'p-element-paste-icon',
                menu: [
                    {
                        text: '-',
                        cls: 'x-btn-text-icon-bold',
                        canActivate: false
                    },
                    '-',
                    {
                        text: this.strings.paste_element,
                        iconCls: 'p-element-add-icon',
                        handler: function (menu) {
                            if (Phlexible.Clipboard.isInactive() || Phlexible.Clipboard.getType() != 'element') {
                                return;
                            }

                            var forNode = Phlexible.Clipboard.getItem();
                            var node = menu.parentMenu.parentMenu.node;

                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('tree_copy'),
                                params: {
                                    for_tree_id: forNode.id,
                                    id: node.id
                                },
                                success: function (response) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        node.reload();

                                        Phlexible.success(data.msg);
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                },
                                scope: this
                            });
                        }
                    },
                    {
                        text: this.strings.paste_alias,
                        iconCls: 'p-element-alias_add-icon',
                        handler: function (menu) {
                            if (Phlexible.Clipboard.isInactive() || Phlexible.Clipboard.getType() != 'element') {
                                return;
                            }

                            var forNode = Phlexible.Clipboard.getItem();
                            var node = menu.parentMenu.parentMenu.node;

                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('tree_create_instance'),
                                params: {
                                    for_tree_id: forNode.id,
                                    id: node.id
                                },
                                success: function (response) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        node.reload();

                                        Phlexible.success(data.msg);
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                },
                                scope: this
                            });
                        },
                        scope: this
                    }
                ],
                disabled: true
            },
            '-',
            {
                text: this.strings.delete_element,
                iconCls: 'p-element-delete-icon',
                handler: function (menu) {
                    var node = menu.parentMenu.node;
                    Ext.MessageBox.confirm(this.strings.warning, this.strings.confirm_delete_element, function (btn) {
                        if (btn == 'yes') {
                            this.onDeleteElement(node);
                        }
                    }, this);
                },
                scope: this
            },
            '-',
            {
                hidden: true
            },
            {
                hidden: true
            },
            {
                hidden: true
            },
            {
                text: this.strings.reload,
                iconCls: 'p-element-reload-icon',
                handler: function (menu) {
                    var node = menu.parentMenu.node;
                    node.attributes.children = false;
                    node.reload(function (node) {
                        var tree = node.getOwnerTree();
                        var n = tree.getNodeById(this.element.tid);
                        if (n) {
                            tree.getSelectionModel().select(n);
                        }
                    }.createDelegate(this));
                },
                scope: this
            }
        ];
    },

    /*
     load: function(id, title){
     this.disable();

     if(title) {
     this.setTitle(this.strings.elementtype);
     this.root.setText(this.strings.elementtype + ' "' + title + '"');
     }
     this.loader.dataUrl = Phlexible.Router.generate('elements_tree_id', {id: id});

     this.root.reload();
     this.expandAll();
     },
     */

    onDeleteElement: function (node) {
        var params = {
            'id[0]': node.id
        };

        var select = false;
        if (this.element.treeNode && (node.id == this.element.treeNode.id || this.element.treeNode.isAncestor(node))) {
            select = true;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('tree_delete_check'),
            params: params,
            success: function (response) {
                var result = Ext.decode(response.responseText);

                // no instances
                if (!result.data.length) {
                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('tree_delete'),
                        params: params,
                        success: this.onDeleteElementSuccess.createDelegate(this, [node.parentNode, true], true),
                        scope: this
                    });
                } else {
                    var w = new Phlexible.elements.DeleteInstancesWindow({
                        data: result.data
                    });
                    w.show();
                }
            },
            scope: this
        });

        /* Ext.Ajax.request({
         url: Phlexible.Router.generate('tree_delete'),
         params: params,
         success: this.onDeleteElementSuccess.createDelegate(this, [node.parentNode, true], true),
         scope: this
         });*/
    },

    onDeleteElementSuccess: function (response, options, node, select) {
        var data = Ext.decode(response.responseText);

        if (!data.success) {
            Ext.MessageBox.alert('Error', data.msg);
        } else {
            node.attributes.children = false;
            node.reload();
            if (select) {
                node.select();
                this.fireEvent('nodeSelect', node, true);
            }
        }
    }
});

Ext.reg('elements-tree', Phlexible.elements.ElementsTree);