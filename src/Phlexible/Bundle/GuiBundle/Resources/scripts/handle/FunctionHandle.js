Ext.provide('Phlexible.gui.menuhandle.handle.FunctionHandle');

Ext.require('Phlexible.gui.menuhandle.handle.Handle');

Phlexible.gui.menuhandle.handle.FunctionHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    handle: function () {
        var component = this.getComponent();

        component();
    }
});