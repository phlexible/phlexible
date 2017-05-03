Ext.provide('Phlexible.users.edit.Password');

Phlexible.users.edit.Password = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.password,
    iconCls: 'p-user-user_password-icon',
    border: false,
    bodyStyle: 'padding:5px',
    hideMode: 'offsets',
    defaultType: 'textfield',
    defaults: {
        msgTarget: 'under'
    },

    initComponent: function() {
        this.items = [{
            xtype: 'checkbox',
            boxLabel: this.strings.add_optin,
            hideLabel: true,
            checked: true,
            name: 'optin',
            border: false,
            disabled: this.mode !== 'add',
            hidden: this.mode !== 'add',
            listeners: {
                check: function(c, checked) {
                    this.getPasswordFormPanel().getComponent(2).setDisabled(checked);
                },
                scope: this
            }
        },{
            xtype: 'checkbox',
            boxLabel: this.strings.edit_optin,
            hideLabel: true,
            checked: false,
            name: 'optin',
            border: false,
            disabled: this.mode === 'add',
            hidden: this.mode === 'add',
            listeners: {
                check: function(c, checked) {
                    this.getPasswordFormPanel().getComponent(2).setDisabled(checked);
                },
                scope: this
            }
        },{
            xtype: 'fieldset',
            text: this.strings.password,
            title: this.strings.password,
            autoHeight: true,
            disabled: this.mode === 'add',
            items: [{
                xtype: 'passwordfield',
                name: 'password',
                hideLabel: true,
                inputType: "password",
                minLength: Phlexible.Config.get('system.password_min_length'),
                width: 200,
                showStrengthMeter: true,
                showCapsWarning: true
            },{
                xtype: 'panel',
                border: false,
                bodyStyle: 'padding-bottom: 10px;',
                html: this.strings.generate_password_text
            },{
                xtype: 'textfield',
                emptyText: this.strings.generated_password,
                hideLabel: true,
                readOnly: true,
                width: 200,
                append: [
                    {
                        xtype: 'button',
                        text: this.strings.generate,
                        iconCls: 'p-user-user_password-icon',
                        handler: function (btn) {
                            btn.setIconClass('x-tbar-loading');
                            btn.disable();
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('users_password'),
                                success: function (response) {
                                    var data = Ext.decode(response.responseText);

                                    var panel = this.getPasswordFormPanel();
                                    if (data.success) {
                                        panel.getComponent(2).getComponent(0).setValue(data.password);
                                        panel.getComponent(2).getComponent(2).setValue(data.password);
                                    }

                                    btn.setIconClass('p-user-user_password-icon');
                                    btn.enable();
                                },
                                scope: this
                            });
                        },
                        scope: this
                    }
                ]
            }]
        }];

        Phlexible.users.edit.Password.superclass.initComponent.call(this);
    },

    loadUser: function(user) {
        this.getForm().loadRecord(user);
    },

    isValid: function() {
        if (!this.getForm().isValid()) {
            Ext.MessageBox.alert('Validation failed', 'Check the password tab for details.');
            return false;
        }

        return true;
    },

    getData: function() {
        var values = this.getForm().getValues();

        return {
            password: values.password
        };
    }
});

Ext.reg('user_edit_password', Phlexible.users.edit.Password);

Phlexible.PluginRegistry.prepend('userEditPanels', {
    xtype: 'user_edit_password'
});
