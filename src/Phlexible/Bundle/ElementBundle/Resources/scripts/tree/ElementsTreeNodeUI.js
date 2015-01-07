Ext.provide('Phlexible.elements.ElementsTreeNodeUI');

Phlexible.elements.ElementsTreeNodeUI = Ext.extend(Ext.tree.TreeNodeUI, {
    // private
    renderElements: function (n, a, targetNode, bulkRender) {
        // add some indent caching, this helps performance when rendering a large tree
        this.indentMarkup = n.parentNode ? n.parentNode.ui.getChildIndent() : '';

        var cb = typeof a.checked == 'boolean';

        var text = n.text;

        text = this.applyNodeConfigToText(n, a, text);

        //if (a.icon) {
        //    a.icon = Phlexible.basePath + a.icon;
        //}

        var href = a.href ? a.href : Ext.isGecko ? "" : "#";
        var buf = ['<li class="x-tree-node"><div ext:tree-node-id="', n.id, '" class="x-tree-node-el x-tree-node-leaf x-unselectable ', a.cls, '" unselectable="on">',
            '<span class="x-tree-node-indent">', this.indentMarkup, "</span>",
            '<img src="', this.emptyIcon, '" class="x-tree-ec-icon x-tree-elbow" />',
            '<img src="', a.icon || this.emptyIcon, '" class="x-tree-node-icon', (a.icon ? " x-tree-node-inline-icon" : ""), (a.iconCls ? " " + a.iconCls : ""), '" unselectable="on" />',
            cb ? ('<input class="x-tree-node-cb" type="checkbox" ' + (a.checked ? 'checked="checked" />' : '/>')) : '',
            '<a hidefocus="on" class="x-tree-node-anchor" href="', href, '" tabIndex="1" ',
            a.hrefTarget ? ' target="' + a.hrefTarget + '"' : "", '><span unselectable="on">', text, "</span></a></div>",
            '<ul class="x-tree-node-ct" style="display:none;"></ul>',
            "</li>"].join('');

        var nel;
        if (bulkRender !== true && n.nextSibling && (nel = n.nextSibling.ui.getEl())) {
            this.wrap = Ext.DomHelper.insertHtml("beforeBegin", nel, buf);
        } else {
            this.wrap = Ext.DomHelper.insertHtml("beforeEnd", targetNode, buf);
        }

        this.elNode = this.wrap.childNodes[0];
        this.ctNode = this.wrap.childNodes[1];
        var cs = this.elNode.childNodes;
        this.indentNode = cs[0];
        this.ecNode = cs[1];
        this.iconNode = cs[2];
        var index = 3;
        if (cb) {
            this.checkbox = cs[3];
            // fix for IE6
            this.checkbox.defaultChecked = this.checkbox.checked;
            index++;
        }
        this.anchor = cs[index];
        this.textNode = cs[index].firstChild;
    },

    // private
    onTextChange: function (node, text, oldText) {
        text = this.applyNodeConfigToText(node, node.attributes, text);

        Phlexible.elements.ElementsTreeNodeUI.superclass.onTextChange.call(this, node, text, oldText);
    },

    applyNodeConfigToText: function (n, a, text) {
        if (a.restricted) {
            text = '[' + text + ']';
        }

        if (a.navigation) {
            text = '<span style="color: red; padding: 0;">»</span> ' + text;
        }

        if (a.allow_children === false) {
            text = text + ' <span style="color: blue; padding: 0;" qtip="' + Phlexible.elements.Strings.hide_children + '">(...)</span>';
        }

        text += ' [' + a.id + ']';

        return text;
    },

    // private
    onDblClick: function (e) {
        e.preventDefault();
        if (this.disabled) {
            return;
        }
        if (this.checkbox) {
            this.toggleCheck();
        }
        if (!this.animating && this.node.isExpandable() && !this.node.isExpanded()) {
            this.node.expand();
        }
        this.fireEvent("dblclick", this.node, e);
    }
});
