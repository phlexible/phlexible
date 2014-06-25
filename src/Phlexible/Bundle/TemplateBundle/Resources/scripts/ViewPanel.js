Phlexible.templates.ViewPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.templates.Strings.view,
    cls: 'p-templates-view-panel',
    layout: 'fit',
    autoScroll: true,

    loadSrc: function(templateID, src) {
        this.templateID = templateID;

        if(this.body.first()) {
            this.body.first().remove();
        }

        this.pre = this.body.insertFirst({
             tag: 'pre',
             cls: 'brush: dwoo',
             html: src.replace('<', '&lt;')
        });

        SyntaxHighlighter.highlight({}, this.pre.dom);
    }
});

Ext.reg('templates-viewpanel', Phlexible.templates.ViewPanel);