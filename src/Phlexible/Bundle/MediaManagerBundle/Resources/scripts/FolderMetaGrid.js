Phlexible.mediamanager.FolderMetaGrid = Ext.extend(Phlexible.mediamanager.FileMetaGrid, {

    title: Phlexible.metasets.Strings.folder_meta,

    right: Phlexible.mediamanager.Rights.FOLDER_MODIFY,

    initUrls: function() {
        this.urls = {
            load: Phlexible.Router.generate('mediamanager_folder_meta'),
            save: Phlexible.Router.generate('mediamanager_folder_meta_save')
        };

        this.metasetUrls = {
            list: Phlexible.Router.generate('mediamanager_folder_listsets'),
            available: Phlexible.Router.generate('mediamanager_folder_availablesets'),
            add: Phlexible.Router.generate('mediamanager_folder_addset'),
            remove: Phlexible.Router.generate('mediamanager_folder_removeset')
        };
    }
});

Ext.reg('mediamanager-foldermetagrid', Phlexible.mediamanager.FolderMetaGrid);
