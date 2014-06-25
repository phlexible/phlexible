Phlexible.templates.EditorPanel = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.templates.Strings.editor,
    layout: 'fit',
    defaultType: 'textarea',

    initComponent: function() {
        this.items = [{
            //xtype: 'textarea',
            hideLabel: true,
            name: 'blubb',
            readonly: true,
            style: 'font-family: Courier, "Courier New", monospace; font-size: 10px;'
        }];

        this.tbar = [{
            text: 'Save'
        }];

        Phlexible.templates.EditorPanel.superclass.initComponent.call(this);
    },

    loadSrc: function(templateID, src) {
        this.templateID = templateID;
        this.getComponent(0).setValue(src);
    }
});

Ext.reg('templates-editorpanel', Phlexible.templates.EditorPanel);