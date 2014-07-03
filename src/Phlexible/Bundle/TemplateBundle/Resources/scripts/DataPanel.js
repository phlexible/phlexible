Phlexible.templates.DataPanel = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.templates.Strings.data,
    strings: Phlexible.templates.Strings.data,
    cls: 'p-templates-preview-panel',
    autoScroll: true,
    bodyStyle: 'padding: 5px',

    initComponent: function () {
        this.items = [
            {
                xtype: 'textfield',
                name: 'title',
                fieldLabel: this.strings.title,
                msgTarget: 'under',
                width: 300,
                allowBlank: false
            },
            {
                xtype: 'combo',
                hiddenName: 'type',
                fieldLabel: this.strings.type,
                msgTarget: 'under',
                width: 300,
                store: new Ext.data.SimpleStore({
                    data: [
                        ['full', this.strings.full],
                        ['part', this.strings.part]
                    ],
                    fields: ['key', 'value']
                }),
                displayField: 'value',
                valueField: 'key',
                mode: 'local',
                triggerAction: 'all',
                selectOnFocus: true,
                typeAhead: false,
                allowBlank: false,
                editable: false,
                value: 'full'
            },
            {
                xtype: 'combo',
                name: 'template',
                fieldLabel: this.strings.template,
                msgTarget: 'under',
                width: 300,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('templates_files'),
                    root: 'files',
                    fields: ['filename']
                }),
                displayField: 'filename',
                mode: 'remote',
                triggerAction: 'all',
                selectOnFocus: true,
                typeAhead: true,
                allowBlank: false
            }
        ];

        this.tbar = [
            {
                text: this.strings.save,
                handler: function () {
                    this.form.submit({
                        url: Phlexible.Router.generate('templates_update'),
                        params: {
                            id: this.template_id
                        }
                    });
                },
                scope: this
            }
        ];

        Phlexible.templates.DataPanel.superclass.initComponent.call(this);
    },

    loadData: function (template_id, settings) {
        this.template_id = template_id;
        this.form.setValues(settings);
    }
});

Ext.reg('templates-datapanel', Phlexible.templates.DataPanel);