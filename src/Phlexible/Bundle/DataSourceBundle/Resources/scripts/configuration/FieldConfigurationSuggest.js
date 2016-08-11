Ext.provide('Phlexible.datasources.configuration.FieldConfigurationSuggest');

Phlexible.datasources.configuration.FieldConfigurationSuggest = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.datasources.Strings,
    title: Phlexible.datasources.Strings.suggest,
    iconCls: 'p-datasource-field_suggest-icon',
    autoHeight: true,
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
                xtype: 'combo',
                editable: false,
                hiddenName: 'suggest_source',
                name: 'suggest_source',
                fieldLabel: this.strings.source,
                hideMode: 'display',
                allowBlank: false,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('datasources_list'),
                    root: 'datasources',
                    fields: ['id', 'title'],
                    autoLoad: true,
                    listeners: {
                        load: function() {
                            this.getComponent(0).setValue(this.getComponent(0).getValue());
                        },
                        scope: this
                    }
                }),
                displayField: 'title',
                valueField: 'id',
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                listWidth: 200,
                width: 182
            }
        ];

        Phlexible.datasources.configuration.FieldConfigurationSuggest.superclass.initComponent.call(this);
    },

    updateVisibility: function (type) {
        this.getComponent(0).setDisabled(type !== 'suggest');
        this.setVisible(type === 'suggest');
    },

    loadData: function (fieldData, fieldType) {
        this.getComponent(0).setValue(fieldData.suggest_source);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            suggest_source: this.getComponent(0).getValue() || ''
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid();
    }
});

Ext.reg('datasources-configuration-field-configuration-suggest', Phlexible.datasources.configuration.FieldConfigurationSuggest);
