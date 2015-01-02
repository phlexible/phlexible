Ext.ns('Phlexible.gui.menuhandle.handle');

Phlexible.gui.menuhandle.handle.Group = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    createConfig: function (data) {
        var btns = [];

        if (data.menu && Ext.isArray(data.menu)) {
            Ext.each(data.menu, function (menuItem) {
                var handleFactory, handler;

                if (!Phlexible.Handles.has(menuItem.handle)) {
                    console.error('Invalid handle in:', menuItem);
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

                btns.push(handler.createConfig(menuItem));
            }, this);
        }

        return btns;
    }
});