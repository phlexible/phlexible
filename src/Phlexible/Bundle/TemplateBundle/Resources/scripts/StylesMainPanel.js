Phlexible.templates.StylesMainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.templates.Strings.styles,
    layout: 'border',
    iconCls: 'p-template-component-icon',

    initComponent: function() {
        this.templatesGrid = new Phlexible.templates.TemplatesGrid({
            region: 'west',
            width: 300,
            listeners: {
                rowclick: {
                    fn: function(grid, index) {
                        this.templateTabPanel.load(grid.store.getAt(index));
                    },
                    scope: this
                }
            }
        });

        this.templateTabPanel = new Phlexible.templates.TemplateTabPanel({
            region: 'center'
        });

        this.items = [
            this.templatesGrid,
            this.templateTabPanel
        ];

        Phlexible.templates.MainPanel.superclass.initComponent.call(this);
    }
});
