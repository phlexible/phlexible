Phlexible.elementfinder.configuration.FieldConfigurationFinder = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.elementfinder.Strings,
    title: Phlexible.elementfinder.Strings.finder,
    iconCls: 'p-elementfinder-finder-icon',
    autoHeight: true,
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
                xtype: 'multiselect',
                fieldLabel: Phlexible.elements.Strings.elementtype,
                name: 'element_type_ids',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('elementfinder_catch_elementtypes'),
                    root: 'elementtypes',
                    fields: ['id', 'title', 'icon'],
                    autoLoad: true,
                    listeners: {
                        load: function () {
                            if (this.element_type_ids) {
                                this.getComponent(0).setValue(this.element_type_ids);
                            }
                        },
                        scope: this
                    }
                }),
                width: 300,
                emptyText: Phlexible.elements.Strings.select_elementtype,
                valueField: 'id',
                displayField: 'title',
                tpl: '<tpl for="."><div class="ux-mselect-item' + ((Ext.isIE || Ext.isIE7) ? '" unselectable=on' : ' x-unselectable"') + '><img src="{icon}" width="18" height="18" style="vertical-align: middle;" /> {title}</div></tpl>',
                //                    width:280,
                height: 165,
                allowBlank: true
            },{
                xtype: 'checkbox',
                fieldLabel: Phlexible.elements.Strings.elements,
                boxLabel: Phlexible.elements.Strings.in_navigation,
                name: 'in_navigation'
            },{
                xtype: 'numberfield',
                fieldLabel: this.strings.max_depth,
                name: 'max_depth',
                width: 30,
                allowBlank: true
            },{
                xtype: 'combo',
                width: 283,
                listWidth: 300,
                fieldLabel: this.strings.filter,
                hiddenName: 'catch_filter',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('elementfinder_catch_filters'),
                    root: 'filters',
                    fields: ['name', 'class']
                }),
                displayField: 'name',
                valueField: 'class',
                mode: 'remote',
                emptyText: this.strings.no_filter,
                listClass: 'x-combo-list-big',
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true
            },{
                xtype: 'textfield',
                width: 300,
                fieldLabel: this.strings.template,
                name: 'template'
            }
        ];

        Phlexible.elementfinder.configuration.FieldConfigurationFinder.superclass.initComponent.call(this);
    },

    updateVisibility: function (type) {
        var isFinder = type === 'finder';
        this.getComponent(0).setDisabled(!isFinder);
        this.getComponent(1).setDisabled(!isFinder);
        this.getComponent(2).setDisabled(!isFinder);
        this.getComponent(3).setDisabled(!isFinder);
        this.getComponent(4).setDisabled(!isFinder);
        this.setVisible(isFinder);
    },

    loadData: function (fieldData, fieldType) {
        this.element_type_ids = fieldData.element_type_ids;
        this.getComponent(0).setValue(fieldData.element_type_ids);
        this.getComponent(1).setValue(fieldData.in_navigation);
        this.getComponent(2).setValue(fieldData.max_depth);
        this.getComponent(3).setValue(fieldData.filter);
        this.getComponent(4).setValue(fieldData.template);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            element_type_ids: this.getComponent(0).getValue(),
            in_navigation: this.getComponent(1).getValue(),
            max_depth: this.getComponent(2).getValue(),
            filter: this.getComponent(3).getValue(),
            template: this.getComponent(4).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid() &&
            this.getComponent(1).isValid() &&
            this.getComponent(2).isValid() &&
            this.getComponent(3).isValid() &&
            this.getComponent(4).isValid();
    }
});

Ext.reg('elementfinder-configuration-field-configuration-finder', Phlexible.elementfinder.configuration.FieldConfigurationFinder);