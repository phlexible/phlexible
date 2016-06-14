Ext.namespace('Phlexible.users.security');

Phlexible.users.security.ResetWindow = Ext.extend(Ext.Window, {
    modal: false,
    closable: false,
    collapsible: false,
    draggable: false,
    resizable: false,
    border: false,
    shadow: true,
    width: 450,
    height: 380,
    layout: 'border',
    cls: 'p-security-newpassword-window',

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
                labelWidth: 150,
                frame: true,
                monitorValid: true,
                standardSubmit: true,
                url: this.setUrl,
                listeners: {
                    render: function (c) {
                        c.form.el.dom.action = this.setUrl;
                    },
                    scope: this
                },
                items: [
                    {
                        frame: false,
                        border: false,
                        bodyStyle: 'padding-bottom: 20px; text-align: center;',
                        html: this.enterNewPasswordText
                    },
                    {
                        xtype: 'hidden',
                        name: 'fos_user_resetting_form[_token]',
                        value: this.token
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%',
                        fieldLabel: this.newPasswordText,
                        labelSeparator: '',
                        inputType: 'password',
                        name: 'fos_user_resetting_form[plainPassword][first]',
                        msgTarget: 'under',
                        //minLength: this.minLength,
                        allowBlank: false
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%',
                        fieldLabel: this.newPasswordConfirmationText,
                        labelSeparator: '',
                        inputType: 'password',
                        name: 'fos_user_resetting_form[plainPassword][second]',
                        msgTarget: 'under',
                        //minLength: this.minLength,
                        allowBlank: false,
                        validator: function () {
                            var p = this.getComponent(1);
                            return p.getComponent(2).getValue() === p.getComponent(3).getValue();
                        }.createDelegate(this)
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

        Phlexible.users.security.ResetWindow.superclass.initComponent.call(this);
    },

    submit: function () {
        var form = this.getComponent(1).getForm();
        if (!form.isValid()) return;
        form.submit();
    }
});
