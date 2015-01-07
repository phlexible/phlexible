Ext.provide('Phlexible.mediamanager.FolderMeta');

Phlexible.mediamanager.FolderMeta = Ext.extend(Phlexible.mediamanager.FileMeta, {
    title: Phlexible.mediamanager.Strings.folder_meta,

    right: Phlexible.mediamanager.Rights.FOLDER_MODIFY,

    initUrls: function () {
        this.urls = {
            load: Phlexible.Router.generate('mediamanager_folder_meta'),
            save: Phlexible.Router.generate('mediamanager_folder_meta_save')
        };

        this.metasetUrls = {
            list: Phlexible.Router.generate('mediamanager_folder_meta_sets_list'),
            save: Phlexible.Router.generate('mediamanager_folder_meta_sets_save'),
            available: Phlexible.Router.generate('metasets_sets_list')
        };
    },

    createMetaGridConfig: function(setId, title, fields, small) {
        return {
            xtype: 'mediamanager-foldermetagrid',
            setId: setId,
            title: title,
            height: 180,
            border: false,
            small: small,
            data: fields
        };
    }
});

Ext.reg('mediamanager-foldermeta', Phlexible.mediamanager.FolderMeta);
