Ext.namespace(
	'Phlexible.users.menuhandle',
	'Phlexible.users.model',
	'Phlexible.users.options'
);

Phlexible.EntryManager.register('users', function(params) {
    return {
        handler: Phlexible.LoadHandler.handlePanel,
        identifier: 'Phlexible_users_MainPanel',
        handleTarget: Phlexible.users.MainPanel,
        params: params
    };
});

Phlexible.PluginRegistry.prepend('userOptionCards', {
    xtype: 'usersoptionstheme',
    title: Phlexible.users.Strings.theme,
    description: Phlexible.users.Strings.theme_description,
    iconCls: 'p-user-theme-icon'
});
Phlexible.PluginRegistry.prepend('userOptionCards', {
    xtype: 'usersoptionspreferences',
    title: Phlexible.users.Strings.preferences,
    description: Phlexible.users.Strings.preferences_description,
    iconCls: 'p-user-preferences-icon'
});
Phlexible.PluginRegistry.prepend('userOptionCards', {
    xtype: 'usersoptionspassword',
    title: Phlexible.users.Strings.password,
    description: Phlexible.users.Strings.password_description,
    iconCls: 'p-user-user_password-icon'
});
Phlexible.PluginRegistry.prepend('userOptionCards', {
    xtype: 'usersoptionsdetails',
    title: Phlexible.users.Strings.personal_details,
    description: Phlexible.users.Strings.personal_details_description,
    iconCls: 'p-user-user-icon'
});

