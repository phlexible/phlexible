Ext.namespace('Phlexible.elementtypes');

Phlexible.elementtypes.Format = {
    title: function (title, meta, record) {
        return '<img src="' + Phlexible.bundleAsset('/phlexibleelementtype/elementtypes/' + record.get('icon')) + '" width="18" height="18" border="0" alt="' + title + '" /> ' + title;
    },

    status: function (status, meta, record) {
        return '';
    }
};
