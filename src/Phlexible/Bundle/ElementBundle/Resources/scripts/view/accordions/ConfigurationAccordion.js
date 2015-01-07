Ext.provide('Phlexible.elements.accordion.Configuration');

Phlexible.elements.accordion.Configuration = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elements.Strings.configuration,
    title: Phlexible.elements.Strings.configuration.configuration,
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
                boxLabel: this.strings.need_authentication
            },{
                // 2
                xtype: 'checkbox',
                name: 'https',
                hideLabel: true,
                boxLabel: this.strings.use_https
            },{
                // 3
                xtype: 'label',
                text: this.strings.route
            },{
                // 4
                xtype: 'textfield',
                name: 'route',
                hideLabel: true
            },{
                // 5
                xtype: 'label',
                text: this.strings.controller
            },{
                // 6
                xtype: 'textfield',
                name: 'controller',
                hideLabel: true
            },{
                // 7
                xtype: 'label',
                text: this.strings.template
            },{
                // 8
                xtype: 'textfield',
                name: 'template',
                hideLabel: true
            },
            {
                // 9
                xtype: 'label',
                text: this.strings.robots,
                style: 'font-weight: bold;'
            },
            {
                // 10
                xtype: 'checkbox',
                name: 'robotsNoIndex',
                hideLabel: true,
                boxLabel: this.strings.robots_no_index
            },
            {
                // 11
                xtype: 'checkbox',
                name: 'robotsNoFollow',
                hideLabel: true,
                boxLabel: this.strings.robots_no_follow
            },
            {
                // 12
                xtype: 'label',
                text: this.strings.internal_search,
                style: 'font-weight: bold;'
            },
            {
                // 13
                xtype: 'checkbox',
                name: 'searchNoIndex',
                hideLabel: true,
                boxLabel: this.strings.search_no_index
            },
            {
                // 14
                xtype: 'label',
                text: this.strings.caching,
                style: 'font-weight: bold;'
            },
            {
                // 15
                xtype: 'checkbox',
                name: 'noCache',
                hideLabel: true,
                boxLabel: this.strings.no_cache
            },{
                // 16
                xtype: 'checkbox',
                name: 'cachePrivate',
                hideLabel: true,
                boxLabel: this.strings.private
            },{
                // 17
                xtype: 'numberfield',
                name: 'cacheMaxAge',
                fieldLabel: this.strings.max_age,
                width: 60
            },{
                // 18
                xtype: 'numberfield',
                name: 'cacheSharedMaxAge',
                fieldLabel: this.strings.shared_max_age,
                width: 60
            }
        ];
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

        if (data.properties.et_type === 'part') {
            this.getComponent(0).hide();
            this.getComponent(2).hide();
            this.getComponent(3).hide();
            this.getComponent(4).hide();
            this.getComponent(5).hide();
            this.getComponent(6).hide();
            this.getComponent(9).hide();
            this.getComponent(10).hide();
            this.getComponent(11).hide();
            this.getComponent(12).hide();
            this.getComponent(13).hide();
        } else {
            this.getComponent(0).show();
            this.getComponent(2).show();
            this.getComponent(3).show();
            this.getComponent(4).show();
            this.getComponent(5).show();
            this.getComponent(6).show();
            this.getComponent(9).show();
            this.getComponent(10).show();
            this.getComponent(11).show();
            this.getComponent(12).show();
            this.getComponent(13).show();
        }

        this.show();
    },

    getData: function () {
        var form = this.getComponent(0).getForm();

        return form.getValues();
    }

});

Ext.reg('elements-configurationaccordion', Phlexible.elements.accordion.Configuration);
