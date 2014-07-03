/*jsl:ignoreall*/

/**
 * disable backspace
 *
 * @see http://stackoverflow.com/questions/3850442/how-to-prevent-browsers-default-history-back-action-for-backspace-button-with-ja
 */
function suppressBackspace(evt) {
    evt = evt || window.event;
    var target = evt.target || evt.srcElement;

    if (evt.keyCode == 8
        && !/input|textarea/i.test(target.nodeName)
        && (target.getAttribute("class")
        && target.getAttribute("class") !== "redactor_ redactor_editor")) {
        return false;
    }
}

document.onkeydown = suppressBackspace;
document.onkeypress = suppressBackspace;

/* Set default ExtJs Ajax Timeout to 2 minutes. */
Ext.Ajax.timeout = 2 * 60 * 1000;

Ext.override(Ext.Container, {
    cascade: function (fn, scope, args) {
        if (fn.apply(scope || this, args || [this]) !== false) {
            if (this.items) {
                var cs = this.items.items;
                for (var i = 0, len = cs.length; i < len; i++) {
                    if (cs[i].cascade) {
                        cs[i].cascade(fn, scope, args);
                    } else {
                        fn.apply(scope || this, args || [cs[i]]); // added this
                    }
                }
            }
        }
    }
});

/**
 * Override to prevent drag and drop from one tree to another.
 *
 * listeners: {
 *     beforenodedrop: function(e){
 *         if (e.dropNode.ownerTree !== e.target.ownerTree) {
 *             e.dropNode = e.dropNode.clone();
 *         }
 *     }
 * }
 *
 * Clone method clones the given node
 */
Ext.override(Ext.tree.TreeNode, {
    clone: function () {
        var atts = Phlexible.clone(this.attributes);
        atts.id = Ext.id(null, "ynode-");
        var clone = new Ext.tree.TreeNode(Ext.apply({}, atts));
        clone.text = this.text;

        for (var i = 0; i < this.childNodes.length; i++) {
            clone.appendChild(this.childNodes[i].clone());
        }
        return clone;
    }
});

/**
 * helpText override
 *
 * @see http://www.extjs.com/forum/showthread.php?t=33162
 */
Ext.override(Ext.form.Field, {
    getForm: function () {
        var form;
        this.ownerCt.bubble(function (container) {
            if (container.form) {
                form = container.form;
                return false;
            }
        }, this);

        return form;
    },
    onRender: function (ct, position) {
        Ext.form.Field.superclass.onRender.call(this, ct, position);
        if (!this.el) {
            var cfg = this.getAutoCreate();
            if (!cfg.name) {
                cfg.name = this.name || this.id;
            }
            if (this.inputType) {
                cfg.type = this.inputType;
            }
            this.el = ct.createChild(cfg, position);
        }
        var type = this.el.dom.type;
        if (type) {
            if (type == 'password') {
                type = 'text';
            }
            this.el.addClass('x-form-' + type);
        }
        if (this.readOnly) {
            this.el.dom.readOnly = true;
        }
        if (this.tabIndex !== undefined) {
            this.el.dom.setAttribute('tabIndex', this.tabIndex);
        }
        this.el.addClass([this.fieldClass, this.cls]);
        this.initValue();
        // everything above is from the original onRender function

        // create the appended fields
        var ac = this.append || false;

        if (Ext.isArray(ac) && ac.length > 0) {
            var form = this.getForm();

            // create a wrap for all the fields including the one created above
            this.wrap = this.el.wrap({ tag: 'div', cls: 'testxxx' });
            // also, wrap the field create above with the same div as the appending fields
            this.appendWrap = this.el.wrap({ tag: 'div', cls: 'x-form-append', style: 'position: relative' });
            for (var i = 0, len = ac.length; i < len; i++) {
                // if the append field has append fields, delete this
                delete ac[i].append;
                // create a div wrapper with the new field within it.
                var cell = this.wrap.createChild({ tag: 'div', cls: 'x-form-append', style: 'position: relative' });
                if (!ac[i].initialConfig) {
                    var field = new Ext.ComponentMgr.create(ac[i], 'button');
                } else {
                    field = ac[i];
                }
                // render the field
                field.render(cell);

                if (form && field.isFormField) {
                    form.items.add(field);
                }

                field.ownerCt = this;

                delete ac[i];
                ac[i] = field;
            }
        }

        // if there is helpText create a div and display the text below the field
        if (this.helpText && typeof this.helpText == 'string') {
            this.wrap = this.appendWrap || this.wrap || this.el.wrap();
            if (this.width) {
                var w = this.width || '100';
                this.helpEl = this.wrap.createChild({
                    tag: 'div',
                    cls: 'x-form-helptext',
                    style: 'width: ' + w + 'px',
                    html: this.helpText
                });
            } else {
                this.helpEl = this.wrap.createChild({
                    tag: 'div',
                    cls: 'x-form-helptext',
                    html: this.helpText
                });
            }
        }
    }
});

/*
 * Load Mask fix
 * see http://www.extjs.com/forum/showthread.php?t=39250
 */
Ext.override(Ext.Element, {

    findParentBy: function (callback, returnEl) {
        var p = this.dom, b = document.body, dq = Ext.DomQuery;
        while (p && p.nodeType == 1 && p != b) {
            if (callback(p, this)) {
                return returnEl ? Ext.get(p) : p;
            }
            p = p.parentNode;
        }
        return null;
    },

    mask: function (msg, msgCls) {
        if (this.getStyle("position") == "static") {
            this.setStyle("position", "relative");
        }
        if (this._maskMsg) {
            this._maskMsg.remove();
        }
        if (this._mask) {
            this._mask.remove();
        }

        this._mask = Ext.DomHelper.append(this.dom, {cls: "ext-el-mask"}, true);

        //-------------
        var p = Ext.fly(this.dom.parentNode, '_internal');
        var parentZIndex = p.findParentBy(function (parent) {
            var zIndex = Ext.fly(parent).getStyle("z-index");
            return  zIndex != "auto" && zIndex != null;
        }, true);
        if (parentZIndex)
            this._mask.setStyle("z-index", parentZIndex.getStyle("z-index") + 1);
        else
            this._mask.setStyle("z-index", 100);
        //--------------

        this.addClass("x-masked");
        this._mask.setDisplayed(true);
        if (typeof msg == 'string') {
            this._maskMsg = Ext.DomHelper.append(this.dom, {cls: "ext-el-mask-msg", cn: {tag: 'div'}}, true);
            var mm = this._maskMsg;
            mm.dom.className = msgCls ? "ext-el-mask-msg " + msgCls : "ext-el-mask-msg";
            mm.dom.firstChild.innerHTML = msg;
            mm.setDisplayed(true);
            mm.center(this);
        }
        if (Ext.isIE && !(Ext.isIE7 && Ext.isStrict) && this.getStyle('height') == 'auto') { // ie will not expand full height automatically
            this._mask.setSize(this.dom.clientWidth, this.getHeight());
        }
        return this._mask;
    }

});

/*
 * removeAll() for Containts
 * @see http://www.extjs.com/forum/showthread.php?t=39700
 */
Ext.override(Ext.Container, {

    removeAll: function (autoDestroy) {
        var item, removed = [];
        while (this.items && ( item = this.items.last())) {
            removed.unshift(this.remove(item, autoDestroy));
        }
        if (!!removed.length) {
            this.doLayout();
        }
        return !!removed.length ? removed  //an array in original layout order
            : null;
    }

});


/**
 * use TreePanels setRootNode dynamically
 * @see http://www.extjs.com/forum/showthread.php?t=45885&highlight=tree+replace+root
 */
Ext.override(Ext.tree.TreePanel, {
    initComponent: function () {
        Ext.tree.TreePanel.superclass.initComponent.call(this);

        if (!this.eventModel) {
            this.eventModel = new Ext.tree.TreeEventModel(this);
        }

        // initialize the loader
        var l = this.loader;
        if (!l) {
            l = new Ext.tree.TreeLoader({
                dataUrl: this.dataUrl
            });
        } else if (typeof l == 'object' && !l.load) {
            l = new Ext.tree.TreeLoader(l);
        }
        this.loader = l;

        this.nodeHash = {};

        /**
         * The root node of this tree.
         * @type Ext.tree.TreeNode
         * @property root
         */
        // setRootNode destroys the existing root, so remove it first.
        if (this.root) {
            var r = this.root;
            delete this.root;
            this.setRootNode(r);
        }

        this.addEvents(

            /**
             * @event append
             * Fires when a new child node is appended to a node in this tree.
             * @param {Tree} tree The owner tree
             * @param {Node} parent The parent node
             * @param {Node} node The newly appended node
             * @param {Number} index The index of the newly appended node
             */
            "append",
            /**
             * @event remove
             * Fires when a child node is removed from a node in this tree.
             * @param {Tree} tree The owner tree
             * @param {Node} parent The parent node
             * @param {Node} node The child node removed
             */
            "remove",
            /**
             * @event movenode
             * Fires when a node is moved to a new location in the tree
             * @param {Tree} tree The owner tree
             * @param {Node} node The node moved
             * @param {Node} oldParent The old parent of this node
             * @param {Node} newParent The new parent of this node
             * @param {Number} index The index it was moved to
             */
            "movenode",
            /**
             * @event insert
             * Fires when a new child node is inserted in a node in this tree.
             * @param {Tree} tree The owner tree
             * @param {Node} parent The parent node
             * @param {Node} node The child node inserted
             * @param {Node} refNode The child node the node was inserted before
             */
            "insert",
            /**
             * @event beforeappend
             * Fires before a new child is appended to a node in this tree, return false to cancel the append.
             * @param {Tree} tree The owner tree
             * @param {Node} parent The parent node
             * @param {Node} node The child node to be appended
             */
            "beforeappend",
            /**
             * @event beforeremove
             * Fires before a child is removed from a node in this tree, return false to cancel the remove.
             * @param {Tree} tree The owner tree
             * @param {Node} parent The parent node
             * @param {Node} node The child node to be removed
             */
            "beforeremove",
            /**
             * @event beforemovenode
             * Fires before a node is moved to a new location in the tree. Return false to cancel the move.
             * @param {Tree} tree The owner tree
             * @param {Node} node The node being moved
             * @param {Node} oldParent The parent of the node
             * @param {Node} newParent The new parent the node is moving to
             * @param {Number} index The index it is being moved to
             */
            "beforemovenode",
            /**
             * @event beforeinsert
             * Fires before a new child is inserted in a node in this tree, return false to cancel the insert.
             * @param {Tree} tree The owner tree
             * @param {Node} parent The parent node
             * @param {Node} node The child node to be inserted
             * @param {Node} refNode The child node the node is being inserted before
             */
            "beforeinsert",

            /**
             * @event beforeload
             * Fires before a node is loaded, return false to cancel
             * @param {Node} node The node being loaded
             */
            "beforeload",
            /**
             * @event load
             * Fires when a node is loaded
             * @param {Node} node The node that was loaded
             */
            "load",
            /**
             * @event textchange
             * Fires when the text for a node is changed
             * @param {Node} node The node
             * @param {String} text The new text
             * @param {String} oldText The old text
             */
            "textchange",
            /**
             * @event beforeexpandnode
             * Fires before a node is expanded, return false to cancel.
             * @param {Node} node The node
             * @param {Boolean} deep
             * @param {Boolean} anim
             */
            "beforeexpandnode",
            /**
             * @event beforecollapsenode
             * Fires before a node is collapsed, return false to cancel.
             * @param {Node} node The node
             * @param {Boolean} deep
             * @param {Boolean} anim
             */
            "beforecollapsenode",
            /**
             * @event expandnode
             * Fires when a node is expanded
             * @param {Node} node The node
             */
            "expandnode",
            /**
             * @event disabledchange
             * Fires when the disabled status of a node changes
             * @param {Node} node The node
             * @param {Boolean} disabled
             */
            "disabledchange",
            /**
             * @event collapsenode
             * Fires when a node is collapsed
             * @param {Node} node The node
             */
            "collapsenode",
            /**
             * @event beforeclick
             * Fires before click processing on a node. Return false to cancel the default action.
             * @param {Node} node The node
             * @param {Ext.EventObject} e The event object
             */
            "beforeclick",
            /**
             * @event click
             * Fires when a node is clicked
             * @param {Node} node The node
             * @param {Ext.EventObject} e The event object
             */
            "click",
            /**
             * @event checkchange
             * Fires when a node with a checkbox's checked property changes
             * @param {Node} this This node
             * @param {Boolean} checked
             */
            "checkchange",
            /**
             * @event dblclick
             * Fires when a node is double clicked
             * @param {Node} node The node
             * @param {Ext.EventObject} e The event object
             */
            "dblclick",
            /**
             * @event contextmenu
             * Fires when a node is right clicked. To display a context menu in response to this
             * event, first create a Menu object (see {@link Ext.menu.Menu} for details), then add
             * a handler for this event:<code><pre>
             new Ext.tree.TreePanel({
    title: 'My TreePanel',
    root: new Ext.tree.AsyncTreeNode({
        text: 'The Root',
        children: [
            { text: 'Child node 1', leaf: true },
            { text: 'Child node 2', leaf: true }
        ]
    }),
    contextMenu: new Ext.menu.Menu({
        items: [{
            id: 'delete-node',
            text: 'Delete Node'
        }],
        listeners: {
            itemclick: function(item) {
                switch (item.id) {
                    case 'delete-node':
                        var n = item.parentMenu.contextNode;
                        if (n.parentNode) {
                            n.remove();
                        }
                        break;
                }
            }
        }
    }),
    listeners: {
        contextmenu: function(node, e) {
//          Register the context node with the menu so that a Menu Item's handler function can access
//          it via its {@link Ext.menu.BaseItem#parentMenu parentMenu} property.
             node.select();
             var c = node.getOwnerTree().contextMenu;
             c.contextNode = node;
             c.showAt(e.getXY());
             }
             }
             });
             </pre></code>
             * @param {Node} node The node
             * @param {Ext.EventObject} e The event object
             */
            "contextmenu",
            /**
             * @event beforechildrenrendered
             * Fires right before the child nodes for a node are rendered
             * @param {Node} node The node
             */
            "beforechildrenrendered",
            /**
             * @event startdrag
             * Fires when a node starts being dragged
             * @param {Ext.tree.TreePanel} this
             * @param {Ext.tree.TreeNode} node
             * @param {event} e The raw browser event
             */
            "startdrag",
            /**
             * @event enddrag
             * Fires when a drag operation is complete
             * @param {Ext.tree.TreePanel} this
             * @param {Ext.tree.TreeNode} node
             * @param {event} e The raw browser event
             */
            "enddrag",
            /**
             * @event dragdrop
             * Fires when a dragged node is dropped on a valid DD target
             * @param {Ext.tree.TreePanel} this
             * @param {Ext.tree.TreeNode} node
             * @param {DD} dd The dd it was dropped on
             * @param {event} e The raw browser event
             */
            "dragdrop",
            /**
             * @event beforenodedrop
             * Fires when a DD object is dropped on a node in this tree for preprocessing. Return false to cancel the drop. The dropEvent
             * passed to handlers has the following properties:<br />
             * <ul style="padding:5px;padding-left:16px;">
             * <li>tree - The TreePanel</li>
             * <li>target - The node being targeted for the drop</li>
             * <li>data - The drag data from the drag source</li>
             * <li>point - The point of the drop - append, above or below</li>
             * <li>source - The drag source</li>
             * <li>rawEvent - Raw mouse event</li>
             * <li>dropNode - Drop node(s) provided by the source <b>OR</b> you can supply node(s)
             * to be inserted by setting them on this object.</li>
             * <li>cancel - Set this to true to cancel the drop.</li>
             * <li>dropStatus - If the default drop action is cancelled but the drop is valid, setting this to true
             * will prevent the animated "repair" from appearing.</li>
             * </ul>
             * @param {Object} dropEvent
             */
            "beforenodedrop",
            /**
             * @event nodedrop
             * Fires after a DD object is dropped on a node in this tree. The dropEvent
             * passed to handlers has the following properties:<br />
             * <ul style="padding:5px;padding-left:16px;">
             * <li>tree - The TreePanel</li>
             * <li>target - The node being targeted for the drop</li>
             * <li>data - The drag data from the drag source</li>
             * <li>point - The point of the drop - append, above or below</li>
             * <li>source - The drag source</li>
             * <li>rawEvent - Raw mouse event</li>
             * <li>dropNode - Dropped node(s).</li>
             * </ul>
             * @param {Object} dropEvent
             */
            "nodedrop",
            /**
             * @event nodedragover
             * Fires when a tree node is being targeted for a drag drop, return false to signal drop not allowed. The dragOverEvent
             * passed to handlers has the following properties:<br />
             * <ul style="padding:5px;padding-left:16px;">
             * <li>tree - The TreePanel</li>
             * <li>target - The node being targeted for the drop</li>
             * <li>data - The drag data from the drag source</li>
             * <li>point - The point of the drop - append, above or below</li>
             * <li>source - The drag source</li>
             * <li>rawEvent - Raw mouse event</li>
             * <li>dropNode - Drop node(s) provided by the source.</li>
             * <li>cancel - Set this to true to signal drop not allowed.</li>
             * </ul>
             * @param {Object} dragOverEvent
             */
            "nodedragover"
        );
        if (this.singleExpand) {
            this.on("beforeexpandnode", this.restrictExpand, this);
        }
    },

    setRootNode: function (node) {
//      Already had one; destroy it.
        if (this.root) {
            this.root.destroy();
        }

        if (!node.render) { // attributes passed
            node = this.loader.createNode(node);
        }
        this.root = node;
        node.ownerTree = this;
        node.isRoot = true;
        this.registerNode(node);
        if (!this.rootVisible) {
            var uiP = node.attributes.uiProvider;
            node.ui = uiP ? new uiP(node) : new Ext.tree.RootTreeNodeUI(node);
        }

//      If we had previously rendered a tree, rerender it.
        if (this.innerCt) {
            this.innerCt.update('');
            this.afterRender();
        }
        return node;
    }
});

Ext.override(Ext.tree.TreeFilter, {
    filterBy: function (fn, scope, startNode) {
        startNode = startNode || this.tree.root;
        if (this.autoClear) {
            this.clear();
        }
        var af = this.filtered, rv = this.reverse;


        var f = function (n) {
            if (n == startNode) {
                return true;
            }
            if (af[n.id]) {
                return false;
            }
            var m = fn.call(scope || n, n);
            if (!m || rv) {
                af[n.id] = n;
                n.ui.hide();

                return true;            //As that, we don't stop cascade in this node.
            }
            else {
                //We show parent. :)
                n.ui.show();
                if (n.parentNode) {
                    n.parentNode.ui.show();
                }
                if (n.parentNode.parentNode) {
                    n.parentNode.parentNode.ui.show();
                }
                return true;
            }
            return true;
        };
        startNode.cascade(f);
        if (this.remove) {
            for (var id in af) {
                if (typeof id != "function") {
                    var n = af[id];
                    if (n && n.parentNode) {

                        n.parentNode.removeChild(n);
                    }
                }
            }
        }
    }
});

// see: http://www.extjs.com/forum/showthread.php?t=53240
Ext.override(Ext.tree.TreeNodeUI, {
    onDblClick: function (e) {
        e.preventDefault();
        if (this.disabled) {
            return;
        }
        if (this.fireEvent("dblclick", this.node, e) !== false) {
            if (this.checkbox) {
                this.toggleCheck();
            }
            if (!this.animating && this.node.isExpandable()) {
                this.node.toggle();
            }
        }
    }
});

// see: http://extjs.com/forum/showthread.php?t=74765
Ext.Element.prototype.contains = function (el) {
    if (el && Ext.isGecko && Object.prototype.toString.call(el) === '[object XULElement]') {
        return false;
    }
    if (!el) {
        return false;
    }
    try {
        return Ext.lib.Dom.isAncestor(this.dom, el.dom ? el.dom : el);
    } catch (e) {
        return false;
    }
};

// @see: http://www.extjs.com/forum/showthread.php?p=397772
Ext.override(Ext.Panel, {
    setIconClass: function (cls) {
        var old = this.iconCls;
        this.iconCls = cls;
        if (this.rendered && this.header) {
            if (this.frame) {
                this.header.addClass('x-panel-icon');
                this.header.replaceClass(old, this.iconCls);
            } else {
                var img = this.header.child('.x-panel-inline-icon');

                if (img) {
                    img.replaceClass(old, this.iconCls);
                } else {
                    var tool = this.header.last('.x-tool');

                    if (tool) {
                        Ext.DomHelper.insertAfter(tool, {
                            tag: 'img', src: Ext.BLANK_IMAGE_URL,
                            cls: 'x-panel-inline-icon ' + this.iconCls
                        });
                    }
                    else {
                        Ext.DomHelper.insertBefore(this.header.dom.firstChild, {
                            tag: 'img', src: Ext.BLANK_IMAGE_URL,
                            cls: 'x-panel-inline-icon ' + this.iconCls
                        });
                    }
                }
            }
        }
        else {
            var ct = this.ownerCt;
            if (ct && ct.isXType('tabpanel')) {
                // apply iconCls to tab strip text
                Ext.fly(ct.getTabEl(this)).child('.x-tab-strip-text').replaceClass(old, this.iconCls);
            }
        }
    }
});

// @see http://www.extjs.com/forum/showthread.php?t=62026
Ext.form.VTypes.email = function (v) {
    return /^([\w\-]+)(\.[\w\-]+)*@([\w\-]+\.){1,5}([A-Za-z]){2,6}$/.test(v);
}


Ext.Toolbar.TextItem.prototype.setText = function (t) {
    this.el.innerHTML = t;
};

/**
 * Sorting htmleditor fields with Ext.ux.Sortable is not possible,
 * because the html iframe is blanked after moving in dom.
 *
 * These extra functions can be used in 'startsort' and 'endsort'
 * callbacks to restore the html iframe value after sorting.
 */
Ext.override(Ext.form.HtmlEditor, {
    removeControl: function () {
        // sync current html value to textarea
        if (!this.sourceEditMode) {
            this.syncValue();
        }
    },

    restoreControl: function () {
        // remove listeners - see onDestroy()
        if (this.doc) {
            try {
                Ext.EventManager.removeAll(this.doc);
                for (var prop in this.doc) {
                    delete this.doc[prop];
                }
            } catch (e) {
            }
        }

        // remove iframe from dom
        Ext.get(this.iframe).remove();
        delete this.iframe;

        // create frame - see "onRender()"
        var iframe = document.createElement('iframe');
        iframe.name = Ext.id();
        iframe.frameBorder = '0';

        iframe.src = Ext.isIE ? Ext.SSL_SECURE_URL : "javascript:;";

        this.wrap.dom.appendChild(iframe);

        this.iframe = iframe;

        // initialize new iframe (this.doc, this.win, ...)
        this.initFrame();

        // set value to html editor
        this.pushValue();

        // fix iframe size
        var sz = this.el.getSize();
        this.setSize(sz.width, this.height || sz.height);

        // to hide new iframe - re-set sourceEditMode
        if (this.sourceEditMode) {
            this.toggleSourceEdit(this.sourceEditMode);
        }
    }
});


//@see http://www.sencha.com/learn/window-faq/
Ext.override(Ext.Window, {
    toFront: function (e) {
        if (this.manager.bringToFront(this)) {
            if (e && !e.getTarget().focus) {
                this.focus();
            }
        }
        return this;
    }
});
