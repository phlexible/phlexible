Ext.provide('Phlexible.contentchannels.ContentchannelForm');

Phlexible.contentchannels.ContentchannelForm = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.contentchannels.Strings,
    title: Phlexible.contentchannels.Strings.serverForm,
    bodyStyle: 'padding: 5px;',
    disabled: true,
    defaults: {
        msgTarget: 'under'
    },
    labelWidth: 140,

    initComponent: function () {
        this.items = [
            {
                name: 'title',
                xtype: 'textfield',
                width: 200,
                fieldLabel: this.strings.title,
                allowBlank: false
            },
            {
                name: 'unique_id',
                xtype: 'textfield',
                width: 200,
                fieldLabel: this.strings.unique_id
            },
            {
                name: 'renderer_classname',
                xtype: 'textfield',
                width: 200,
                fieldLabel: this.strings.renderer_classname,
                allowBlank: false
            },
            {
                name: 'icon',
                xtype: 'textfield',
                width: 200,
                fieldLabel: this.strings.icon,
                allowBlank: false
            },
            {
                name: 'template_folder',
                xtype: 'textfield',
                width: 200,
                fieldLabel: this.strings.template_folder,
                allowBlank: false
            },
            {
                name: 'comment',
                xtype: 'textarea',
                width: 200,
                fieldLabel: this.strings.comment
            }
        ];

        this.tbar = [
            {
                text: this.strings.save,
                iconCls: 'p-contentchannel-save-icon',
                handler: this.save,
                scope: this
            }
        ];

        Phlexible.contentchannels.ContentchannelForm.superclass.initComponent.call(this);
    },

    save: function () {
        if (!this.getForm().isValid()) {
            Ext.MessageBox.alert(this.strings.error, this.strings.saveError);
            return;
        }

        this.getForm().submit({
            url: Phlexible.Router.generate('contentchannels_save'),
            params: {
                id: this.record.id
            },
            success: this.saveSuccess,
            failure: this.saveFailure,
            scope: this
        });
    },

    saveSuccess: function (form, action) {
        this.fireEvent('contentchannel_save');
    },

    saveFailure: function (form, action) {
        Ext.MessageBox.alert('Failure', action.result.msg);
    },

    loadData: function (record) {
        this.record = record;

        this.enable();

        this.getForm().loadRecord(record);
    }
});

Ext.reg('contentchannels-contentchannelsform', Phlexible.contentchannels.ContentchannelForm);

