Ext.provide('Phlexible.elements.ElementsTreeLoader');

Phlexible.elements.ElementsTreeLoader = Ext.extend(Ext.tree.TreeLoader, {
    uiProvider: Ext.tree.TreeNodeUI,

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
        /*
         if(typeof attr.uiProvider == 'string'){
         attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
         }
         */
        attr.uiProvider = this.uiProvider;

        return new Ext.tree.AsyncTreeNode(attr);
    },

    xdoPreload: function (node) {
        if (node.attributes.children) {
            if (node.childNodes.length < 1) { // preloaded?
                var cs = node.attributes.children;
                node.beginUpdate();
                for (var i = 0, len = cs.length; i < len; i++) {
                    var cn = node.appendChild(this.createNode(cs[i]));
                    if (this.preloadChildren) {
                        this.doPreload(cn);
                    }
                }
                node.endUpdate();
            }
            return true;
        } else {
            return false;
        }
    }
});
