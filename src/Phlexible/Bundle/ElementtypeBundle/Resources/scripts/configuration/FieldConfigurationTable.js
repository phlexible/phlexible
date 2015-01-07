Ext.provide('Phlexible.elementtypes.configuration.FieldConfigurationTable');

Phlexible.elementtypes.configuration.FieldConfigurationTable = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.table,
    iconCls: 'p-elementtype-field_table-icon',
    autoHeight: true,
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
//            xtype: 'numberfield',
                xtype: 'uxspinner',
                name: 'table_rows',
                fieldLabel: this.strings.table_rows,
                width: 183,
                maskRe: /[0-9]/,
                strategy: new Ext.ux.form.Spinner.NumberStrategy({
                    minValue: 0,
                    maxValue: 10
                })
            },
            {
//            xtype: 'numberfield',
                xtype: 'uxspinner',
                name: 'table_cols',
                fieldLabel: this.strings.table_cols,
                width: 183,
                maskRe: /[0-9]/,
                strategy: new Ext.ux.form.Spinner.NumberStrategy({
                    minValue: 0,
                    maxValue: 10
                })
            }
        ];

        Phlexible.elementtypes.configuration.FieldConfigurationTable.superclass.initComponent.call(this);
    },

    updateVisibility: function (type) {
        var isTable = type === 'table';
        this.getComponent(0).setDisabled(!isTable);
        this.getComponent(1).setDisabled(!isTable);
        this.setVisible(isTable);
    },

    loadData: function (fieldData, fieldType) {
        this.getComponent(0).setValue(fieldData.table_cols);
        this.getComponent(1).setValue(fieldData.table_rows);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            table_cols: this.getComponent(0).getValue(),
            table_rows: this.getComponent(1).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid()
            && this.getComponent(1).isValid();


    }
});

Ext.reg('elementtypes-configuration-field-configuration-table', Phlexible.elementtypes.configuration.FieldConfigurationTable);