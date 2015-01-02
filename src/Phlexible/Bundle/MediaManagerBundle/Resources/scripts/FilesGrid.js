Ext.ns('Phlexible.mediamanager');

Phlexible.mediamanager.ToolbarHidePlugin = Ext.extend(Object, {
    init: function (panel) {
        this.panel = panel;
        panel.on('render', this.onPanelRender, this);
    },

    onPanelRender: function (panel) {
        var b = panel.getTopToolbar();
        if (b) {
            b.on({
                beforehide: this.onToolbarBeforeChange,
                beforeshow: this.onToolbarBeforeChange,
                hide: this.onToolbarChange,
                show: this.onToolbarChange,
                scope: this
            });
        }
        b = panel.getBottomToolbar();
        if (b) {
            b.on({
                beforehide: this.onToolbarBeforeChange,
                beforeshow: this.onToolbarBeforeChange,
                hide: this.onToolbarChange,
                show: this.onToolbarChange,
                scope: this
            });
        }
    },

    onToolbarBeforeChange: function (b) {
        var height = this.panel.body.getSize().height;
        this.height = height + b.getSize().height;
    },

    onToolbarChange: function (b) {
        this.panel.body.setHeight(this.height);
        this.panel.doLayout();
    }
});

Phlexible.mediamanager.FilesGrid = Ext.extend(Ext.grid.GridPanel, {

    cls: 'p-mediamanager-files-grid',
    strings: Phlexible.mediamanager.Strings,
    enableDragDrop: true,
    ddGroup: 'mediamanager',

    folder_rights: {},

    // private
    initComponent: function () {
        this.addEvents(
            /**
             * @event fileChange
             * Fires when a File is selected
             * @param {Record} r The record of the selected File.
             */
            'fileChange',
            /**
             * @event filterChange
             * Fires when a Filter is changed / cleard
             * @param {string} filterKey The key of the filter
             * @param {string} filterValue The value of the filter
             */
            'filterChange',
            /**
             * @event downloadFiles
             * Fires when one or more Files need to be downloaded
             */
            'downloadFiles'
        );

        this.activeFilter = {};

        if (this.assetType) {
            this.activeFilter['assetType'] = this.assetType;
        }

        if (this.documenttypes) {
            this.activeFilter['documenttypes'] = this.documenttypes;
        }

        this.store = new Ext.data.GroupingStore({
            proxy: new Ext.data.HttpProxy({
                url: Phlexible.Router.generate('mediamanager_file_list')
            }),
            reader: new Ext.data.JsonReader({
                root: 'files',
                totalProperty: 'totalCount',
                //id: 'threadid',
                fields: Phlexible.mediamanager.model.File
            }),
            sortInfo: {field: "name", direction: "ASC"},
            baseParams: {
                limit: Phlexible.Config.get('mediamanager.files.num_files'),
                filter: Ext.encode(this.activeFilter)
            },
            remoteSort: true,
            listeners: {
                load: function (store) {
                    if (this.start_file_id) {
                        var index = store.find('id', this.start_file_id);
                        this.start_file_id = false;

                        if (index != -1) {
                            var r = store.getAt(index);
                            this.selectedRecordDummy = r;
                            this.selModel.selectRecords([r]);
                            this.fireEvent('fileChange', r, this.selModel);
                        }
                    }
                },
                scope: this
            }
        });

        this.selModel = new Ext.grid.RowSelectionModel({
            listeners: {
                rowselect: this.onSelectionChange,
                scope: this
            }
        });

        this.columns = [
            {
                header: this.strings.name,
                dataIndex: 'name',
                sortable: true,
                groupable: false,
                renderer: this.nameRenderer.createDelegate(this),
                width: 150
            },
            {
                header: this.strings.folder,
                dataIndex: 'folder',
                sortable: false,
                hidden: true,
                width: 100
            },
            {
                header: this.strings.type,
                dataIndex: 'document_type_key',
                sortable: true,
                renderer: this.typeRenderer,
                width: 80
            }/*,{
             header: this.strings.asset,
             dataIndex: 'asset_type',
             sortable: true,
             hidden: true,
             width: 80
             }*/,
            {
                header: this.strings.version,
                dataIndex: 'version',
                sortable: true,
                width: 30
            },
            {
                header: this.strings.size,
                dataIndex: 'size',
                sortable: true,
                renderer: Phlexible.Format.size,
                width: 30
            },
            {
                header: this.strings.created_by,
                dataIndex: 'create_user_id',
                sortable: true,
                renderer: this.createdByRenderer,
                width: 60
            },
            {
                header: this.strings.create_date,
                dataIndex: 'create_time',
                sortable: true,
                renderer: Phlexible.Format.date,
                width: 60
            },
            {
                header: this.strings.modified_by,
                dataIndex: 'modify_user_id',
                sortable: true,
                hidden: true,
                renderer: this.modifiedByRenderer,
                width: 60
            },
            {
                header: this.strings.modify_date,
                dataIndex: 'modify_time',
                sortable: true,
                renderer: Phlexible.Format.date,
                hidden: true,
                width: 60
            }
        ];

        this.view = new Phlexible.mediamanager.CustomGridView({
            forceFit: true,
            hideGroupedColumn: true,
            emptyText: this.strings.no_files_in_this_folder,
            defaultMode: this.viewMode || Phlexible.Config.get('mediamanager.files.view')
            //stateKey: "lastResourcesViewMode",
            //tpl: new Phlexible.mediamanager.GridTemplate()
        });

        this.bbar = new Ext.PagingToolbar({
            store: this.store,
            border: false,
            pageSize: this.store.baseParams.limit,
            displayInfo: true,
            items: ['-', new Ext.Slider({
                width: 104,
                value: this.store.baseParams.limit,
                increment: 5,
                minValue: 5,
                maxValue: 250,
                plugins: new Ext.ux.SliderTip(),
                listeners: {
                    drag: function (slider) {
                        this.getBottomToolbar().items.items[13].setText(slider.getValue());
                    },
                    changecomplete: function (slider, value) {
                        var pager = this.getBottomToolbar();
                        pager.pageSize = value;
                        pager.items.items[13].setText(value);
                        this.store.baseParams.limit = value;

                        if (this.store.totalLength < value) {
                            this.store.reload({
                                params: {
                                    start: 0
                                }
                            });
                            return;
                        } else if (pager.cursor % value !== 0) {
                            this.store.reload({
                                params: {
                                    start: Math.floor(pager.cursor / value) * value
                                }
                            });
                            return;
                        }
                        this.store.reload();
                    },
                    scope: this
                }
            }), {
                text: this.store.baseParams.limit
            }]
        });

        this.populateContextMenuItems();

        this.contextMenu = new Ext.menu.Menu({
            items: this.contextMenuItems
        });

        this.plugins = [new Phlexible.mediamanager.ToolbarHidePlugin()];

        this.addListener({
            render: function (c) {
                var firstGridDropTargetEl = c.getView().scroller.dom;
                var firstGridDropTarget = new Ext.dd.DropTarget(firstGridDropTargetEl, {
                    ddGroup: 'versions',
                    notifyDrop: function (ddSource, e, data) {
                        Phlexible.console.log(arguments);
                        return true;
//                                var records =  ddSource.dragData.selections;
//                                Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
//                                firstGrid.store.add(records);
//                                firstGrid.store.sort('name', 'ASC');
//                                return true
                    }
                });
            },
            rowcontextmenu: this.onRowContextMenu,
            scope: this
        });

        Phlexible.mediamanager.FilesGrid.superclass.initComponent.call(this);
    },

    populateContextMenuItems: function () {
        this.contextMenuItemIndex = {
            name: 0,
            rename: 2,
            'delete': 4,
            hide: 5,
            show: 6,
            download: 8,
            properties: 10,
            _last: 10
        };

        this.contextMenuItems = [
            {
                // 0
                cls: 'x-btn-text-icon-bold',
                iconCls: 'p-mediamanager-file-icon',
                text: '.'
            },
            '-',
            {
                // 2
                text: this.strings.rename_file,
                iconCls: 'p-mediamanager-file_edit-icon',
                handler: this.showRenameFileWindow,
                scope: this
            },
            '-',
            {
                // 4
                text: this.strings.delete_file,
                iconCls: 'p-mediamanager-file_delete-icon',
                handler: this.showDeleteFileWindow,
                scope: this
            },
            {
                // 5
                text: this.strings.hide_file,
                iconCls: 'p-mediamanager-file_delete-icon',
                handler: this.showHideFileWindow,
                scope: this,
                hidden: true
            },
            {
                // 6
                text: this.strings.show_file,
                iconCls: 'p-mediamanager-file_delete-icon',
                handler: this.showFiles,
                scope: this,
                hidden: true
            },
            '-',
            {
                // 8
                text: this.strings.download_file,
                iconCls: 'p-mediamanager-download-icon',
                handler: this.download,
                scope: this
            },
            '-',
            {
                // 10
                text: this.strings.properties,
                iconCls: 'p-mediamanager-file_properties-icon',
                handler: this.showDetailWindow,
                scope: this
            }
        ];
    },

    checkRights: function (right) {
        return this.folder_rights.indexOf(right) !== -1;
    },

    getDragDropText: function () {
        var sel = this.getSelectionModel().getSelections();
        if (sel.length != 1) {
            return Phlexible.mediamanager.templates.DragMulti.apply(sel);
        } else {
            return Phlexible.mediamanager.templates.DragSingle.apply(sel);
        }
    },

    loadFiles: function (site_id, folder_id, folder_name, folder_rights) {
        this.site_id = site_id;
        this.folder_id = folder_id;
        this.folder_rights = folder_rights;

        this.store.baseParams.site_id = this.site_id;
        this.store.baseParams.folder_id = this.folder_id;

        this.store.load();

        this.setTitle(this.strings.files_for_folder + ' "' + folder_name + '"');
    },

    loadSearch: function (search_values) {
        var params;
        if (search_values) {
            if (search_values.below) {
                search_values.below = this.folder_id;
            }
            params = {
                searchValues: Ext.encode(search_values)
            };

//            this.store.groupBy('folder');
        } else {
            params = {
                folderID: this.folder_id
            };

//            this.store.clearGrouping();
        }

        params.start = 0;

        this.store.load({
            params: params
        });

        this.setTitle(this.strings.search_results);
    },

    setAssetTypeFilter: function(assetType) {
        this.setFilter('assetType', assetType);
    },

    setUserFilter: function(key, value) {

    },

    setTimeFilter: function (key, value) {
        var time = new Date();

        switch (value) {
            case "1day":
                time.add(Date.DAY, -1)

                break;

            case "2days":
                time.add(Date.DAY, -2)
                break;

            case "1week":
                time.add(Date.DAY, -7)
                break;

            case "1month":
                time.add(Date.MONTH, -1)
                break;

            case "6months":
                time.add(Date.MONTH, -6)
                break;

            default:
                return;
        }

        if (time && key) {
            this.setFilter(key, time.format('U'));
        }
    },

    setFilter: function (key, value) {
        this.activeFilter[key] = value;

        this.store.baseParams['filter'] = Ext.encode(this.activeFilter);
        this.store.reload();

        this.fireEvent('filterChange', this, this.activeFilter);
    },

    clearFilter: function () {
        this.activeFilter = {};

        this.store.baseParams['filter'] = Ext.encode(this.activeFilter);
        this.store.reload();

        this.fireEvent('filterChange', this, this.activeFilter);
    },

    nameRenderer: function (name, e, r) {
        var documentTypeClass = Phlexible.documenttypes.DocumentTypes.getClass(r.data.document_type_key) || Phlexible.documenttypes.DocumentTypes.getClass('_unknown');
        documentTypeClass += "-small";

        var prefix = '';
        var style = '';

        prefix += Phlexible.mediamanager.Bullets.getWithTrailingSpace(r.data);

        if (r.data.hidden) {
            style += 'text-decoration: line-through;';
        }
        return '<span class="m-mimetype ' + documentTypeClass + '" style="' + style + '"><div>' + prefix + name + '<\/div><\/span>';
    },

    typeRenderer: function (name, e, r) {
        return r.data.document_type;
    },

    createdByRenderer: function (name, e, r) {
        return r.data.create_user;
    },

    modifiedByRenderer: function (name, e, r) {
        return r.data.modify_user;
    },

    showRenameFileWindow: function () {
        var selFile = this.selModel.getSelected();

        var w = new Phlexible.mediamanager.RenameFileWindow({
            values: {
                file_name: selFile.data.name
            },
            submitParams: {
                file_id: selFile.data.id
            },
            listeners: {
                success: this.onRename,
                scope: this
            }
        });

        w.show();
    },

    showDetailWindow: function () {
        var selFile = this.selModel.getSelected();

        var w = new Phlexible.mediamanager.FileDetailWindow({
            file_id: selFile.data.id,
            file_version: selFile.data.version,
            file_name: selFile.data.name,
            document_type_key: selFile.data.document_type_key,
            asset_type: selFile.data.asset_type,
            cache: selFile.data.cache,
            rights: this.folder_rights
        });
        w.show();
    },

    showDeleteFileWindow: function () {
        var fileArr = this.getSelectionModel().getSelections();

        if (fileArr.length > 1) {
            var txt = this.strings.delete_files_warning;
        } else {
            var txt = this.strings.delete_file_warning;
        }

        Ext.MessageBox.confirm(this.strings.confirm, txt, function (btn, e, x, fileArr) {
            if (btn == 'yes') {
                this.deleteFiles(fileArr);
            }
        }.createDelegate(this, [fileArr], true));
    },

    deleteFiles: function (file) {
        var fileID = '';
        for (var i = 0; i < file.length; i++) {
            fileID += (fileID ? ',' : '') + file[i].get('id');
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_delete'),
            params: {
                site_id: this.site_id,
                file_id: fileID
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data.success) {
                    Ext.MessageBox.alert('Status', 'Delete failed: ' + data.msg);
                }

                this.store.reload();
            },
            scope: this
        });
    },

    showHideFileWindow: function () {
        var fileArr = this.getSelectionModel().getSelections();

        if (fileArr.length > 1) {
            var txt = this.strings.hide_files_warning;
        } else {
            var txt = this.strings.hide_file_warning;
        }

        Ext.MessageBox.confirm(this.strings.confirm, txt, function (btn, e, x, fileArr) {
            if (btn == 'yes') {
                this.hideFiles(fileArr);
            }
        }.createDelegate(this, [fileArr], true));
    },

    hideFiles: function (file) {
        var fileID = '';
        for (var i = 0; i < file.length; i++) {
            fileID += (fileID ? ',' : '') + file[i].get('id');
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_hide'),
            params: {
                site_id: this.site_id,
                file_id: fileID
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data.success) {
                    Ext.MessageBox.alert('Status', 'Hide failed: ' + data.msg);
                }

                this.store.reload();
            },
            scope: this
        });
    },

    showFiles: function (file) {
        var fileArr = this.getSelectionModel().getSelections();
        var fileID = '';
        for (var i = 0; i < fileArr.length; i++) {
            fileID += (fileID ? ',' : '') + fileArr[i].get('id');
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_show'),
            params: {
                site_id: this.site_id,
                file_id: fileID
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data.success) {
                    Ext.MessageBox.alert('Status', 'Show failed: ' + data.msg);
                }

                this.store.reload();
            },
            scope: this
        });
    },

    onSelectionChange: function (sm, index, r) {
        if (this.selectedRecordDummy != r) {
            this.selectedRecordDummy = r;

            this.fireEvent('fileChange', r, sm);
        }
    },

    onRowContextMenu: function (grid, rowIndex, event) {
        event.stopEvent();

        var contextmenu = this.contextMenu;

        var r = grid.getStore().getAt(rowIndex);
        var sm = grid.getSelectionModel();
//        Phlexible.console.log(this.folder_rights);
        if (!sm.isSelected(r)) {
            sm.selectRow(rowIndex);
        }
        var selections = sm.getSelections();
        if (selections.length < 1) {
            return;
        } else if (selections.length > 1) {
            contextmenu.items.items[this.contextMenuItemIndex.name].setText('[' + String.format(this.strings.x_files, selections.length) + ']');

            contextmenu.items.items[this.contextMenuItemIndex.rename].disable();
            //if(this.folder_rights.file_modify == '1') {
            //    contextmenu.items.items[this.contextMenuItemIndex.rename].enable();
            //} else {
            //    contextmenu.items.items[this.contextMenuItemIndex.rename].disable();
            //}

            var used = 0;
            var hasHidden = false;
            var allHidden = true;
            var hasPresent = false;
            var allPresent = true;

            for (var i = 0; i < selections.length; i++) {
                if (selections[i].data.used) {
                    used |= selections[i].data.used;
                }
                if (selections[i].data.hidden) {
                    hasHidden = true;
                } else {
                    allHidden = false;
                }
                if (selections[i].data.present) {
                    hasPresent = true;
                } else {
                    allPresent = false;
                }
            }
            var deletePolicy = Phlexible.Config.get('mediamanager.delete_policy');

            contextmenu.items.items[this.contextMenuItemIndex['delete']].setText(this.strings.delete_files);
            contextmenu.items.items[this.contextMenuItemIndex.hide].setText(this.strings.hide_files);
            contextmenu.items.items[this.contextMenuItemIndex.show].setText(this.strings.show_files);

            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) &&
                (!used ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_OLD && (used == 1 || used == 2 || used == 3)) ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_ALL))) {
                contextmenu.items.items[this.contextMenuItemIndex['delete']].enable();
                contextmenu.items.items[this.contextMenuItemIndex['delete']].show();
                contextmenu.items.items[this.contextMenuItemIndex.hide].disable();
                contextmenu.items.items[this.contextMenuItemIndex.hide].hide();
            }
            else if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) == '1' &&
                (deletePolicy === Phlexible.mediamanager.DeletePolicy.HIDE_OLD && (used == 1 || used == 2 || used == 3))) {
                contextmenu.items.items[this.contextMenuItemIndex['delete']].disable();
                contextmenu.items.items[this.contextMenuItemIndex['delete']].hide();
                if (!allHidden) {
                    contextmenu.items.items[this.contextMenuItemIndex.hide].enable();
                    contextmenu.items.items[this.contextMenuItemIndex.hide].show();
                } else {
                    contextmenu.items.items[this.contextMenuItemIndex.hide].disable();
                    contextmenu.items.items[this.contextMenuItemIndex.hide].hide();
                }
            }
            else {
                contextmenu.items.items[this.contextMenuItemIndex['delete']].disable();
                contextmenu.items.items[this.contextMenuItemIndex['delete']].show();
                contextmenu.items.items[this.contextMenuItemIndex.hide].disable();
                contextmenu.items.items[this.contextMenuItemIndex.hide].hide();
            }

            if (hasHidden) {
                contextmenu.items.items[this.contextMenuItemIndex.show].enable();
                contextmenu.items.items[this.contextMenuItemIndex.show].show();
            } else {
                contextmenu.items.items[this.contextMenuItemIndex.show].disable();
                contextmenu.items.items[this.contextMenuItemIndex.show].hide();
            }

            contextmenu.items.items[this.contextMenuItemIndex.download].setText(this.strings.download_files);
            contextmenu.items.items[this.contextMenuItemIndex.download].setIconClass('p-mediamanager-download_files-icon');
            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DOWNLOAD) == '1' && hasPresent) {
                contextmenu.items.items[this.contextMenuItemIndex.download].enable();
            } else {
                contextmenu.items.items[this.contextMenuItemIndex.download].disable();
            }
        }
        else {
            contextmenu.items.items[this.contextMenuItemIndex.name].setText(r.get('name'));

            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_MODIFY) == '1') {
                contextmenu.items.items[this.contextMenuItemIndex.rename].enable();
            } else {
                contextmenu.items.items[this.contextMenuItemIndex.rename].disable();
            }

            var used = r.data.used;
            var deletePolicy = Phlexible.Config.get('mediamanager.delete_policy');

            contextmenu.items.items[this.contextMenuItemIndex['delete']].setText(this.strings.delete_file);
            contextmenu.items.items[this.contextMenuItemIndex.hide].setText(this.strings.hide_file);
            contextmenu.items.items[this.contextMenuItemIndex.show].setText(this.strings.show_file);

            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) == '1' &&
                (!used ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_OLD && (used == 1 || used == 2 || used == 3)) ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_ALL))) {
                contextmenu.items.items[this.contextMenuItemIndex['delete']].enable();
                contextmenu.items.items[this.contextMenuItemIndex['delete']].show();
                contextmenu.items.items[this.contextMenuItemIndex.hide].disable();
                contextmenu.items.items[this.contextMenuItemIndex.hide].hide();
            }
            else if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) == '1' &&
                (deletePolicy === Phlexible.mediamanager.DeletePolicy.HIDE_OLD && (used == 1 || used == 2 || used == 3))) {
                contextmenu.items.items[this.contextMenuItemIndex['delete']].disable();
                contextmenu.items.items[this.contextMenuItemIndex['delete']].hide();
                if (!r.data.hidden) {
                    contextmenu.items.items[this.contextMenuItemIndex.hide].enable();
                    contextmenu.items.items[this.contextMenuItemIndex.hide].show();
                }
                else {
                    contextmenu.items.items[this.contextMenuItemIndex.hide].disable();
                    contextmenu.items.items[this.contextMenuItemIndex.hide].hide();
                }
            }
            else {
                contextmenu.items.items[this.contextMenuItemIndex['delete']].disable();
                contextmenu.items.items[this.contextMenuItemIndex['delete']].show();
                contextmenu.items.items[this.contextMenuItemIndex.hide].disable();
                contextmenu.items.items[this.contextMenuItemIndex.hide].hide();
            }

            if (r.data.hidden) {
                contextmenu.items.items[this.contextMenuItemIndex.show].enable();
                contextmenu.items.items[this.contextMenuItemIndex.show].show();
            } else {
                contextmenu.items.items[this.contextMenuItemIndex.show].disable();
                contextmenu.items.items[this.contextMenuItemIndex.show].hide();
            }

            contextmenu.items.items[this.contextMenuItemIndex.download].setText(this.strings.download_file);
            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DOWNLOAD) == '1' && selections[0].data.present) {
                contextmenu.items.items[this.contextMenuItemIndex.download].enable();
            } else {
                contextmenu.items.items[this.contextMenuItemIndex.download].disable();
            }
        }

        var coords = event.getXY();
        contextmenu.showAt([coords[0], coords[1]]);
    },

    onRename: function () {
        this.store.reload();
    },

    download: function () {
        var sel = this.getSelectionModel().getSelections();

        if (sel.length > 1) {
            this.fireEvent('downloadFiles');
        } else if (sel.length === 1) {
            this.fireEvent('downloadFile');
        }
    }

});

Ext.reg('mediamanager-filesgrid', Phlexible.mediamanager.FilesGrid);