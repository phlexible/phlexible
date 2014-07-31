Phlexible.mediamanager.MediamanagerPanel = Ext.extend(Ext.Panel, {
    layout: 'border',
    closable: true,
    cls: 'p-mediamanager-main-panel',
    iconCls: 'p-mediamanager-component-icon',
    strings: Phlexible.mediamanager.Strings,
    border: true,

    mode: '',
    params: {},

    dndFormInput: false,

    loadParams: function (params) {
        if (params.start_folder_path) {
            if (params.start_file_id) {
                this.getFilesGrid().start_file_id = params.start_file_id;
            }
            if (params.start_folder_path.substr(0, 5) !== '/root') {
                params.start_folder_path = '/root' + params.start_folder_path;
            }

            var n = this.getFolderTree().getSelectionModel().getSelectedNode();
            if (!n || n.getPath() !== params.start_folder_path) {
                this.getFolderTree().selectPath(params.start_folder_path);
            } else if (params.start_file_id) {
                var i = this.getFilesGrid().getStore().find('id', params.start_file_id);
                if (i !== false) {
                    this.getFilesGrid().getSelectionModel().selectRow([i]);
                }
                this.getFilesGrid().start_file_id = params.start_file_id;
            }
        }
    },

    // private
    initComponent: function () {
        if (!this.noTitle) {
            this.title = this.strings.media;
        }

        if (this.params.start_folder_path) {
            if (this.params.start_folder_path.substr(0, 5) !== '/root') {
                this.params.start_folder_path = '/root' + this.params.start_folder_path;
            }
        }

        /*
         this.searchPanel = new Phlexible.mediamanager.FilesSearchPanel({
         height: 200,
         collapsible: true,
         collapsed: true,
         border: true,
         bodyStyle: 'padding: 3px;',
         listeners: {
         xsearch: {
         fn: this.onSearch,
         scope: this
         }
         }
         });
         */

        this.tbarIndex = {
            newFolder: 0,
            upload: 2,
            download: 4,
            view: 6,
            filter: 8
        };

        this.items = [
            {
                xtype: 'locationbar',
                region: 'north',
                height: 28,
                border: false,
                stopNodeId: 'root',
                noHome: true
                //handler: function(node) {
                //    node.select();
                //    node.getOwnerTree().onClick(node);
                //}
            },
            {
                region: 'center',
                layout: 'border',
                border: false,

                items: [
                    this.createFolderTreeConfig(),
                    this.createFilesGridConfig(),
                    this.createAttributesPanelConfig()
                ],
                tbar: [
                    {
                        // 0
                        text: this.strings.new_folder,
                        iconCls: 'p-mediamanager-folder_add-icon',
                        handler: this.onNewFolder,
                        scope: this
                    },
                    ' ',
                    // 2
                    {
                        text: this.strings.upload_files,
                        iconCls: 'p-mediamanager-upload-icon'
                    },
                    ' ',
                    {
                        // 4
                        text: this.strings.download,
                        iconCls: 'p-mediamanager-download-icon',
                        menu: [
                            {
                                text: this.strings.download_folder,
                                iconCls: 'p-mediamanager-download_folder-icon',
                                handler: this.onDownloadFolder,
                                scope: this
                            },
                            {
                                text: this.strings.download_files,
                                iconCls: 'p-mediamanager-download_files-icon',
                                handler: this.onDownloadFiles,
                                scope: this
                            }
                        ]
                    },
                    '->',
                    {
                        // 6
                        xtype: 'splitbutton',
                        text: this.strings.views,
                        iconCls: 'p-mediamanager-view_tile-icon',
                        handler: function (button) {
                            this.getFilesGrid().view.nextViewMode();
                        },
                        scope: this,
                        menu: [
                            {
                                text: this.strings.view_extralarge,
                                iconCls: 'p-mediamanager-view_extralarge-icon',
                                handler: function () {
                                    this.getFilesGrid().view.extraLargeThumbnails();
                                },
                                scope: this
                            },
                            {
                                text: this.strings.view_large,
                                iconCls: 'p-mediamanager-view_large-icon',
                                handler: function () {
                                    this.getFilesGrid().view.largeThumbnails();
                                },
                                scope: this
                            },
                            {
                                text: this.strings.view_medium,
                                iconCls: 'p-mediamanager-view_medium-icon',
                                handler: function () {
                                    this.getFilesGrid().view.mediumThumbnails();
                                },
                                scope: this
                            },
                            {
                                text: this.strings.view_small,
                                iconCls: 'p-mediamanager-view_small-icon',
                                handler: function () {
                                    this.getFilesGrid().view.smallThumbnails();
                                },
                                scope: this
                            },
                            {
                                text: this.strings.view_tiles,
                                iconCls: 'p-mediamanager-view_tile-icon',
                                handler: function () {
                                    this.getFilesGrid().view.tileView();
                                },
                                scope: this
                            },
                            {
                                text: this.strings.view_details,
                                iconCls: 'p-mediamanager-view_detail-icon',
                                handler: function () {
                                    this.getFilesGrid().view.detailView();
                                },
                                scope: this
                            },
                            /*{
                             text: 'Timeline',
                             iconCls: 'p-mediamanager-view_timeline-icon',
                             handler: function(){
                             this.getFilesGrid().view.timelineView();
                             },
                             scope: this
                             },*/
                            '-',
                            {
                                xtype: 'checkbox',
                                text: this.strings.show_hidden_files,
                                checked: false,
                                handler: function () {
                                    this.getFilesGrid().getStore().baseParams.show_hidden = !this.getFilesGrid().getStore().baseParams.show_hidden ? 1 : 0;
                                    this.getFilesGrid().getStore().reload();
                                },
                                scope: this
                            }
                        ]
                    },
                    ' ',
                    {
                        // 8
                        xtype: 'button',
                        text: this.strings.filters,
                        iconCls: 'p-mediamanager-filter_no-icon',
                        //handler: function() {
                        //Ext.getCmp('mediamanager-files-grid').view.nextViewMode();
                        //},
                        menu: [
                            {
                                text: this.strings.filter_no,
                                iconCls: 'p-mediamanager-filter_no-icon',
                                handler: function () {
                                    this.getFilesGrid().clearFilter();
                                },
                                scope: this
                            },
                            '-',
                            {
                                text: this.strings.filter_my_created,
                                iconCls: 'p-mediamanager-filter_mine-icon',
                                handler: function () {
                                    this.getFilesGrid().setFilter('create_user_id', Phlexible.Config.get('user.id'));
                                },
                                scope: this
                            },
                            {
                                text: this.strings.filter_my_modified,
                                iconCls: 'p-mediamanager-filter_mine_modified-icon',
                                handler: function () {
                                    this.getFilesGrid().setFilter('modify_user_id', Phlexible.Config.get('user.id'));
                                },
                                scope: this
                            },
                            {
                                text: this.strings.filter_by_age_created,
                                iconCls: 'p-mediamanager-filter_age-icon',
                                menu: [
                                    {
                                        text: this.strings.filter_age_3d,
                                        iconCls: 'p-mediamanager-filter_age-icon',
                                        checked: false,
                                        group: 'age',
                                        handler: function () {
                                            this.getFilesGrid().setTimeFilter('filterTimeCreated', '3days');
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_age_10d,
                                        iconCls: 'p-mediamanager-filter_age-icon',
                                        checked: false,
                                        group: 'age',
                                        handler: function () {
                                            this.getFilesGrid().setTimeFilter('filterTimeCreated', '10days');
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_age_1m,
                                        iconCls: 'p-mediamanager-filter_age-icon',
                                        checked: false,
                                        group: 'age',
                                        handler: function () {
                                            this.getFilesGrid().setTimeFilter('filterTimeCreated', '1month');
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_age_6m,
                                        iconCls: 'p-mediamanager-filter_age-icon',
                                        checked: false,
                                        group: 'age',
                                        handler: function () {
                                            this.getFilesGrid().setTimeFilter('filterTimeCreated', '6month');
                                        },
                                        scope: this
                                    }
                                ]
                            },
                            {
                                text: this.strings.filter_by_age_modified,
                                iconCls: 'p-mediamanager-filter_age_modified-icon',
                                menu: [
                                    {
                                        text: this.strings.filter_age_3d,
                                        iconCls: 'p-mediamanager-filter_age_modified-icon',
                                        checked: false,
                                        group: 'age',
                                        handler: function () {
                                            this.getFilesGrid().setTimeFilter('filterTimeModified', '3days');
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_age_10d,
                                        iconCls: 'p-mediamanager-filter_age_modified-icon',
                                        checked: false,
                                        group: 'age',
                                        handler: function () {
                                            this.getFilesGrid().setTimeFilter('filterTimeModified', '10days');
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_age_1m,
                                        iconCls: 'p-mediamanager-filter_age_modified-icon',
                                        checked: false,
                                        group: 'age',
                                        handler: function () {
                                            this.getFilesGrid().setTimeFilter('filterTimeModified', '1month');
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_age_6m,
                                        iconCls: 'p-mediamanager-filter_age_modified-icon',
                                        checked: false,
                                        group: 'age',
                                        handler: function () {
                                            this.getFilesGrid().setTimeFilter('filterTimeModified', '6month');
                                        },
                                        scope: this
                                    }
                                ]
                            },
                            {
                                text: this.strings.filter_by_type,
                                iconCls: 'p-mediamanager-filter_document-icon',
                                menu: [
                                    {
                                        text: this.strings.filter_type_image,
                                        iconCls: 'p-mediamanager-filter_image-icon',
                                        handler: function () {
                                            this.getFilesGrid().setFilter('asset_type', Phlexible.mediamanager.IMAGE);
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_type_video,
                                        iconCls: 'p-mediamanager-filter_video-icon',
                                        handler: function () {
                                            this.getFilesGrid().setFilter('asset_type', Phlexible.mediamanager.VIDEO);
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_type_audio,
                                        iconCls: 'p-mediamanager-filter_audio-icon',
                                        handler: function () {
                                            this.getFilesGrid().setFilter('asset_type', Phlexible.mediamanager.AUDIO);
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_type_flash,
                                        iconCls: 'p-mediamanager-filter_flash-icon',
                                        handler: function () {
                                            this.getFilesGrid().setFilter('asset_type', Phlexible.mediamanager.FLASH);
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_type_document,
                                        iconCls: 'p-mediamanager-filter_document-icon',
                                        handler: function () {
                                            this.getFilesGrid().setFilter('asset_type', Phlexible.mediamanager.DOCUMENT);
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_type_archive,
                                        iconCls: 'p-mediamanager-filter_archive-icon',
                                        handler: function () {
                                            this.getFilesGrid().setFilter('asset_type', Phlexible.mediamanager.ARCHIVE);
                                        },
                                        scope: this
                                    },
                                    {
                                        text: this.strings.filter_type_other,
                                        iconCls: 'p-mediamanager-filter_other-icon',
                                        handler: function () {
                                            this.getFilesGrid().setFilter('asset_type', Phlexible.mediamanager.OTHER);
                                        },
                                        scope: this
                                    }
                                ]
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'mediamanager-uploadstatusbar',
                region: 'south'
            }/*,new Teamstorage.extendedsearch.ExtendedSearch({
             region: 'south',
             height: 200,
             minHeight: 200,
             collapsible: true,
             collapsed: true,
             split: true,
             floatable: false,
             titleCollapse: true,
             listeners: {
             search: {
             fn: this.onSearch,
             scope: this
             }
             }
             })*/
        ];

        Phlexible.mediamanager.MediamanagerPanel.superclass.initComponent.call(this);
    },

    getLocationBar: function () {
        return this.getComponent(0);
    },

    getMainPanel: function () {
        return this.getComponent(1);
    },

    getFolderTree: function () {
        return this.getMainPanel().getComponent(0);
    },

    getAttributesPanel: function () {
        return this.getMainPanel().getComponent(2);
    },

    getFilesGrid: function () {
        return this.getMainPanel().getComponent(1);
    },

    getStatusBar: function () {
        return this.getComponent(2);
    },

    createFolderTreeConfig: function () {
        return {
            xtype: 'mediamanager-foldertree',
            region: 'west',
            width: 200,
            start_folder_path: this.params.start_folder_path || false,
            split: true,
            minWidth: 100,
            maxWidth: 400,
            listeners: {
                render: function (c) {
                    this.getLocationBar().bindTree(c);
                },
                reload: this.onReload,
                folderChange: this.onFolderChange,
                nodedragover: function (e) {
                    console.log('nodedragover');
                    if (e.target.id == 'root') {
                        // root node can not be dragged
                        return false;
                    }
                    if (e.data.node) {
                        // tree -> tree move (folder -> folder)
                        if (e.target.id == 'trash') {
                            console.warn('id is trash')
                            return false;
                        }
                        if (e.target.id == e.data.node.parentNode.id) {
                            console.log(e.target.id == e.data.node.parentNode.id, e.target.id, e.data.node.parentNode.id, e.target, e.data.node.parentNode);
                            return false;
                        }
                    }
                    else {
                        // list -> tree move (file -> folder)
                        var selections = e.data.selections;

                        for (var i = 0; i < selections.length; i++) {
                            if (selections[i].data.folder_id == e.target.id) {
                                return false;
                            }
                        }
                    }
                    console.log('true');
                    return true;
                },
                beforenodedrop: this.onMove,
                scope: this
            }
        };
    },

    createFilesGridConfig: function () {
        return {
            xtype: 'mediamanager-filesgrid',
            region: 'center',
            border: true,
            viewMode: this.params.file_view || false,
            assetType: this.params.asset_type || false,
            start_file_id: this.params.start_file_id || false,
            viewConfig: {
                modeChange: function (e, mode) {
                    this.getMainPanel().getTopToolbar().items.get(this.tbarIndex.view).setIconClass('p-mediamanager-view_' + mode + '-icon');
                },
                scope: this
            },
            listeners: {
                fileChange: this.onFileChange,
                rowdblclick: this.onFileDblClick,
                filterChange: this.onFilterChange,
                downloadFiles: this.onDownloadFiles,
                downloadFile: this.onDownloadFile,
                render: this.initUploader,
                scope: this
            }
        };
    },

    createDropper: function (c) {
        var div = document.createElement('div');
        div.style.position = 'absolute';
        div.style.left = '10px';
        div.style.right = '10px';
        div.style.bottom = '10px';
        div.style.height = '30px';
        div.style.border = '2px dashed lightgrey';
        div.style.textAlign = 'center';
        div.style.verticalAlign = 'center';
        div.style.lineHeight = '30px';
        div.style.backgroundColor = '#f3f3f3';
        div.style.color = 'gray';
        div.style.opacity = 0.8;
        div.style.padding = '10px';
        div.id = 'dropper';
        var text = document.createTextNode('Drop files here for quick upload');
        div.appendChild(text);
        this.dropper = c.body.dom.appendChild(div);

        plupload.addEvent(div, 'dragenter', function (e) {
            div.style.borderColor = 'lightblue';
        });
        plupload.addEvent(div, 'dragleave', function (e) {
            div.style.borderColor = 'lightgrey';
        });
        plupload.addEvent(div, 'drop', function (e) {
            div.style.borderColor = 'lightgrey';
        });
        plupload.addEvent(c.body.dom, 'drop', function (e) {
            e.preventDefault();
        });

        return div;
    },

    createAttributesPanelConfig: function () {
        return {
            xtype: 'mediamanager-attributespanel',
            region: 'east',
            width: 290,
            collapsible: true,
            collapsed: this.params.hide_properties || false,
            mode: this.mode,
            listeners: {
                versionSelect: function (file_id, file_version, file_name, folder_id, document_type_key, asset_type) {
                    if (this.mode == 'select') {
                        this.fireEvent('fileSelect', file_id, file_version, file_name, folder_id);
                    }
                    else {
                        var w = new Phlexible.mediamanager.FileDetailWindow({
                            iconCls: document_type_key,
                            file_id: file_id,
                            file_version: file_version,
                            file_name: file_name,
                            document_type_key: document_type_key,
                            asset_type: asset_type
                        });
                        w.show();
                    }
                },
                versionDownload: this.onDownloadFile,
                scope: this
            }
        };
    },

    initUploader: function () {
        //if (Phlexible.config.mediamanager.upload.disable_flash) {
        //    return;
        //}
        var sessionID = Phlexible.Cookie.get('phlexible');
        if (!sessionID) {
            Phlexible.console.warn("No session ID, upload via flash _will_ fail!");
        }

        var addBtn = this.getMainPanel().getTopToolbar().items.items[this.tbarIndex.upload];
        /*
        var btn = addBtn.el.child('button');
        var suoID = Ext.id();
        var p = btn.parent();
        var em = p.createChild({
            tag: 'em',
            style: {
                position: 'relative',
                display: 'block'
            }
        });
        addBtn.el.child('button').appendTo(em);
        em.createChild({
            tag: 'div',
            id: suoID,
            style: 'display: block; position: absolute; top: 0pt; left: 0pt;'
        });
        */

        var dropper = this.createDropper(this.getFilesGrid());

        var uploader = new plupload.Uploader({
            runtimes: 'html5,flash,silverlight,html4',
            file_data_name: 'Filedata',
            browse_button: addBtn.id,
            //container: 'container',
            filters: {
                max_file_size: '2000mb'
            },
            url: Phlexible.Router.generate('mediamanager_upload'),
            flash_swf_url: Phlexible.component('/phlexiblemediamanager/plupload/Moxie.swf'),
            silverlight_xap_url: Phlexible.component('/phlexiblemediamanager/plupload/Moxie.xap'),
            drop_element: dropper,
            multipart: true,
            multipart_params: {
            }
        });

        uploader.bind('Init', function (up, params) {
            Phlexible.console.debug('uploader::Init', 'runtime:' + params.runtime, 'features:', up.features, 'caps:', up.caps);

            if (!up.features.dragdrop) {
                dropper.style.visibility = 'hidden';
            }

            if (params.runtime === 'flash') {
                up.params.multipart_params.sid = sessionID;
            }
        }, this);

        uploader.bind('FilesAdded', function (up, files) {
            up.refresh(); // Reposition Flash/Silverlight

            up.settings.multipart_params.folder_id = this.folder_id;
        }, this);

        uploader.bind('QueueChanged', function (up) {
            Phlexible.console.debug('uploader::QueueChanged');
            if (up.state == plupload.STOPPED) {
                up.start();
            }
        }, this);

        uploader.bind('Error', function (up, err) {
            up.refresh(); // Reposition Flash/Silverlight
            this.getFilesGrid().getStore().reload();
        }, this);

        uploader.bind('ChunkUploaded', function (up, file, info) {
            Phlexible.console.debug('uploader::ChunkUploaded', 'id:' + file.id, 'info:', info);
        }, this);

        uploader.bind('FileUploaded', function (up, file, info) {
            this.onUploadComplete();
        }, this);

        this.getStatusBar().bindUploader(uploader);

        uploader.init();

        window.up = uploader;
    },

    onReload: function () {
        this.getFolderTree().getRoot().reload();
        this.getFilesGrid().getStore().reload();
    },

    onFolderChange: function (folder_id, folder_name, node) {
        this.folder_id = folder_id;
        this.site_id = node.attributes.site_id;

        if (node.id == 'root') return;
        if (this.dndFormInput) document.getElementById('folder_id').value = folder_id;

        //this.locationBar.setNode(node);
        if (this.getFolderTree().checkRights(Phlexible.mediamanager.Rights.FOLDER_CREATE)) {
            this.getComponent(1).getTopToolbar().items.get(this.tbarIndex.newFolder).enable();
        } else {
            this.getComponent(1).getTopToolbar().items.get(this.tbarIndex.newFolder).disable();
        }
        if (this.getFolderTree().checkRights(Phlexible.mediamanager.Rights.FILE_CREATE)) {
            this.getComponent(1).getTopToolbar().items.get(this.tbarIndex.upload).enable();
        } else {
            this.getComponent(1).getTopToolbar().items.get(this.tbarIndex.upload).disable();
        }

        if (node.attributes.versions) {
            this.getAttributesPanel().getComponent(2).getComponent(0).show();
        } else {
            this.getAttributesPanel().getComponent(2).getComponent(0).hide();
        }

        if (this.getFolderTree().checkRights(Phlexible.mediamanager.Rights.FILE_DOWNLOAD)) {
            this.getComponent(1).getTopToolbar().items.get(this.tbarIndex.download).enable();
            this.getComponent(1).getTopToolbar().items.get(this.tbarIndex.download).menu.items.get(1).disable();
        } else {
            this.getComponent(1).getTopToolbar().items.get(this.tbarIndex.download).disable();
        }

//        if((!this.getFolderTree().checkRights('folder_modify') || !this.getFolderTree().checkRights('file_modify')) && (!this.getFolderTree().checkRights('folder_create') || !this.getFolderTree().checkRights('file_create'))) {
//            this.getComponent(1).getTopToolbar().items.get(8).disable();
//        } else {
//            this.getComponent(1).getTopToolbar().items.get(8).enable();
//
//            if(this.getFolderTree().checkRights('folder_modify') && this.getFolderTree().checkRights('file_modify')) {
//                this.getComponent(1).getTopToolbar().items.get(8).menu.items.get(0).enable();
//            } else {
//                this.getComponent(1).getTopToolbar().items.get(8).menu.items.get(0).disable();
//            }
//
//            if(this.getFolderTree().checkRights('folder_create') && this.getFolderTree().checkRights('file_create')) {
//                this.getComponent(1).getTopToolbar().items.get(8).menu.items.get(1).enable();
//            } else {
//                this.getComponent(1).getTopToolbar().items.get(8).menu.items.get(1).disable();
//            }
//        }

        var folder_rights = node.attributes.rights;

        this.getFilesGrid().loadFiles(node.attributes.site_id, folder_id, folder_name, folder_rights);

        this.getAttributesPanel().empty();
        this.getAttributesPanel().setFolderRights(folder_rights);
        this.getAttributesPanel().loadFolderMeta(folder_id);

        if (node && node.attributes && node.attributes.used_in) {
            this.getAttributesPanel().setFolderUsage(node.attributes.used_in);
        }

        Phlexible.mediamanager.lastParams = {
            start_folder_path: node.getPath()
        };
    },

    onFileChange: function (r, sm) {
        this.getAttributesPanel().load(r);

        if (sm.getSelections().length >= 1 && this.getFolderTree().checkRights(Phlexible.mediamanager.Rights.FILE_DOWNLOAD)) {
            this.getComponent(1).getTopToolbar().items.get(this.tbarIndex.download).menu.items.get(1).enable();
        } else {
            this.getComponent(1).getTopToolbar().items.get(this.tbarIndex.download).menu.items.get(1).disable();
        }
    },

    onFileDblClick: function (grid, rowIndex) {
        var r = grid.getStore().getAt(rowIndex);
        if (this.mode == 'select') {
            var file_id = r.data.id;
            var file_version = r.data.version;
            var file_name = r.data.name;
            var folder_id = r.data.folder_id;
            this.fireEvent('fileSelect', file_id, file_version, file_name, folder_id);
        } else {
            var w = new Phlexible.mediamanager.FileDetailWindow({
                file_id: r.data.id,
                file_version: r.data.version,
                file_name: r.data.name,
                document_type_key: r.data.document_type_key,
                asset_type: r.data.asset_type,
                cache: r.data.cache,
                rights: grid.folder_rights
            });
            w.show();
        }
    },

    onFilterChange: function (e, key, value) {
        var s;
        switch (key) {
            case 'create_user_id':
                s = 'mine';
                break;

            case 'modify_user_id':
                s = 'mine_modified';
                break;

            case 'filterTimeCreated':
                s = 'age';
                break;

            case 'filterTimeModified':
                s = 'age_modified';
                break;

            case 'asset_type':
                s = value.toLowerCase();
                break;

            default:
                s = 'no';
        }
        this.getComponent(1).getTopToolbar().items.get(this.tbarIndex.filter).setIconClass('p-mediamanager-filter_' + s + '-icon');
    },

    onNewFolder: function () {
        this.getFolderTree().showNewFolderWindow();
    },

    onMove: function (e) {
        if (e.data.selections) {
            this.onMoveFile(e);
        }
    },

    onMoveFile: function (e) {
        var fileIDs = [];
        for (var i = 0; i < e.data.selections.length; i++) {
            fileIDs.push(e.data.selections[i].data.id);
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_move'),
            method: 'post',
            params: {
                folderID: e.target.id,
                fileIDs: Ext.encode(fileIDs)
            },
            success: this.onMoveFileSuccess,
            scope: this
        });
    },

    onMoveFileSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.getFilesGrid().getStore().reload();
            if (data.data.length) {
                var msg = "The following file(s) have not been moved, since an identical file already exists in the target folder:<br /><br />";
                for (var i = 0; i < data.data.length; i++) {
                    msg += '- ' + data.data[i] + "<br />";
                }
                Ext.Msg.alert('Warning', msg);
            }
        } else {
            Ext.Msg.alert('Failure', data.msg);
        }
    },

    onUploadComplete: function () {
        this.reloadFilesSortedLatest();
        if (!this.uploadChecker) {
            this.uploadChecker = new Phlexible.mediamanager.UploadChecker({
                listeners: {
                    reload: function() {
                        this.reloadFilesSortedLatest();
                    },
                    scope: this
                }
            });
        }
        this.uploadChecker.check();
    },

    reloadFilesSortedLatest: function() {
        var store = this.getFilesGrid().getStore();
        if (Phlexible.Config.get('mediamanager.upload.enable_upload_sort')) {
            if (!store.lastOptions) store.lastOptions = {};
            if (!store.lastOptions.params) store.lastOptions.params = {};
            store.lastOptions.params.start = 0;
            var sort = store.getSortState();
            if (sort.field != 'create_time' || sort.direction != 'DESC') {
                store.sort('create_time', 'DESC');
            }
            else {
                store.reload();
            }
        }
        else {
            store.reload();
        }

    },

    onSearch: function (search_values) {
        this.getFilesGrid().loadSearch(search_values);
    },

    onDownloadFolder: function () {
        var selFolder = this.getFolderTree().getSelectionModel().getSelectedNode();
        var folder_id = selFolder.id;
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_download_folder_zip'),
            params: {
                folder_id: folder_id
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success && data.data.filename) {
                    document.location.href = Phlexible.Router.generate('mediamanager_download_zip', {filename: data.data.filename});
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    onDownloadFiles: function () {
        var selections = this.getFilesGrid().getSelectionModel().getSelections();
        var file_ids = [];
        for (var i = 0; i < selections.length; i++) {
            file_ids.push(selections[i].data.id);
        }
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_download_file_zip'),
            params: {
                data: Ext.encode(file_ids)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success && data.data.filename) {
                    document.location.href = Phlexible.Router.generate('mediamanager_download_zip', {filename: data.data.filename});
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    onDownloadFile: function (file_id, file_version) {
        if (!file_id) {
            file_id = this.getFilesGrid().getSelectionModel().getSelected().data.id;
        }

        var href = Phlexible.Router.generate('mediamanager_download_file', {id: file_id});

        if (file_version) {
            href += '/' + file_version;
        }

        document.location.href = href;
    }
});

Ext.reg('mediamanager-mainpanel', Phlexible.mediamanager.MediamanagerPanel);
