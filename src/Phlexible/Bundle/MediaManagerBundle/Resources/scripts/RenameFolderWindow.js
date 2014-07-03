Phlexible.mediamanager.RenameFolderWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.mediamanager.Strings.rename_folder,
    width: 400,
    minWidth: 400,
    height: 220,
    minHeight: 220,
    iconCls: 'p-mediamanager-folder_rename-icon',

    textHeader: Phlexible.mediamanager.Strings.rename_folder_header,
    textDescription: Phlexible.mediamanager.Strings.rename_folder_desc,
    textOk: Phlexible.mediamanager.Strings.rename,
    textCancel: Phlexible.mediamanager.Strings.cancel,

    extraCls: 'p-mediamanager-renamefolder',

    labelWidth: 70,

    formItems: [
        {
            fieldLabel: Phlexible.mediamanager.Strings.name,
            name: 'folder_name',
            msgTarget: 'under',
            anchor: '-70'
        }
    ],

    /**
     * Return the Submit URL
     * @return string
     */
    getSubmitUrl: function () {
        return Phlexible.Router.generate('mediamanager_folder_rename');
    }
});
