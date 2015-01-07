/*jsl:ignoreall*/
Ext.namespace('Ext.ux');

Ext.ux.InlineToolbarTabPanel = function (config) {
    Ext.apply(this, config);
    this.id = this.getId();
    this.config = config;
    this.tbarsWidth = 0;
    Ext.ux.InlineToolbarTabPanel.superclass.constructor.call(this, config);
};

// plugin code
Ext.extend(Ext.ux.InlineToolbarTabPanel, Ext.TabPanel, {

    getScrollArea: function () {
        this.tbarsWidth = 0
        for (var i = 0; i < this.config.toolbars.length; i++) {
            this.tbarsWidth += this.config.toolbars[i].width;
        }
        var newScrollArea = this.el.dom.clientWidth - this.tbarsWidth;
        return parseInt(newScrollArea, 10) || 0;
    },

    afterRender: function () {
        Ext.ux.InlineToolbarTabPanel.superclass.afterRender.call(this);
        this.renderToolbar();
    },

    onResize: function (w, h) {
        Ext.ux.InlineToolbarTabPanel.superclass.onResize.call(this, w, h);
        this.renderToolbar();
    },

    autoSizeTabs: function () {
        Ext.ux.InlineToolbarTabPanel.superclass.autoSizeTabs.call(this);
        this.renderToolbar();
    },

    autoScrollTabs: function () {
        Ext.ux.InlineToolbarTabPanel.superclass.autoScrollTabs.call(this);
        this.renderToolbar();
    },

    getToolbar: function () {
        return this.toolbar.getTopToolbar();
    },

    renderToolbar: function () {
        for (var i = 0; i < this.config.toolbars.length; i++) {
            var tbar = this.config.toolbars[i];
            var toolbarDiv = tbar.id || this.id + 'Toolbar' + i;
            var toolbarAlign = tbar.align || 'right';
            if (!document.getElementById(toolbarDiv)) {
                if (toolbarAlign.toLowerCase() != 'left' && toolbarAlign.toLowerCase() != 'right') {
                    Ext.DomHelper.insertBefore(this[this.stripTarget], '<div id="' + toolbarDiv + '" class="x-tab-toolbar-wrap" style="height:26px;overflow:hidden;border-left:1px solid #8DB2E3;border-bottom:1px solid #8DB2E3;margin-left:0px;"></div>');
                }
                else {
                    Ext.DomHelper.insertBefore(this[this.stripTarget], '<div id="' + toolbarDiv + '" class="x-tab-toolbar-wrap" style="float:' + toolbarAlign + ';height:26px;overflow:hidden;border-left:1px solid #8DB2E3;border-bottom:1px solid #8DB2E3;margin-left:0px;"></div>');
                }
                this.toolbar = new Ext.Panel({
                    renderTo: toolbarDiv,
                    border: false,
                    tbar: tbar.tbar
                });
            }
            toolbarExt = Ext.get(toolbarDiv);
            toolbarExt.setWidth(tbar.width || 150);
            toolbarExt.setHeight(26);
        }
        this[this.stripTarget].setHeight(26);
        if (Ext.isIE) {
            if (!this.scrolling) {
                this.strip.setWidth(this.getScrollArea() - 2);
            }
            else {
            }
            this[this.stripTarget].setWidth(this.getScrollArea());
        }
        else {
            this[this.stripTarget].setWidth('auto');
        }
    }
});
Ext.reg('inlinetoolbartabpanel', Ext.ux.InlineToolbarTabPanel)