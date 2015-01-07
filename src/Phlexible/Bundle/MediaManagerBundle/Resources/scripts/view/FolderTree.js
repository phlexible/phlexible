Ext.provide('Phlexible.mediamanager.FolderTreeLoader');
Ext.provide('Phlexible.mediamanager.FolderTree');

Ext.require('Phlexible.mediamanager.NewFolderWindow');
Ext.require('Phlexible.mediamanager.FolderDetailWindow');
Ext.require('Phlexible.mediamanager.RenameFolderWindow');

Phlexible.mediamanager.FolderTreeLoader = Ext.extend(Ext.tree.TreeLoader, {
    /**
     * Override this function for custom TreeNode node implementation
     */
    createNode: function (attr) {
        // apply baseAttrs, nice idea Corey!
        if (this.baseAttrs) {
            Ext.applyIf(attr, this.baseAttrs);
        }
        if (this.applyLoader !== false) {
            attr.loader = this;
        }
        if (typeof attr.uiProvider == 'string') {
            attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        }
        if (typeof attr.uiProvider == 'string') {
            attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        }

        return new Ext.tree.AsyncTreeNode(attr);
    },

    getParams: function (node) {
        var buf = [], bp = this.baseParams;
        for (var key in bp) {
            if (typeof bp[key] != "function") {
                buf.push(encodeURIComponent(key), "=", encodeURIComponent(bp[key]), '&');
            }
        }
        buf.push("node=", encodeURIComponent(node.id));
        if (node.attributes.site_id) {
            buf.push('&', "site_id=", encodeURIComponent(node.attributes.site_id));
        }
        if (node.attributes.slot) {
            buf.push('&', "slot=", encodeURIComponent(node.attributes.slot));
        }
        return buf.join("");
    }
});

Phlexible.mediamanager.FolderTree = Ext.extend(Ext.tree.TreePanel, {
    strings: Phlexible.mediamanager.Strings,
    cls: 'p-foldertree',
    enableDD: true,
    containerScroll: true,
    ddGroup: 'mediamanager',
    ddAppendOnly: true,
    ddScroll: true,
    rootVisible: false,
    autoScroll: true,
    useArrows: true,
    lines: false,

    // private
    initComponent: function () {
        //this.title = this.strings.folders;

        this.addEvents(
            /**
             * @event folderChange
             * Fires after a Folder has been selected
             * @param {Number} folder_id The ID of the selected Folder.
             * @param {String} folder_name The Name of the selected Folder.
             * @param {Ext.tree.AsyncTreeNode} node The TreeNode of the selected Folder.
             */
            'folderChange',
            'reload'
        );

        this.loader = new Phlexible.mediamanager.FolderTreeLoader({
            dataUrl: Phlexible.Router.generate('mediamanager_folder_list'),
            baseAttrs: {
                uiProvider: Phlexible.mediamanager.FolderTreeNodeUI
            },
            preloadChildren: true,
            listeners: {
                load: function (loader, node) {
                    if (this.start_folder_path) {
                        this.selectPath(this.start_folder_path);
                    }
                },
                scope: this
            }
        });

        this.root = new Ext.tree.AsyncTreeNode({
            text: 'Home',
            draggable: false,
            id: 'root',
            expanded: true,
            iconCls: 't-mediamanager-home-icon'
        });

        this.selModel = new Ext.tree.DefaultSelectionModel({
            listeners: {
                selectionchange: function (sm, node) {
                    if (!node) {
                        return;
                    }
                    this.onClick(node);
                },
                scope: this
            }
        });

        /*
         this.root.appendChild(new Ext.tree.AsyncTreeNode({
         text: this.strings.root,
         draggable: false,
         id: -1,
         expanded: true
         }));
         this.root.appendChild(new Ext.tree.AsyncTreeNode({
         text: this.strings.trash,
         draggable: false,
         id: 'trash',
         cls: 'p-trash-node',
         expanded: true
         }));
         */

        this.populateContextMenuItems();

        this.contextMenu = new Ext.menu.Menu({
            items: this.contextMenuItems
        });

        this.addListener({
            load: this.onLoad,
            contextmenu: this.onContextMenu,
            movenode: this.onMove,
            nodedragover: function (e) {
                // target node is no site
                if (!e.target.attributes.site_id) {
                    return false;
                }

                // from grid
                if (e.data.selections) {
                    if (e.data.selections[0].data.site_id != e.target.attributes.site_id) {
                        return false;
                    }
                }
                // from tree
                else if (e.dropNode) {
                    if (!e.dropNode.attributes.site_id || e.dropNode.attributes.site_id != e.target.attributes.site_id) {
                        return false;
                    }
                }

                console.log('nodedragover ok');
                return true;
            },
            scope: this
        });

        Phlexible.mediamanager.FolderTree.superclass.initComponent.call(this);
    },

    populateContextMenuItems: function () {
        var contextMenuItems = [
            {
                cls: 'x-btn-text-icon-bold',
                text: '.',
                canActivate: false
            },
            '-',
            {
                cls: 'x-btn-text-icon',
                text: this.strings.reload,
                iconCls: 'p-mediamanager-folder_reload-icon',
                handler: this.onReload,
                scope: this
            },
            {
                cls: 'x-btn-text-icon',
                text: this.strings.expand_all,
                iconCls: 'p-mediamanager-folder_expand-icon',
                handler: this.onExpandAll,
                scope: this
            },
            {
                cls: 'x-btn-text-icon',
                text: this.strings.collapse_all,
                iconCls: 'p-mediamanager-folder_collapse-icon',
                handler: this.onCollapseAll,
                scope: this
            },
            '-',
            {
                cls: 'x-btn-text-icon',
                text: this.strings.rename_folder,
                iconCls: 'p-mediamanager-folder_rename-icon',
                handler: this.showRenameFolderWindow,
                scope: this
            },
            {
                cls: 'x-btn-text-icon',
                text: this.strings.new_folder,
                iconCls: 'p-mediamanager-folder_add-icon',
                handler: this.showNewFolderWindow,
                scope: this
            },
            '-',
            {
                cls: 'x-btn-text-icon',
                text: this.strings.delete_folder,
                iconCls: 'p-mediamanager-folder_delete-icon',
                handler: this.showDeleteFolderWindow,
                scope: this
            },
            '-',
            {
                cls: 'x-btn-text-icon',
                text: this.strings.folder_rights,
                iconCls: 'p-mediamanager-folder_rights-icon',
                handler: this.showRightsWindow,
                scope: this
            },
            {
                cls: 'x-btn-text-icon',
                text: this.strings.properties,
                iconCls: 'p-mediamanager-folder_properties-icon',
                handler: this.showPropertiesWindow,
                scope: this
            }
        ];

        this.contextMenuIndex = {
            header: 0,
            reload: 2,
            expand: 3,
            collapse: 4,
            rename: 6,
            create: 7,
            'delete': 9,
            rights: 11,
            properties: 12
        };
        this.contextMenuItems = contextMenuItems;
    },

    checkRights: function (right) {
        var node = this.getSelectionModel().getSelectedNode();

        if (!node) return false;

        if (node.attributes.rights && node.attributes.rights.indexOf(right) !== -1) return true;

        return false;
    },

    onLoad: function (node) {
        //if(this.start_folder_id) {
        // do nothing
        //} else
        if (!this.start_folder_path && this.getSelectionModel().getSelectedNode() === null) {
            if (this.root.firstChild) {
                this.root.firstChild.select();
            }
            //this.onClick(this.root.firstChild);
        }
    },

    onClick: function (node) {
        this.selID = node.id;

        var path = [];
        var pNode = node;
        do {
            path.unshift({
                text: pNode.attributes.text,
                node: pNode
            });
        } while ((pNode = pNode.parentNode) && pNode.id != 'root')

        var folder = {
            path: path,
            id: node.id,
            title: node.text
        };

        this.fireEvent('folderChange', node.id, node.text, node);
    },

    onCreateFolder: function () {
        var selModel = this.getSelectionModel();
        selModel.suspendEvents();
        var node = this.getSelectionModel().getSelectedNode();
        var id = node.id;
        var parentNode = node; //.parentNode;
        if (parentNode && parentNode.reload) {
            parentNode.attributes.children = false;
            parentNode.reload(function () {
                this.getSelectionModel().resumeEvents();
//                var newNode = parentNode.findChild('id', id);
                this.onClick(parentNode);
            }.createDelegate(this));
        }
    },

    onReload: function () {
        var node = this.getSelectionModel().getSelectedNode();
        if (node && node.reload) {
            node.attributes.children = false;
            node.reload(function () {
                this.fireEvent('folderChange', node.id, node.text, node);
            }.createDelegate(this));
        }
    },

    onRename: function (dialog, result) {
        var node = this.getSelectionModel().getSelectedNode();

        node.setText(result.data.folder_name);
    },

    onExpandAll: function () {
        var node = this.getSelectionModel().getSelectedNode();

        node.expand(true);
    },

    onCollapseAll: function () {
        var node = this.getSelectionModel().getSelectedNode();

        node.collapse();
    },

    onMove: function (tree, node, oldParent, newParent, index) {
        var targetID = newParent.id;
        var sourceID = node.id;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_folder_move'),
            params: {
                site_id: node.attributes.site_id,
                target_id: targetID,
                source_id: sourceID
            },
            method: 'post',
            success: this.onMoveSuccess.createDelegate(this, [node], true),
            scope: this
        });
    },

    onMoveSuccess: function (response, e, node) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            node.select();
        } else {
            Ext.Msg.alert('Failure', data.msg);
        }
    },

    showRightsWindow: function () {
        this.showPropertiesWindow('rights');
    },

    showPropertiesWindow: function (activeTabId) {
        var selFolder = this.getSelectionModel().getSelectedNode();

        if (!selFolder) return;

        var w = new Phlexible.mediamanager.FolderDetailWindow({
            folder_id: selFolder.id,
            folder_name: selFolder.text,
            folder_rights: selFolder.attributes.rights,
            activeTabId: activeTabId
        });
        w.show();
        return;

        var w = new Phlexible.mediamanager.PropertiesWindow({
            site_id: selFolder.attributes.site_id,
            folder_id: selFolder.id
        });

        w.show();
    },

    showNewFolderWindow: function () {
        var selFolder = this.getSelectionModel().getSelectedNode();

        if (!selFolder) return;

        var w = new Phlexible.mediamanager.NewFolderWindow({
            submitParams: {
                site_id: selFolder.attributes.site_id,
                parent_id: selFolder.id
            },
            listeners: {
                success: this.onCreateFolder,
                scope: this
            }
        });

        w.show();
    },

    showRenameFolderWindow: function () {
        var selFolder = this.getSelectionModel().getSelectedNode();

        if (!selFolder) return;

        var w = new Phlexible.mediamanager.RenameFolderWindow({
            values: {
                folder_name: selFolder.text
            },
            submitParams: {
                site_id: selFolder.attributes.site_id,
                folder_id: selFolder.id
            },
            listeners: {
                success: this.onRename,
                scope: this
            }
        });

        w.show();
    },

    showFolderRightsWindow: function () {
        var selFolder = this.getSelectionModel().getSelectedNode();

        if (!selFolder) return;

        var w = new Phlexible.mediamanager.FolderRightsWindow({
            site_id: selFolder.attributes.site_id,
            folder_id: selFolder.id,
            folder_title: selFolder.text,
            listeners: {
                updateRights: function () {
                    return;
                    node = selFolder.parentNode;
                    node.reload(function () {
                        this.fireEvent('folderChange', node.id, node.text, node);
                    }.createDelegate(this));
                },
                scope: this
            }
        });

        w.show();
    },

    showDeleteFolderWindow: function () {
        var selFolder = this.getSelectionModel().getSelectedNode();

        if (!selFolder) return;

        Ext.MessageBox.confirm('Confirm', 'Do you really want to delete the folder "' + selFolder.text + '" with all files and subfolders?', function (btn, e, x, site_id, folder_id) {
            if (btn == 'yes') {
                this.deleteFolder(site_id, folder_id);
            }
        }.createDelegate(this, [selFolder.attributes.site_id, selFolder.id], true));
    },

    deleteFolder: function (site_id, folder_id) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_folder_delete'),
            params: {
                site_id: site_id,
                folder_id: folder_id
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);
                if (data.success) {
                    var parent_id = data.data.parent_id,
                        node;
                    this.root.cascade(function (n) {
                        if (n.id === parent_id) {
                            node = n;
                            return false;
                        }
                    });
                    if (!node) return;
                    node.select();
                    node.attributes.children = false;
                    //this.onClick(node);
                    node.reload();
                } else {
                    Ext.MessageBox.alert('Status', 'Delete failed: ' + data.msg);
                }
            },
            scope: this
        });
    },

    onContextMenu: function (node, event) {
        event.stopEvent();

        var coords = event.getXY();

        if (node.attributes.slot) {
            if (node.attributes.slot == 'search') {
                var cm = new Ext.menu.Menu({
                    items: [
                        {
                            text: 'Delete',
                            handler: function (btn) {
                                Ext.Ajax.request({
                                    url: Phlexible.Router.generate('extendedsearch_data_delete'),
                                    params: {
                                        id: node.attributes.slot_id
                                    },
                                    success: function (response) {
                                        var node = this.getRootNode().findChild('slot', 'searches');
                                        if (node) {
                                            node.attributes.children = false;
                                            node.reload(function () {
                                                if (node) {
                                                    if (node.hasChildNodes()) {
                                                        node.ui.wrap.style.display = 'block';
                                                    } else {
                                                        node.ui.wrap.style.display = 'none';
                                                    }
                                                }
                                            });
                                        }
                                    },
                                    scope: this
                                });
                            },
                            scope: this
                        }
                    ]
                });

                cm.showAt([coords[0], coords[1]]);

                return;
            }

            return;
        }

        var contextmenu = this.contextMenu;

        if (!node.isSelected()) {
            node.select();
            //this.onClick(node);
        }

        contextmenu.items.items[this.contextMenuIndex.header].setText(node.text);
        contextmenu.items.items[this.contextMenuIndex.header].setIconClass('p-mediamanager-folder-icon');

        var isRoot = node.parentNode.id == 'root';

        // collapse
        if (!node.isLeaf()) {
            contextmenu.items.items[this.contextMenuIndex.expand].enable();
        }
        else {
            contextmenu.items.items[this.contextMenuIndex.expand].disable();
        }

        // expand
        if (!node.isLeaf() && node.isExpanded()) {
            contextmenu.items.items[this.contextMenuIndex.collapse].enable();
        }
        else {
            contextmenu.items.items[this.contextMenuIndex.collapse].disable();
        }

        // rename
        if (!this.checkRights(Phlexible.mediamanager.Rights.FOLDER_MODIFY)) {
            contextmenu.items.items[this.contextMenuIndex.rename].disable();
        }
        else {
            contextmenu.items.items[this.contextMenuIndex.rename].enable();
        }

        // create
        if (!this.checkRights(Phlexible.mediamanager.Rights.FOLDER_CREATE)) {
            contextmenu.items.items[this.contextMenuIndex.create].disable();
        }
        else {
            contextmenu.items.items[this.contextMenuIndex.create].enable();
        }

        // delete
        var deletePolicy = Phlexible.Config.get('mediamanager.delete_policy');
        var used = node.attributes.used;

        if (isRoot || !this.checkRights(Phlexible.mediamanager.Rights.FOLDER_DELETE)) {
            contextmenu.items.items[this.contextMenuIndex['delete']].disable();
        }
        else {
            if (deletePolicy == Phlexible.mediamanager.DeletePolicy.HIDE_OLD && !used) {
                contextmenu.items.items[this.contextMenuIndex['delete']].enable();
            }
            else if (deletePolicy == Phlexible.mediamanager.DeletePolicy.DELETE_OLD && used < 4) {
                contextmenu.items.items[this.contextMenuIndex['delete']].enable();
            }
            else if (deletePolicy == Phlexible.mediamanager.DeletePolicy.DELETE_ALL) {
                contextmenu.items.items[this.contextMenuIndex['delete']].enable();
            }
            else {
                contextmenu.items.items[this.contextMenuIndex['delete']].disable();
            }
        }

        // rights
        if (!this.checkRights(Phlexible.mediamanager.Rights.FOLDER_RIGHTS)) {
            contextmenu.items.items[this.contextMenuIndex.rights].disable();
        }
        else {
            contextmenu.items.items[this.contextMenuIndex.rights].enable();
        }

        contextmenu.showAt([coords[0], coords[1]]);
    }
});

Ext.reg('mediamanager-foldertree', Phlexible.mediamanager.FolderTree);