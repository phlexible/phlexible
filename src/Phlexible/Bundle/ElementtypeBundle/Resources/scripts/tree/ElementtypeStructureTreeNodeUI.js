Ext.provide('Phlexible.elementtypes.ElementtypeStructureTreeNodeUI');

Phlexible.elementtypes.ElementtypeStructureTreeNodeUI = Ext.extend(Ext.tree.TreeNodeUI, {
    // private
    renderElements: function (n, a, targetNode, bulkRender) {
        Phlexible.elementtypes.ElementtypeStructureTreeNodeUI.superclass.renderElements.call(this, n, a, targetNode, bulkRender);
        this.onTextChange(n, n.text);
    },

    // private
    onTextChange: function (node, text, oldText) {
        text = this.applyNodeConfigToText(node, text);
        Phlexible.elementtypes.ElementtypeStructureTreeNodeUI.superclass.onTextChange.call(this, node, text, oldText);
    },

    applyNodeConfigToText: function (n, text) {
        if (n.attributes.type == 'group' || n.attributes.type == 'accordion' && n.attributes.properties && n.attributes.properties.configuration) {
            var isOptional = false,
                isRepeatable = false,
                isSingleLine = false;

            if (n.attributes.type == 'group') {
                var minRepeat = n.attributes.properties.configuration.repeat_min ? (parseInt(n.attributes.properties.configuration.repeat_min, 10) || 0) : 1;
                var maxRepeat = n.attributes.properties.configuration.repeat_max ? (parseInt(n.attributes.properties.configuration.repeat_max, 10) || 0) : 1;

                isOptional = !minRepeat;
                isRepeatable = minRepeat != maxRepeat && maxRepeat > 1 ? true : false;
                isSingleLine = (n.attributes.properties.configuration.group_single_line ? true : false);
            }

            var isSortable = (n.attributes.properties.configuration.sortable ? true : false);

            if (isOptional || isRepeatable || isSingleLine || isSortable) {
                text += ' ';
            }

            if (isOptional) {
                text += '<img qtip="Is optional" src="' + Phlexible.bundleAsset('/phlexibleelementtype/images/is_optional.png') + '" width="15" height="15" style="vertical-align: middle;" />';
            }
            if (isRepeatable) {
                text += '<img qtip="Is repeatable" src="' + Phlexible.bundleAsset('/phlexibleelementtype/images/is_repeatable.png') + '" width="15" height="15" style="vertical-align: middle;" />';
            }
            if (isSingleLine) {
                text += '<img qtip="Is single line" src="' + Phlexible.bundleAsset('/phlexibleelementtype/images/is_singleline.png') + '" width="15" height="15" style="vertical-align: middle;" />';
            }
            if (isSortable) {
                text += '<img qtip="Children are sortable" src="' + Phlexible.bundleAsset('/phlexibleelementtype/images/is_sortable.png') + '" width="15" height="15" style="vertical-align: middle;" />';
            }
        }
        else {
            if (n.attributes.properties && n.attributes.properties.configuration) {
                var isSynchronized = (n.attributes.properties.configuration['synchronized'] == 'synchronized' || n.attributes.properties.configuration['synchronized'] == 'synchronized_unlink' ? true : false);

                if (isSynchronized) {
                    text += ' ';
                }

                if (isSynchronized) {
                    text += '<img qtip="Is synchronized" src="' + Phlexible.bundleAsset('/phlexibleelementtype/images/is_synchronized.png') + '" width="15" height="15" style="vertical-align: middle;" />';
                }
            }
        }

        return text;
    }
});
