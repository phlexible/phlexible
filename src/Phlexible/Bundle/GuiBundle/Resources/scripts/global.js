Ext.require('Phlexible.gui.util.Console');
Phlexible.console = new Phlexible.gui.util.Console();

Ext.provide('Phlexible.Handles');
Ext.require('Phlexible.gui.util.Handles');
Phlexible.Handles = new Phlexible.gui.util.Handles();

Ext.provide('Phlexible.Cookie');
Ext.require('Phlexible.gui.util.Cookie');
Phlexible.Cookie = new Phlexible.gui.util.Cookie();

Ext.provide('Phlexible.globalKeyMap');
Phlexible.globalKeyMap = new Ext.KeyMap(document);

Phlexible.globalKeyMap.accessKey = function (key, handler, scope) {
    var h = function (keyCode, e) {
        if (Ext.isIE) {
            // IE6 doesn't allow cancellation of the F5 key,
            // so trick it into thinking some other key was pressed (backspace in this case)
            e.browserEvent.keyCode = 8;
        }
        e.preventDefault();
        handler.call(scope || this, keyCode, e);
        e.stopEvent();
        return false;
    };
    this.on(key, h, scope);
};

Ext.require('Phlexible.globalKeyMap');
Ext.require('Phlexible.gui.Actions');
Phlexible.globalKeyMap.accessKey({key: 'y', alt: true}, function () {
    Phlexible.gui.Actions.show();
});

Ext.provide('Phlexible.EntryManager');
Ext.require('Phlexible.gui.util.EntryManager');
Phlexible.EntryManager = new Phlexible.gui.util.EntryManager();

Ext.provide('Phlexible.PluginRegistry');
Ext.require('Phlexible.gui.util.PluginRegistry');
Phlexible.PluginRegistry = new Phlexible.gui.util.PluginRegistry();

Ext.provide('Phlexible.Router');
Ext.require('Phlexible.gui.util.Router');
Phlexible.Router = new Phlexible.gui.util.Router();
