Phlexible.translations.TranslationGrid = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.translations.Strings.translations,
    strings: Phlexible.translations.Strings,
    loadMask: true,
	viewConfig: {
		emptyText: Phlexible.translations.Strings.no_translations,
		deferEmptyText: true
	},

    initComponent: function() {
        var fields = [{
            name: 'id',
            type: 'string'
        },{
            name: 'domain',
            type: 'string'
        },{
            name: 'key',
            type: 'string'
        }];

        Ext.each(Phlexible.Config.get('set.language.backend'), function(item) {
            fields.push({
                name: item[0],
                type: 'string'
            });
        });

        Phlexible.translations.Word = Ext.data.Record.create(fields);

        // create the Data Store
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('translations_list'),
            root: 'items',
            totalProperty: 'total',
            id: 'id',
            fields: fields,
            baseParams: { limit: 25 },
            remoteSort: true,
            autoLoad: true
        });

        this.columns = [{
            header: this.strings.domain,
            width: 80,
            dataIndex: 'domain',
            sortable: true
        },{
            header: this.strings.key,
            width: 200,
            dataIndex: 'key',
            sortable: true
        }];

        Ext.each(Phlexible.Config.get('set.language.backend'), function(item) {
            this.columns.push({
                header: Phlexible.inlineIcon('p-flags-' + item[0] + '-icon') + ' ' + item[0], // Phlexible.languages.Strings[item[0]],
                width: 250,
                dataIndex: item[0],
                sortable: true,
                hidden: false
            });
        }, this);

        // --------------------------------------------------
        // create the selectionmodel
        this.selModel = new Ext.grid.RowSelectionModel({
            singleSelect : false
        });

        /*
        this.bbar = new Ext.PagingToolbar({
            store: this.store,
            pageSize: this.store.baseParams.limit,
            displayInfo: true,
            displayMsg: this.strings.paging_display_msg,
            emptyMsg: this.strings.paging_empty_msg,
            beforePageText: this.strings.paging_before_page_text,
            afterPageText: this.strings.paging_after_page_text
        });
        */

        Phlexible.translations.TranslationGrid.superclass.initComponent.call(this);
    }
});

Ext.reg('translations-grid', Phlexible.translations.TranslationGrid);