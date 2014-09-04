Phlexible.elements.accordion.Configuration = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.configuration,
    cls: 'p-elements-page-accordion',
    iconCls: 'p-element-action-icon',
    border: false,
    autoHeight: true,
    labelWidth: 100,
    bodyStyle: 'padding: 5px',
    //labelAlign: 'top',

    key: 'configuration',

    initComponent: function () {
        this.populateItems();

        Phlexible.elements.accordion.Configuration.superclass.initComponent.call(this);
    },

    populateItems: function () {
        this.items = [
            {
                // 0
                xtype: 'checkbox',
                name: 'navigation',
                hideLabel: true,
                boxLabel: this.strings.in_navigation
            },{
                // 1
                xtype: 'checkbox',
                name: 'needAuthentication',
                hideLabel: true,
                boxLabel: '_need_authentication'
            },{
                // 2
                xtype: 'checkbox',
                name: 'https',
                hideLabel: true,
                boxLabel: this.strings.use_https
            },{
                // 3
                xtype: 'textfield',
                name: 'route',
                fieldLabel: '_route'
            },{
                // 4
                xtype: 'textfield',
                name: 'controller',
                fieldLabel: '_controller'
            },{
                // 5
                xtype: 'textfield',
                name: 'template',
                fieldLabel: '_template'
            },
            {
                // 6
                xtype: 'label',
                text: '_robots',
                style: 'font-weight: bold;'
            },
            {
                // 7
                xtype: 'checkbox',
                name: 'robotsNoIndex',
                hideLabel: true,
                boxLabel: '_no_index'
            },
            {
                // 8
                xtype: 'checkbox',
                name: 'robotsNoFollow',
                hideLabel: true,
                boxLabel: '_no_follow'
            },
            {
                // 9
                xtype: 'label',
                text: '_internal_search',
                style: 'font-weight: bold;'
            },
            {
                // 10
                xtype: 'checkbox',
                name: 'searchNoIndex',
                hideLabel: true,
                boxLabel: '_no_index'
            },
            {
                // 11
                xtype: 'label',
                text: '_caching',
                style: 'font-weight: bold;'
            },
            {
                // 12
                xtype: 'checkbox',
                name: 'noCache',
                hideLabel: true,
                boxLabel: '_no_cache'
            },{
                // 13
                xtype: 'checkbox',
                name: 'cachePrivate',
                hideLabel: true,
                boxLabel: '_private'
            },{
                // 14
                xtype: 'numberfield',
                name: 'cacheMaxAge',
                fieldLabel: '_max_age',
                width: 60
            },{
                // 15
                xtype: 'numberfield',
                name: 'cacheSharedMaxAge',
                fieldLabel: '_shared_max_age',
                width: 60
            }
        ];

        if (Phlexible.User.isGranted('elements_accordion_page_advanced')) {
        }
    },

    load: function (data) {
        if (data.properties.et_type !== 'full' && data.properties.et_type !== 'part') {
            this.hide();
            return;
        }

        if (data.configuration.routes && data.configuration.routes[data.properties.language]) {
            data.configuration.route = data.configuration.routes[data.properties.language];
        }

        this.getForm().setValues(data.configuration);

        if (Phlexible.User.isGranted('elements_accordion_page_advanced')) {
            //this.getComponent(4).setDisabled(this.getComponent(3).getValue());
        }

        if (data.properties.et_type === 'part') {
            this.getComponent(0).hide();
            this.getComponent(2).hide();
            this.getComponent(3).hide();
            this.getComponent(6).hide();
            this.getComponent(7).hide();
            this.getComponent(8).hide();
            this.getComponent(9).hide();
            this.getComponent(10).hide();
        } else {
            this.getComponent(0).show();
            this.getComponent(2).show();
            this.getComponent(3).show();
            this.getComponent(6).show();
            this.getComponent(7).show();
            this.getComponent(8).show();
            this.getComponent(9).show();
            this.getComponent(10).show();
        }

        this.show();
    },

    getData: function () {
        var form = this.getComponent(0).getForm();

        return form.getValues();
    }

});

Ext.reg('elements-configurationaccordion', Phlexible.elements.accordion.Configuration);
