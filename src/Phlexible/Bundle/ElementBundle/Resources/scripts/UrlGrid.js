Phlexible.elements.UrlGrid = Ext.extend(Ext.grid.GridPanel, {
    title: 'urls',
    iconCls: 'p-element-url-icon',

    initComponent: function () {
        this.store = new Ext.data.SimpleStore({
            fields: ['bla'],
            data: [['bla']]
        });

        this.columns = [{
            header: 'bla',
            dataIndex: 'bla'
        }];

        Phlexible.elements.UrlGrid.superclass.initComponent.call(this);

        this.element.on('load', this.onLoadElement, this);

        this.store.baseParams = {
            filter_tid: null,
            filter_teaser_id: null
        };

        this.on('show', function () {
            if ((this.store.baseParams.tid != this.element.tid) || (this.store.baseParams.teaser_id != this.element.properties.teaser_id)) {
                this.onRealLoad();
            }
        }, this);
    },

    onLoadElement: function (element) {
        if (!this.hidden) {
            this.onRealLoad();
        }
    },

    onRealLoad: function () {
        this.store.load();
    }
});

Ext.reg('elements-urlgrid', Phlexible.elements.UrlGrid);
