Phlexible.gui.menuhandle.AccountGroup = Ext.extend(Phlexible.gui.menuhandle.handle.Menu, {
    iconCls: 'p-gui-account-icon',
    getText: function () {
        return Phlexible.Config.get('user.details.firstname') && Phlexible.Config.get('user.details.lastname') ? Phlexible.Config.get('user.details.firstname') + ' ' + Phlexible.Config.get('user.details.lastname') : Phlexible.Config.get('user.details.username');
    }
});
