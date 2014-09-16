Phlexible.frontendmedia.configuration.FieldConfigurationFile = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.frontendmedia.Strings,
    title: Phlexible.frontendmedia.Strings.file,
    iconCls: 'p-frontendmedia-field_file-icon',
    autoHeight: true,
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
                xtype: 'twincombobox',
                fieldLabel: this.strings.asset_types,
                width: 200,
                listWidth: 200,
                store: new Ext.data.JsonStore({
                    fields: ['key', 'title'],
                    data: [
                        {key: 'image', title: 'Image'},
                        {key: 'audio', title: 'Audio'},
                        {key: 'video', title: 'Video'},
                        {key: 'documents', title: 'Document'},
                        {key: 'flash', title: 'Flash'}
                    ]
                }),
                displayField: 'title',
                valueField: 'key',
                editable: false,
                triggerAction: 'all',
                mode: 'local'
            },
            {
                xtype: 'twincombobox',
                fieldLabel: this.strings.document_types,
                width: 200,
                listWidth: 200,
                store: new Ext.data.JsonStore({
                    fields: ['id', 'key', 'de', 'en'],
                    url: Phlexible.Router.generate('documenttypes_list'),
                    root: 'documenttypes',
                    sortInfo: {
                        field: 'key',
                        direction: 'asc'
                    }
                }),
                displayField: 'key',
                valueField: 'key',
                editable: false,
                triggerAction: 'all',
                mode: 'remote'
            }
        ];

        Phlexible.frontendmedia.configuration.FieldConfigurationFile.superclass.initComponent.call(this);
    },

    updateVisibility: function (type) {
        var isFile = type === 'file';
        this.getComponent(0).setDisabled(!isFile );
        this.getComponent(1).setDisabled(!isFile );
        this.setVisible(isFile );
    },

    loadData: function (fieldData, fieldType) {
        fieldData.asset_types = fieldData.asset_types || '';
        fieldData.document_types = fieldData.document_types || '';

        this.getComponent(0).setValue(fieldData.asset_types);
        this.getComponent(1).setValue(fieldData.document_types);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            asset_types: this.getComponent(0).getValue(),
            document_types: this.getComponent(1).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid()
            && this.getComponent(1).isValid();
    }
});

Ext.reg('frontendmedia-configuration-field-configuration-file', Phlexible.frontendmedia.configuration.FieldConfigurationFile);