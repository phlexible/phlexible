Phlexible.templates.TemplateTabPanel = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    layoutOnTabChange: true,
    deferredRender: false,
    initComponent: function() {

        this.items = [{
            xtype: 'templates-viewpanel'
        },{
            xtype: 'templates-editorpanel'
        }];

        this.disable();

        Phlexible.templates.TemplateTabPanel.superclass.initComponent.call(this);
    },

    load: function(r) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('templates_template'),
            params: {
                id: r.id
            },
            success: this.onSuccess,
            scope: this
        });
    },

    onSuccess: function(response) {
        this.enable();

        var data = Ext.decode(response.responseText);

        this.getComponent(0).loadSrc(data.id, data.content);
        this.getComponent(1).loadSrc(data.id, data.content);
    }

});

Ext.reg('templates-templatestabs', Phlexible.templates.TemplateTabPanel);