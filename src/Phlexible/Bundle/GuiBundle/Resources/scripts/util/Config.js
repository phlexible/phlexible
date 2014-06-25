Phlexible.gui.util.Config = function(values) {
    this.values = values;
};
Phlexible.gui.util.Config.prototype.get = function(key, defaultValue) {
    if (!this.has(key)) {
        if (defaultValue !== undefined) {
            return defaultValue;
        }
        throw new Error(key + ' not set.');
    }
    return this.values[key];
};
Phlexible.gui.util.Config.prototype.has = function(key) {
    return this.values[key] !== undefined;
};
Phlexible.gui.util.Config.prototype.set = function(key, value) {
    this.values[key] = value;;
};
