Phlexible.frontend.accordion.Teaser = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.teaser,
    cls: 'p-elements-page-accordion',
    iconCls: 'p-teasers-teaser-icon',
    border: false,
    autoHeight: true,
    labelWidth: 60,
    bodyStyle: 'padding: 5px',
    labelAlign: 'top',

    key: 'teaser',

    initComponent: function () {
        // 0
        this.items = [
            {
                // 0
                xtype: 'checkbox',
                name: 'disable_cache',
                hideLabel: true,
                boxLabel: this.strings.disable_caching,
                listeners: {
                    check: {
                        fn: function (c, state) {
                            this.getComponent(2).setDisabled(state);
                        },
                        scope: this
                    }
                }
            },
            {
                // 1
                xtype: 'numberfield',
                name: 'cache_lifetime',
                fieldLabel: this.strings.cache_lifetime
            }
        ];

        Phlexible.frontend.accordion.Teaser.superclass.initComponent.call(this);
    },

    load: function (data) {
        if (data.properties.et_type != 'part') {
            this.hide();
            return;
        }

        this.getForm().loadRecord(new Ext.data.Record(data.teaser));

        this.getComponent(1).setDisabled(this.getComponent(1).getValue());

        this.show();
    },

    getData: function () {
        var form = this.getForm();

        return form.getValues();
    }
});

Ext.reg('frontend-teaseraccordion', Phlexible.frontend.accordion.Teaser);