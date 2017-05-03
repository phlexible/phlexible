Ext.provide('Phlexible.users.edit.Details');

Phlexible.users.edit.Details = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.personal_details,
    iconCls: 'p-user-user_detail-icon',
    border: false,
    bodyStyle: 'padding:5px',
    labelWidth: 130,
    labelAlign: 'top',
    defaultType: 'textfield',
    defaults: {
        msgTarget: 'under'
    },

    initComponent: function() {
        this.items = [{
            name: 'username',
            fieldLabel: this.strings.username,
            anchor: '100%',
            allowBlank: false
        },{
            name: 'firstname',
            fieldLabel: this.strings.firstname,
            anchor: '100%',
            allowBlank: false
        },{
            name: 'lastname',
            fieldLabel: this.strings.lastname,
            anchor: '100%',
            allowBlank: false
        },{
            name: 'email',
            fieldLabel: this.strings.email,
            anchor: '100%',
            allowBlank: false,
            vtype: 'email'
        }];

        Phlexible.users.edit.Details.superclass.initComponent.call(this);
    },

    loadUser: function(user) {
        this.getForm().loadRecord(user);
    },

    isValid: function() {
        if (!this.getForm().isValid()) {
            Ext.MessageBox.alert('Validation failed', 'Check the detail tab for details.');
            return false;
        }

        return true;
    },

    getData: function() {
        return this.getForm().getValues();
    }
});

Ext.reg('user_edit_details', Phlexible.users.edit.Details);

Phlexible.PluginRegistry.prepend('userEditPanels', {
    xtype: 'user_edit_details'
});
