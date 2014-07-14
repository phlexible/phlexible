Phlexible.mediamanager.FolderMeta = Ext.extend(Phlexible.mediamanager.FileMeta, {
    title: Phlexible.mediamanager.Strings.folder_meta,

    right: Phlexible.mediamanager.Rights.FOLDER_MODIFY,

    initUrls: function () {
        this.urls = {
            load: Phlexible.Router.generate('mediamanager_folder_meta'),
            save: Phlexible.Router.generate('mediamanager_folder_meta_save')
        };

        this.metasetUrls = {
            list: Phlexible.Router.generate('mediamanager_folder_meta_set_list'),
            available: Phlexible.Router.generate('mediamanager_folder_meta_set_available'),
            add: Phlexible.Router.generate('mediamanager_folder_meta_set_add'),
            remove: Phlexible.Router.generate('mediamanager_folder_meta_set_remove')
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
