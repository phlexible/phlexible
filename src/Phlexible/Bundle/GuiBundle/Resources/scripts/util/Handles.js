Ext.provide('Phlexible.gui.util.Handles');

Phlexible.gui.util.Handles = function() {
    this.handles = {};
};
Phlexible.gui.util.Handles.prototype.get = function(key) {
    return this.handles[key];
};
Phlexible.gui.util.Handles.prototype.has = function(key) {
    return this.handles[key] !== undefined;
};
Phlexible.gui.util.Handles.prototype.add = function(key, fn) {
    this.handles[key] = fn;
};
