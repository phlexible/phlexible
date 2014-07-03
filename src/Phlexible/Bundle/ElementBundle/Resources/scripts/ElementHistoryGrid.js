Phlexible.elements.ElementHistoryGrid = Ext.extend(Phlexible.elements.HistoryGrid, {
    initComponent: function () {
        Phlexible.elements.ElementHistoryGrid.superclass.initComponent.call(this);

        this.element.on('load', this.onLoadElement, this);

        this.store.baseParams = {
            filter_tid: null,
            filter_teaser_id: null
        };

        this.on('show', function () {
            if ((this.store.baseParams.tid != this.element.tid) || (this.store.baseParams.teaser_id != this.element.properties.teaser_id)) {
                this.onRealLoad(this.element.eid, this.element.tid, this.element.properties.teaser_id);
            }
        }, this);
    },

    onLoadElement: function (element) {
        if (!this.hidden) {
            this.onRealLoad(element.eid, element.tid, element.properties.teaser_id);
        }
    },

    onRealLoad: function (eid, tid, teaser_id) {
        if (teaser_id) {
            this.getColumnModel().setColumnHeader(2, this.strings.teaser_id);
            this.store.baseParams.filter_eid = '';
//            this.store.baseParams.filter_tid = '';
//            this.store.baseParams.filter_teaser_id = teaser_id;
        } else {
            this.getColumnModel().setColumnHeader(2, this.strings.tid);
            this.store.baseParams.filter_eid = eid;
//            this.store.baseParams.filter_tid = tid;
//            this.store.baseParams.filter_teaser_id = '';
        }

        this.store.load();
    }
});

Ext.reg('elements-elementhistorygrid', Phlexible.elements.ElementHistoryGrid);
