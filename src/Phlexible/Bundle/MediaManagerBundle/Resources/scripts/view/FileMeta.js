Ext.provide('Phlexible.mediamanager.FileMeta');

Ext.require('Phlexible.mediamanager.Meta');
Ext.require('Phlexible.mediamanager.FileMetaGrid');

Phlexible.mediamanager.FileMeta = Ext.extend(Phlexible.mediamanager.Meta, {
    title: Phlexible.mediamanager.Strings.file_meta,

    getRight: function() {
        return Phlexible.mediamanager.Rights.FILE_MODIFY;
    },

    initUrls: function () {
        this.urls = {
            load: Phlexible.Router.generate('mediamanager_file_meta'),
            save: Phlexible.Router.generate('mediamanager_file_meta_save')
        };

        this.metasetUrls = {
            list: Phlexible.Router.generate('mediamanager_file_meta_sets_list'),
            save: Phlexible.Router.generate('mediamanager_file_meta_sets_save'),
            available: Phlexible.Router.generate('metasets_sets_list')
        };
    },

    createMetaGridConfig: function(setId, title, fields, small) {
        return {
            xtype: 'mediamanager-filemetagrid',
            setId: setId,
            title: title,
            autoHeight: true,
            border: false,
            small: small,
            data: fields
        };
    }
});

Ext.reg('mediamanager-filemeta', Phlexible.mediamanager.FileMeta);
