Phlexible.siteroots.UrlGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    title: Phlexible.siteroots.Strings.url_mappings,
    strings: Phlexible.siteroots.Strings,
    border: false,
    autoExpandColumn: 'hostname',

    viewConfig: {
        emptyText: Phlexible.siteroots.Strings.no_url_mappings
    },

    initComponent: function(){

        // Create RowActions Plugin
        this.actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
//          autoWidth:false,
            width: 150,
            actions:[{
                iconCls: 'p-siteroot-delete-icon',
                tooltip: this.strings.delete,
                callback: function(grid, record, action, row, col) {
                    var r = grid.store.getAt(row);

                    Ext.MessageBox.confirm(this.strings.remove, this.strings.sure, function(btn) {
                        if (btn === 'yes') {
                            this.onDeleteUrl(r);
                        }
                    }, this);
                }.createDelegate(this)
            }],
            getData: function(value, cell, record, row, col, store) {
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

        this.tbar = [{
            text: this.strings.add_mapping,
            iconCls: 'p-siteroot-add-icon',
            handler: this.onAddUrl,
            scope: this
        }];

        this.store = new Ext.data.JsonStore({
            fields: Phlexible.siteroots.UrlRecord
        });

        this.columns = [{
            header: 'ID',
            hidden: true,
            dataIndex: 'id'
        }, this.cc2 = new Phlexible.siteroots.LanguageCheckColumn({
            header: this.strings['default'],
            dataIndex: 'default',
            languageIndex: 'language',
            width: 50
        }),{
            id:'hostname',
            header: this.strings.hostname,
            dataIndex: 'hostname',
            sortable: true,
            vtype: 'url',
            editor: new Ext.form.TextField({
               //allowBlank: false
           })
        },{
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
        },{
            header: this.strings.target,
            dataIndex: 'target',
            sortable: true,
            width: 200,
            editor: new Phlexible.elements.EidSelector({
                labelSeparator: '',
                element: {
                    siteroot_id: this.siterootId
                },
                width: 300,
                listWidth: 283,
                treeWidth: 283
            })
        },
            this.actions
        ];

        this.plugins = [this.cc2, this.actions];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.addListener({
            afterEdit: this.onValidateEdit,
            scope: this
        });

        Phlexible.siteroots.UrlGrid.superclass.initComponent.call(this);
    },

    renderLanguage: function(v, md, r, ri, ci, store) {
        var editor = this.getColumnModel().getCellEditor(3, 0);

        var estore = editor.field.store;
        var eri = estore.find('language', v);
        if (eri !== -1) {
            return estore.getAt(eri).get('language');
        }

        return v;
    },

    onValidateEdit: function(event) {

        if (event.record.get('hostname') === '') {
            this.startEditing(event.row, 2);
        }

        if (event.record.get('target') === '') {
            this.startEditing(event.row, 4);
        }
    },

    /**
     * Action if site
     */
    onAddUrl: function() {

        // create new empty record
        var newRecord = new Phlexible.siteroots.UrlRecord({
            id: '',
            hostname: '',
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
    loadData: function(id, title, data) {
        this.deletedRecords = [];
        this.store.commitChanges();

        // remember current siteroot id
        this.siterootId = id;

        this.store.loadData(data.urls);

        var cm = this.getColumnModel();
        var editor = cm.getCellEditor(4, 0);
        editor.field.setSiterootId(id);
    },

    /**
     * Start deletion of record.
     *
     * @param {Object} r
     */
    onDeleteUrl: function(r) {
        if (!this.deletedRecords) {
            this.deletedRecords = [];
        }

        // remember record -> they are deleted on save
        this.deletedRecords.push(r);

        // delete record from store
        this.store.remove(r);
    },

    isValid: function() {
        var valid = true;

        this.store.each(function(r) {
            if (!r.data.target || !r.data.hostname || !r.data.language) {
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
    getSaveData: function() {

        // fetch deleted records
        var delRecords = [];
        Ext.each(this.deletedRecords || [], function(r) {
            if (new String(r.data.id).length > 0) {
                delRecords.push(r.data);
            }
        });

        // fetch modified records
        var modRecords = [];
        Ext.each(this.store.getModifiedRecords() || [], function(r) {
            modRecords.push(r.data);
        });

        // check data
        for(var i = 0; i < modRecords.length; ++i) {

            // get current record
            var r = modRecords[i];

            if(r.hostname.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_url_empty);
                return false;
            }

            if(r.target.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_target_empty);
                return false;
            }

            if(r.language.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_language_empty);
                return false;
            }

        }

        return {
            'mappings': {
                del_records: delRecords,
                mod_records: modRecords
            }
        };
    }

});

Ext.reg('siteroots-urls', Phlexible.siteroots.UrlGrid);