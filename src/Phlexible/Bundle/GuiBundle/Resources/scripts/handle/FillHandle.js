Ext.provide('Phlexible.gui.menuhandle.handle.FillHandle');

Ext.require('Phlexible.gui.menuhandle.handle.Handle');

Phlexible.gui.menuhandle.handle.FillHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    createConfig: function (data) {
        return '->';
    }
});