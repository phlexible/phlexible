Phlexible.mediamanager.FolderPropertiesTemplate = new Ext.XTemplate(
    '<div style="padding: 10px;">',
    '<table border="0" cellpadding="0" cellspacing="7">',
    '<tr>',
    '<td>{[this.strings.type]}:</td>',
    '<td>{[this.strings[values.type]]}</div>',
    '</tr>',
    '<tr>',
    '<td>{[this.strings.path]}:</td>',
    '<td>{path}</td>',
    '</tr>',
    '<tr>',
    '<td>{[this.strings.size]}:</td>',
    '<td>{[Phlexible.Format.size(values.size)]} ({size} Bytes)</td>',
    '</tr>',
    '<tr>',
    '<td>{[this.strings.contents]}:</td>',
    '<td>{folders} {[values.folders == 1 ? this.strings.folder : this.strings.folders]}, {files} {[values.files == 1 ? this.strings.file : this.strings.files]}</td>',
    '</tr>',
    '<tr>',
    '<td>{[this.strings.create_date]}:</td>',
    '<td>{[Phlexible.Format.date(values.create_time)]}</td>',
    '</tr>',
    '<tr>',
    '<td>{[this.strings.created_by]}:</td>',
    '<td>{values.create_user}</td>',
    '</tr>',
    '<tpl if="values.modify_date">',
    '<tr>',
    '<td>{[this.strings.modify_date]}:</td>',
    '<td>{[Phlexible.Format.date(values.modify_time)]}</td>',
    '</tr>',
    '</tpl>',
    '<tpl if="values.modify_user">',
    '<tr>',
    '<td>{[this.strings.modified_by]}:</td>',
    '<td>{values.modify_user}</td>',
    '</tr>',
    '</tpl>',
    '</table>',
    '</div>',
    {
        strings: Phlexible.mediamanager.Strings
    }
);
Phlexible.mediamanager.FolderPropertiesPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.mediamanager.Strings,
    title: Phlexible.mediamanager.Strings.properties,
    iconCls: 'p-mediamanager-folder_properties-icon',
    cls: 'p-mediamanager-properties-panel',
    disabled: true,
    bodyStyle: 'padding: 10px',

    initComponent: function () {
        this.html = 'Loading...';

        Phlexible.mediamanager.FolderPropertiesPanel.superclass.initComponent.call(this);
    },

    loadData: function (folder_id) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_folder_properties'),
            loadMask: true,
            params: {
                folder_id: folder_id
            },
            success: function (response) {
                data = Ext.decode(response.responseText);

                this.applyData(data);

                this.enable();
            },
            scope: this
        });
    },

    applyData: function (data) {
        Phlexible.mediamanager.FolderPropertiesTemplate.overwrite(this.el, data);
    }
});

Ext.reg('mediamanager-folderproperties', Phlexible.mediamanager.FolderPropertiesPanel);