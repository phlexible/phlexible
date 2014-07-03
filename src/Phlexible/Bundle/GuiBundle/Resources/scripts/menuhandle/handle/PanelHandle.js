Phlexible.gui.menuhandle.handle.PanelHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    handle: function () {
        var identifier = this.getIdentifier(),
            component = this.getComponent(),
            parameters = {};

        if (typeof(component) !== 'string') {
            throw Error('Component has to be a panel-classname-string.');
        }
        component = Phlexible.evalClassString(component);
        if (typeof(component) !== 'function') {
            throw Error('Not a function.');
        }

        Phlexible.console.debug('PanelHandle.handle(' + component + ', ' + identifier + ')', parameters);

        Phlexible.Frame.loadPanel(identifier, component, parameters);
    },

    getIdentifier: function () {
        return this.getComponent();
    }
});