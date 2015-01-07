Ext.provide('Phlexible.mediamanager.FolderTreeNodeUI');

Phlexible.mediamanager.FolderTreeNodeUI = Ext.extend(Ext.tree.TreeNodeUI, {

    // private
    renderElements: function (n, a, targetNode, bulkRender) {

        //prefix = '';

        if (a.used & 8) {
            if (!a.cls) a.cls = '';
            a.cls += ' x-tree-node-green';
            //prefix += '<img src="'+Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_green.gif')+'" width="8" height="12" style="vertical-align: middle;" />';
        }
        if (a.used & 4) {
            if (!a.cls) a.cls = '';
            a.cls += ' x-tree-node-orange';
            //prefix += '<img src="'+Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_yellow.gif')+'" width="8" height="12" style="vertical-align: middle;" />';
        }
        if (a.used & 2) {
            if (!a.cls) a.cls = '';
            a.cls += ' x-tree-node-black';
            //prefix += '<img src="'+Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_gray.gif')+'" width="8" height="12" style="vertical-align: middle;" />';
        }
        if (a.used & 1) {
            if (!a.cls) a.cls = '';
            a.cls += ' x-tree-node-gray';
            //prefix += '<img src="'+Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_black.gif')+'" width="8" height="12" style="vertical-align: middle;" />';
        }

        //if (prefix) {
        //    prefix += '&nbsp;';
        //}

        //n.text = prefix + n.text;

        return Phlexible.mediamanager.FolderTreeNodeUI.superclass.renderElements.call(this, n, a, targetNode, bulkRender);
    }

});
