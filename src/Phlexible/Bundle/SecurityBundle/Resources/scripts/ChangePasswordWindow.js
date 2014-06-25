Ext.namespace('Phlexible.users');

Phlexible.users.ChangePasswordWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.users.Strings,
    modal: false,
    closable: false,
    collapsible: false,
    draggable: false,
    resizable: false,
    border: false,
    shadow: true,
    width: 450,
    height: 400,
    layout: 'border',
    cls: 'p-users-changepassword-window',

    initComponent: function() {

        this.items = [{
            region: 'north',
            height: 150,
            frame: false,
            border: true,
            style: 'text-align: center;',
            html: '<img src="' + Phlexible.baseUrl + '/resources/app/logo" width="300" height="120" style="padding-top: 15px" />'
        },{
            region: 'center',
            xtype: 'form',
            url: Phlexible.baseUrl + '/users/changepassword/set',
            bodyStyle: 'padding: 10px;',
            labelWidth: 150,
            frame: true,
            monitorValid: true,
            items: [{
                frame: false,
                border: false,
                bodyStyle: 'padding-bottom: 20px; text-align: center;',
                html: this.strings.enter_password
            },{
                xtype: 'textfield',
                width: 150,
                fieldLabel: this.strings.current_password,
                inputType: 'password',
                name: 'current_password',
                msgTarget: 'under',
                allowBlank: false,
                blankText: this.strings.error_enter_password
            },{
                xtype: 'textfield',
                width: 150,
                fieldLabel: this.strings.new_password,
                inputType: 'password',
                name: 'new_password',
                msgTarget: 'under',
                minLength: Phlexible.Config.get('system.password_min_length'),
                allowBlank: false,
                blankText: this.strings.error_enter_password
            },{
                xtype: 'textfield',
                width: 150,
                fieldLabel: this.strings.new_password_repeat,
                inputType: 'password',
                name: 'new_password_repeat',
                msgTarget: 'under',
                minLength: Phlexible.Config.get('system.password_min_length'),
                allowBlank: false,
                blankText: this.strings.enter_password,
                invalidText: this.strings.passwords_dont_match,
                validator: function() {
                    var p = this.getComponent(1);
                    return p.getComponent(2).getValue() === p.getComponent(3).getValue();
                }.createDelegate(this)
            }],
            bindHandler : function(){
                var valid = true;
                this.form.items.each(function(f){
                    if(!f.isValid(true)){
                        valid = false;
                        return false;
                    }
                });
                if(this.ownerCt.buttons){
                    for(var i = 0, len = this.ownerCt.buttons.length; i < len; i++){
                        var btn = this.ownerCt.buttons[i];
                        if(btn.formBind === true && btn.disabled === valid){
                            btn.setDisabled(!valid);
                        }
                    }
                }
                this.fireEvent('clientvalidation', this, valid);
            }
        }];

        this.buttons = [{
            text: this.strings.set,
            disabled: true,
            formBind: true,
//            iconCls: 'p-auth-login-icon',
            handler: this.submit,
            scope: this
        }];

        this.on('render', function(){
            var keyMap = this.getKeyMap();
            keyMap.addBinding({
                key: Ext.EventObject.ENTER,
                fn: this.submit,
                scope: this
            });
        }, this);

        this.on('show', function() {
            this.getComponent(1).getComponent(1).focus(false, 10);
        }, this);

        this.on('move', function() {
            this.getComponent(1).getComponent(1).focus(false, 10);
        }, this);

        this.on('resize', function() {
            this.getComponent(1).getComponent(1).focus(false, 10);
        }, this);

        Phlexible.core.users.ChangePasswordWindow.superclass.initComponent.call(this);
    },

    submit: function() {
        this.getComponent(1).form.submit({
            url: Phlexible.baseUrl + '/users/changepassword/save',
            reset: false,
            success: function(form, action) {
                if (action.result.data.target) {
                    document.location.href = action.result.data.target;
                }
                else {
                    document.location.href = Phlexible.baseUrl;
                }
            },
            failure: function(form, e) {
                if (e.failureType == 'server') {
                    if(e.result.data.msg) {
                        Ext.Msg.alert(this.strings.login_failure, e.result.data.msg);
                    }
                } else {
                    Ext.Msg.alert(this.strings.login_failure, this.strings.error_login_failed);
                }
            },
            scope: this.loginForm
        });
    }

});
