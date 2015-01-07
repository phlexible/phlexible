Ext.provide('Phlexible.gui.menuhandle.handle.SpacerHandle');

Ext.require('Phlexible.gui.menuhandle.handle.Handle');

Phlexible.gui.menuhandle.handle.SpacerHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    createConfig: function (data) {
        return ' ';
    }
});