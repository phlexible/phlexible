Phlexible.elementtypes.configuration.FieldConfigurationGroup = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.group,
    iconCls: 'p-elementtype-container_group-icon',
    autoHeight: true,
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
                xtype: 'uxspinner',
                name: 'repeat_min',
                fieldLabel: this.strings.repeat_min,
                width: 183,
                maskRe: /[0-9]/,
                strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 0})
            },
            {
                xtype: 'uxspinner',
                name: 'repeat_max',
                fieldLabel: this.strings.repeat_max,
                width: 183,
                maskRe: /[0-9]/,
                strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 0})
            },
            {
                xtype: 'uxspinner',
                name: 'repeat_default',
                fieldLabel: this.strings.repeat_default,
                width: 183,
                maskRe: /[0-9]/,
                strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 0})
            },
            {
                xtype: 'checkbox',
                fieldLabel: '',
                labelSeparator: '',
                boxLabel: this.strings.group_show_border,
                name: 'group_show_border'
            },
            {
                xtype: 'checkbox',
                fieldLabel: '',
                labelSeparator: '',
                boxLabel: this.strings.group_single_row,
                name: 'group_single_line'
            },
            {
                xtype: 'numberfield',
                fieldLabel: this.strings.label_width,
                name: 'label_width',
                width: 200
            }
        ];

        Phlexible.elementtypes.configuration.FieldConfigurationGroup.superclass.initComponent.call(this);
    },

    updateVisibility: function (type) {
        var isGroup = type === 'group';
        this.getComponent(0).setDisabled(!isGroup);
        this.getComponent(1).setDisabled(!isGroup);
        this.getComponent(2).setDisabled(!isGroup);
        this.getComponent(3).setDisabled(!isGroup);
        this.getComponent(4).setDisabled(!isGroup);
        this.getComponent(5).setDisabled(!isGroup);
        this.setVisible(isGroup);
    },

    loadData: function (fieldData, fieldType) {
        this.getComponent(0).setValue(fieldData.repeat_min);
        this.getComponent(1).setValue(fieldData.repeat_max);
        this.getComponent(2).setValue(fieldData.repeat_default);
        this.getComponent(3).setValue(fieldData.group_show_border);
        this.getComponent(4).setValue(fieldData.group_single_line);
        this.getComponent(5).setValue(fieldData.label_width);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            repeat_min: this.getComponent(0).getValue() || '',
            repeat_max: this.getComponent(1).getValue() || '',
            repeat_default: this.getComponent(2).getValue() || '',
            group_show_border: this.getComponent(3).getValue(),
            group_single_line: this.getComponent(4).getValue(),
            label_width: this.getComponent(5).getValue() || ''
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid()
            && this.getComponent(1).isValid()
            && this.getComponent(2).isValid()
            && this.getComponent(3).isValid()
            && this.getComponent(4).isValid()
            && this.getComponent(5).isValid();
    }
});

Ext.reg('elementtypes-configuration-field-configuration-group', Phlexible.elementtypes.configuration.FieldConfigurationGroup);