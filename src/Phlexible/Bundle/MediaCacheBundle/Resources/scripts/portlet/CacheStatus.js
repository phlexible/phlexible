Ext.namespace('Phlexible.mediacache.portlet');

Phlexible.mediacache.portlet.CacheStatus = Ext.extend(Ext.ux.Portlet, {
    title: Phlexible.mediacache.Strings.cache_status,
    strings: Phlexible.mediacache.Strings,
    iconCls: 'p-mediacache-portlet-icon',
    bodyStyle: 'padding: 5px 5px 5px 5px',

    firstData: null,
    firstTs: null,

    initComponent: function() {
        var itemsLeft = parseInt(this.record.get('data'), 10);

        if (itemsLeft) {
            this.html = '<span id="media_cache_status">' + String.format(this.strings.cache_status_items, itemsLeft) + '</span>';
            this.firstData = itemsLeft;
            this.firstTs = new Date();
        }
        else {
            this.html = '<span id="media_cache_status">' + this.strings.cache_status_empty + '</span>';
        }

        Phlexible.mediacache.portlet.CacheStatus.superclass.initComponent.call(this);
    },

    updateData: function(itemsLeft) {
        if (!this.rendered) {
            return;
        }

        itemsLeft = parseInt(itemsLeft, 10);

        if (itemsLeft) {
            if (this.firstData && this.firstData > itemsLeft) {
                var itemsDiff = this.firstData - itemsLeft;
                var dateDiff = (new Date() - this.firstTs) / 1000;
                var itemsPerSecond = itemsDiff / dateDiff;
                var itemsPerMinute = parseInt(itemsPerSecond * 60, 10);
                var secondsLeft = itemsLeft / itemsPerSecond;
                //var minutesLeft = parseInt(secondsLeft / 60, 10);

                this.body.first().update(String.format(this.strings.cache_status_items_remaining, itemsLeft, itemsPerMinute, Phlexible.Format.age(secondsLeft)));

                Ext.fly('media_cache_status').frame('#8db2e3', 1);
            }
            else {
                this.body.first().update(String.format(this.strings.cache_status_items, itemsLeft));
                this.firstData = itemsLeft;
                this.firstTs = new Date();
            }
        }
        else {
            this.body.first().update(String.format(this.strings.cache_status_empty));
            this.firstTs = null;
            this.firstData = null;
        }
    }
});
