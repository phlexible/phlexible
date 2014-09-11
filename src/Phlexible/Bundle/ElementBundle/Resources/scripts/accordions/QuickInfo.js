Phlexible.elements.accordion.QuickInfoTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="data-wrap">',
    '<table style="width:100%">',

    '<tr><th>{[Phlexible.elements.Strings.title]}</th><td>{backend_title}</td></tr>',

    '<tpl if="!teaser_id">',
    '<tr><th>{[Phlexible.elements.Strings.tid]}:</th><td>{tid}</td></tr>',
    '</tpl>',

    '<tpl if="teaser_id">',
    '<tr><th>{[Phlexible.elements.Strings.teaser_id]}:</th><td>{teaser_id}</td></tr>',
    '</tpl>',

    //'<tr><th>{[Phlexible.elements.Strings.author]}:</th><td>{author}</td></tr>',
    //'<tr><th>{[Phlexible.elements.Strings.created]}:</th><td>{create_date}</td></tr>',
    '<tr><th>{[Phlexible.elements.Strings.status]}:</th>',
    '<tpl if="!status || status == Phlexible.elements.STATUS_OFFLINE"><td>{[Phlexible.elements.Strings.not_published]}</td></tpl>',
    '<tpl if="status && status == Phlexible.elements.STATUS_ONLINE"><td style="color: green">{[Phlexible.elements.Strings.published]}</td></tpl>',
    '<tpl if="status && status == Phlexible.elements.STATUS_ASYNC"><td style="color: red">{[Phlexible.elements.Strings.published_async]}</td></tpl>',
    '</tr>',
    '<tpl if="values.masterlanguage != values.language">',
    '<tr><th>{[Phlexible.elements.Strings.masterlanguage]}:</th><td style="color: red;">{[Phlexible.inlineIcon("p-flags-"+values.masterlanguage+"-icon")]} {[Phlexible.gui.Strings[values.masterlanguage]]}</td></tr>',
    '</tpl>',

    '</table>',
    '</div>',
    '</tpl>'
);

Phlexible.elements.accordion.QuickInfo = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    //title: Phlexible.elements.Strings.data,
    cls: 'p-elements-data-accordion',
    border: false,
    //autoHeight: true,
    //startPinned: true,
    //plugins: [Ext.ux.plugins.ToggleCollapsible],

    initComponent: function () {
        this.items = new Ext.DataView({
            store: new Ext.data.SimpleStore({
                id: 'dummy_id',
                fields: [
                    'dummy_id',
                    'backend_title', //this.strings.backend_title, '&nbsp;'],
                    'page_title', //this.strings.page_title, '&nbsp;'],
                    'navigation_title', //this.strings.navigation_title, '&nbsp;'],
                    'tid', //this.strings.tid, '&nbsp;'],
                    'teaser_id',
                    'eid', //this.strings.eid, '&nbsp;'],
                    'version', //this.strings.version, '&nbsp;'],
                    'language', //this.strings.language, '&nbsp;'],
                    'unique_id', //this.strings.unique_id, '&nbsp;'],
                    'et_id', //this.strings.element_type_id, '&nbsp;'],
                    'et_title', //this.strings.element_type_title, '&nbsp;'],
                    'et_version', //this.strings.element_type_version, '&nbsp;'],
                    'et_unique_id', //this.strings.element_type_unique_id, '&nbsp;'],
                    'author', //this.strings.author, '&nbsp;'],
                    'status', //this.strings.status, '&nbsp;'],
                    'create_date', //this.strings.created, '&nbsp;'],
                    'publish_date',
                    'masterlanguage'//, this.strings.published, '&nbsp;']
                ]
            }),
            tpl: Phlexible.elements.accordion.QuickInfoTemplate,
            autoHeight: true,
            singleSelect: true,
            overClass: 'x-view-over',
            itemSelector: 'div.data-wrap'
        });

        Phlexible.elements.accordion.QuickInfo.superclass.initComponent.call(this);
    },

    load: function (data) {
        var store = this.getComponent(0).store;
        store.removeAll();
        var r = new Ext.data.Record(data.properties);
        store.add(r);
        return;
        /*
         r.beginEdit();
         for(var i in data.properties) {
         r.set(i, data.properties[i]);
         continue;
         if(i == 'status') {
         data[i] = data.properties[i] ? this.strings.published : this.strings.not_published;
         }
         r = store.getById(i);
         if(r) {
         //                r.beginEdit();
         r.set('value', data.properties[i]);
         //                r.endEdit();
         //                r.commit();
         }
         }
         r.endEdit();
         r.commit();
         */
    }
});

Ext.reg('elements-accordion-quickinfo', Phlexible.elements.accordion.QuickInfo);