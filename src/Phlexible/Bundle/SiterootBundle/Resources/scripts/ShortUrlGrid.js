Phlexible.siteroots.ShortUrlGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    title: Phlexible.siteroots.Strings.short_urls,
    strings: Phlexible.siteroots.Strings,
    border: false,
    autoExpandColumn: 'path',

    siterootId: 0,

    viewConfig: {
        emptyText: Phlexible.siteroots.Strings.no_short_urls
    },

    initComponent: function () {

        // Create RowActions Plugin
        this.actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
//          autoWidth:false,
            width: 150,
            actions: [
                {
                    iconCls: 'p-siteroot-delete-icon',
                    tooltip: this.strings.delete,
                    callback: function (grid, record, action, row, col) {
                        var r = grid.store.getAt(row);

                        Ext.MessageBox.confirm(this.strings.remove, this.strings.sure, function (btn) {
                            if (btn === 'yes') {
                                this.onDeleteUrl(r);
                            }
                        }, this);
                    }.createDelegate(this)
                }
            ],
            getData: function (value, cell, record, row, col, store) {
                switch (record.get('handler')) {
                    case 'Siteroot':
                        record.data.hide_config = false;
                        break;

                    default:
                        record.data.hide_config = true;
                        break;
                }

                return record.data || {};
            }
        });

        this.tbar = [
            {
                text: this.strings.add_short_url,
                iconCls: 'p-siteroot-add-icon',
                handler: this.onAddUrl,
                scope: this
            }
        ];

        this.store = new Ext.data.JsonStore({
            fields: Phlexible.siteroots.ShortUrlRecord
        });

        this.columns = [
            {
                id: 'id',
                header: 'ID',
                hidden: true,
                dataIndex: 'id'
            },
            {
                id: 'path',
                header: this.strings.path,
                dataIndex: 'path',
                sortable: true,
                editor: new Ext.form.TextField({
                    //allowBlank: true
                })
            },
            {
                id: 'language',
                header: this.strings.language,
                dataIndex: 'language',
                sortable: true,
                renderer: this.renderLanguage.createDelegate(this),
                width: 100,
                editor: new Ext.ux.IconCombo({
                    allowBlank: true,
                    editable: false,
                    triggerAction: 'all',
                    selectOnFocus: true,
                    mode: 'local',
                    displayField: 'title',
                    valueField: 'language',
                    iconClsField: 'icon',
                    emptyText: '',
                    store: new Ext.data.SimpleStore({
                        fields: ['language', 'title', 'icon'],
                        data: Phlexible.Config.get('set.language.frontend')
                    })
                })
            },
            {
                id: 'target',
                header: this.strings.target,
                dataIndex: 'target',
                sortable: true,
                width: 200,
                editor: new Phlexible.elements.EidSelector({
                    labelSeparator: '',
                    element: { siteroot_id: this.siterootId },
                    width: 300,
                    listWidth: 283,
                    treeWidth: 283
                })
            },
            this.actions
        ];

        this.plugins = [this.actions];

        this.sm = new Ext.grid.RowSelectionModel({singleSelect: true});

        this.addListener({
            rowcontextmenu: {
                fn: this.onRowContextMenu,
                scope: this
            },
            afterEdit: {
                fn: this.onValidateEdit,
                scope: this
            }
        });

        Phlexible.siteroots.ShortUrlGrid.superclass.initComponent.call(this);
    },

    renderLanguage: function (v, md, r, ri, ci, store) {
        var editor = this.getColumnModel().getCellEditor(2, 0);

        var estore = editor.field.store;
        var eri = estore.find('language', v);
        if (eri !== -1) {
            return estore.getAt(eri).get('language');
        }

        return v;
    },

    onValidateEdit: function (event) {
        if (event.record.get('path') === '') {
            this.startEditing(event.row, 1);
        }

        if (event.record.get('target') === '') {
            this.startEditing(event.row, 3);
        }
    },

    /**
     * Store the current record and shoe context menu
     *
     * @param {Object} grid
     * @param {Number} rowIndex
     * @param {Object} event
     */
    onRowContextMenu: function (grid, rowIndex, event) {
        event.stopEvent();

        var r = grid.store.getAt(rowIndex);
        this.contextMenu.record = r;

        var coords = event.getXY();
        this.contextMenu.showAt([coords[0], coords[1]]);

        var sm = grid.getSelectionModel();
        if (!sm.hasSelection() || (sm.getSelected().id != r.id)) {
            sm.selectRow(rowIndex);
        }
    },

    /**
     * Action if site
     */
    onAddUrl: function () {

        // create new empty record
        var newRecord = new Phlexible.siteroots.ShortUrlRecord({
            id: '',
            path: '',
            language: '',
            target: ''
        });

        // add empty record to store
        this.store.insert(0, newRecord);
        this.selModel.selectFirstRow();
        this.startEditing(0, 2);
    },

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Number} id
     * @param {String} title
     * @param {Object} data
     */
    loadData: function (id, title, data) {
        this.deletedRecords = [];
        this.store.commitChanges();

        // remember current siteroot id
        this.siterootId = id;

        this.store.loadData(data.shorturls);

        var cm = this.getColumnModel();
        var editor = cm.getCellEditor(3, 0);
        editor.field.setSiterootId(id);
    },

    /**
     * Start deletion of record.
     *
     * @param {Object} r
     */
    onDeleteUrl: function (r) {

        if (!this.deletedRecords) {
            this.deletedRecords = [];
        }

        // remember record -> they are deleted on save
        this.deletedRecords.push(r);

        // delete record from store
        this.store.remove(r);
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {

        // check data
        var valid = true;
        Ext.each(this.store.getModifiedRecords() || [], function (r) {
            if (r.data.path.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_path_empty);
                valid = false;
                return false;
            }

            if (r.data.target.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_target_empty);
                valid = false;
                return false;
            }

            if (r.data.language.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_language_empty);
                valid = false;
                return false;
            }
        });

        if (!valid) {
            return false;
        }

        // fetch deleted records
        var deleted = [];
        Ext.each(this.deletedRecords || [], function (r) {
            if (r.data.id) {
                deleted.push(r.data.id);
            }
        });

        // fetch modified records
        var modified = [], created = [];
        Ext.each(this.store.getModifiedRecords() || [], function (r) {
            if (r.data.id) {
                modified.push(r.data);
            } else {
                created.push(r.data);
            }
        });

        return {
            shorturls: {
                deleted: deleted,
                modified: modified,
                created: created
            }
        };
    }

});

Ext.reg('siteroots-shorturls', Phlexible.siteroots.ShortUrlGrid);