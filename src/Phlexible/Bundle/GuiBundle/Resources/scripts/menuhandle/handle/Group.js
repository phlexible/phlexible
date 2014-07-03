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

                handler = new handlerCls();
                btns.push(handler.createConfig(menuItem));
            }, this);
        }

        return btns;
    }
});