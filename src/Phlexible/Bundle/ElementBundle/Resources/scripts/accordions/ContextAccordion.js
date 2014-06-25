Phlexible.elements.ContextAccordion = Ext.extend(Ext.grid.EditorGridPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.country_context,
    iconCls: 'p-flags-de-icon',
    border: false,
    autoHeight: true,
    autoExpandColumn: 1,
    viewConfig: {
        emptyText: Phlexible.elements.Strings.no_country_context
    },
    hidden: true,

    key: 'context',

    initComponent: function() {
        this.store = new Ext.data.ObjectStore({
            fields: ['id', 'country', 'active'],
            id: 'id'
        });

        this.sm = new Ext.grid.RowSelectionModel();

        var c1 = new Ext.grid.CheckColumn({
            header: this.strings.active,
            dataIndex: 'active',
            width: 50,
            xrenderer: function(v) {
                var iconCls = v ? 'tick' : 'cross';

                return '<img src="' + Ext.BLANK_IMAGE_URL + '" width="16" height="16" class="p-elements-' + iconCls + '-icon" />';
            }
        });

        this.plugins = [c1];

        this.columns = [
            c1,
        {
            header: this.strings.country,
            dataIndex: 'country',
            renderer: function(v, md, r) {
                var icon = Phlexible.inlineIcon('p-flags-' + r.data.id + '-icon');

                return icon + ' ' + v;
            }
        }];

        Phlexible.elements.ContextAccordion.superclass.initComponent.call(this);
    },

    load: function(data) {
        if (!Ext.isArray(data.context) || !data.context.length) {
            this.hide();
            return;
        }

        this.store.loadData(data.context);

        this.show();
    },

    getData: function() {
        var data = [];

        var records = this.store.getRange();

        for(var i=0; i<records.length; i++) {
            if (records[i].data.active) {
                data.push(records[i].data.id);
            }
        }

        return data;
    }
});

Ext.reg('elements-contextaccordion', Phlexible.elements.ContextAccordion);
