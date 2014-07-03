Phlexible.gui.menuhandle.handle.Menu = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    menu: [],

    createConfig: function (data) {
        if (!data.menu || !Ext.isArray(data.menu)) {
            return null;
        }

        var config = this.createBasicConfig();

        if (data.menu && Ext.isArray(data.menu)) {
            config.menu = [];

            Ext.each(data.menu, function (menuItem) {
                var handlerCls = Phlexible.evalClassString(menuItem.xtype),
                    handler;

                if (!handlerCls) {
                    console.warn('Invalid handler classname', menuItem);
                    return;
                }


                handler = new handlerCls();

                if (menuItem.parameters) {
                    handler.setParameters(menuItem.parameters);
                }

                config.menu.push(handler.createConfig(menuItem));
            }, this);
        }

        return config;
    }
});