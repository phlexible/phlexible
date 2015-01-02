Ext.ns('Phlexible.fields');

Phlexible.fields.FieldTypes = {
    addField: function (field, fieldConfig) {
        Phlexible.fields.FieldTypes[field] = fieldConfig;
    },
    getField: function (field) {
        return Phlexible.fields.FieldTypes[field];
    },
    hasField: function (field) {
        return !!Phlexible.fields.FieldTypes[field];
    }
};
