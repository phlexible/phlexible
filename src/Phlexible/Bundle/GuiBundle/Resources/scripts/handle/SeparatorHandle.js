Ext.provide('Phlexible.gui.menuhandle.handle.SeparatorHandle');

Ext.require('Phlexible.gui.menuhandle.handle.Handle');

Phlexible.gui.menuhandle.handle.SeparatorHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    createConfig: function (data) {
        return '-';
    }
});