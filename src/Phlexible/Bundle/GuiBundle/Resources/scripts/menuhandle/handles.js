Phlexible.Handles.add('main', function() {
    return new Phlexible.gui.menuhandle.handle.Group();
});

Phlexible.Handles.add('menus', function() {
    return new Phlexible.gui.menuhandle.handle.Group();
});

Phlexible.Handles.add('tray', function() {
    return new Phlexible.gui.menuhandle.handle.Group();
});

Phlexible.Handles.add('account', function() {
    return new Phlexible.gui.menuhandle.handle.Menu({
        iconCls: 'p-gui-account-icon',
        getText: function () {
            return Phlexible.User.getDisplayName();
        }
    });
});

Phlexible.Handles.add('administration', function() {
    return new Phlexible.gui.menuhandle.handle.Menu({
        text: Phlexible.gui.Strings.administration,
        iconCls: 'p-gui-menu_admin-icon'
    });
});

Phlexible.Handles.add('configuration', function() {
    return new Phlexible.gui.menuhandle.handle.Menu({
        text: Phlexible.gui.Strings.configuration,
        iconCls: 'p-gui-menu_config-icon'
    });
});

Phlexible.Handles.add('debug', function() {
    return new Phlexible.gui.menuhandle.handle.Menu({
        text: Phlexible.gui.Strings.debug,
        iconCls: 'p-gui-menu_debug-icon'
    });
});

Phlexible.Handles.add('tools', function() {
    return new Phlexible.gui.menuhandle.handle.Menu({
        text: '_tools',
        iconCls: 'p-gui-menu_tools-icon'
    });
});

Phlexible.Handles.add('fill', function() {
    return new Phlexible.gui.menuhandle.handle.Handle({
        createConfig: function (data) {
            return '->';
        }
    });
});

Phlexible.Handles.add('separator', function() {
    return new Phlexible.gui.menuhandle.handle.Handle({
        createConfig: function (data) {
            return '|';
        }
    });
});

Phlexible.Handles.add('spacer', function() {
    return new Phlexible.gui.menuhandle.handle.Handle({
        createConfig: function (data) {
            return ' ';
        }
    });
});

Phlexible.Handles.add('bundles', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.gui.Strings.bundles,
        iconCls: 'p-gui-manager-icon',
        component: 'gui-bundles'
    });
});

Phlexible.Handles.add('help', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.gui.Strings.help,
        iconCls: 'p-gui-help-icon',
        component: 'gui-help'
    });
});

Phlexible.Handles.add('properties', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.gui.Strings.properties,
        iconCls: 'p-gui-php-icon',
        component: ''
    });
});

Phlexible.Handles.add('phpinfo', function() {
    return new Phlexible.gui.menuhandle.handle.WindowHandle({
        text: 'PHP Info',
        iconCls: 'p-gui-php-icon',
        component: 'Phlexible.gui.PhpInfoWindow'
    });
});
