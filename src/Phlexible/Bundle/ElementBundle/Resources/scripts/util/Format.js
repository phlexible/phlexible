Ext.provide('Phlexible.elements.Format');

Phlexible.elements.Format = {
    elementIcon: function (icon, meta, record) {
        return '<img src="' + icon + '" width="18" height="18" border="0" title="' + record.get('element_type') + '" alt="' + record.get('element_type') + '" />';
    },

    status: function (status, meta, record) {
        return '';
    }
};
