Phlexible.users.options.Details = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.personal_details,
    bodyStyle: 'padding: 15px',
    border: false,
    hideMode: 'offsets',
    labelWidth: 150,
    defaultType: 'textfield',
    defaults:{
        msgTarget: 'under'
    },
    labelAlign: 'top',
    monitorValid: true,

    initComponent: function() {
        this.items = [{
            fieldLabel: this.strings.firstname,
            name: 'firstname',
            allowBlank: false,
            value: Phlexible.Config.get('user.details.firstname'),
            anchor: '100%'
        },{
            fieldLabel: this.strings.lastname,
            name: 'lastname',
            allowBlank: false,
            value: Phlexible.Config.get('user.details.lastname'),
            anchor: '100%'
        },{
            fieldLabel: this.strings.email,
            name: 'email',
            allowBlank: false,
            value: Phlexible.Config.get('user.details.email'),
            vtype: 'email',
            anchor: '100%'
        }];

        this.buttons = [{
            text: this.strings.save,
            handler: function() {
                this.form.submit({
                    url: Phlexible.Router.generate('users_options_savedetails'),
                    success: function(form, result) {
                        if (result.success) {
                            var values = form.getValues();
                            Phlexible.Config.set('user.details.firstname', values.firstname);
                            Phlexible.Config.set('user.details.lastname', values.lastname);
                            Phlexible.Config.set('user.details.email', values.email);

                            this.fireEvent('cancel');
                        } else {
                            Ext.Msg.alert('Failure', result.msg);
                        }
                    },
                    scope: this
                });
            },
            scope: this
        },{
            text: this.strings.cancel,
            formBind: true,
            handler: function() {
                this.fireEvent('cancel');
            },
            scope: this
        }];

        Phlexible.users.options.Details.superclass.initComponent.call(this);
    }
});

Ext.reg('usersoptionsdetails', Phlexible.users.options.Details);
