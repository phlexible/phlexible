Phlexible.mediamanager.NewFolderWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.mediamanager.Strings.new_folder,
    width: 400,
    height: 210,
    modal: true,
    iconCls: 'p-mediamanager-folder_add-icon',

    textHeader: Phlexible.mediamanager.Strings.create_new_folder_header,
    textDescription: Phlexible.mediamanager.Strings.create_new_folder_desc,
    textOk: Phlexible.mediamanager.Strings.create,
    textCancel: Phlexible.mediamanager.Strings.cancel,

    extraCls: 'p-mediamanager-newfolder',

    labelWidth: 70,

    formItems: [
        {
            xtype: 'textfield',
            fieldLabel: Phlexible.mediamanager.Strings.name,
            name: 'folder_name',
            anchor: '-70',
            msgTarget: 'under'
        }
    ],

    /**
     * Return the Submit URL
     * @return string
     */
    getSubmitUrl: function () {
        return Phlexible.Router.generate('mediamanager_folder_create');
    }
});
