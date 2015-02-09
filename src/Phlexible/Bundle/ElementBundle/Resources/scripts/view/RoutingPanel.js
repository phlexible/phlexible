Ext.provide('Phlexible.elements.RoutingPanel');

Phlexible.elements.RoutingPanel = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.elements.Strings.routing.routing,
    iconCls: 'p-element-routing-icon',
    strings: Phlexible.elements.Strings.routing,
    bodyStyle: 'margin: 5px',

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            fields: ['path', 'type', 'created', 'superseded'],
            data: [
                {path: '/test/index.html', 'type': 'historical', created: '2014-12-12', superseded: '2014-12-13'},
                {path: '/whatever/index.html', 'type': 'historical', created: '2014-12-14', superseded: '2014-12-16'}
            ]
        });

        this.items = [{
            xtype: 'fieldset',
            title: this.strings.route,
            autoHeight: true,
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
            },{
                xtype: 'checkbox',
                name: 'https',
                labelSeparator: this.strings.scheme,
                boxLabel: this.strings.https
            }, {
                xtype: 'textfield',
                name: 'controller',
                fieldLabel: this.strings.controller,
                width: 300
            }, {
                xtype: 'textfield',
                name: 'template',
                fieldLabel: this.strings.template,
                width: 300
            }]
        },{
            xtype: 'fieldset',
            title: this.strings.security,
            autoHeight: true,
            items: [{
                xtype: 'textfield',
                name: this.strings.security,
                fieldLabel: this.strings.security,
                width: 300
            }]
        },{
            xtype: 'fieldset',
            title: this.strings.cache,
            autoHeight: true,
            items: [{
                xtype: 'textfield',
                name: 'expires',
                fieldLabel: this.strings.expires,
                width: 300
            },{
                xtype: 'checkbox',
                name: 'public',
                labelSeparator: '',
                fieldLabel: '',
                boxLabel: this.strings.public
            },{
                xtype: 'numberfield',
                name: 'maxage',
                fieldLabel: this.strings.maxage,
                width: 300
            },{
                xtype: 'numberfield',
                name: 'smaxage',
                fieldLabel: this.strings.smaxage,
                width: 300
            },{
                xtype: 'textfield',
                name: 'vary',
                fieldLabel: this.strings.vary,
                width: 300
            }]
        },{
            xtype: 'panel',
            border: false,
            items: [{
                xtype: 'grid',
                title: this.strings.old_urls,
                store: this.store,
                width: 600,
                viewConfig: {
                    forceFit: true
                },
                columns: [{
                    header: this.strings.path,
                    dataIndex: 'path',
                    width: 500
                },{
                    header: this.strings.type,
                    dataIndex: 'type',
                    width: 100
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
        this.getForm().setValues({
            path: this.element.data.urls.online || this.element.data.urls.preview
        });
    }
});

Ext.reg('elements-routing', Phlexible.elements.RoutingPanel);
