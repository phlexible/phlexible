Ext.provide('Phlexible.dashboard.PortalPanel');

Ext.require('Ext.ux.Portal');
Ext.require('Phlexible.dashboard.store.Portlet');

Phlexible.dashboard.PortalPanel = Ext.extend(Ext.ux.Portal, {
    store: Phlexible.dashboard.store.Portlet,
    cls: 'p-dashboard-portal-panel',
    border: false,
    cols: 3,
    panels: {},

    initComponent: function () {
        this.addEvents(
            'portletAdd',
            'portletClose',
            'portletCollapse',
            'portletExpand'
        );

        Phlexible.Frame.on('frameready', function() {
            Phlexible.Frame.getSystemMessage().on('message', this.processMessage, this);
        });

        var items = [];
        for (var i = 0; i < this.cols; i++) {
            items.push({
                id: 'col' + i,
                col: i,
                columnWidth: 1 / this.cols,
                style: 'padding:10px 10px 10px 10px'
                //,items: [{title: 'Column' + (i+1), html: 'test'}]
            });
        }

        this.items = items;

//        Phlexible.Frame.loader.on('beforeload', function(){
//            Phlexible.Frame.getSystemMessage().purgeListeners();
//        }, this);

        //this.store.on('load', this.updatePanels, this);

//        Phlexible.StartMessage = new Phlexible.dashboard.Message();

        Phlexible.dashboard.PortalPanel.superclass.initComponent.call(this);

        this.on({
            render: function () {
                Ext.DomHelper.append(this.el, {
                    tag: 'img',
                    src: Phlexible.bundleAsset('/phlexiblegui/images/watermark.gif'),
                    width: 250,
                    height: 89,
                    cls: 'p-dashboard-watermark'
                });

                if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
                    Ext.DomHelper.append(this.el, {
                        tag: 'div',
                        cls: 'p-dashboard-info'
                    }, true).load(Phlexible.url('/dashboard/info'));
                }
            },
            scope: this
        });
    },

    updatePanels: function () {

        for (var i = 0; i < this.store.getCount(); i++) {
            var r = this.store.getAt(i);
            this.addRecordPanel(r);
        }

        this.doLayout();

    },

    getCol: function (pos) {
        return this.items.get(pos);
    },

    getBestCol: function () {
        var childs = {};
        var best;
        var max = 999;

        for (i = 0; i < this.items.getCount(); i++) {
            var item = this.items.get(i);
            if (!item.items.getCount()) {
                return item;
            }
            var cnt = item.getSize().height;
            if (cnt < max) {
                max = cnt;
                best = item;
            }
        }

        return best;
    },

    addRecordPanel: function (r, skipEvent) {
        var col = parseInt(r.get('col'), 10);
        if (col !== false && col < this.cols) {
            col = this.getCol(col);
        } else {
            col = this.getBestCol();
        }
        if (r.get('class')) {
            var classname = Phlexible.evalClassString(r.get('class'));
            if (classname) {
                r.set('col', col.col);
                r.set('pos', col.items.length + 1);

                var tools = [];
                var plugins = [];

                tools.push({
                    id: 'close',
                    handler: function (e, target, panel) {
                        delete this.panels[panel.record.id];
                        this.fireEvent('portletClose', panel, panel.record);
                    },
                    scope: this
                });

                var o = new classname({
                    id: r.data.id,
                    record: r,
                    collapsed: r.get('mode') == 'collapsed',
                    tools: tools,
                    plugins: plugins,
                    listeners: {
                        collapse: function (panel) {
                            panel.record.set('mode', 'collapsed');

                            this.fireEvent('portletCollapse', panel, panel.record);
                        },
                        expand: function (panel) {
                            panel.record.set('mode', 'expanded');

                            this.fireEvent('portletExpand', panel, panel.record);
                        },
                        scope: this
                    }
                });
                var panel = col.add(o);

                this.panels[r.id] = o;

                this.doLayout();

                if (!skipEvent) {
                    this.fireEvent('portletAdd', panel, panel.record);
                }
            }
        }
    },

    getSaveData: function () {
        var data = {}, x = 0, y = 0;

        this.items.each(function (col) {
            col.items.each(function (item) {
                data[item.id] = {
                    id: item.id,
                    mode: item.record.data.mode,
                    col: x,
                    pos: y
                };
                y++;
            }, this);
            x++;
            y = 0;
        }, this);

        return data;
    },

    processMessage: function (event) {
        if (typeof event == "object" && event.type == "start") {
            var data = event.data;

            var r;
            for (var id in data) {
                var panel = this.panels[id];

                if (!panel || !panel.updateData) {
                    continue;
                }

                panel.updateData(data[id]);
            }
        }
    }
});

Ext.reg('dashboard-portalpanel', Phlexible.dashboard.PortalPanel);