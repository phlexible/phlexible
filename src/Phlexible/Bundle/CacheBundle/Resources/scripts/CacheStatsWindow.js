Phlexible.cache.CacheStatsWindow = Ext.extend(Ext.Window, {
    title: Phlexible.cache.Strings.cache_statistics,
    strings: Phlexible.cache.Strings,
    width: 800,
    height: 560,
    iconCls: 'p-cache-stats-icon',
    layout: 'fit',
    constrainHeader: true,

    initComponent: function() {
        this.items = [{
            xtype: 'panel',
            layout: 'accordion',
            border: false,
            items: []
        }];

        this.tbar = [{
            text: this.strings.reload,
            iconCls: 'p-cache-reload-icon',
            handler: this.reload,
            scope: this
        },'-',{
            text: this.strings.flush_all_servers,
            iconCls: 'p-cache-flush-icon',
            handler: function(){
                this.flush(null);
            },
            scope: this
        }];

        Phlexible.cache.CacheStatsWindow.superclass.initComponent.call(this);

        this.reload();
    },

    reload: function() {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('cache_data'),
            success: this.onReloadSuccess,
            scope: this
        });
    },

    flush: function(id) {
        var text;
        var params = {};

        if (id === null || id === undefined) {
            text = this.strings.warning_flush_all_servers;
        } else {
            params.id = id;
            text = this.strings.warning_flush_server;
        }

        Ext.MessageBox.confirm('Confirm', text, function(btn, a, b, params) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: Phlexible.Router.generate('cache_flush'),
                    params: params,
                    success: function(){
                        this.reload();
                    },
                    scope: this
                });
            }
        }.createDelegate(this, [params], true), this);
    },

    onReloadSuccess: function(response) {
        var data = Ext.decode(response.responseText);

        this.getComponent(0).removeAll();

        for (var name in data) {
            var server = data[name],
                config = {
                    xtype: 'panel',
                    layout: 'border',
                    title: name,
                    items: [{
                        xtype: 'propertygrid',
                        region: 'center',
                        border: false,
                        source: server
                    }],
                    tbar: [{
                        text: this.strings.flush_server,
                        iconCls: 'p-cache-flush-icon',
                        handler: function() {
                            this.flush(server.name);
                        },
                        scope: this
                    }]
                };

            if (server.charts) {

                var usageData = new Ext.util.MixedCollection(),
                    hitsData = new Ext.util.MixedCollection(),
                    charts = [];

                usageData.add('data', server.charts.usage);
                charts.push({
                    xtype: 'panel',
                    border: false,
                    bodyStyle: 'padding: 10px',
                    items: [{
                        xtype: 'googlechart',
                        title: this.strings.cache_usage,
                        width: 270,
                        height: 150,

                        data: usageData,
                        chartType: "pie3d",
                        //            dataType: "extended",
                        labels: ["free", "used"],
                        chartColors: ["44cc44", "cc4444"]
                    }]
                });

                if (server.charts.hits_misses) {
                    hitsData.add('data', server.charts.hits_misses);
                    charts.push({
                        xtype: 'panel',
                        border: false,
                        bodyStyle: 'padding: 10px',
                        items: [{
                            xtype: 'googlechart',
                            title: this.strings.hits_and_misses,
                            width: 270,
                            height: 150,

                            data: hitsData,
                            chartType: "pie3d",
                            //            dataType: "extended",
                            labels: ["hits", "misses"],
                            chartColors: ["44cc44", "cc4444"]
                        }]
                    });
                }

                config.items.push({
                    xtype: 'panel',
                    region: 'east',
                    width: 300,
                    border: true,
                    items: charts
                });
            }

            this.getComponent(0).add(config);
        };

        this.doLayout();
    }
});
