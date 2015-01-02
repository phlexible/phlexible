Ext.ns('Phlexible.gui.menuhandle.handle');

Phlexible.gui.menuhandle.handle.XtypeHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    handle: function () {
        var identifier = this.getIdentifier(),
            component = this.getComponent(),
            parameters = this.getParameters();

        if (typeof(component) !== 'string') {
            throw Error('Component has to be a xtype-string.');
        }

        Phlexible.console.debug('XtypeHandle.handle(' + component + ', ' + identifier + ')', parameters);

        Phlexible.Frame.loadPanel(identifier, component, parameters);
    },

    getIdentifier: function () {
        return this.getComponent();
    },

    getParameters: function () {
        return this.parameters || {};
    },

    setParameters: function (parameters) {
        this.parameters = parameters;
    }
});