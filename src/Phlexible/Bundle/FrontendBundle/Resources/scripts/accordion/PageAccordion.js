Phlexible.frontend.accordion.Page = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.page,
    cls: 'p-elements-page-accordion',
    iconCls: 'p-element-page-icon',
    border: false,
    autoHeight: true,
    labelWidth: 60,
    bodyStyle: 'padding: 5px',
    labelAlign: 'top',

    key: 'page',

    initComponent: function () {
        this.items = [
            {
                // 0
                xtype: 'checkbox',
                name: 'navigation',
                hideLabel: true,
                boxLabel: this.strings.in_navigation
            },
            {
                // 1
                xtype: 'checkbox',
                name: 'restricted',
                hideLabel: true,
                boxLabel: this.strings.is_restricted
            }
        ];

        if (Phlexible.User.isGranted('elements_accordion_page_advanced')) {
            this.items.push({
                // 3
                xtype: 'checkbox',
                name: 'disable_cache',
                hideLabel: true,
                boxLabel: this.strings.disable_caching,
                listeners: {
                    check: {
                        fn: function (c, state) {
                            this.getComponent(4).setDisabled(state);
                        },
                        scope: this
                    }
                }
            });
            this.items.push({
                // 4
                xtype: 'numberfield',
                name: 'cache_lifetime',
                fieldLabel: this.strings.cache_lifetime
            });
            this.items.push({
                // 5
                xtype: 'checkbox',
                name: 'https',
                hideLabel: true,
                boxLabel: this.strings.use_https
            });
            this.items.push({
                // 6
                xtype: 'combo',
                hiddenName: 'code',
                fieldLabel: this.strings.http_response_code,
                store: new Ext.data.SimpleStore({
                    fields: ['code', 'text'],
                    data: [
                        ['200', '200 OK'],
                        ['403', '403 Forbidden'],
                        ['404', '404 Not Found'],
                        ['500', '500 Internal Server Error']
                    ]
                }),
                width: 120,
                listWidth: 137,
                valueField: 'code',
                displayField: 'text',
                mode: 'local',
                typeAhead: false,
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true
            });
        }

        Phlexible.frontend.accordion.Page.superclass.initComponent.call(this);
    },

    load: function (data) {
        if (data.properties.et_type == 'structure' || data.properties.et_type == 'part') {
            this.hide();
            return;
        }

        var page = {
            navigation: data.page.navigation || false,
            restricted: data.page.restricted || false,
            disable_cache: data.page.disable_cache || false,
            cache_lifetime: data.page.cache_lifetime || '',
            code: data.page.code || 200,
            https: data.page.https || false
        };
        this.getForm().loadRecord(new Ext.data.Record(page));

        if (Phlexible.User.isGranted('elements_accordion_page_advanced')) {
            this.getComponent(4).setDisabled(this.getComponent(3).getValue());
        }

        this.show();
    },

    getData: function () {
        var form = this.getComponent(0).getForm();

        return form.getValues();
    }

});

Ext.reg('frontend-pageaccordion', Phlexible.frontend.accordion.Page);
