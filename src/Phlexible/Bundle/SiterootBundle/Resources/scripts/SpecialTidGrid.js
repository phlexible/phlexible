Phlexible.siteroots.SpecialTidGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    title: Phlexible.siteroots.Strings.special_tids,
    strings: Phlexible.siteroots.Strings,
    border: false,

    viewConfig: {
        emptyText: Phlexible.siteroots.Strings.no_special_tids
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
                                this.onDeleteSpecialTid(r);
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

        this.store = new Ext.data.JsonStore({
            fields: Phlexible.siteroots.SpecialTidRecord
        });

        this.columns = [
            {
                header: this.strings.key,
                dataIndex: 'key',
                width: 300,
                sortable: true,
                editor: new Ext.form.TextField()
            },
            {
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
                header: this.strings.tid,
                dataIndex: 'tid',
                width: 200,
                sortable: true,
                editor: new Ext.form.NumberField()
            },
            this.actions
        ];

        this.plugins = [this.actions];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.tbar = [
            {
                text: this.strings.add_specialtid,
                iconCls: 'p-siteroot-add-icon',
                handler: this.onAddSpecialTid,
                scope: this
            }
        ];

        Phlexible.siteroots.SpecialTidGrid.superclass.initComponent.call(this);
    },

    /**
     * Action if site
     */
    onAddSpecialTid: function () {

        // create new empty record
        var newRecord = new Phlexible.siteroots.SpecialTidRecord({
            id: '',
            siteroot_id: this.siterootId,
            key: '',
            language: '',
            tid: 0
        });

        // add empty record to store
        this.store.insert(0, newRecord);
        this.selModel.selectFirstRow();
        this.startEditing(0, 0);
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

        this.store.loadData(data.specialtids);
    },

    renderLanguage: function (v, md, r, ri, ci, store) {
        var editor = this.getColumnModel().getCellEditor(1, 0);

        var estore = editor.field.store;
        var eri = estore.find('language', v);
        if (eri !== -1) {
            return estore.getAt(eri).get('language');
        }

        return v;
    },

    /**
     * Start deletion of record.
     *
     * @param {Object} r
     */
    onDeleteSpecialTid: function (r) {

        if (!this.deletedRecords) {
            this.deletedRecords = [];
        }

        // remember record -> they are deleted on save
        this.deletedRecords.push(r);

        // delete record from store
        this.store.remove(r);
    },

    isValid: function () {
        var valid = true;

        this.store.each(function (r) {
            if (!r.data.tid) {
                valid = false;
                return false;
            }
        }, this);

        if (valid) {
            this.header.child('span').removeClass('error');
        } else {
            this.header.child('span').addClass('error');
        }

        return valid;
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {

        // fetch deleted records
        var delRecords = [];
        Ext.each(this.deletedRecords || [], function (r) {
            if (new String(r.data.id).length > 0) {
                delRecords.push(r.data);
            }
        });

        // fetch modified records
        var modRecords = [];
        Ext.each(this.store.getModifiedRecords() || [], function (r) {
            modRecords.push(r.data);
        });

        // check data
        for (var i = 0; i < modRecords.length; ++i) {

            // get current record
            var r = modRecords[i];

            if (r.key.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_key_empty);
                return false;
            }

//            if(r.language.length <= 0) {
//                Ext.Msg.alert(this.strings.failure, this.strings.err_language_empty);
//                return false;
//            }
        }

        return {
            'specialtids': {
                del_records: delRecords,
                mod_records: modRecords
            }
        };
    }

});

Ext.reg('siteroots-specialtids', Phlexible.siteroots.SpecialTidGrid);