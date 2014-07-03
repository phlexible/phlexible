/**
 * @class Ext.form.FileField
 * @extends Ext.form.Field
 * Basic text field.  Can be used as a direct replacement for traditional text inputs, or as the base
 * class for more sophisticated input controls (like {@link Ext.form.TextArea} and {@link Ext.form.ComboBox}).
 * @constructor
 * Creates a new TextField
 * @param {Object} config Configuration options
 */
Ext.form.VideoFileField = Ext.extend(Ext.form.FileField, {
    addIconCls: 'p-mediamanager-video_add-icon',
    removeIconCls: 'p-mediamanager-video_delete-icon',

    emptyAddText: Phlexible.mediamanager.Strings.click_to_add_video,

    getPlaceholder: function () {
        return Phlexible.component('/mediamanagerbundle/images/form-file-video.gif');
    },

    onAdd: function () {
        if (this.disabled) return;

        var w = new Phlexible.mediamanager.MediamanagerWindow({
            width: 800,
            height: 600,
            mode: 'select',
            params: {
                start_file_id: this.file_id || false,
                start_folder_path: this.folder_path || false,
//                asset_type: Phlexible.mediamanager.VIDEO,
                file_view: 'medium',
                hide_properties: true
            },
            listeners: {
                fileSelectWindow: {
                    fn: this.onFileSelect,
                    scope: this
                }
            }
        });
        w.show();
    },

    onFileSelect: function (w, file_id, file_version, file_name, folder_id) {
        this.setFile(file_id, file_version, file_name, folder_id);

        w.close();
    }
});
Ext.reg('videofilefield', Ext.form.VideoFileField);
