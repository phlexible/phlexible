Phlexible.mediatemplates.pdf2swf.FormPanel = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.mediatemplates.Strings.pdf2swf_template,
    strings: Phlexible.mediatemplates.Strings,
//    labelWidth: 80,
    labelAlign: 'top',
    disabled: true,
    layout: 'accordion',

    debugPreview: false,

    initComponent: function () {
        this.items = [
            {
                xtype: 'panel',
                layout: 'form',
                title: this.strings.pdf2swf,
                iconCls: 'p-mediatemplate-type_pdf-icon',
                bodyStyle: 'padding: 5px',
                border: false,
                autoScroll: true,
                items: [
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'quality',
                        fieldLabel: this.strings.quality,
                        helpText: this.strings.help_quality,
                        minValue: 1,
                        maxValue: 100
                    },
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'flash_version',
                        fieldLabel: this.strings.flash_version,
                        helpText: this.strings.help_flash_version,
                        minValue: 0,
                        maxValue: 100
                    }
                ]
            }
        ];

        this.tbar = [
            {
                text: this.strings.save,
                iconCls: 'p-mediatemplate-save-icon',
                handler: this.saveParameters,
                scope: this
            },
            '->',
            {
                xtype: 'tbsplit',
                text: this.strings.preview,
                iconCls: 'p-mediatemplate-preview-icon',
                handler: function () {
                    var values = this.getForm().getValues();

                    if (values.method) {
                        values.xmethod = values.method;
                        delete values.xmethod;
                    }
                    values.template = this.template_key;
                    values.debug = this.debugPreview;

                    this.fireEvent('preview', values, this.debugPreview);
                },
                scope: this,
                menu: [
                    {
                        text: this.strings.debug,
                        checked: this.debugPreview,
                        checkHandler: function (checkItem, checked) {
                            this.debugPreview = checked;
                        },
                        scope: this
                    }
                ]
            }
        ];

        this.on('clientvalidation', function (f, valid) {
            this.getTopToolbar().items.items[0].setDisabled(!valid);
        }, this);

        Phlexible.mediatemplates.pdf2swf.FormPanel.superclass.initComponent.call(this);
    },

    loadParameters: function (template_key) {
        this.disable();
        this.template_key = template_key;

        this.getForm().reset();
        this.getForm().load({
            url: Phlexible.Router.generate('mediatemplates_form_load'),
            params: {
                template_key: template_key
            },
            success: function (form, data) {
                this.enable();

                this.fireEvent('paramsload');
            },
            scope: this
        });

    },

    saveParameters: function () {
        this.getForm().submit({
            url: Phlexible.Router.generate('mediatemplates_form_save'),
            params: {
                template_key: this.template_key
            },
            success: function (form, action) {
                var data = Ext.decode(action.response.responseText);
                if (data.success) {
                    Phlexible.success(data.msg);
                    this.fireEvent('paramssave');
                }
                else {
                    Ext.Msg.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    }
});

Ext.reg('mediatemplates-pdf2swfformpanel', Phlexible.mediatemplates.pdf2swf.FormPanel);