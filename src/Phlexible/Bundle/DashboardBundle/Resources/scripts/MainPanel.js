Phlexible.dashboard.MainPanel = Ext.extend(Ext.Panel, {
    title: 'Start',
    iconCls: 'p-dashboard-component-icon',
    cls: 'p-dashboard-main-panel',
    header: false,
    border: false,
    layout: 'border',

    initComponent: function() {
        this.closable = false;

        this.items = [
            this.listPanel = new Phlexible.dashboard.ListPanel({
                region: 'west',
                collapsible: true,
                collapsed: true,
//                collapseMode: 'mini',
                listeners: {
                    portletOpen: {
                        fn: function(store, r){
                            r.set('mode', 'opened');
                            this.portletPanel.addRecordPanel(r);
                            store.remove(r);
                        },
                        scope: this
                    },
                    portletSave: {
                        fn: this.onDoSave,
                        scope: this
                    }
                }
            }),
            this.portletPanel = new Phlexible.dashboard.PortalPanel({
                region: 'center',
                listeners: {
                    portletAdd: {
                        fn: function(panel, record) {
                            this.onSave();
                        },
                        scope: this
                    },
                    portletClose: {
                        fn: function(panel, record) {
                            Phlexible.dashboard.ListStore.add(record);
                            Phlexible.dashboard.ListStore.sort('title', 'ASC');
                            panel.ownerCt.remove(panel, true);
                            panel.destroy();

                            this.onSave();
                        },
                        scope: this
                    },
                    portletCollapse: {
                        fn: function(panel, record) {
                            this.onSave();
                        },
                        scope: this
                    },
                    portletExpand: {
                        fn: function(panel, record) {
                            this.onSave();
                        },
                        scope: this
                    },
                    drop: {
                        fn: function(e) {
                            var r = e.panel.record;
                            r.set('col', e.columnIndex);
                            r.set('pos', e.position);

                            this.onSave();
                        },
                        scope: this
                    }
                }
            })
        ];

        this.saveTask = new Ext.util.DelayedTask(this.onDoSave, this);

        Phlexible.dashboard.MainPanel.superclass.initComponent.call(this);
    },

    onRender: function(ct, position) {
        Phlexible.dashboard.MainPanel.superclass.onRender.call(this, ct, position);

        Ext.Ajax.request({
            url: Phlexible.Router.generate('dashboard_portlets'),
            success: this.onLoad,
			failure: function() {
				Ext.MessageBox.alert('Load error', 'Error loading portlets.');
			},
			scope: this
        });
    },

    onLoad: function(response) {
        var data = Ext.decode(response.responseText);
        var matrix = [];
        var id, i, row, r;
        var cols = this.portletPanel.cols;

        for (i = 0; i < cols; i++) {
            matrix.push(new Ext.util.MixedCollection());
        }

        for (i=0; i<data.length; i++) {
            row = data[i];
            id = row.id;

            row.col = false;
            row.pos = false;
            row.mode = 'closed';

            if (Phlexible.Config.get('user.portlets')[id]) {
                row.col  = parseInt(Phlexible.Config.get('user.portlets')[id].col, 10);
                row.pos  = parseInt(Phlexible.Config.get('user.portlets')[id].pos, 10);
                row.mode = Phlexible.Config.get('user.portlets')[id].mode;
            }

            if (row.col !== false && row.pos !== false && row.mode !== 'closed') {
                matrix[row.col].insert(row.pos, row);
            }
            else {
                r = new Phlexible.dashboard.PortletRecord(row, row.id);
                Phlexible.dashboard.ListStore.add(r);
            }
        }

        for(i = 0; i< cols; i++) {
            matrix[i].each(function(item) {
                r = new Phlexible.dashboard.PortletRecord(item, item.id);
                this.portletPanel.addRecordPanel(r, true);
            }, this);
        }
    },

    onSave: function() {
        this.saveTask.cancel();
        this.saveTask.delay(1000);
    },

    onDoSave: function() {
        this.saveTask.cancel();

        var data = this.portletPanel.getSaveData();

        Ext.Ajax.request({
            url: Phlexible.Router.generate('dashboard_portlets_save'),
            params: {
                portlets: Ext.encode(data)
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);
                if(!data.success) {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            }
        });
    }
});


