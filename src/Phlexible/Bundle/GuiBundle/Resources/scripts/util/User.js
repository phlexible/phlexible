Phlexible.gui.util.User = function(resources) {
    this.resources = resources;
};
Phlexible.gui.util.User.prototype.isGranted = function(resource) {
    Phlexible.console.info('isGranted()', resource, '===', this.resources.indexOf(resource) !== -1);
    return this.resources.indexOf(resource) !== -1;
};
