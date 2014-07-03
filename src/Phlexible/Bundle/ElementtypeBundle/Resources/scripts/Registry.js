Phlexible.fields.Registry = {
    factory: {},

    hasFactory: function (key) {
        return !!this.factory[key];
    },

    addFactory: function (key, fn) {
        this.factory[key] = fn;
    },

    getFactory: function (key) {
        return this.factory[key];
    }
};
