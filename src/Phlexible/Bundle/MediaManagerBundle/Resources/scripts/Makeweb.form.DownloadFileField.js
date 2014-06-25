/**
 * @class Ext.form.FileField
 * @extends Ext.form.Field
 * Basic text field.  Can be used as a direct replacement for traditional text inputs, or as the base
 * class for more sophisticated input controls (like {@link Ext.form.TextArea} and {@link Ext.form.ComboBox}).
 * @constructor
 * Creates a new TextField
 * @param {Object} config Configuration options
 */
Ext.form.DownloadFileField = Ext.extend(Ext.form.FileField,  {
    addIconCls: 'p-mediamanager-download_add-icon',
    removeIconCls: 'p-mediamanager-download_delete-icon',

	getPlaceholder: function() {
		return Phlexible.component('/mediamanagerbundle/images/form-file-download.gif');
	},

    onAdd: function() {
        if (this.disabled) return;

        var w = new Phlexible.mediamanager.MediamanagerWindow({
            width: 800,
            height: 600,
            mode: 'select',
            params: {
                start_file_id: this.file_id || false,
                start_folder_path: this.folder_path || false,
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

    onFileSelect: function(w, file_id, file_version, file_name, folder_id) {
        this.setFile(file_id, file_version, file_name, folder_id);

        w.close();
    },

    // private
    xonRender: function(ct, position){
        Ext.form.Field.superclass.onRender.call(this, ct, position);
        if(!this.el){
            this.el = ct.createChild({
                tag: "div",
                cls: 'x-form-field x-form-empty-field p-form-file p-form-file-download'
                }, position
            );
            this.hiddenEl = this.el.createChild({
                tag: 'input',
                type: 'hidden',
                name: this.name || this.id
            });

            var fileBoxButtons = this.el.createChild({
                tag: 'div',
                cls: 'FileBoxButtons'
            });

            var addButton = fileBoxButtons.createChild({
                tag: 'img',
                src: Phlexible.component('/mediamanager/icons/download_add.png'),
                alt: 'add',
                width: 16,
                height: 16
            });
            addButton.on('click', this.onAdd, this);

//            var disableButton = fileBoxButtons.createChild({
//                tag: 'img',
//                src: Phlexible.component('/mediamanager/icons/download_delete.png'),
//                alt: 'add',
//                width: 16,
//                height: 16
//            });
//            disableButton.on('click', this.onDisable, this);

            this.delButton = fileBoxButtons.createChild({
                tag: 'img',
                src: Phlexible.component('/mediamanager/icons/download_delete.png'),
                alt: 'add',
                width: 16,
                height: 16
            });
            this.delButton.setVisibilityMode(Ext.Element.DISPLAY);
            this.delButton.hide();
            this.delButton.on('click', this.onClear, this);

            var fileBoxImageContainer = this.el.createChild({
                tag: 'div',
                cls: 'FileBoxImage'
            });
            this.emptyAddButton = fileBoxImageContainer.createChild({
                tag: 'span',
                cls: 'AddText',
                html: Phlexible.mediamanager.Strings.click_to_add_file
            });
            this.emptyAddButton.on('click', this.onAdd, this);
            this.fileBoxImage = fileBoxImageContainer.createChild({
                tag: 'img',
                cls: 'FileBoxImage',
                border: 0,
                alt: '',
                src: this.getPlaceholder(),
                height: 80,
                width: 80
            });
            /*
            this.fileBoxLegend = this.el.createChild({
                tag: 'div',
                cls: 'FileBoxLegend'
            });
            this.createTable();
            */
            this.el.createChild({
                tag: 'div',
                cls: 'x-form-clear-left'
            });

            this.fileBoxLink = this.el.createChild({
                tag: 'div',
                cls: 'FileBoxLink'
            });
            this.fileBoxLink.setVisibilityMode(Ext.Element.DISPLAY);
            this.fileBoxLink.hide();
            var gotoButton = this.fileBoxLink.createChild({
                tag: 'img',
                src: Phlexible.component('/mediamanager/icons/download_link.png'),
                alt: 'preview',
                width: 16,
                height: 16
            });
            gotoButton.on('click', this.onGoto, this);
            this.fileTitle = this.fileBoxLink.createChild({
                tag: 'span',
                cls: 'FileName',
                qtip: this.fileTitle,
                html: this.fileTitle ? this.fileTitle.shorten(16) : ''
            });
        }
        if(this.tabIndex !== undefined){
            this.el.dom.setAttribute('tabIndex', this.tabIndex);
        }

        this.el.addClass([this.fieldClass, this.cls]);
        this.initValue();
    }
});
Ext.reg('downloadfilefield', Ext.form.DownloadFileField);
