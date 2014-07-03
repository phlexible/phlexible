Ext.namespace("Ext.ux.plugins");

Ext.ux.plugins.ToggleCollapsible = {

    init: function (panel) {

        var renderCollapsible = function (panel, events, startPinned) {
            if (!startPinned & panel.collapsible) {
                // Pin/Unpin tools
                if (panel.header) {
                    panel.tools['pin'].hide();
                    var activeItem = panel.ownerCt.getLayout().activeItem;
                    if (activeItem && activeItem != panel) {
                        panel.collapse();
                    } else {
                        panel.tools['unpin'].show();
                    }
                }

                // Toggle tool
                if (!panel.hideCollapseTool) {
                    var tool = panel.tools['toggle'];
                    if (tool) {
                        tool.show();
                    }
                    else {
                        panel.addTool({
                            id: 'toggle',
                            handler: panel.toggleCollapse,
                            scope: panel
                        });
                    }
                }

                // Events
                if (events && panel.titleCollapse && panel.header) {
                    panel.header.on('click', panel.toggleCollapse, panel);
                    panel.header.setStyle('cursor', 'pointer');
                }
            }
            else {
                // Pin/Unpin tools
                panel.tools['pin'].show();
                panel.tools['unpin'].hide();

                // Toggle tool
                if (!panel.hideCollapseTool) {
                    panel.tools['toggle'].hide();
                }

                // Events
                if (events && panel.titleCollapse && panel.header) {
                    panel.header.un('click', panel.toggleCollapse, panel);
                    panel.header.setStyle('cursor', null);
                }
            }
        };

        var toggleCollapsible = function (panel) {
            panel.collapsible = !panel.collapsible;
            renderCollapsible(panel, true);
        };

        // Add new tools without overwriting existing ones
        if (!panel.tools) {
            panel.tools = [];
        }

        panel.tools.push({
            id: 'pin',
            hidden: true,
            handler: function () {
                toggleCollapsible(panel);
            }
        });
        panel.tools.push({
            id: 'unpin',
            hidden: true,
            handler: function () {
                toggleCollapsible(panel);
            }
        });

        panel.on('expand', function () {
            if (panel.collapsible) {
                panel.tools['pin'].hide();
                panel.tools['unpin'].show();
            }
            else {
                panel.tools['pin'].show();
                panel.tools['unpin'].hide();
            }
        });

        panel.on('collapse', function () {
            panel.tools['unpin'].hide();
            panel.tools['pin'].hide();
        });

        panel.on('render', function () {
            if (this.startPinned) {
                this.collapsible = false;
            }
            renderCollapsible(panel, false, this.startPinned);
        });
    }
};


// Allow more than one panel expanded
Ext.override(Ext.layout.Accordion, {
    beforeExpand: function (p) {
        var oldActive = this.activeItem;
        // The following is the only line changed from the original beforeExpand function.
        if (oldActive && oldActive.collapsible) {
            //if(oldActive){
            oldActive.collapse(this.animate);
        }
        this.activeItem = p;
        if (this.activeOnTop) {
            p.el.dom.parentNode.insertBefore(p.el.dom, p.el.dom.parentNode.firstChild);
        }
        this.layout();
    }
});
