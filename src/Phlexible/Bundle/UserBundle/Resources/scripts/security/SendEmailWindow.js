Ext.namespace('Phlexible.users.security');

Phlexible.users.security.SendEmailWindow = Ext.extend(Ext.Window, {
    modal: false,
    closable: false,
    collapsible: false,
    draggable: false,
    resizable: false,
    border: false,
    shadow: true,
    width: 420,
    height: 320,
    layout: 'border',

    initComponent: function () {

        this.items = [
            {
                region: 'north',
                height: 150,
                frame: false,
                border: true,
                style: 'text-align: center;',
                html: '<img src="' + Phlexible.bundlePath + '/phlexiblegui/images/logo.gif" width="300" height="120" style="padding-top: 15px" />'
            },
            {
                region: 'center',
                xtype: 'form',
                bodyStyle: 'padding: 10px;',
                labelWidth: 160,
                frame: true,
                monitorValid: true,
                standardSubmit: true,
                url: this.sendEmailUrl,
                listeners: {
                    render: function (c) {
                        c.form.el.dom.action = this.sendEmailUrl;
                    },
                    scope: this
                },
                items: [
                    {
                        frame: false,
                        border: false,
                        bodyStyle: 'padding-bottom: 10px; text-align: center;',
                        html: this.usernameText,
                        hidden: this.checkEmail || this.passwordAlreadyRequested,
                        disabled: this.checkEmail || this.passwordAlreadyRequested
                    },
                    {
                        frame: false,
                        border: false,
                        bodyStyle: 'padding-bottom: 10px; text-align: center; color: red;',
                        html: this.errorMessage || '',
                        hidden: !this.invalidUsername,
                        disabled: !this.invalidUsername
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%',
                        fieldLabel: this.usernameText,
                        labelSeparator: '',
                        name: 'username',
                        msgTarget: 'under',
                        allowBlank: false,
                        value: this.invalidUsername || '',
                        hidden: this.checkEmail || this.passwordAlreadyRequested,
                        disabled: this.checkEmail || this.passwordAlreadyRequested
                    },
                    {
                        html: this.checkEmailText,
                        style: 'padding-top: 10px;',
                        hidden: !this.checkEmail
                    },
                    {
                        html: this.passwordAlreadyRequestedText,
                        style: 'padding-top: 10px;',
                        hidden: !this.passwordAlreadyRequested
                    }
                ],
                bindHandler: function () {
                    var valid = true;
                    this.form.items.each(function (f) {
                        if (!f.isValid(true)) {
                            valid = false;
                            return false;
                        }
                    });
                    if (this.ownerCt.buttons) {
                        for (var i = 0, len = this.ownerCt.buttons.length; i < len; i++) {
                            var btn = this.ownerCt.buttons[i];
                            if (btn.formBind === true && btn.disabled === valid) {
                                btn.setDisabled(!valid);
                            }
                        }
                    }
                    this.fireEvent('clientvalidation', this, valid);
                }
            }
        ];

        this.buttons = [
            {
                text: this.backToLoginText,
                iconCls: 'p-user-login-icon',
                handler: function () {
                    document.location.href = this.loginUrl;
                },
                scope: this
            },
            {
                text: this.submitText,
                disabled: true,
                hidden: this.checkEmail || this.passwordAlreadyRequested,
                formBind: true,
                handler: this.submit,
                scope: this
            }
        ];

        this.on({
            render: function () {
                var keyMap = this.getKeyMap();
                keyMap.addBinding({
                    key: Ext.EventObject.ENTER,
                    fn: this.submit,
                    scope: this
                });
            },
            show: function () {
                this.getComponent(1).getComponent(2).focus(false, 10);
            },
            move: function () {
                this.getComponent(1).getComponent(2).focus(false, 10);
            },
            resize: function () {
                this.getComponent(1).getComponent(2).focus(false, 10);
            },
            scope: this
        });

        Phlexible.users.security.SendEmailWindow.superclass.initComponent.call(this);
    },

    submit: function () {
        var form = this.getComponent(1).getForm();
        if (!form.isValid()) return;
        form.submit();
    }
});
