Phlexible.gui.menuhandle.handle.Group = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    createConfig: function (data) {
        var btns = [];

        if (data.menu && Ext.isArray(data.menu)) {
            Ext.each(data.menu, function (menuItem) {
                var handlerCls = Phlexible.evalClassString(menuItem.xtype),
                    handler;

                if (!handlerCls) {
                    console.error('Invalid handler classname', menuItem);
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

                handler = new handlerCls();

                if (menuItem.parameters) {
                    handler.setParameters(menuItem.parameters);
                }

                btns.push(handler.createConfig(menuItem));
            }, this);
        }

        return btns;
    }
});