Phlexible.users.menuhandle.LogoutHandle = Ext.extend(Phlexible.gui.menuhandle.handle.FunctionHandle, {
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