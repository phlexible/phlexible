Ext.provide('Phlexible.gui.util.User');

Phlexible.gui.util.User = function (id, username, email, firstname, lastname, properties, roles) {
    this.id = id;
    this.username = username;
    this.email = email;
    this.firstname = firstname;
    this.lastname = lastname;
    this.properties = properties;
    this.roles = roles;
};
Phlexible.gui.util.User.prototype.isGranted = function (role) {
    var isGranted = this.roles.indexOf(role) !== -1;
    if (isGranted) {
        Phlexible.console.info('isGranted(' + role + ') ===', isGranted);
    } else {
        Phlexible.console.error('isGranted(' + role + ') ===', isGranted);
    }
    return isGranted;
};
Phlexible.gui.util.User.prototype.getId = function () {
    return this.id;
};
Phlexible.gui.util.User.prototype.getUsername = function () {
    return this.username;
};
Phlexible.gui.util.User.prototype.getEmail = function () {
    return this.email;
};
Phlexible.gui.util.User.prototype.getFirstname = function () {
    return this.firstname;
};
Phlexible.gui.util.User.prototype.getLastname = function () {
    return this.lastname;
};
Phlexible.gui.util.User.prototype.getDisplayName = function () {
    return this.firstname && this.lastname
        ? this.firstname + ' ' + this.lastname
        : this.username;
};
