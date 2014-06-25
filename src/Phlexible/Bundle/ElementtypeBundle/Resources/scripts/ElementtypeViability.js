Phlexible.elementtypes.ElementtypeViability = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.elementtypes.Strings.viability,
    strings: Phlexible.elementtypes.Strings,
    border: true,
    autoScroll: true,
    viewConfig: {
        forceFit: true,
        emptyText: Phlexible.elementtypes.Strings.no_viability
    },
//    autoExpandColumn: 'title',

    initComponent: function() {
        // Create RowActions Plugin
        this.actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            width: 150,
            actions:[{
                iconCls: 'p-elementtype-delete-icon',
                tooltip: this.strings.remove,
                callback: function(grid, record, action, row, col) {
                    var r = grid.store.getAt(row);

                    this.store.remove(r);
                }.createDelegate(this)
            }]
        });

        this.store = new Ext.data.JsonStore({
            url: '',//Phlexible.Router.generate('elementtypes_list_viability'),
            id: 'id',
            root: 'viability',
            fields: ['id', 'title', 'icon'],
            sortInfo: {field: 'title', direction: 'ASC'}
        });

        this.columns = [{
            header: 'ID',
            dataIndex: 'id',
            width: 30,
            hidden: true,
            sortable: true
        },{
            id: 'title',
            header: this.strings.elementtype,
            dataIndex: 'title',
            width: 250,
            sortable: true,
            renderer: function(value, meta, r) {
                return '<img src="' + Phlexible.component('/elementtypes/elementtypes/' + r.get('icon')) + '" width="18" height="18" style="vertical-align: middle;" /> ' + value;
            }
        },
            this.actions
        ];

        this.plugins = [this.actions];

        this.sm = new Ext.grid.RowSelectionModel({
            multiSelect : true
        });

        this.tbar = [{
            text: this.strings.save,
            iconCls: 'p-elementtype-save-icon',
            handler: function() {
                var records = this.store.getRange(),
                    ids = [];
                Ext.each(records, function(r) {
                    ids.push(r.get('id'));
                });
                Ext.Ajax.request({
                    url: Phlexible.Router.generate('elementtypes_viability_save', {id: this.elementtypeId}),
                    params: {
                        "ids[]": ids
                    },
                    success: function() {

                    },
                    scope: this
                });
            },
            scope: this
        },'-',{
            xtype: 'combo',
            emptyText: this.strings.add_elementtype,
            triggerAction: 'all',
            listClass:'x-combo-list-big',
            editable: false,
            tpl: '<tpl for="."><div class="x-combo-list-item"><img src="{[Phlexible.component(\"/elementtypes/elementtypes/\" + values.icon)]}" width="18" height="18" style="vertical-align: middle;" /> {title}</div></tpl>',
            width: 250,
            listWidth: 250,
            store: new Ext.data.JsonStore({
                url: '',//Phlexible.Router.generate('elementtypes_list_for_type'),
                root: 'elementtypes',
                id: 'id',
                fields: ['id', 'title', 'icon'],
                sortInfo: {field: 'title', direction: 'ASC'},
                listeners: {
                    load: function(comboStore) {
                        var r = this.store.getRange();

                        for(var i=0; i<r.length; i++) {
                            if(comboStore.indexOfId(r[i].id) !== -1) {
                                comboStore.remove(comboStore.getById(r[i].id));
                            }
                        }
                    },
                    scope: this
                }
            }),
            displayField: 'title',
            valueField: 'id',
            listeners: {
                select: this.onAdd,
                scope: this
            }
        }];

        Phlexible.elementtypes.ElementtypeViability.superclass.initComponent.call(this);
    },

    getCombo: function() {
        return this.getTopToolbar().items.items[2];
    },

    onAdd: function(combo, record, index) {
        if (!this.store.getById(record.id)) {
            this.store.add(new Ext.data.Record({
                id: record.id,
                title: record.data.title,
                icon: record.data.icon
            }, record.id));
            this.store.sort('title', 'ASC');
            combo.store.remove(record);
        }

        combo.setValue('');
    },

    empty: function() {
        this.getCombo().lastQuery = null;
        this.store.removeAll();
    },

    load: function(id, title, version, type) {
        this.elementtypeId = id;
        this.getCombo().lastQuery = null;
        this.store.proxy.conn.url = Phlexible.Router.generate('elementtypes_viability_list', {id: id});
        this.store.reload();
        this.setType(type);
    },

    setType: function(type) {
        this.getCombo().store.proxy.conn.url = Phlexible.Router.generate('elementtypes_viability_for_type', {type: type});
        if (this.getCombo().lastQuery) {
            this.getCombo().getStore().reload();
        }
    }
});

Ext.reg('elementtypes-viability', Phlexible.elementtypes.ElementtypeViability);
