Ext.namespace('Phlexible.security');

Phlexible.security.ValidateWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.security.Strings,
    modal: false,
    closable: false,
    collapsible: false,
    draggable: false,
    resizable: false,
    border: false,
    shadow: true,
    width: 420,
    height: 360,
    layout: 'border',
    cls: 'p-security-validate-window',

    initComponent: function () {

        this.items = [
            {
                region: 'north',
                height: 150,
                frame: false,
                border: true,
                style: 'text-align: center;',
                html: '<img src="' + Phlexible.componentsPath + '/phlexiblegui/images/logo.gif" width="300" height="120" style="padding-top: 15px" />'
            },
            {
                region: 'center',
                xtype: 'form',
                //url: Phlexible.baseUrl + '/security/reset/send',
                bodyStyle: 'padding: 10px;',
                labelWidth: 100,
                frame: true,
                monitorValid: true,
                items: [
                    {
                        frame: false,
                        border: false,
                        bodyStyle: 'padding-bottom: 20px; text-align: center;',
                        html: this.strings.enter_email
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%',
                        fieldLabel: this.strings.email,
                        name: 'email',
                        msgTarget: 'under',
                        allowBlank: false,
                        vtype: 'email'
                    },
                    {
                        html: this.strings.validation_ok,
                        style: 'padding-top: 10px;',
                        hidden: true
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
                text: this.strings.back_to_login,
                handler: function () {
                    document.location.href = this.loginUrl;
                },
                scope: this
            },
            {
                text: this.strings.validate,
                disabled: true,
                formBind: true,
//            iconCls: 'p-auth-login-icon',
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
                this.getComponent(1).getComponent(1).focus(false, 10);
            },
            move: function () {
                this.getComponent(1).getComponent(1).focus(false, 10);
            },
            resize: function () {
                this.getComponent(1).getComponent(1).focus(false, 10);
            },
            scope: this
        });

        Phlexible.security.ValidateWindow.superclass.initComponent.call(this);
    },

    submit: function () {
        this.getComponent(1).form.submit({
            url: this.validateUrl,
            reset: false,
            success: function (form, action) {
                this.buttons[0].hide();
                this.getComponent(1).getComponent(1).disable();
                this.getComponent(1).getComponent(2).show();
            },
            failure: function (form, e) {
                if (e.failureType == 'server') {
                    if (e.result.data.msg) {
                        Ext.Msg.alert(this.strings.login_failure, e.result.data.msg);
                    }
                } else {
                    Ext.Msg.alert(this.strings.login_failure, this.strings.error_login_failed);
                }
            },
            scope: this
        });
    }

});
