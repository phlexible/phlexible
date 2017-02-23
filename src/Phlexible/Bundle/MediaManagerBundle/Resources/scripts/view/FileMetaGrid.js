Ext.provide('Phlexible.mediamanager.FileMetaGrid');

Ext.require('Phlexible.mediamanager.MetaGrid');

Phlexible.mediamanager.FileMetaGrid = Ext.extend(Phlexible.mediamanager.MetaGrid, {
    title: Phlexible.mediamanager.Strings.file_meta
});

Ext.reg('mediamanager-filemetagrid', Phlexible.mediamanager.FileMetaGrid);
