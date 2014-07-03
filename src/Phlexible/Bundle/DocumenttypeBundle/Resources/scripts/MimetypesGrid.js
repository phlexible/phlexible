Phlexible.documenttypes.MimetypesGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    title: Phlexible.documenttypes.Strings.mimetypes,
    strings: Phlexible.documenttypes.Strings,
    iconCls: 'p-documenttype-component-icon',
    loadMask: true,
    viewConfig: {
        forceFit: true
    },
    stripeRows: true,

    initComponent: function () {
        this.store = new Ext.data.SimpleStore({
            fields: ['mimetype'],
            sortInfo: {field: 'mimetype', direction: 'ASC'}
        });

        this.selModel = new Ext.grid.RowSelectionModel();

        this.columns = [
            {
                header: this.strings.mimetype,
                dataIndex: 'mimetype',
                sortable: true,
                width: 220,
                editor: new Ext.form.TextField({
                    allowBlank: false
                })
            }
        ];

        Phlexible.documenttypes.MimetypesGrid.superclass.initComponent.call(this);
    },

    loadMimetypes: function (mimetypes) {
        if (mimetypes) {
            var mimetypesData = [];
            Ext.each(mimetypes, function (mimetype) {
                mimetypesData.push([mimetype]);
            });
            this.store.loadData(mimetypesData);
        } else {
            this.store.removeAll();
        }
    }
});

Ext.reg('documenttypes-mimetypesgrid', Phlexible.documenttypes.MimetypesGrid);