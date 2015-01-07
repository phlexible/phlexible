Ext.provide('Phlexible.gui.menuhandle.handle.BubbleMenu');

Ext.require('Phlexible.gui.menuhandle.handle.Menu');

Phlexible.gui.menuhandle.handle.BubbleMenu = Ext.extend(Phlexible.gui.menuhandle.handle.Menu, {
    createConfig: function (data) {
        if (data.menu && Ext.isArray(data.menu) && data.menu.length === 1) {
            var handleFactory, handler;

            if (!Phlexible.Handles.has(data.menu[0].handle)) {
                console.error('Invalid handle in:', data.menu[0]);
                return;
            }

            handleFactory = Phlexible.Handles.get(data.menu[0].handle);
            handler = handleFactory();

            if (data.menu[0].parameters) {
                handler.setParameters(data.menu[0].parameters);
            }

            return handler.createConfig(data.menu[0]);
        }

        return Phlexible.gui.menuhandle.handle.BubbleMenu.superclass.createConfig.call(this, data);
    }
});