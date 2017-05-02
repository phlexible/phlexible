Ext.provide('Phlexible.users.edit.Account');

Phlexible.users.edit.Account = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.account,
    iconCls: 'p-user-user_account-icon',
    border: false,
    xtype: 'form',
    bodyStyle: 'padding:5px',
    labelWidth: 130,
    labelAlign: 'top',

    initComponent: function() {
        this.items = [{
            xtype: 'checkbox',
            boxLabel: this.strings.enabled,
            hideLabel: true,
            name: 'enabled'
        },{
            xtype: 'checkbox',
            boxLabel: this.strings.cant_change_password,
            hideLabel: true,
            name: 'noPasswordChange'
        }];

        Phlexible.users.edit.Account.superclass.initComponent.call(this);
    },

    loadUser: function(user) {
        var properties = {
            enabled: user.get('enabled')
        };
        Ext.apply(properties, user.get('properties'));

        this.getForm().setValues(properties);
    },

    isValid: function() {
        if (!this.getForm().isValid()) {
            Ext.MessageBox.alert('Validation failed', 'Check the account tab for details.');
            return false;
        }

        return true;
    },

    getData: function() {
        var values = this.getForm().getValues(),
            data = {
                enabled: values.enabled ? 1 : 0
            };

        data["property_noPasswordChange"] = values.noPasswordChange ? 1 : 0;

        return data;
    }
});

Ext.reg('user_edit_account', Phlexible.users.edit.Account);

Phlexible.PluginRegistry.prepend('userEditPanels', {
    xtype: 'user_edit_account'
});
