Ext.ns('Phlexible.mediamanager');

Phlexible.mediamanager.FolderMetaGrid = Ext.extend(Phlexible.mediamanager.FileMetaGrid, {
    title: Phlexible.metasets.Strings.folder_meta,

    right: Phlexible.mediamanager.Rights.FOLDER_MODIFY
});

Ext.reg('mediamanager-foldermetagrid', Phlexible.mediamanager.FolderMetaGrid);
