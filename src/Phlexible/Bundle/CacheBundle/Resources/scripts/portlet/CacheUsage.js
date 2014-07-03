Ext.namespace('Phlexible.cache.portlet');

Phlexible.cache.portlet.CacheUsage = Ext.extend(Ext.ux.Portlet, {
    title: Phlexible.cache.Strings.cache_usage,
    extraCls: 'p-cache-portlet',
    iconCls: 'p-cache-stats-icon',
    bodyStyle: 'padding: 5px 5px 5px 5px',

    initComponent: function () {
        var data = this.record.get('data');


        this.items = [];

        var len = data.length;
        for (var i = 0; i < len; i++) {
            this.items.push({
                xtype: 'label',
                text: data[i].title
            });
            this.items.push({
                cls: this.getPercentClass(data[i].percent) + (i < (len - 1) ? ' m-cache-spacer' : ''),
                xtype: 'progress',
                value: data[i].percent,
                text: data[i].total ? Math.ceil(data[i].percent * 100) + '%' + ' (' + data[i].used + ' / ' + data[i].total + ')' : 'No stats provided by cache type.'
            });
        }

        Phlexible.cache.portlet.CacheUsage.superclass.initComponent.call(this);
    },

    updateData: function (data) {
        for (var i = 0; i < data.length; i++) {
            var pb = this.getComponent((i * 2) + 1);
            var el = pb.getEl();
            el.removeClass('p-cache-status-low');
            el.removeClass('p-cache-status-normal');
            el.removeClass('p-cache-status-high');
            el.addClass(this.getPercentClass(data.percent));
            pb.updateProgress(data.percent, Math.ceil(data[i].percent * 100) + '%' + ' (' + data[i].used + ' / ' + data[i].total + ')');
        }
    },

    getPercentClass: function (percent) {
        if (percent < 0.5) {
            return 'p-cache-status-low';
        }
        else if (percent > 0.8) {
            return 'p-cache-status-high';
        }

        return 'p-cache-status-normal';

    }
});

Ext.reg('cache-portlet-cacheusage', Phlexible.cache.portlet.CacheUsage);