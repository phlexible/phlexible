Ext.provide('Phlexible.teasers.ElementLayoutTree');

Ext.require('Phlexible.teasers.ElementLayoutTreeNodeUI');
Ext.require('Phlexible.teasers.ElementLayoutTreeLoader');
Ext.require('Phlexible.teasers.NewTeaserWindow');
Ext.require('Phlexible.teasers.NewTeaserInstanceWindow');

Phlexible.teasers.ElementLayoutTree = Ext.extend(Ext.tree.TreePanel, {
    title: Phlexible.teasers.Strings.layout,
    strings: Phlexible.teasers.Strings,
    rootVisible: false,
    disabled: true,
    autoScroll: true,
    enableDD: true,
    cls: 'p-elements-layout-tree',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            setLanguage: this.onSetLanguage,
            publishAdvanced: function (element) {
                if (element.properties.teaser_id) {
                    element.teaserNode = null;
                    this.getRootNode().reload();
                }
            },
            setOffline: function (element) {
                if (element.properties.teaser_id) {
                    if (element.teaserNode && 'function' == typeof element.teaserNode.setText) {
                        var iconEl = element.teaserNode.getUI().getIconEl();
                        if (iconEl.src.match(/\/status\/[a-z]+/)) {
                            iconEl.src = iconEl.src.replace(/\/status\/[a-z]+/, '');
                        }
                    }
                }
            },
            setOfflineAdvanced: function (element) {
                if (element.properties.teaser_id) {
                    element.teaserNode = null;
                    this.getRootNode().reload();
                }
            },
            save: function (element, result) {
                if (element.properties.teaser_id) {
                    if (element.teaserNode && 'function' == typeof element.teaserNode.setText) {
                        var data = result.data;
                        element.teaserNode.setText(data.title);
                        var iconEl = element.teaserNode.getUI().getIconEl();
                        if (data.status) {
                            if (iconEl.src.match(/\/status\/[a-z]+/)) {
                                iconEl.src = iconEl.src.replace(/\/status\/[a-z]+/, '/status/' + data.status);
                            } else {
                                iconEl.src += '?status=' + data.status;
                            }
                        } else {
                            if (iconEl.src.match(/\/status\/[a-z]+/)) {
                                iconEl.src = iconEl.src.replace(/\/status\/[a-z]+/, '');
                            }
                        }
                    } else {
                        Phlexible.console.warn('element.teaserNode is undefined');
                    }
                }
            },
            scope: this
        });

        this.loader = new Phlexible.teasers.ElementLayoutTreeLoader({
            dataUrl: Phlexible.Router.generate('teasers_layout_tree'),
            baseParams: {
                language: this.element.language
            },
            preloadChildren: true,
            listeners: {
                load: function (loader, rootNode) {
                    if (this.selectId) {
                        var targetNode = null;
                        rootNode.cascade(function (currentNode) {
                            if (currentNode.id == this.selectId) {
                                //Phlexible.console.info('loader.select()');
                                currentNode.select();
                                targetNode = currentNode;
                                return false;
                            }
                        }, this);
                        this.fireEvent('teaserselect', this.selectId, targetNode, this.selectLanguage);
                        this.selectId = null;
                        this.selectLanguage = null;
                    }
                },
                scope: this
            }
        });

        this.root = new Ext.tree.TreeNode({
            text: 'Root',
            id: -1,
            cls: 'node_level_0',
            type: 'root',
            expanded: true,
            allowDrag: false,
            allowDrop: false
        });

        /*this.selModel = new Ext.tree.DefaultSelectionModel({
         listeners: {
         selectionchange: {
         fn: function(sm, node) {
         if(node.attributes.type == 'teaser' && !node.attributes.inherit) {
         this.fireEvent('teaserselect', node.attributes.eid);
         }
         },
         scope: this
         }
         }
         });*/

        this.contextMenu = new Ext.menu.Menu({
            element: this.element,
            items: [
                {
                    // 0
                    text: '.',
                    cls: 'x-btn-text-icon-bold',
                    iconCls: 'p-teaser-layoutarea-icon'
                },
                '-',
                {
                    // 2
                    text: this.strings.add_teaser,
                    iconCls: 'p-teaser-teaser_add-icon',
                    handler: function (item) {
                        var node = item.parentMenu.node;
                        var w = new Phlexible.teasers.NewTeaserWindow({
                            submitParams: {
                                siteroot_id: this.element.siteroot_id,
                                tree_id: node.attributes.parent_tid, //this.element.tid,
                                eid: node.attributes.parent_eid, //this.element.eid,
                                layoutarea_id: node.attributes.area_id
                            },
                            listeners: {
                                success: function (window, result) {
                                    this.element.setLanguage(result.data.language, true);

                                    this.selectId = result.id;
                                    this.selectLanguage = result.data.language;

                                    this.loader.baseParams.language = this.selectLanguage;
                                    this.root.reload();
                                },
                                scope: this
                            }
                        });
                        w.show();
                    },
                    scope: this
                },
                {
                    // 3
                    text: this.strings.add_teaser_reference,
                    iconCls: 'p-teaser-teaser_reference-icon',
                    handler: function (item) {
                        var node = item.parentMenu.node;
                        var w = new Phlexible.teasers.NewTeaserInstanceWindow({
                            element: this.element,
                            listeners: {
                                teaserSelect: function (forTeaserId, layoutAreaId, tid) {
                                    Ext.Ajax.request({
                                        url: Phlexible.Router.generate('teasers_layout_createinstance'),
                                        params: {
                                            for_teaser_id: forTeaserId,
                                            id: layoutAreaId,
                                            tid: tid
                                        },
                                        success: function (response) {
                                            var data = Ext.decode(response.responseText);

                                            if (data.success) {
                                                this.getRootNode().reload();

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
                        });
                        w.show();
                    },
                    scope: this
                },
                '-',
                {
                    // 5
                    text: this.strings.inherited,
                    checked: true,
                    handler: function (item) {
                        var node = item.parentMenu.node;
                        if (!node.attributes.inherit) {
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('teasers_layout_inherit'),
                                params: {
                                    tree_id: node.attributes.parent_tid,
                                    teaser_id: node.id
                                },
                                success: function (response, options, node) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        Phlexible.success(data.msg);

                                        node.getOwnerTree().getRootNode().reload();
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                    //                          node.parentNode.reload();
                                }.createDelegate(this, [node], true)
                            });
                        } else {
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('teasers_layout_stop'),
                                params: {
                                    tree_id: node.attributes.parent_tid,
                                    teaser_id: node.id
                                },
                                success: function (response, options, node) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        Phlexible.success(data.msg);

                                        node.getOwnerTree().getRootNode().reload();
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                    //                          node.parentNode.reload();
                                }.createDelegate(this, [node], true)
                            });
                        }
                    },
                    scope: this
                },
                {
                    // 6
                    text: this.strings.shown_here,
                    checked: true,
                    handler: function (item) {
                        var node = item.parentMenu.node;
                        if (!node.attributes.hide) {
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('teasers_layout_hide'),
                                params: {
                                    tree_id: node.attributes.parent_tid,
                                    teaser_id: node.id
                                },
                                success: function (response, options, node) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        Phlexible.success(data.msg);

                                        node.getOwnerTree().getRootNode().reload();
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                    //                          node.parentNode.reload();
                                }.createDelegate(this, [node], true)
                            });
                        } else {
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('teasers_layout_show'),
                                params: {
                                    tree_id: node.attributes.parent_tid,
                                    teaser_id: node.id
                                },
                                success: function (response, options, node) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        Phlexible.success(data.msg);

                                        node.getOwnerTree().getRootNode().reload();
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                    //                          node.parentNode.reload();
                                }.createDelegate(this, [node], true)
                            });
                        }
                    },
                    scope: this
                },
                // 7
                '-',
                {
                    // 8
                    text: Phlexible.elements.Strings.copy,
                    iconCls: 'p-element-copy-icon',
                    handler: function (menu) {
                        var node = menu.parentMenu.node;
                        Phlexible.Clipboard.set(node.text, node, 'teaser');
                    }
                },
                {
                    // 9
                    text: Phlexible.elements.Strings.paste,
                    iconCls: 'p-element-paste-icon',
                    menu: [
                        {
                            text: '-',
                            cls: 'x-btn-text-icon-bold',
                            canActivate: false
                        },
                        '-',
                        {
                            text: Phlexible.elements.Strings.paste_alias,
                            iconCls: 'p-teaser-teaser_reference-icon',
                            handler: function (menu) {
                                if (Phlexible.Clipboard.isInactive() || Phlexible.Clipboard.getType() != 'teaser') {
                                    return;
                                }

                                var forNode = Phlexible.Clipboard.getItem();
                                var node = menu.parentMenu.parentMenu.node;

                                Ext.Ajax.request({
                                    url: Phlexible.Router.generate('teasers_layout_createinstance'),
                                    params: {
                                        for_teaser_id: forNode.id,
                                        id: node.attributes.area_id,
                                        tid: node.attributes.parent_tid
                                    },
                                    success: function (response) {
                                        var data = Ext.decode(response.responseText);

                                        if (data.success) {
                                            node.getOwnerTree().getRootNode().reload();

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
                // 10
                '-',
                {
                    // 11
                    text: this.strings.delete_teaser,
                    iconCls: 'p-teaser-teaser_delete-icon',
                    hidden: true,
                    handler: function (item) {
                        var node = item.parentMenu.node;
                        Ext.MessageBox.confirm('Confirm', 'Are you sure?', function (btn, text, x, node) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    url: Phlexible.Router.generate('teasers_layout_delete'),
                                    params: {
                                        teaser_id: node.id,
                                        type: node.attributes.type
                                    },
                                    success: function (node) {
                                        // reload full element if current teaser is deleted
                                        if (this.element.teaserNode &&
                                            this.element.teaserNode.id &&
                                            this.element.teaserNode.id == node.id) {
                                            this.element.load(node.attributes.parent_tid, null, null, 1);
                                        }
                                        else {
                                            // reload layout panel only
                                            node.getOwnerTree().getRootNode().reload();
                                        }

//                                  node.parentNode.reload();
                                    }.createDelegate(this, [node], false)
                                });
                            }
                        }.createDelegate(this, [node], true));
                    },
                    scope: this
                }
            ]
        });

        this.on({
            beforeclick: function () {
                return false;
            },
            dblclick: function (node, e) {
                if (node.attributes.type == 'area' || node.attributes.type == 'layout') {
                    this.fireEvent('areaselect', node.attributes.area_id, node);
                    return false;
                }

                node.select();
                if (node.attributes.type == 'element' && !node.attributes.inherited) {
                    this.fireEvent('teaserselect', node.attributes.id, node);
                }
                else if (node.attributes.type == 'catch') {
                    // legacy
                    this.fireEvent('catchselect', node.attributes.id, node.attributes.catchConfig, node);
                    return false;
                }

                e.stopEvent();

                return true;
            },
            scope: this,
            contextmenu: {
                fn: function (node, event) {
                    event.stopEvent();
                    var coords = event.getXY();

                    this.node = node;

                    var type = node.attributes.type;

                    if (type === 'area' || type === 'layout') {
                        this.items.items[0].setText('[Layoutarea]');
                        this.items.items[0].setIconClass('p-teaser-layoutarea-icon');

                        this.items.items[2].show();
                        this.items.items[3].show();
                        this.items.items[4].show();

                        this.items.items[5].hide();
                        this.items.items[6].hide();
                        this.items.items[7].show();
                        this.items.items[8].hide();
                        this.items.items[9].show();
                        this.items.items[10].hide();
                        this.items.items[11].hide();

                        if (!Phlexible.Clipboard.isInactive() && Phlexible.Clipboard.getType() === 'teaser') {
                            this.items.items[9].menu.items.items[0].setText(String.format(Phlexible.elements.Strings.paste_as, Phlexible.Clipboard.getText()));
                            this.items.items[9].enable();
                        } else {
                            this.items.items[9].disable();
                        }
                    }
                    else if (type === 'teaser' || type === 'element') {
                        this.items.items[0].setText('[Teaser]');
                        this.items.items[0].setIconClass('p-teaser-teaser-icon');

                        this.items.items[2].hide();
                        this.items.items[3].hide();
                        this.items.items[4].hide();

                        this.items.items[5].show();
                        if (node.attributes.inherit) {
                            this.items.items[5].setChecked(true);
                        } else {
                            this.items.items[5].setChecked(false);
                        }

                        this.items.items[6].show();
                        if (node.attributes.hide) {
                            this.items.items[6].setChecked(false);
                        }
                        else {
                            this.items.items[6].setChecked(true);
                        }

                        if (node.attributes.inherited) {
                            this.items.items[7].hide();
                            this.items.items[8].hide();
                            this.items.items[9].hide();
                            this.items.items[10].hide();
                            this.items.items[11].hide();
                        }
                        else {

                            this.items.items[7].show();
                            this.items.items[8].show();
                            this.items.items[9].hide();
                            this.items.items[10].show();
                            this.items.items[11].setText(Phlexible.teasers.Strings.delete_teaser);
                            this.items.items[11].setIconClass('p-teaser-teaser_delete-icon');
                            this.items.items[11].show();
                        }

                        this.items.items[5].show();
                    }
                    else if (type == 'catch') {
                        // legacy
                        this.items.items[0].setText('[Catch]');
                        this.items.items[0].setIconClass('p-teaser-catch-icon');

                        this.items.items[2].hide();
                        this.items.items[3].hide();
                        this.items.items[4].hide();

                        this.items.items[5].hide();

                        this.items.items[6].hide();
                        this.items.items[7].hide();
                        this.items.items[8].hide();
                        this.items.items[9].hide();

                        this.items.items[10].hide();
                        this.items.items[11].setText(Phlexible.teasers.Strings.delete_catch);
                        this.items.items[11].setIconClass('p-teaser-catch_delete-icon');
                        this.items.items[11].show();
                    }
                    else {
                        return;
                    }

                    if (this.element.isAllowed('CREATE')) {
                        this.items.items[2].enable();
                        this.items.items[3].enable();
                        this.items.items[4].enable();
                    }
                    else {
                        this.items.items[2].disable();
                        this.items.items[3].disable();
                        this.items.items[4].disable();
                        this.items.items[9].disable();
                    }

                    if (this.element.isAllowed('DELETE')) {
                        this.items.items[11].enable();
                    }
                    else {
                        this.items.items[11].disable();
                    }

                    this.showAt([coords[0], coords[1]]);
                },
                scope: this.contextMenu
            }
        });

        Phlexible.teasers.ElementLayoutTree.superclass.initComponent.call(this);
    },

    onSetLanguage: function (element, language) {
        if (element.properties && element.properties.et_type != 'part') {
            return;
        }

        this.doLoad(element, language);
    },

    onLoadElement: function (element) {
        if (element.properties.et_type != 'full' && element.properties.et_type != 'structure') {
            return;
        }

        this.doLoad(element);
    },

    doLoad: function (element, language) {
        if (!element.tid) {
            return;
        }
        
        this.disable();

        this.loader.baseParams = {
            tid: element.tid,
            eid: element.eid,
            siteroot_id: element.siteroot_id,
            language: language || element.language
        };

        var root = new Ext.tree.AsyncTreeNode({
            text: 'Root',
            draggable: false,
            id: -1,
            cls: 'node_level_0',
            type: 'root',
            expanded: true,
            listeners: {
                load: this.enable,
                scope: this
            }
        });

        this.setRootNode(root);

        root.reload(function (node) {
            if (!node.hasChildNodes()) {
                this.collapse();
            } else {
                this.expand();
            }
        }.createDelegate(this));
    }
});

Ext.reg('teasers-layout-tree', Phlexible.teasers.ElementLayoutTree);
