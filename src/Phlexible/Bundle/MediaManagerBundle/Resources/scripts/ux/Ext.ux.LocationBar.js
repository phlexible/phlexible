/*
 * Copyright 2008, brainbits GmbH  All rights reserved.
 * Author: Stephan Wentz. swentz[at]brainbits.net
 *
 * http://www.brainbits.net/
 */

Ext.ns('Ext.ux');

Ext.provide('Ext.ux.LocationBar');

/**
 * LocationBar class
 * Version:  0.1
 * @class Ext.ux.Locationbar
 * @extends Ext.Toolbar
 * Locationbar class.
 * @constructor
 * Creates a new LocationBar
 * @param {Object/Array} config A config object or an array of buttons to add
 */
Ext.ux.LocationBar = Ext.extend(Ext.Toolbar, {

    /**
     * @cfg {Array} strings Translations for different strings
     */
    strings: {
        back: 'Back',
        forward: 'Forward',
        reload: 'Reload',
        home: 'Home'
    },

    /**
     * @cfg {Number} maxItem Maximum number of items the Locationbar takes before the first items are removed (defaults to 15).
     * Set to 0 for unlimited items.
     */
    maxItems: 15,

    /**
     * @cfg {String} emptyText The that is shown if no history is available (defaults to 'No node selected.').
     */
    emptyText: 'No node selected.',

    /**
     * @cfg {Boolean} noReload If set to true the reload button will not be rendered (defaults to false).
     */
    noReload: false,

    /**
     * @cfg {Boolean} noHome If set to true the home button will not be rendered (defaults to false).
     */
    noHome: false,

    /**
     * @cfg {Function} selectHandler  The function to
     * call when clicked. Arguments passed are:<ul>
     * <li><b>node</b> : Object<p style="margin-left:1em">The node associated with the clicked item.</p></li>
     * </ul>
     */
    selectHandler: null,

    /**
     * @cfg {Function} reloadHandler  The function to
     * call when clicked. Arguments passed are:<ul>
     * <li><b>node</b> : Object<p style="margin-left:1em">The node associated with the current item.</p></li>
     * </ul>
     */
    reloadHandler: null,

    /**
     * @cfg {String} locationItems Initial items (defaults to []).
     */
    locationItems: [],

    /**
     * @cfg {String} folderIconCls Iconclass for folder icons.
     */
    folderIconCls: 'x-locationbar-folder-icon',

    /**
     * @cfg {String} folderIconCls Iconclass for the backward icon.
     */
    backwardIconCls: 'x-locationbar-back-icon',

    /**
     * @cfg {String} folderIconCls Iconclass for the forward icon.
     */
    forwardIconCls: 'x-locationbar-forward-icon',

    /**
     * @cfg {String} folderIconCls Iconclass for the reload icon.
     */
    reloadIconCls: 'x-locationbar-reload-icon',

    /**
     * @cfg {String} folderIconCls Iconclass for the reload icon.
     */
    homeIconCls: 'x-locationbar-home-icon',

    /**
     * @cfg {Ext.tree.TreePanel} tree The treePanel this Locationbar is associated with.
     */
    tree: null,

    /**
     * @cfg {String} stopNodeId If set, going up the breadcrumb will stop at this ID.
     */
    stopNodeId: null,

    /**
     * @cfg {Object} searchBox A custom search box
     */
    searchBox: null,

    // private
    historyItemNodes: {},

    // private
    historyItems: [],

    // private
    currentItem: false,

    // private
    historyNext: false,

    // private
    initComponent: function () {

        if (this.tree) {
            this.bindTree(this.tree);
            delete this.tree;
        }

        //this.addListener('render', this.repaint, this);

        Ext.ux.LocationBar.superclass.initComponent.call(this);
    },

    bindTree: function (tree) {
        tree.getSelectionModel().addListener('selectionchange', function (sm, node) {
            if (node) {
                this.setNode(node);
            } else {
                this.clear();
            }
        }, this);
    },

    // private
    autoCreate: {
        cls: 'x-toolbar x-small-editor x-locationbar',
        html: '<table cellspacing="0"><tr></tr></table>'
    },

    // private
    onRender: function (ct, position) {
        Ext.ux.LocationBar.superclass.onRender.call(this, ct, position);

        this.repaint();
    },

    // private
    onClick: function (node) {
        if (this.selectHandler) {
            this.selectHandler(node);
        } else {
            if (node.parentNode) {
                node.ensureVisible();
            }
            node.select();
        }

    },

    // private
    onReload: function (node) {
        if (this.reloadHandler) {
            this.reloadHandler(node);
        } else if (node.reload) {
            node.reload();
        }
    },

    /**
     * Clears all items from the LocationBar.
     */
    clear: function () {
        this.locationItems = [];

        this.repaint;
    },

    /**
     * Sets the current Treenode
     * If a tree was provided as a config to this LocationBar, this should
     * be called automatically.
     * @param {Ext.ux.TreeNode} node The currently selected TreeNode
     */
    setNode: function (node) {
        var path = [], pNode = node, i, isInvisibleRoot, conf, children, childNode;
        var tree = node.getOwnerTree();
        var rootNode = tree.getRootNode();
        var selectedNode = tree.getSelectionModel().getSelectedNode();
        var selectedPath = [];
        if (selectedNode) {
            selectedPath = selectedNode.getPath().substr(1).split('/');
        }

        do {
            isInvisibleRoot = pNode.id === rootNode.id && !tree.rootVisible;
            conf = {
                isInvisibleRoot: isInvisibleRoot,
                text: pNode.attributes.text,
                node: pNode
                //handler: isInvisibleRoot ? Ext.emptyFn() : this.onClick.createDelegate(this, [pNode], false)
            };
            if (pNode.childNodes.length) {
                children = [];

                for (i = 0; i < pNode.childNodes.length; i++) {
                    childNode = pNode.childNodes[i];
                    if (childNode.hidden) continue;
                    children[i] = {
                        text: childNode.attributes.text,
                        node: childNode,
                        iconCls: this.folderIconCls,
                        isSelected: childNode.isSelected(),
                        handler: this.onClick.createDelegate(this, [childNode], false)
                    };
                    if (selectedPath.indexOf(childNode.id) !== -1) {
                        children[i].text = '<b>' + children[i].text + '</b>';
                    }
                }
                if (!isInvisibleRoot) {
                    conf.xtype = 'tbsplit';
                }
                conf.menu = children;
            }
            conf.fullPath = pNode.getPath('text').substr(1);
            path.unshift(conf);
        } while (pNode.parentNode && (pNode = pNode.parentNode) && (!this.stopNodeId || pNode.id != this.stopNodeId))

        this.locationItems = [];

        for (i = 0; i < path.length; i++) {
            this.addPathItemRaw(path[i]);
        }
        this.currentItem = path[path.length - 1];

        this.addHistoryItemRaw(this.currentItem);

        this.repaint();
    },

    // private
    addHistoryItemRaw: function (item) {
        if (this.historyItems.indexOf(item.text) != -1) {
            this.historyItems.remove(item.text);
            delete this.historyItemNodes[item.text];
        }

        this.historyItems.push(item.text);
        this.historyItemNodes[item.text] = item;
    },

    // private
    addPathItemRaw: function (item) {
        // if number of items > maxItems, remove last
        if (this.maxItems && this.locationItems.length > this.maxItems) {
            this.locationItems.pop();
        }

        // put new item at the end
        this.locationItems.push(item);
    },

    // private
    repaint: function () {
        this.items.each(function (item) {
            this.items.remove(item);
            item.destroy();
        }, this.items);

        // back button
        this.add({
            cls: 'x-btn-icon',
            iconCls: this.backwardIconCls,
            tooltip: this.strings.back,
            handler: function () {
                this.historyNext = this.historyItems.pop();
                var itemKey = this.historyItems.pop();
                var item = this.historyItemNodes[itemKey];
                this.onClick(item.node);
            },
            scope: this,
            disabled: this.historyItems.length > 1 ? false : true
        });

        // forward button
        // TODO: disabled, FUBAR
        this.add({
            cls: 'x-btn-icon',
            iconCls: this.forwardIconCls,
            tooltip: this.strings.forward,
            handler: function () {
                var node = this.historyNext.node;
                this.historyNext = false;
                this.onClick(node);
            },
            scope: this,
            hidden: true,
            disabled: true //this.historyNext ? false : true
        });

        if (!this.noReload || !this.noHome) {
            this.add(' ');
            this.add('-');
        }

        if (!this.noReload) {
            this.add(' ');
            // reload button
            this.add({
                cls: 'x-btn-icon',
                iconCls: this.reloadIconCls,
                tooltip: this.strings.reload,
                handler: function () {
                    this.onReload(this.currentItem.node);
                },
                scope: this
            });
        }

        if (!this.noHome) {
            this.add(' ');
            // home button
            this.add({
                cls: 'x-btn-icon',
                iconCls: this.homeIconCls,
                tooltip: this.strings.home,
                handler: function () {
                    //this.onReload(this.currentItem.node);
                },
                scope: this
            });
        }

        this.add(' ', '-', ' ');

        if (this.locationItems.length) {
            // folder icon
            this.add({
                cls: 'x-btn-icon',
                iconCls: this.folderIconCls,
                ctCls: 'x-locationbar-location x-locationbar-location-first',
                disabled: true
            });

            for (var i = 0; i < this.locationItems.length; i++) {
                var locationItem = this.locationItems[i];

                var item = {};

                if (typeof locationItem === 'object') {
                    item = locationItem;
                }
                else {
                    item.text = locationItem;
                }

                if (!item.isInvisibleRoot) {
                    if (!item.text) {
                        item.text = 'n/a';
                    }
                    item.handler = this.onClick.createDelegate(this, [locationItem.node], false);
                } else {
                    item.text = '';
                }

                item.ctCls = 'x-locationbar-location';

                this.add(item);
            }

            // spacer
            this.addItem(new Ext.ux.LocationBar.Fill());

            menu = [];
            for (var i = this.historyItems.length - 2; i >= 0; i--) {
                menu.push({
                    text: this.historyItemNodes[this.historyItems[i]].fullPath,
                    iconCls: this.folderIconCls,
                    node: this.historyItemNodes[this.historyItems[i]].node,
                    handler: function (item) {
                        this.onClick(item.node);
                    },
                    scope: this
                });
            }
            this.add({
                cls: 'x-btn-icon',
                ctCls: 'x-locationbar-location x-locationbar-location-last',
                menuAlign: 'tr-br?',
                menu: menu
            });
        } else {
            this.add({
                cls: 'x-btn-icon',
                iconCls: this.folderIconCls,
                ctCls: 'x-locationbar-location x-locationbar-location-first',
                disabled: true
            });

            if (this.emptyText) {
                this.add({
                    xtype: 'lbtext',
                    text: this.emptyText
                });
            }

            this.addItem(new Ext.ux.LocationBar.Fill());

            this.add({
                cls: 'x-btn-icon',
                ctCls: 'x-locationbar-location x-locationbar-location-last',
                menuAlign: 'tr-br?',
                disabled: true
            });
        }

        if (this.searchBox) {
            this.add('-');
            this.add(this.searchBox);
        }
    }
});

Ext.reg('locationbar', Ext.ux.LocationBar);

Ext.ux.LocationBar.Fill = Ext.extend(Ext.Toolbar.Fill, {
    // private
    render: function (td) {
        td.className = 'x-locationbar-location';

        // insert a &nbsp;
        var data = document.createTextNode('\u00a0');
        this.el.appendChild(data);

        Ext.ux.LocationBar.Fill.superclass.render.call(this, td);
    }
});
Ext.reg('lbfill', Ext.ux.LocationBar.Fill);

Ext.ux.LocationBar.TextItem = Ext.extend(Ext.Toolbar.TextItem, {
    // private
    render: function (td) {
        td.className = 'x-locationbar-location';

        Ext.ux.LocationBar.Fill.superclass.render.call(this, td);
    }
});
Ext.reg('lbtext', Ext.ux.LocationBar.TextItem);
