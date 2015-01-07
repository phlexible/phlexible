Ext.provide('Phlexible.fields.Prototypes');

Phlexible.fields.Prototypes = function () {
};
Ext.extend(Phlexible.fields.Prototypes, Ext.util.Observable, {
    ids: {},
    prototypes: {},

    mediaFields: ['file', 'folder'],
    listFields: ['select', 'multiselect', 'form'],

    clear: function () {
        this.ids = {};
        this.prototypes = {};
    },

    incCount: function (dsId, parentId) {
        if (parentId) {
            dsId = parentId + '___' + dsId;
        }

        if (!this.ids[dsId]) {
            this.ids[dsId] = 0;
        }

        this.ids[dsId]++;

        return this.ids[dsId];
    },

    decCount: function (dsId, parentId) {
        if (parentId) {
            dsId = parentId + '___' + dsId;
        }

        if (!this.ids[dsId]) {
            this.ids[dsId] = 0;
        }

        this.ids[dsId]--;

        return this.ids[dsId];
    },

    getCount: function (dsId, parentId) {
        if (parentId) {
            dsId = parentId + '___' + dsId;
        }

        if (!this.ids[dsId]) {
            return 0;
        }

        return this.ids[dsId];
    },

    getPrototype: function (dsId) {
        return this.prototypes[dsId];
    },

    hasPrototype: function (dsId) {
        return !!this.prototypes[dsId];
    },

    setPrototype: function (dsId, pt) {
        this.prototypes[dsId] = pt;
    },

    addFieldPrototype: function (item) {
//        Phlexible.console.log('field pt: ' + item.ds_id);
        if (this.hasPrototype(item.dsId)) {
            return false;
        }

        var pt = {
            factory: item.type,
            dsId: item.dsId,
            parentDsId: item.parentDsId,
            //id: item.id,
            parentId: item.parentId,
            name: item.name,
            type: item.type,
            configuration: item.configuration,
            validation: item.validation,
            contentchannels: item.contentchannels,
            comment: item.comment,
            options: item.options,
            'function': item['function'],
            labels: item.labels,
            //content: '', //item.content,
            //rawContent: '', //item.rawContent,
            //default_content: item.default_content,
            templates: item.templates
        };

        if (Phlexible.fields.FieldTypes[item.type].copyFields) {
            Ext.each(Phlexible.fields.FieldTypes[item.type].copyFields, function (field) {
                pt[field] = item[field];
            });
        }

        this.setPrototype(item.dsId, pt);

        return pt;
    },

    addGroupPrototype: function (item) {
//        Phlexible.console.log('group pt: ' + item.ds_id);
        if (this.hasPrototype(item.dsId)) {
            return false;
        }

        var children = [],
            pt;
        if (item.children) {
            for (var i = 0; i < item.children.length; i++) {
                if (item.children[i].type == 'group') {
                    pt = this.addGroupPrototype(item.children[i]);
                } else {
                    pt = this.addFieldPrototype(item.children[i]);
                }
                if (pt) {
                    children.push(pt);
                }
            }
        }

        pt = {
            factory: item.type,
            dsId: item.dsId,
            parentDsId: item.parentDsId,
            fieldId: item.fieldId,
            name: item.name,
            //id: item.id,
            parentId: item.parentId,
            type: item.type,
            configuration: item.configuration,
            validation: item.validation,
            labels: item.labels,
            contentchannels: item.contentchannels,
            comment: item.comment,
            children: children
        };

        this.setPrototype(item.dsId, pt);

        return pt;
    }
});
