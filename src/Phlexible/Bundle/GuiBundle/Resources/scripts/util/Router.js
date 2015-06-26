Ext.provide('Phlexible.gui.util.Router');

/**
 * @constructor
 */
Phlexible.gui.util.Router = function () {
};
/**
 * Set routing data
 *
 * @param {Object} data Routing structure:
 * {
 *     baseUrl: 'xxx',
 *     basePath: 'yyy',
 *     routes: {
 *         my_route: {
 *             path: '/my/route/{key}/{name}',
 *             variables: ['key', 'name'],
 *             defaults: {name: 'test'}
 *         }
 *     }
 * }
 */
Phlexible.gui.util.Router.prototype.setData = function (data) {
    if (data.baseUrl) {
        this.baseUrl = data.baseUrl;
    }
    if (data.basePath) {
        this.basePath = data.basePath;
    }
    if (data.routes) {
        this.routes = data.routes;
    }
};
/**
 * Dump routes
 *
 * @param {String} part Route name part for searches, optional
 */
Phlexible.gui.util.Router.prototype.dump = function (part) {
    for (var key in this.routes) {
        var route = this.routes[key];
        if (!part || key.match(new RegExp(part))) {
            Phlexible.console.debug(key, route.path);
        }
    }
    ;
};
/**
 * Generate URL
 *
 * @param {String} name Route name
 * @param {Object} parameters Route parameters
 * @return {String} Generated URL
 */
Phlexible.gui.util.Router.prototype.generate = function (name, parameters) {
    if (!this.routes[name]) {
        throw new Error('Unknown route ' + name);
    }

    var route = this.routes[name],
        path = route.path,
        variables = route.variables,
        defaults = route.defaults;

    if (variables) {
        parameters = Phlexible.clone(parameters || {});
        Ext.each(variables, function (variable) {
            var placeholder = '{' + variable + '}';
            if (parameters[variable] !== undefined) {
                path = path.replace(placeholder, parameters[variable]);
                delete parameters[variable];
                return;
            }
            if (defaults[variable] !== undefined) {
                path = path.replace(placeholder, defaults[variable]);
                return;
            }
            throw new Error('Missing parameter ' + variable + ' on route ' + name);
        });
        var query = '';
        for (var key in parameters) {
            if (typeof(parameters[key]) !== 'object')
                query += '&' + key + '=' + parameters[key];
        }
        if (query) {
            path += '?' + query;
        }
    }

    return path;
};
