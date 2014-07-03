Phlexible.siteroots.PropertyGrid = Ext.extend(Ext.grid.PropertyGrid, {
    title: Phlexible.siteroots.Strings.properties,
    strings: Phlexible.siteroots.Strings,
    border: false,
    viewConfig: {
        forceFit: true,
        emptyText: Phlexible.siteroots.Strings.no_properties
    },

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Number} id
     * @param {String} title
     * @param {Object} data
     */
    loadData: function (id, title, data) {
        this.setSource(data.properties);
    },

    isValid: function () {
        var valid = true;

        return valid;
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {
        var values = this.getSource();

        return {
            properties: values
        };
    }
});

Ext.reg('siteroots-properties', Phlexible.siteroots.PropertyGrid);