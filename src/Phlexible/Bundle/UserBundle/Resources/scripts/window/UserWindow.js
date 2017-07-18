Ext.provide('Phlexible.users.UserWindow');

Ext.require('Ext.ux.TabPanel');

Phlexible.users.UserWindow = Ext.extend(Ext.Window, {
    title: Phlexible.users.Strings.user,
    strings: Phlexible.users.Strings,
    plain: true,
    iconCls: 'p-user-user-icon',
    width: 530,
    minWidth: 530,
    height: 400,
    minHeight: 400,
    layout: 'fit',
    border: false,
    modal: true,

    initComponent: function () {
        this.addEvents(
            'save'
        );

        var panels = Phlexible.PluginRegistry.get('userEditPanels');

        this.items = [{
            xtype: 'uxtabpanel',
            tabPosition: 'left',
            tabStripWidth: 150,
            activeTab: 0,
            border: true,
            deferredRender: false,
            items: panels
        }];

        this.tbar = new Ext.Toolbar({
            hidden: true,
            cls: 'p-users-disabled',
            items: [
                '->',
                {
                    iconCls: 'p-user-user_account-icon',
                    text: this.strings.account_is_disabled,
                    handler: function () {
                        this.getComponent(0).setActiveTab(4);
                    },
                    scope: this
                }]
        });

        this.buttons = [
            {
                text: this.strings.cancel,
                handler: this.close,
                scope: this
            },
            {
                text: this.strings.save,
                iconCls: 'p-user-save-icon',
                handler: this.save,
                scope: this
            }
        ];

        Phlexible.users.UserWindow.superclass.initComponent.call(this);
    },

    show: function (user) {
        this.user = user;

        if (user.get('username')) {
            this.setTitle(this.strings.user + ' "' + user.get('username') + '"');
        } else {
            this.setTitle(this.strings.new_user);
        }

        Phlexible.users.UserWindow.superclass.show.call(this);

        this.getComponent(0).items.each(function(p) {
            if (typeof p.loadUser === 'function') {
                p.loadUser(user);
            }
        });

        if (!user.get('enabled')) {
            this.getTopToolbar().show();
        }
    },

    save: function () {
        var data = {};
        var valid = true;

        this.getComponent(0).items.each(function(p) {
            if (typeof p.isValid === 'function' && typeof p.getData === 'function') {
                if (p.isValid()) {
                    Ext.apply(data, p.getData());
                } else {
                    valid = false;
                }
            }
        });

        if (!valid) {
            return;
        }

        var url, method;
        if (this.user.get('uid')) {
            url = Phlexible.Router.generate('users_users_update', {userId: this.user.get('uid')});
            method = 'PUT';
        } else {
            url = Phlexible.Router.generate('users_users_create');
            method = 'POST';
        }

        Ext.Ajax.request({
            url: url,
            method: method,
            params: data,
            success: this.onSaveSuccess,
            scope: this
        });
    },

    onSaveSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.uid = data.uid;
            Phlexible.success(data.msg);
            this.fireEvent('save', this.uid);
            this.close();
        } else {
            Ext.Msg.alert('Failure', data.msg);
        }

    }
});
