Ext.provide('Phlexible.users.options.Details');

Ext.require('Phlexible.PluginRegistry');

Phlexible.users.options.Details = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.personal_details,
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
                fieldLabel: this.strings.firstname,
                name: 'firstname',
                allowBlank: false,
                value: Phlexible.User.getFirstname(),
                anchor: '100%'
            },
            {
                fieldLabel: this.strings.lastname,
                name: 'lastname',
                allowBlank: false,
                value: Phlexible.User.getLastname(),
                anchor: '100%'
            },
            {
                fieldLabel: this.strings.email,
                name: 'email',
                allowBlank: false,
                value: Phlexible.User.getEmail(),
                vtype: 'email',
                anchor: '100%'
            }
        ];

        this.buttons = [
            {
                text: this.strings.save,
                handler: function () {
                    this.form.submit({
                        url: Phlexible.Router.generate('users_options_savedetails'),
                        success: function (form, result) {
                            if (result.success) {
                                var values = form.getValues();
                                Phlexible.User.setFirstname(values.firstname);
                                Phlexible.User.setLastname(values.lastname);
                                Phlexible.User.setEmail(values.email);

                                this.fireEvent('save');
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
                formBind: true,
                handler: function () {
                    this.fireEvent('cancel');
                },
                scope: this
            }
        ];

        Phlexible.users.options.Details.superclass.initComponent.call(this);
    }
});

Ext.reg('usersoptionsdetails', Phlexible.users.options.Details);

Phlexible.PluginRegistry.append('userOptionCards', {
    xtype: 'usersoptionsdetails',
    title: Phlexible.users.Strings.personal_details,
    description: Phlexible.users.Strings.personal_details_description,
    iconCls: 'p-user-user-icon'
});
