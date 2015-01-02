Phlexible.xHandles = function() {
    this.handles = {};
};
Phlexible.xHandles.prototype.get = function(key) {
    return this.handles[key];
}
Phlexible.xHandles.prototype.has = function(key) {
    return this.handles[key] !== undefined;
}
Phlexible.xHandles.prototype.add = function(key, fn) {
    this.handles[key] = fn;
}
Phlexible.Handles = new Phlexible.xHandles;
