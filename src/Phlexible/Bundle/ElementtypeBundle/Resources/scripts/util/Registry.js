Ext.provide('Phlexible.fields.Registry');

Phlexible.fields.Registry = {
    factories: {},

    hasFactory: function (key) {
        return !!this.factories[key];
    },

    addFactory: function (key, fn) {
        this.factories[key] = fn;
    },

    getFactory: function (key) {
        return this.factories[key];
    }
};
