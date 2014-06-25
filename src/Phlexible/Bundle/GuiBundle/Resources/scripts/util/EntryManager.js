Phlexible.gui.util.EntryManager = function() {
    this.fns = {};
};
Ext.extend(Phlexible.gui.util.EntryManager, Ext.util.Observable, {
    register: function(key, fn) {
        this.fns[key] = fn;
    },
    get: function(key) {
        return this.fns[key];
    }
});