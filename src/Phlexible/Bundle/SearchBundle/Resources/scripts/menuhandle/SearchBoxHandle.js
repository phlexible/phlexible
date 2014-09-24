Phlexible.search.menuhandle.SearchBoxHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    /**
     * Create and return config
     *
     * @private
     * @param {Object} data
     * @return {Object}
     */
    createConfig: function (data) {
        return new Phlexible.search.SearchBox({
            bla: 1,
            width: 150
        });
    }
});

Phlexible.search.menuhandle.SearchBoxSeparatorHandle = Ext.extend(Phlexible.gui.menuhandle.handle.Handle, {
    createConfig: function (data) {
        return '-';
    }
});