Phlexible.teasers.ElementLayoutTreeLoader = Ext.extend(Ext.tree.TreeLoader, {
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
        attr.uiProvider = this.uiProvider || Phlexible.teasers.ElementLayoutTreeNodeUI;

        return new Ext.tree.AsyncTreeNode(attr);
    }
});
