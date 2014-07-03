Ext.namespace('Phlexible.security', 'Ext.ux');

Ext.ux.PasswordField = function (config) {
    // call parent constructor
    Ext.ux.PasswordField.superclass.constructor.call(this, config);

    this.showCapsWarning = config.showCapsWarning || true;

};

Ext.extend(Ext.ux.PasswordField, Ext.form.TextField, {
    /**
     * @cfg {String} inputType The type attribute for input fields -- e.g. text, password (defaults to "password").
     */
    inputType: 'password',

    capslockText: 'Caps Lock is on.',

    // private
    onRender: function (ct, position) {
        Ext.ux.PasswordField.superclass.onRender.call(this, ct, position);

        // create caps lock warning box
        var id = Ext.id();
        this.alertBox = Ext.DomHelper.append(document.body, {
            tag: 'div',
            style: 'width: 10em; z-index: 10000;',
            children: [
                {
                    tag: 'div',
                    style: 'text-align: center; color: red;',
                    html: this.capslockText,
                    id: id
                }
            ]
        }, true);
        Ext.fly(id).boxWrap();
        this.alertBox.hide();
    },
    initEvents: function () {
        Ext.ux.PasswordField.superclass.initEvents.call(this);

        this.el.on('keypress', this.keypress, this);
    },
    keypress: function (e) {
        var charCode = e.getCharCode();
        if (
            (e.shiftKey && charCode >= 97 && charCode <= 122) ||
                (!e.shiftKey && charCode >= 65 && charCode <= 90)
            ) {
            if (this.showCapsWarning) {
                this.showWarning(e.target);
            }
        } else {
            this.hideWarning();
        }
    },
    showWarning: function (el) {
        this.alertBox.alignTo(el, 'l-r', [5, 0]);
        this.alertBox.show();
    },
    hideWarning: function () {
        this.alertBox.hide();
    }
});
Ext.reg('passwordfield', Ext.ux.PasswordField);

Phlexible.security.LoginWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.security.Strings,
    modal: false,
    closable: false,
    collapsible: false,
    draggable: false,
    resizable: false,
    border: false,
    shadow: true,
    width: 420,
    height: 380,
    layout: 'border',
    cls: 'p-security-login-window',

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
                bodyStyle: 'padding: 10px;',
                labelWidth: 100,
                frame: true,
                monitorValid: true,
                url: this.checkPath,
                standardSubmit: true,
                listeners: {
                    render: function (c) {
                        c.form.el.dom.action = this.checkPath;
                    },
                    scope: this
                },
                items: [
                    {
                        frame: false,
                        border: false,
                        bodyStyle: 'padding-bottom: 10px; text-align: center;',
                        html: this.strings.enter_username_password
                    },
                    {
                        frame: false,
                        border: false,
                        bodyStyle: 'padding-bottom: 10px; text-align: center; color: red;',
                        html: this.errorMessage || ''
                    },
                    {
                        xtype: 'hidden',
                        name: '_csrf_token',
                        value: this.csrfToken
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%',
                        fieldLabel: this.strings.username,
                        name: '_username',
                        msgTarget: 'under',
                        allowBlank: false,
                        blankText: this.strings.error_enter_username,
                        value: this.lastUsername || ''
                    },
                    {
                        xtype: 'passwordfield',
                        anchor: '100%',
                        fieldLabel: this.strings.password,
                        name: '_password',
                        msgTarget: 'under',
                        allowBlank: false,
                        blankText: this.strings.error_enter_password,
                        capslockText: this.strings.capslock
                    },
                    {
                        bodyStyle: 'text-align: right;',
                        html: '<a id="newpassword" href="#">' + this.strings.lost_password + '</a>'
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
                text: this.strings.login,
                formBind: true,
                iconCls: 'p-security-login-icon',
                handler: this.submit,
                scope: this
            }
        ];

        this.on('render', function (c) {
            var keyMap = this.getKeyMap();
            keyMap.addBinding({
                key: Ext.EventObject.ENTER,
                fn: this.submit,
                scope: this
            });

            var b = this.body;
            b.on('mousedown', this.doAction, this, {delegate: 'a'});
            b.on('click', Ext.emptyFn, null, {delegate: 'a', preventDefault: true});
        }, this);

        this.on('show', function () {
            var c;
            if (this.getUsernameField().getValue()) {
                c = this.getPasswordField();
            } else {
                c = this.getUsernameField();
            }
            c.focus(false, 10);
        }, this);

        this.on('move', function () {
            var c;
            if (this.getUsernameField().getValue()) {
                c = this.getPasswordField();
            } else {
                c = this.getUsernameField();
            }
            c.focus(false, 10);
        }, this);

        this.on('resize', function () {
            var c;
            if (this.getUsernameField().getValue()) {
                c = this.getPasswordField();
            } else {
                c = this.getUsernameField();
            }
            c.focus(false, 10);
        }, this);

        Phlexible.security.LoginWindow.superclass.initComponent.call(this);
    },

    getUsernameField: function () {
        return this.getComponent(1).getComponent(3);
    },

    getPasswordField: function () {
        return this.getComponent(1).getComponent(4);
    },

    doAction: function (e, t) {
        if (t.id == 'newpassword') {
            document.location.href = this.resetUrl;
        }
    },

    submit: function () {
        var form = this.getComponent(1).getForm();
        if (!form.isValid()) return;
        form.submit({
            params: {
                csrfToken: this.csrfToken
            },
            scope: this,
            waitTitle: this.strings.please_wait,
            waitMsg: this.strings.logging_in
        });
    }
});
