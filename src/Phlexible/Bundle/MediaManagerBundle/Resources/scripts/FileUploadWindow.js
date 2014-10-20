Phlexible.mediamanager.JavaUploadWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.mediamanager.Strings,
    iconCls: 'p-mediamanager-file_upload-icon',
    width: 700,
    height: 400,
    modal: true,
    resizable: false,

    params: {
        lang: 'de',
        showLogWindow: true,
        showStatusBar: true,
        debugLevel: 100,
        maxChunkSize: 0,
        postURL: 'ftp://swentz:warhammer@192.168.1.46/',
        afterUploadURL: 'javascript:alert(\"done\");'
    },

    initComponent: function () {
        var html = '';
        html += '<applet width="' + (this.width - 30) + '" height="' + (this.height - 40) + '" mayscript="" name="jupload" archive="/resources/asset/java/mediamanager/wjhk.jupload.jar, /resources/asset/java/mediamanager/jakarta-commons-oro.jar, /resources/asset/java/mediamanager/jakarta-commons-net.jar" code="wjhk.jupload2.JUploadApplet">';

        for (var i in this.params) {
            html += '<param name="' + i + '" value="' + this.params[i] + '" />';
        }

        html += 'Java 1.5 or higher plugin required.';
        html += '</applet>';

        this.html = html;

        Phlexible.mediamanager.JavaUploadWindow.superclass.initComponent.call(this);
    },

    show: function (btn, siteID, folderID, folderTitle) {
        if (folderID) {
            if (folderTitle) {
                this.setTitle(this.strings.upload_file_to_folder + ' "' + folderTitle + '"');
            } else {
                this.setTitle(this.strings.upload_file);
            }
//            this.swfPanel.addPostParam('folder_id', folderID);
            Phlexible.mediamanager.JavaUploadWindow.superclass.show.call(this, btn);
        }
    }
});
Phlexible.mediamanager.FileUploadWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.mediamanager.Strings,
    width: 450,
    height: 350,
    layout: 'fit',
    iconCls: 'p-mediamanager-file_upload-icon',
    closeAction: 'hide',
    modal: true,

    // private
    initComponent: function () {
        var sessionID = Phlexible.Cookie.get('phlexible');
        if (!sessionID) {
            alert("No session ID, upload _will_ fail!");
        }

        this.items = [
            this.swfPanel = new Ext.ux.SwfUploadPanel({
                border: false,
                debug: true,

                upload_url: document.location.protocol + '//' + document.location.hostname + Phlexible.Router.generate('mediamanager_upload'),
                post_params: {
                    sid: sessionID
                },
                file_types: '*.*',
                file_types_description: this.strings.all_files,
                file_size_limit: "2000mb",
                flash_url: document.location.protocol + '//' + document.location.hostname + Phlexible.bundleAsset('/phlexiblemediamanager/flash/swfupload.swf'),

                // Set to true if you only want to select one file from the FileDialog.
                single_file_select: false,
                // This will prompt for removing files from queue.
                confirm_delete: true,
                // Remove file from grid after uploaded.
                remove_completed: true,

                strings: {
                    text_add: this.strings.swfupload_text_add,
                    text_upload: this.strings.swfupload_text_upload,
                    text_cancel: this.strings.swfupload_text_cancel,
                    text_clear: this.strings.swfupload_text_clear,
                    text_progressbar: this.strings.swfupload_text_progressbar,
                    text_remove: this.strings.swfupload_text_remove,
                    text_remove_sure: this.strings.swfupload_text_remove_sure,
                    text_error: this.strings.swfupload_text_error,
                    text_uploading: this.strings.swfupload_text_uploading,
                    header_filename: this.strings.swfupload_header_filename,
                    header_size: this.strings.swfupload_header_size,
                    header_status: this.strings.swfupload_header_status,
                    status: {
                        0: this.strings.swfupload_status_0,
                        1: this.strings.swfupload_status_1,
                        2: this.strings.swfupload_status_2,
                        3: this.strings.swfupload_status_3,
                        4: this.strings.swfupload_status_4
                    },
                    error_queue_exceeded: this.strings.swfupload_error_queue_exceeded,
                    error_queue_slots_0: this.strings.swfupload_error_queue_slots_0,
                    error_queue_slots_1: this.strings.swfupload_error_queue_slots_1,
                    error_queue_slots_2: this.strings.swfupload_error_queue_slots_2,
                    error_size_exceeded: this.strings.swfupload_error_size_exceeded,
                    error_zero_byte_file: this.strings.swfupload_error_zero_byte_file,
                    error_invalid_filetype: this.strings.swfupload_error_invalid_filetype,
                    error_file_not_found: this.strings.swfupload_error_file_not_found,
                    error_security_error: this.strings.swfupload_error_security_error
                },

                listeners: {
                    startUpload: function (panel) {
                        panel.addPostParam('upload_id', new Ext.ux.GUID().toString());
                    },
                    allUploadsComplete: {
                        fn: this.onUploadComplete,
                        scope: this
                    }
                }
            })
        ];

        Phlexible.mediamanager.FileUploadWindow.superclass.initComponent.call(this);
    },

    show: function (btn, siteID, folderID, folderTitle) {
        if (folderID) {
            if (folderTitle) {
                this.setTitle(this.strings.upload_file_to_folder + ' "' + folderTitle + '"');
            } else {
                this.setTitle(this.strings.upload_file);
            }
            this.swfPanel.addPostParam('site_id', siteID);
            this.swfPanel.addPostParam('folder_id', folderID);
            Phlexible.mediamanager.FileUploadWindow.superclass.show.call(this);
        }
    },

    onUploadComplete: function () {
        this.fireEvent('fileUploadComplete');
    }
});
