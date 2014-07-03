Ext.ns('Phlexible.mediamanager.options');

Phlexible.mediamanager.options.MediaSettings = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.mediamanager.Strings.mediasettings,
    strings: Phlexible.mediamanager.Strings,
    xtype: 'form',
    bodyStyle: 'padding: 15px',
    border: false,
    autoScroll: true,

    initComponent: function () {
        this.items = [
            {
                xtype: 'checkbox',
                hideLabel: true,
                boxLabel: this.strings.disable_flash_uploader,
                name: 'disable_flash',
                checked: Phlexible.Config.get('mediamanager.upload.disable_flash'),
                helpText: this.strings.disable_flash_uploader_help
            },
            {
                xtype: 'checkbox',
                hideLabel: true,
                boxLabel: this.strings.enable_upload_sort,
                name: 'enable_upload_sort',
                checked: Phlexible.Config.get('mediamanager.upload.enable_upload_sort'),
                helpText: this.strings.enable_upload_sort_help
            }
        ];

        this.buttons = [
            {
                text: this.strings.save,
                handler: function () {
                    this.form.submit({
                        url: Phlexible.Router.generate('mediamanager_options_savemedia'),
                        success: function (form, result) {
                            var data = result.result;
                            if (data.success) {
                                var values = form.getValues();
                                Phlexible.Config.set('mediamanager.upload.disable_flash', values.disable_flash);
                                this.fireEvent('cancel');
                            }
                            else {
                                Ext.MessageBox.alert('Failure', data.msg);
                            }
                        },
                        scope: this
                    });
                },
                scope: this
            },
            {
                text: this.strings.cancel,
                handler: function () {
                    this.fireEvent('cancel');
                },
                scope: this
            }
        ];

        Phlexible.mediamanager.options.MediaSettings.superclass.initComponent.call(this);
    }
});

Ext.reg('mediamanagermediasettings', Phlexible.mediamanager.options.MediaSettings);

Phlexible.PluginRegistry.append('userOptionCards', {
    xtype: 'mediamanagermediasettings',
    title: Phlexible.mediamanager.Strings.mediasettings,
    description: Phlexible.mediamanager.Strings.mediasettings_description,
    iconCls: 'p-mediamanager-component-icon'
});
