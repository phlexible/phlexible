Phlexible.templates.TemplateUsagePanel = Ext.extend(Ext.Panel, {
    title: Phlexible.templates.Strings.usage,
    html: '123',

    loadData: function (templateID, data) {
        this.templateID = templateID;
        this.getEl().update(data);
    }
});
