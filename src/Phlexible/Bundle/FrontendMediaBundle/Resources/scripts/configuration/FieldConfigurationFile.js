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
                hiddenName: 'assettype',
                fieldLabel: this.strings.asset_type,
                width: 200,
                listWidth: 200,
                store: new Ext.data.JsonStore({
                    fields: ['key', 'title'],
                    data: [
                        {key: 'image', title: 'Image'},
                        {key: 'audio', title: 'Audio'},
                        {key: 'video', title: 'Video'},
                        {key: 'document', title: 'Document'},
                        {key: 'flash', title: 'Flash'},
                        {key: 'archive', title: 'Archive'},
                        {key: 'other', title: 'Other'}
                    ]
                }),
                displayField: 'title',
                valueField: 'key',
                editable: false,
                triggerAction: 'all',
                mode: 'local'
            },
            {
                xtype: 'lovcombo',
                hiddenName: 'documenttypes',
                fieldLabel: this.strings.documenttypes,
                width: 200,
                listWidth: 200,
                store: new Ext.data.JsonStore({
                    fields: ['key', 'upperkey'],
                    url: Phlexible.Router.generate('documenttypes_list'),
                    root: 'documenttypes',
                    id: 'key',
                    sortInfo: {
                        field: 'key',
                        direction: 'asc'
                    },
                    autoLoad: true,
                    listeners: {
                        load: function() {
                            this.getComponent(1).setValue(this.getComponent(1).getValue());
                        },
                        scope: this
                    }
                }),
                displayField: 'upperkey',
                valueField: 'key',
                editable: false,
                triggerAction: 'all',
                mode: 'remote'
            },
            {
                xtype: 'twincombobox',
                hiddenName: 'viewMode',
                fieldLabel: this.strings.view_mode,
                width: 200,
                listWidth: 200,
                store: new Ext.data.JsonStore({
                    fields: ['key', 'title'],
                    data: [
                        {key: 'extralarge', title: 'Extra Large'},
                        {key: 'large', title: 'Large'},
                        {key: 'medium', title: 'Medium'},
                        {key: 'small', title: 'Small'},
                        {key: 'tile', title: 'Tile'},
                        {key: 'detail', title: 'Detail'}
                    ]
                }),
                displayField: 'title',
                valueField: 'key',
                editable: false,
                triggerAction: 'all',
                mode: 'local'
            }
        ];

        Phlexible.frontendmedia.configuration.FieldConfigurationFile.superclass.initComponent.call(this);
    },

    updateVisibility: function (type) {
        var isFile = type === 'file';
        this.getComponent(0).setDisabled(!isFile);
        this.getComponent(1).setDisabled(!isFile);
        this.getComponent(2).setDisabled(!isFile);
        this.setVisible(isFile);
    },

    loadData: function (fieldData, fieldType) {
        fieldData.assetType = fieldData.assetType || null;
        fieldData.documenttypes = fieldData.documenttypes || '';
        fieldData.viewMode = fieldData.viewMode || '';

        this.getComponent(0).setValue(fieldData.assetType);
        this.getComponent(1).setValue(fieldData.documenttypes);
        this.getComponent(2).setValue(fieldData.viewMode);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            assetType: this.getComponent(0).getValue(),
            documenttypes: this.getComponent(1).getValue(),
            viewMode: this.getComponent(2).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid()
            && this.getComponent(1).isValid()
            && this.getComponent(2).isValid();
    }
});

Ext.reg('frontendmedia-configuration-field-configuration-file', Phlexible.frontendmedia.configuration.FieldConfigurationFile);