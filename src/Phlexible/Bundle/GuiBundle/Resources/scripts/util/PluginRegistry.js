Ext.ns('Phlexible.gui.util');

Phlexible.gui.util.PluginRegistry = function () {
    this.plugins = {};
};
Phlexible.gui.util.PluginRegistry.prototype.addObject = function (plugin, key, value) {
    if (!this.plugins[plugin]) {
        this.plugins[plugin] = {};
    }
    this.plugins[plugin][key] = value;
};
Phlexible.gui.util.PluginRegistry.prototype.append = function (plugin, value) {
    if (!this.plugins[plugin]) {
        this.plugins[plugin] = [];
    }
    this.plugins[plugin].push(value);
};
Phlexible.gui.util.PluginRegistry.prototype.prepend = function (plugin, value) {
    if (!this.plugins[plugin]) {
        this.plugins[plugin] = [];
    }
    this.plugins[plugin].unshift(value);
};
Phlexible.gui.util.PluginRegistry.prototype.get = function (plugin) {
    return this.plugins[plugin];
};
