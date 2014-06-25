Phlexible.gui.menuhandle.handle.BubbleMenu = Ext.extend(Phlexible.gui.menuhandle.handle.Menu, {
    createConfig: function(data) {
        if (data.menu && Ext.isArray(data.menu) && data.menu.length === 1) {
			var handlerCls = Phlexible.evalClassString(data.menu[0].xtype),
				handler;

			if (!handlerCls) {
				console.warn('Invalid handler classname', data.menu[0]);
				return;
			}

			handler = new handlerCls();

			if (data.menu[0].parameters) {
				handler.setParameters(data.menu[0].parameters);
			}

			return handler.createConfig(data.menu[0]);
		}

		return Phlexible.gui.menuhandle.handle.BubbleMenu.superclass.createConfig.call(this, data);
    }
});