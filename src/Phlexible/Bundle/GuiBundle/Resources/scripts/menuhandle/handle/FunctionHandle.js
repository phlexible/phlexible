Phlexible.gui.menuhandle.handle.FunctionHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    handle: function () {
        var component = this.getComponent();

        component();
    }
});