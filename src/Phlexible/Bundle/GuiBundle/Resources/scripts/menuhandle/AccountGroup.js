Phlexible.gui.menuhandle.AccountGroup = Ext.extend(Phlexible.gui.menuhandle.handle.Menu, {
    iconCls: 'p-gui-account-icon',
    getText: function () {
        return Phlexible.User.getDisplayName();
    }
});
