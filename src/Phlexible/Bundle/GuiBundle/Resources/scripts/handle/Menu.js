Ext.provide('Phlexible.gui.menuhandle.handle.Menu');

Ext.require('Phlexible.gui.menuhandle.handle.Handle');

Phlexible.gui.menuhandle.handle.Menu = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    menu: [],

    createConfig: function (data) {
        if (!data.menu || !Ext.isArray(data.menu)) {
            return null;
        }

        var config = this.createBasicConfig();

        if (data.menu && Ext.isArray(data.menu)) {
            subMenu = [];

            Ext.each(data.menu, function (menuItem) {
                var handleFactory, handler;

                if (!Phlexible.Handles.has(menuItem.handle)) {
                    Phlexible.console.error('Invalid handle in:', menuItem);
                    return;
                }

                if (menuItem.roles) {
                    var allowed = false;
                    Ext.each(menuItem.roles, function(role) {
                        if (Phlexible.User.isGranted(role)) {
                            allowed = true;
                            return false;
                        }
                    });
                    if (!allowed) {
                        return;
                    }
                }

                handleFactory = Phlexible.Handles.get(menuItem.handle);
                handler = handleFactory();

                if (menuItem.parameters) {
                    handler.setParameters(menuItem.parameters);
                }

                subMenu.push(handler.createConfig(menuItem));
            }, this);

            if (subMenu.length) {
                subMenu.sort(function(a,b) { return (a.text > b.text) - (b.text > a.text) } );
                config.menu = subMenu;
            } else {
                config.hidden = true;
            }
        }

        return config;
    }
});
