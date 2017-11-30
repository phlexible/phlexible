Ext.provide('Phlexible.elements.tab.Routing');

Phlexible.elements.tab.Routing = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.elements.Strings.routing.routing,
    iconCls: 'p-element-routing-icon',
    strings: Phlexible.elements.Strings.routing,
    bodyStyle: 'margin: 5px',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            internalSave: this.onInternalSave,
            scope: this
        });

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
                name: 'methods',
                fieldLabel: this.strings.methods,
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

        Phlexible.elements.tab.Routing.superclass.initComponent.call(this);

        this.store.baseParams = {
            filter_tid: null,
            filter_teaser_id: null
        };
    },

    onLoadElement: function (element) {
        if (element.properties.et_type !== 'full') {
            this.disable();
            //this.hide();

            this.getForm().reset();

            return;
        }

        this.enable();
        //this.show();

        this.getForm().reset();

        this.getForm().setValues(this.element.data.configuration.routing || {});
    },

    onRealLoad: function () {
        this.getForm().setValues({
            path: this.element.data.urls.online || this.element.data.urls.preview
        });
    },

    onInternalSave: function (parameters, errors) {
        if (!this.getForm().isValid()) {
            errors.push('Required routing fields are missing.');
            return false;
        }

        parameters.routing = Ext.encode(this.getForm().getValues());
    }
});

Ext.reg('elements-tab-routing', Phlexible.elements.tab.Routing);
