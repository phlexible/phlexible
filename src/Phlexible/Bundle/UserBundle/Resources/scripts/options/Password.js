Ext.provide('Phlexible.users.options.Password');

Ext.require('Phlexible.PluginRegistry');

Phlexible.users.options.Password = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.password,
    bodyStyle: 'padding: 15px',
    border: true,
    hideMode: 'offsets',
    labelWidth: 150,
    defaultType: 'textfield',
    defaults: {
        msgTarget: 'under'
    },
    labelAlign: 'top',
    monitorValid: true,
    header: false,

    initComponent: function () {
        this.items = [
            {
                xtype: 'passwordfield',
                fieldLabel: this.strings.password,
                name: 'password',
                //inputType: 'password',
//                anchor: '100%',
                width: 200,
                value: '',
                minLength: Phlexible.Config.get('system.password_min_length'),
                showStrengthMeter: true,
                showCapsWarning: true,
                invalidText: this.strings.passwords_dont_match,
                validator: function () {
                    return this.getComponent(0).getValue() === this.getComponent(1).getValue();
                }.createDelegate(this)
            },
            {
                fieldLabel: this.strings.password_repeat,
                name: 'password_repeat',
                inputType: 'password',
//                anchor: '100%',
                width: 200,
                minLength: Phlexible.Config.get('system.password_min_length'),
                listeners: {
                    valid: function (f) {
                        f.ownerCt.getComponent(0).validate();
                    },
                    scope: this
                }
            }
        ];

        this.buttons = [
            {
                text: this.strings.save,
                formBind: true,
                handler: function () {
                    this.form.submit({
                        url: Phlexible.Router.generate('users_options_savepassword'),
                        success: function (form, result) {
                            if (result.success) {
                                this.fireEvent('back');
                            } else {
                                Ext.Msg.alert('Failure', result.msg);
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
                    this.fireEvent('back');
                },
                scope: this
            }
        ];

        Phlexible.users.options.Password.superclass.initComponent.call(this);
    }
});

Ext.reg('usersoptionspassword', Phlexible.users.options.Password);

Phlexible.PluginRegistry.append('userOptionCards', {
    xtype: 'usersoptionspassword',
    title: Phlexible.users.Strings.password,
    description: Phlexible.users.Strings.password_description,
    iconCls: 'p-user-user_password-icon',
    available: function() {
        return !Phlexible.Config.get('user.property.noPasswordChange', false);
    }
});