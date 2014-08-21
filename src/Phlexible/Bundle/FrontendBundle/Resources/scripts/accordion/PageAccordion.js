Phlexible.frontend.accordion.Page = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.configuration,
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
            },{
                // 1
                xtype: 'textfield',
                name: 'pattern',
                fieldLabel: 'Routing pattern'
            },{
                xtype: 'textfield',
                name: 'controller',
                fieldLabel: 'Routing controller'
            },{
                xtype: 'checkbox',
                name: 'restricted',
                hideLabel: true,
                boxLabel: '_needs_authentication'
            },{
                xtype: 'checkbox',
                name: 'https',
                hideLabel: true,
                boxLabel: this.strings.use_https
            },
            {
                // 2
                xtype: 'fieldset',
                title: '_cache',
                autoHeight: true,
                hidden: true,
                items: [{
                    xtype: 'checkbox',
                    name: 'disable_cache',
                    hideLabel: true,
                    boxLabel: this.strings.disable_caching
                },{
                    xtype: 'checkbox',
                    name: 'disable_cache',
                    hideLabel: true,
                    boxLabel: this.strings.disable_caching
                },{
                    xtype: 'checkbox',
                    name: 'disable_cache',
                    hideLabel: true,
                    boxLabel: this.strings.disable_caching
                },{
                    xtype: 'checkbox',
                    name: 'disable_cache',
                    hideLabel: true,
                    boxLabel: this.strings.disable_caching
                },{
                    xtype: 'checkbox',
                    name: 'disable_cache',
                    hideLabel: true,
                    boxLabel: this.strings.disable_caching
                }]
            }
        ];

        if (Phlexible.User.isGranted('elements_accordion_page_advanced')) {
        }

        Phlexible.frontend.accordion.Page.superclass.initComponent.call(this);
    },

    load: function (data) {
        if (data.properties.et_type == 'structure' || data.properties.et_type == 'part') {
            this.hide();
            return;
        }

        var pattern = null;
        if (data.attributes.routes && data.attributes.routes['de']) {
            pattern = data.attributes.routes['de'];
        }

        this.getForm().setValues({
            navigation: data.properties.navigation || false,
            restricted: data.attributes.restricted || false,
            pattern: pattern,
            controller: data.attributes.controller || null,
            https: data.attributes.https || false
        });

        if (Phlexible.User.isGranted('elements_accordion_page_advanced')) {
            //this.getComponent(4).setDisabled(this.getComponent(3).getValue());
        }

        this.show();
    },

    getData: function () {
        var form = this.getComponent(0).getForm();

        return form.getValues();
    }

});

Ext.reg('frontend-pageaccordion', Phlexible.frontend.accordion.Page);
