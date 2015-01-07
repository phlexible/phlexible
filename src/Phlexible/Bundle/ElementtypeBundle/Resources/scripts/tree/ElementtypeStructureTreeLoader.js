Ext.provide('Phlexible.elementtypes.ElementtypeStructureTreeLoader');

Phlexible.elementtypes.ElementtypeStructureTreeLoader = Ext.extend(Ext.tree.TreeLoader, {
    createNode: function (attr) {
        // apply baseAttrs, nice idea Corey!
        if (this.baseAttrs) {
            Ext.applyIf(attr, this.baseAttrs);
        }
        if (this.applyLoader !== false) {
            attr.loader = this;
        }
        attr.uiProvider = Phlexible.elementtypes.ElementtypeStructureTreeNodeUI;
        if (attr.nodeType) {
            return new Ext.tree.TreePanel.nodeTypes[attr.nodeType](attr);
        } else {
            return attr.leaf ?
                new Ext.tree.TreeNode(attr) :
                new Ext.tree.AsyncTreeNode(attr);
        }
    }
});
