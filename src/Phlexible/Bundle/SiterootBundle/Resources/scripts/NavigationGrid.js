Phlexible.siteroots.NavigationGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    title: Phlexible.siteroots.Strings.navigations,
    strings: Phlexible.siteroots.Strings,
    border: false,

    viewConfig: {
        forceFit: true,
        emptyText: Phlexible.siteroots.Strings.no_navigations
    },

    initComponent: function(){
        this.store = new Ext.data.JsonStore({
            fields: Phlexible.siteroots.NavigationRecord
        });

        // Create RowActions Plugin
        this.actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
//          autoWidth:false,
            width: 150,
            actions:[{
                iconCls: 'p-siteroot-handler-config-icon',
                hideIndex: 'hide_config',
                tooltip: '_configure',
                callback: function(grid, record, action, row, col) {
                    var r = grid.store.getAt(row);

                    switch(r.get('handler')) {
                        case 'Siteroot':
                            var w = new Phlexible.siteroots.SiterootNavigationWindow({
                                record: r,
                                siterootId: this.siterootId
                            });

                            w.show();

                            break;
                    }
                }
            },{
                iconCls: 'p-siteroot-flag-icon',
                tooltip: this.strings.flags,
                callback: function(grid, record, action, row, col) {
                    var r = grid.store.getAt(row);

                    var w = new Phlexible.siteroots.NavigationFlagsWindow({
                        record: r
                    });

                    w.show();
                }.createDelegate(this)
            },{
                iconCls: 'p-siteroot-delete-icon',
                tooltip: this.strings.delete,
                callback: function(grid, record, action, row, col) {
                    var r = grid.store.getAt(row);

                    Ext.MessageBox.confirm(this.strings.remove, this.strings.sure, function(btn) {
                        if (btn === 'yes') {
                            this.onDeleteNavigation(r);
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

        this.columns = [{
            header: this.strings.title,
            dataIndex: 'title',
            editor: new Ext.form.TextField(),
            width: 150
        },{
            header: this.strings.handler,
            dataIndex: 'handler',
            editor: new Ext.form.ComboBox({
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('siteroots_data_handlers'),
                    root: 'handler',
                    fields: ['title', 'supports']
                }),
                displayField: 'title',
                mode: 'remote',
                triggerAction: 'all',
                selectOnFocus: true,
                editable: false,
                typeAhead: false,
                listeners: {
                    select: {
                        fn: function(combo, r) {
                            this.getSelectionModel().getSelected().set('supports', r.get('supports'));
                        },
                        scope: this
                    }
                }
            }),
            width: 150
        },{
            header: this.strings.start_tid,
            dataIndex: 'start_tid',
            editor: new Ext.form.TextField(),
            width: 50
        },{
            header: this.strings.max_depth,
            dataIndex: 'max_depth',
            editor: new Ext.form.NumberField(),
            width: 50
        },{
            header: this.strings.flags,
            dataIndex: 'flags',
            editor: new Ext.form.NumberField(),
            width: 50,
            hidden: true
        },{
            header: this.strings.additional,
            dataIndex: 'additional',
            editor: new Ext.form.TextField(),
            width: 100,
            hidden: true
        },
            this.actions
        ];

        this.plugins = [
            this.actions
        ];

        this.sm = new Ext.grid.RowSelectionModel({singleSelect: true});

        this.tbar = [{
            text: this.strings.add_navigation,
            iconCls: 'p-siteroot-add-icon',
            handler: this.onAddNavigation,
            scope: this
        }];

        Phlexible.siteroots.NavigationGrid.superclass.initComponent.call(this);
    },

    /**
     * Action if site
     */
    onAddNavigation: function() {
        // create new empty record
        var newRecord = new Phlexible.siteroots.NavigationRecord({
            id: '',
            siteroot_id: this.siterootId,
            title: '',
            handler: '',
            supports: 0,
            start_eid: 0,
            max_depth: 0,
            flags: 0
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
    loadData: function(id, title, data) {
        this.deletedRecords = [];
        this.store.commitChanges();

        this.siterootId = id;

        this.store.loadData(data.navigations);
    },

    /**
     * Start deletion of record.
     *
     * @param {Object} btn
     * @param {String} text
     * @param {Object} r
     */
    onDeleteNavigation: function(r) {
        if(!this.deletedRecords) {
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

            if(r.title.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_title_empty);
                return false;
            }

            if(!r.handler) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_handler_empty);
                return false;
            }
        }

        return {
            'navigations': {
                del_records: delRecords,
                mod_records: modRecords
            }
        };
    }

});

Ext.reg('siteroots-navigations', Phlexible.siteroots.NavigationGrid);