Ext.provide('Phlexible.elements.RoutingPanel');

Phlexible.elements.RoutingPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.elements.Strings.routing.routing,
    iconCls: 'p-element-routing-icon',
    strings: Phlexible.elements.Strings.routing,

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            fields: ['url'],
            data: [{url: 'http://www.test.de'}]
        });

        this.items = [{
            xtype: 'fieldset',
            title: this.strings.route,
            items: [{
                xtype: 'textfield',
                name: 'name',
                fieldLabel: this.strings.name,
                width: 300
            },{
                xtype: 'textfield',
                name: 'path',
                fieldLabel: this.strings.path,
                width: 300
            },{
                xtype: 'textfield',
                name: 'defaults',
                fieldLabel: this.strings.defaults,
                width: 300
            },{
                xtype: 'textfield',
                name: 'method',
                fieldLabel: this.strings.method,
                width: 300
            }]
        }, {
            xtype: 'fieldset',
            title: this.strings.route,
            items: [{
                xtype: 'checkbox',
                name: 'https',
                hideLabel: true,
                boxLabel: this.strings.https
            }, {
                xtype: 'textfield',
                name: 'controller',
                fieldLabel: this.strings.controller
            }, {
                xtype: 'textfield',
                name: 'template',
                fieldLabel: this.strings.template
            }]
        },{
            xtype: 'fieldset',
            title: this.strings.security,
            items: [{
                xtype: 'textfield',
                name: this.strings.security,
                fieldLabel: this.strings.security,
                width: 300
            }]
        },{
            xtype: 'fieldset',
            title: 'cache',
            items: [{
                xtype: 'textfield',
                name: 'expires',
                fieldLabel: this.strings.expires
            },{
                xtype: 'checkbox',
                name: 'public',
                hideLabel: true,
                boxLabel: this.strings.public
            },{
                xtype: 'numberfield',
                name: 'maxage',
                fieldLabel: this.strings.maxage
            },{
                xtype: 'numberfield',
                name: 'smaxage',
                fieldLabel: this.strings.smaxage
            },{
                xtype: 'textfield',
                name: 'vary',
                fieldLabel: this.strings.vary
            }]
        },{
            xtype: 'panel',
            bodyStyle: 'margin: 5px',
            border: false,
            items: [{
                xtype: 'grid',
                title: this.strings.old_urls,
                store: this.store,
                width: 500,
                viewConfig: {
                    forceFit: true
                },
                columns: [{
                    header: this.strings.url,
                    dataIndex: 'url',
                    width: 500
                }]
            }]
        }];

        Phlexible.elements.RoutingPanel.superclass.initComponent.call(this);

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
        if (element.properties.et_type !== 'full') {
            this.disable();
            //this.hide();
            return;
        }

        this.enable();
        //this.show();

        if (!this.hidden) {
            this.onRealLoad();
        }
    },

    onRealLoad: function () {
        //this.store.load();
        //this.getComponent(0).getComponent(0).setValue(this.element.urls.preview);
        this.getComponent(0).getComponent(1).setValue(this.element.properties.page_title);
    }
});

Ext.reg('elements-routing', Phlexible.elements.RoutingPanel);
