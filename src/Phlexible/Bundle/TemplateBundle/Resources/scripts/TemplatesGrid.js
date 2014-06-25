Phlexible.templates.TemplatesGrid = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.templates.Strings,
    autoExpandColumn: 'name',
//    viewConfig: {
//        autoFill: true,
//        forceFit: true
//    },

    initComponent: function() {
        // create the data store
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('templates_list'),
            fields: ['id', 'name', 'path', 'filename'],
            root: 'templates',
            totalProperty: 'total',
            id: 'id',
            autoLoad: true,
            sortInfo: {field: 'name', direction: 'ASC'}
        });

        // create the column model
        this.columns = [{
            id: 'name',
            header: this.strings.name,
            width: 100,
            dataIndex: 'name',
            fixed: true,
            resizable: false
        },{
            header: this.strings.path,
            width: 100,
            dataIndex: 'path',
            fixed: true,
            resizable: false
        },{
            header: this.strings.filename,
            width: 100,
            dataIndex: 'filename',
            fixed: true,
            resizable: false
        }];

        // create the selection model
        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.tbar = [{
            text: this.strings.reload,
            handler: function() {
            },
            scope: this
        }];

        Phlexible.templates.TemplatesGrid.superclass.initComponent.call(this);
    }
});

Ext.reg('templates-templatesgrid', Phlexible.templates.TemplatesGrid);