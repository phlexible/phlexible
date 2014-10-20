Phlexible.teasers.ElementLayoutTreeNodeUI = Ext.extend(Ext.tree.TreeNodeUI, {
    // private
    renderElements: function (n, a, targetNode, bulkRender) {
        Phlexible.teasers.ElementLayoutTreeNodeUI.superclass.renderElements.call(this, n, a, targetNode, bulkRender);
        this.onTextChange(n, n.text);
    },

    // private
    onTextChange: function (node, text, oldText) {
        text = this.applyNodeConfigToText(node, text);
        Phlexible.teasers.ElementLayoutTreeNodeUI.superclass.onTextChange.call(this, node, text, oldText);
    },

    applyNodeConfigToText: function (n, text) {
        /*
        var prefix = '';
        if (n.attributes.inherited) {
            prefix += '<img qtip="Is inherited" src="' + Phlexible.bundleAsset('/phlexibleteaser/icons/inherited.png') + '" width="16" height="16" style="vertical-align: middle;" />';
        }
        if (n.attributes.inherit !== undefined && !n.attributes.inherit) {
            prefix += '<img qtip="Inherit stopped" src="' + Phlexible.bundleAsset('/phlexibleteaser/icons/stop_inherit.png') + '" width="16" height="16" style="vertical-align: middle;" />';
        }
        if (prefix) {
            text += ' ' + prefix;
        }
        */
        return text;
    }
});
