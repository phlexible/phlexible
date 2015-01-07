Ext.provide('Phlexible.elements.accordion.DataTemplate');
Ext.provide('Phlexible.elements.accordion.Data');

Phlexible.elements.accordion.DataTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="data-wrap">',
    '<table style="width:100%">',

    '<tr><td colspan="2" style="padding-top: 3px"><div style="float: left; font-style: italic; margin-right: 5px;">{[Phlexible.elements.Strings.element]}</div><hr /><div style="clear: left;" /></td></tr>',
    '<tr><th>{[Phlexible.elements.Strings.eid]}:</th><td>{eid}</td></tr>',
    '<tr><th>{[Phlexible.elements.Strings.version]}:</th><td>{version}</td></tr>',
    '<tpl if="unique_id && unique_id != \'null\'">',
    '<tr><th>{[Phlexible.elements.Strings.unique_id]}:</th><td>{unique_id}</td></tr>',
    '</tpl>',
    '<tr><th>{[Phlexible.elements.Strings.masterlanguage]}:</th><td>{[Phlexible.inlineIcon("p-flags-"+values.masterlanguage+"-icon")]} {masterlanguage}</td></tr>',
    '<tr><th>{[Phlexible.elements.Strings.author]}:</th><td>{author}</td></tr>',
    '<tr><th>{[Phlexible.elements.Strings.created]}:</th><td>{create_date}</td></tr>',

    '<tpl if="online_version && online_version != \'null\'">',
    '<tr><td colspan="2" style="padding-top: 3px"><div style="float: left; font-style: italic; margin-right: 5px;">{[Phlexible.elements.Strings.online_version]}</div><hr /><div style="clear: left;" /></td></tr>',
    '<tr><th>{[Phlexible.elements.Strings.version]}:</th><td>{online_version}</td></tr>',
    '<tr><th>{[Phlexible.elements.Strings.publisher]}:</th><td>{publisher}</td></tr>',
    '<tr><th>{[Phlexible.elements.Strings.published]}:</th><td>{publish_date}</td></tr>',
    '</tpl>',

    '<tr><td colspan="2" style="padding-top: 3px"><div style="float: left; font-style: italic; margin-right: 5px;">{[Phlexible.elements.Strings.elementtype]}</div><hr /><div style="clear: left;" /></td></tr>',
    //'<tr><th>{[Phlexible.elements.Strings.id]}:</th><td>{et_id}</td></tr>',
    '<tpl if="et_unique_id && et_unique_id != \'null\'">',
    '<tr><th>{[Phlexible.elements.Strings.unique_id]}:</th><td>{et_unique_id}</td></tr>',
    '</tpl>',
    '<tr><th>{[Phlexible.elements.Strings.title]}:</th><td>{et_title}</td></tr>',
    '<tr><th>{[Phlexible.elements.Strings.version]}:</th><td>{et_version}</td></tr>',

    '</table>',
    '</div>',
    '</tpl>'
);

Phlexible.elements.accordion.Data = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.information,
    cls: 'p-elements-data-accordion',
    iconCls: 'p-element-information-icon',
    border: false,
    autoHeight: true,
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
                    'publish_date' //, this.strings.published, '&nbsp;']
                ]
            }),
            tpl: Phlexible.elements.accordion.DataTemplate,
            autoHeight: true,
            singleSelect: true,
            overClass: 'x-view-over',
            itemSelector: 'div.data-wrap'
        });

        Phlexible.elements.accordion.Data.superclass.initComponent.call(this);
    },

    load: function (data) {
        var store = this.getComponent(0).store;
        store.removeAll();
        var r = new Ext.data.Record(data.properties);
        store.add(r);
        return;

        r.beginEdit();
        for (var i in data.properties) {
            r.set(i, data.properties[i]);
            continue;
            if (i == 'status') {
                data[i] = data.properties[i] ? this.strings.published : this.strings.not_published;
            }
            r = store.getById(i);
            if (r) {
//                r.beginEdit();
                r.set('value', data.properties[i]);
//                r.endEdit();
//                r.commit();
            }
        }
        r.endEdit();
        r.commit();
    }
});

Ext.reg('elements-dataaccordion', Phlexible.elements.accordion.Data);