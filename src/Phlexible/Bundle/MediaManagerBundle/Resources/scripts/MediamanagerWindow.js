Phlexible.mediamanager.MediamanagerWindow = Ext.extend(Ext.Window, {
    title: 'Mediamanager',
    iconCls: 'p-mediamanager-component-icon',
    width: 800,
    height: 600,
    layout: 'fit',
    modal: true,
    border: false,
    constrainHeader: true,

    mode: '',
    params: {},

    initComponent: function () {
        this.addEvents(
            'fileSelectWindow'
        );

        if (!this.params.start_folder_path && Phlexible.mediamanager.lastParams && Phlexible.mediamanager.lastParams.start_folder_path) {
            this.params.start_folder_path = Phlexible.mediamanager.lastParams.start_folder_path;
        }

        this.items = [
            new Phlexible.mediamanager.MediamanagerPanel({
                noTitle: true,
                mode: this.mode,
                params: this.params,
                listeners: {
                    fileSelect: {
                        fn: function (file_id, file_version, file_name, folder_id) {
                            this.fireEvent('fileSelectWindow', this, file_id, file_version, file_name, folder_id);
                        },
                        scope: this
                    }
                }
            })
        ];

        Phlexible.mediamanager.MediamanagerWindow.superclass.initComponent.call(this);
    }
});
