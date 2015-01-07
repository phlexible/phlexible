Ext.provide('Phlexible.gui.menuhandle.handle.ComponentHandle');

Ext.require('Phlexible.gui.menuhandle.handle.Handle');

Phlexible.gui.menuhandle.handle.ComponentHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    createConfig: function (data) {
        return {
            xtype: this.componentXtype,
            width: 150
        };
    }
});