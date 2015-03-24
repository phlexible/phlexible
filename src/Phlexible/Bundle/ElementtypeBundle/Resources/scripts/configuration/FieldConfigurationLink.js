Ext.provide('Phlexible.elementtypes.configuration.FieldConfigurationLink');

Phlexible.elementtypes.configuration.FieldConfigurationLink = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.link,
    iconCls: 'p-elementtype-field_link-icon',
    autoHeight: true,
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
                xtype: 'checkboxgroup',
                fieldLabel: this.strings.link_types,
                columns: 1,
                allowBlank: false,
                items: [
                    {
                        boxLabel: this.strings.link_internal,
                        name: 'link_allow_internal',
                        listeners: {
                            check: function (cb, value) {
                                this.getComponent(1).setDisabled(!value && !this.getComponent(0).items.items[1].getValue());
                            },
                            scope: this
                        }
                    },
                    {
                        boxLabel: this.strings.link_intra,
                        name: 'link_allow_intra',
                        listeners: {
                            check: function (cb, value) {
                                this.getComponent(1).setDisabled(!value && !this.getComponent(0).items.items[0].getValue());
                            },
                            scope: this
                        }
                    },
                    {
                        boxLabel: this.strings.link_external,
                        name: 'link_allow_external'
                    },
                    {
                        boxLabel: this.strings.link_email,
                        name: 'link_allow_email'
                    }
                ]
            },
            {
                xtype: 'lovcombo',
                fieldLabel: this.strings.elementtypes,
                hiddenName: 'link_element_types',
                maxHeight: 200,
                width: 183,
                listWidth: 200,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('elementtypes_list', {type: 'full'}),
                    fields: ['id', 'title'],
                    id: 'id',
                    root: 'elementtypes',
                    autoLoad: true
                }),
                displayField: 'title',
                valueField: 'id',
                editable: false,
                triggerAction: 'all',
                mode: 'local'
            }
        ];

        Phlexible.elementtypes.configuration.FieldConfigurationLink.superclass.initComponent.call(this);
    },

    updateVisibility: function (type) {
        var isLink = type === 'link';
        this.getComponent(0).setDisabled(!isLink);
        this.getComponent(1).setDisabled(!isLink);
        this.setVisible(isLink);
    },

    loadData: function (fieldData, fieldType) {
        fieldData.link_element_types = fieldData.link_element_types || '';

        this.getComponent(0).items.items[0].setValue(fieldData.link_allow_internal);
        this.getComponent(0).items.items[1].setValue(fieldData.link_allow_intra);
        this.getComponent(0).items.items[2].setValue(fieldData.link_allow_external);
        this.getComponent(0).items.items[3].setValue(fieldData.link_allow_email);
        this.getComponent(1).setValue(fieldData.link_element_types);

        if (fieldData.link_allow_internal || fieldData.link_allow_intra) {
            this.getComponent(1).enable();
        }
        else {
            this.getComponent(1).disable();
        }

        this.isValid();
    },

    getSaveValues: function () {
        return {
            link_allow_internal: this.getComponent(0).items.items[0].getValue(),
            link_allow_intra: this.getComponent(0).items.items[1].getValue(),
            link_allow_external: this.getComponent(0).items.items[2].getValue(),
            link_allow_email: this.getComponent(0).items.items[3].getValue(),
            link_element_types: this.getComponent(1).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid()
            && this.getComponent(1).isValid();
    }
});

Ext.reg('elementtypes-configuration-field-configuration-link', Phlexible.elementtypes.configuration.FieldConfigurationLink);