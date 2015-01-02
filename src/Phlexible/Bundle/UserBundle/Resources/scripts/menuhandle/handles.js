Phlexible.Handles.add('logout', function() {
    return new Phlexible.gui.menuhandle.handle.FunctionHandle({
        text: Phlexible.users.Strings.logout,
        iconCls: 'p-user-logout-icon',
        component: function () {
            var close = Phlexible.Frame.checkClose();

            if (close !== false) {
                Ext.Msg.alert(close);
                return;
            }

            document.location.href = Phlexible.Router.generate('fos_user_security_logout');
        }
    });
});

Phlexible.Handles.add('options', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.users.Strings.options,
        iconCls: 'p-user-preferences-icon',
        component: 'Phlexible.users.OptionsWindow'
    });
});

Phlexible.Handles.add('users', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.users.Strings.users,
        iconCls: 'p-user-users-icon',
        component: 'users-main'
    });
});
