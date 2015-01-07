Ext.provide('Phlexible.elements.FileLinkWindow');

Phlexible.elements.FileLinkWindow = Ext.extend(Ext.Window, {
    title: 'File Links',
    width: 700,
    height: 250,
    resizable: false,
    //iconCls: 'p-element-add-icon',

    initComponent: function () {
        /*
         Ext.Ajax.request({
         url: Phlexible.Router.generate('elements_search_filelinks'),
         success: function(response) {
         var data = Ext.decode(response.responseText);

         var form = this.getComponent(0);

         form.getComponent(0).setValue(data.download);
         form.getComponent(1).setValue(data.inline);
         form.getComponent(2).setValue(data.mimetype);
         },
         scope: this
         });
         */
        var prefix = document.location.protocol + '//' + document.location.hostname;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_search_medialink'),
            params: {
                file_id: this.file_id
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                this.getComponent(0).getForm().setValues(data);
                this.getComponent(0).enable();
            },
            scope: this
        });

        this.items = [
            {
                xtype: 'form',
                border: false,
                bodyStyle: 'padding: 5px',
                labelWidth: 120,
                disabled: true,
                items: [
                    {
                        xtype: 'textfield',
                        name: 'download',
                        fieldLabel: 'Download',
                        anchor: '-10',
                        readOnly: true
                    },
                    {
                        xtype: 'textfield',
                        name: 'inline',
                        fieldLabel: 'Inline',
                        anchor: '-10',
                        readOnly: true
                    },
                    {
                        xtype: 'textfield',
                        name: 'icon',
                        fieldLabel: 'Icon 16x16',
                        anchor: '-10',
                        readOnly: true
                    }
                ]
            }
        ];

        this.buttons = [
            {
                text: 'Close',
                handler: function () {
                    this.close();
                },
                scope: this
            }
        ];

        Phlexible.elements.FileLinkWindow.superclass.initComponent.call(this);
    }
});
