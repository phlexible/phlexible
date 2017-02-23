Ext.provide('Phlexible.mediamanager.FolderMetaGrid');

Ext.require('Phlexible.mediamanager.MetaGrid');

Phlexible.mediamanager.FolderMetaGrid = Ext.extend(Phlexible.mediamanager.MetaGrid, {
    title: Phlexible.metasets.Strings.folder_meta
});

Ext.reg('mediamanager-foldermetagrid', Phlexible.mediamanager.FolderMetaGrid);
