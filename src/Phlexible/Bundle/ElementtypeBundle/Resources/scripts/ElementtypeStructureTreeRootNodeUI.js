Phlexible.elementtypes.ElementtypeStructureRootTreeNodeUI = Ext.extend(Ext.tree.RootTreeNodeUI, {
    // private
    render: function () {
        if (!this.rendered) {
            var targetNode = this.node.ownerTree.innerCt.dom;
            //this.node.expanded = true;
            targetNode.innerHTML = '<div class="x-tree-root-node"></div>';
            this.wrap = this.ctNode = targetNode.firstChild;
        }
    }
});
