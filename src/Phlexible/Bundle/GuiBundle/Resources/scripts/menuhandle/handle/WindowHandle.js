Phlexible.gui.menuhandle.handle.WindowHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    handle: function() {
        var component = Phlexible.evalClassString(this.getComponent()),
            parameters = {};

        if (typeof(component) === 'string') {
            component = Phlexible.evalClassString(component);
        }
        if (typeof(component) !== 'function') {
            throw Error('Not a function.');
        }

        Phlexible.console.debug('WindowHandle.handle(' + component + ')', parameters);

        var win = new component();
        win.show();
    }
});